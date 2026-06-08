@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Grupos</b></h1>
    <hr>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Grupos Registrados</h3>

                    <div class="card-tools">

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Crear nuevo grupo
                        </button>

                        <!-- Modal de Creacion -->
                        <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #007bff; color: white;">
                                        <h5 class="modal-title" id="exampleModalLabel">Registro de un nuevo grupo</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ url('/admin/grupos/create') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="">Nombre del grupo</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-layer-group"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" name="nombre_create"
                                                                value="{{ old('nombre_create') }}"
                                                                placeholder="Escriba aquí..." required>
                                                        </div>
                                                        @error('nombre_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Cupo máximo</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-users"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="cupo_maximo_create"
                                                                value="{{ old('cupo_maximo_create') }}"
                                                                min="1" placeholder="Número máximo" required>
                                                        </div>
                                                        @error('cupo_maximo_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Inscritos</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-user-check"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="inscritos_create"
                                                                value="{{ old('inscritos_create', 0) }}"
                                                                min="0" placeholder="Cantidad actual" required>
                                                        </div>
                                                        @error('inscritos_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="">Turno</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-clock"></i></span>
                                                            </div>
                                                            <select class="form-control" name="turno_id_create" required>
                                                                <option value="">Seleccione un turno</option>
                                                                @foreach($turnos as $turno)
                                                                    <option value="{{ $turno->id }}" {{ old('turno_id_create') == $turno->id ? 'selected' : '' }}>{{ $turno->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @error('turno_id_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <hr>
                                                <div class="row">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="example" class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Nombre</th>
                                <th>Cupo máximo</th>
                                <th>Inscritos</th>
                                <th>Turno</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grupos as $grupo)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $grupo->nombre }}</td>
                                    <td>{{ $grupo->cupo_maximo }}</td>
                                    <td>{{ $grupo->inscritos }}</td>
                                    <td>{{ $grupo->turno->nombre }}</td>
                                    <td>
                                        <div class="row d-flex justify-content-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#ModalUpdate{{ $grupo->id }}">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </button>

                                            <form action="{{ url('/admin/grupos/' . $grupo->id) }}" method="post"
                                                id="miFormulario{{ $grupo->id }}" class="ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="preguntar{{ $grupo->id }}(event)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                        <script>
                                            function preguntar{{ $grupo->id }}(event) {
                                                event.preventDefault();
                                                Swal.fire({
                                                    title: '¿Desea eliminar este registro?',
                                                    text: '',
                                                    icon: 'question',
                                                    showDenyButton: true,
                                                    confirmButtonText: 'Eliminar',
                                                    confirmButtonColor: '#a5161d',
                                                    denyButtonColor: '#270a0a',
                                                    denyButtonText: 'Cancelar',
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById('miFormulario{{ $grupo->id }}').submit();
                                                    }
                                                });
                                            }
                                        </script>

                                        <div class="modal fade" id="ModalUpdate{{ $grupo->id }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header"
                                                        style="background-color: #09ae5b; color: white;">
                                                        <h5 class="modal-title" id="exampleModalLabel">Editar grupo</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('/admin/grupos/' . $grupo->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="">Nombre del grupo</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i
                                                                                        class="fas fa-layer-group"></i></span>
                                                                            </div>
                                                                            <input type="text" class="form-control"
                                                                                name="nombre"
                                                                                value="{{ old('nombre', $grupo->nombre) }}"
                                                                                placeholder="Escriba aquí..." required>
                                                                        </div>
                                                                        @error('nombre')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="">Cupo máximo</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i
                                                                                        class="fas fa-users"></i></span>
                                                                            </div>
                                                                            <input type="number" class="form-control"
                                                                                name="cupo_maximo"
                                                                                value="{{ old('cupo_maximo', $grupo->cupo_maximo) }}"
                                                                                min="1" placeholder="Número máximo" required>
                                                                        </div>
                                                                        @error('cupo_maximo')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="">Inscritos</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i
                                                                                        class="fas fa-user-check"></i></span>
                                                                            </div>
                                                                            <input type="number" class="form-control"
                                                                                name="inscritos"
                                                                                value="{{ old('inscritos', $grupo->inscritos) }}"
                                                                                min="0" placeholder="Cantidad actual" required>
                                                                        </div>
                                                                        @error('inscritos')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="">Turno</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i
                                                                                        class="fas fa-clock"></i></span>
                                                                            </div>
                                                                            <select class="form-control" name="turno_id" required>
                                                                                <option value="">Seleccione un turno</option>
                                                                                @foreach($turnos as $turno)
                                                                                    <option value="{{ $turno->id }}" {{ old('turno_id', $grupo->turno_id) == $turno->id ? 'selected' : '' }}>{{ $turno->nombre }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        @error('turno_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <hr>
                                                                <div class="row">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-success">Actualizar</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

@stop


@section('css')
@stop

@section('js')
    @if ($errors->any())
        <script>
            $(document).ready(function() {
                @if (session('modal_id'))
                    $('#ModalUpdate{{ session('modal_id') }}').modal('show');
                @else
                    $('#ModalCreate').modal('show');
                @endif
            });
        </script>
    @endif
@stop
