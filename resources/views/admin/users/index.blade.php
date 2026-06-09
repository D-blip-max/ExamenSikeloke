@extends('adminlte::page')

@section('content_header')
    <h1><b>CU02 · Gestionar Usuarios</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h3 class="card-title">Usuarios del sistema</h3>
                        <p class="text-muted mb-0">Crear, editar y eliminar cuentas con roles desde modales.</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                        <i class="fas fa-user-plus"></i> Crear usuario
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuario...">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                @foreach($users as $user)
                                    <tr class="user-row" data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $user->getRoleNames()->join(' ')) }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->getRoleNames()->first() ?? 'Sin rol' }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ModalUpdate{{ $user->id }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ url('/admin/users/' . $user->id) }}" method="POST" class="d-inline" id="deleteUserForm{{ $user->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteUser({{ $user->id }}, event)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($users->isEmpty())
                        <p class="text-center text-muted mt-3">No hay usuarios registrados.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="ModalCreateLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="ModalCreateLabel">Crear usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.users.create') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Nombre</label><b> (*)</b>
                            <input type="text" class="form-control" name="name_create" value="{{ old('name_create') }}" required>
                            @error('name_create')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label>Correo</label><b> (*)</b>
                            <input type="email" class="form-control" name="email_create" value="{{ old('email_create') }}" required>
                            @error('email_create')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label><b> (*)</b>
                            <input type="password" class="form-control" name="password_create" required>
                            @error('password_create')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label>Rol</label><b> (*)</b>
                            <select class="form-control" name="role_create" required>
                                <option value="">Seleccione un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role_create') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_create')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach($users as $user)
        <div class="modal fade" id="ModalUpdate{{ $user->id }}" tabindex="-1" aria-labelledby="ModalUpdateLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="ModalUpdateLabel{{ $user->id }}">Editar usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/admin/users/' . $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="modal_id" value="{{ $user->id }}">
                            <div class="form-group">
                                <label>Nombre</label><b> (*)</b>
                                <input type="text" class="form-control" name="name" value="{{ old('modal_id') == $user->id ? old('name', $user->name) : $user->name }}" required>
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group">
                                <label>Correo</label><b> (*)</b>
                                <input type="email" class="form-control" name="email" value="{{ old('modal_id') == $user->id ? old('email', $user->email) : $user->email }}" required>
                                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group">
                                <label>Nueva contraseña</label>
                                <input type="password" class="form-control" name="password" placeholder="Dejar en blanco para no cambiar">
                                @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group">
                                <label>Rol</label><b> (*)</b>
                                <select class="form-control" name="role" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ (old('modal_id') == $user->id ? old('role', $user->getRoleNames()->first()) : $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(old('modal_id'))
                    $('#ModalUpdate{{ old('modal_id') }}').modal('show');
                @else
                    $('#ModalCreate').modal('show');
                @endif
            });
        </script>
    @endif
@stop

@section('js')
    <script>
        const rows = document.querySelectorAll('.user-row');
        document.getElementById('searchInput').addEventListener('input', function () {
            const value = this.value.toLowerCase();
            rows.forEach(row => {
                row.style.display = row.dataset.search.includes(value) ? '' : 'none';
            });
        });

        function confirmDeleteUser(id, event) {
            event.preventDefault();
            Swal.fire({
                title: '¿Desea eliminar este usuario?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: 'Eliminar',
                denyButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteUserForm' + id).submit();
                }
            });
        }
    </script>
@stop
