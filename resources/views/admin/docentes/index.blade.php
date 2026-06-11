@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Docentes</b></h1>
    <hr>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Docentes Registrados</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control float-right" placeholder="Buscar docente...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        &nbsp;
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Crear nuevo docente
                        </button>

                        <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #007bff; color: white;">
                                        <h5 class="modal-title" id="exampleModalLabel">Registro de un nuevo docente</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="docenteCreateForm" action="{{ url('/admin/docentes/create') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Carnet de Identidad (CI)</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" name="ci_create"
                                                                value="{{ old('ci_create') }}" required>
                                                        </div>
                                                        @error('ci_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nombre Completo</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" name="nombre_create"
                                                                value="{{ old('nombre_create') }}" required>
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
                                                        <label>Correo Electrónico</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                            </div>
                                                            <input type="email" class="form-control" name="correo_create"
                                                                value="{{ old('correo_create') }}" required>
                                                        </div>
                                                        @error('correo_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Especialidad</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" name="especialidad_create"
                                                                value="{{ old('especialidad_create') }}" required>
                                                        </div>
                                                        @error('especialidad_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Turno</label><b> (*)</b>
                                                        <select class="form-control" name="turno_id_create" required>
                                                            <option value="">Seleccione un turno</option>
                                                            @foreach ($turnos as $turno)
                                                                <option value="{{ $turno->id }}" {{ old('turno_id_create') == $turno->id ? 'selected' : '' }}>
                                                                    {{ $turno->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('turno_id_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Maestría</label><b> (*)</b>
                                                        <select class="form-control" name="maestria_create" required>
                                                            <option value="">Seleccione</option>
                                                            <option value="1" {{ old('maestria_create') == '1' ? 'selected' : '' }}>Sí</option>
                                                            <option value="0" {{ old('maestria_create') == '0' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                        @error('maestria_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Diplomado Educativo</label><b> (*)</b>
                                                        <select class="form-control" name="diplomado_edu_create" required>
                                                            <option value="">Seleccione</option>
                                                            <option value="1" {{ old('diplomado_edu_create') == '1' ? 'selected' : '' }}>Sí</option>
                                                            <option value="0" {{ old('diplomado_edu_create') == '0' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                        @error('diplomado_edu_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Estado</label><b> (*)</b>
                                                        <select class="form-control" name="estado_create" required>
                                                            <option value="">Seleccione estado</option>
                                                            <option value="ACTIVO" {{ old('estado_create') == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                                                            <option value="NO ACTIVO" {{ old('estado_create') == 'NO ACTIVO' ? 'selected' : '' }}>NO ACTIVO</option>
                                                        </select>
                                                        @error('estado_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <hr>
                                                <div class="row" style="width: 100%; justify-content: flex-end; padding-right: 15px;">
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
                    <table id="docentesTable" class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>CI</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Especialidad</th>
                                <th>Turno</th>
                                <th>Maestría</th>
                                <th>Diplomado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="docentesBody">
                            @forelse ($docentes as $docente)
                                <tr class="docente-row" data-search="{{ strtolower($docente->ci . ' ' . $docente->nombre . ' ' . $docente->correo . ' ' . $docente->especialidad) }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $docente->ci }}</td>
                                    <td>{{ $docente->nombre }}</td>
                                    <td>{{ $docente->correo }}</td>
                                    <td>{{ $docente->especialidad }}</td>
                                    <td>{{ $docente->turno->nombre ?? '-' }}</td>
                                    <td>{{ $docente->maestria ? 'Sí' : 'No' }}</td>
                                    <td>{{ $docente->diplomado_edu ? 'Sí' : 'No' }}</td>
                                    <td>{{ $docente->estado }}</td>
                                    <td>
                                        <div class="row d-flex justify-content-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#ModalUpdate{{ $docente->id }}">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </button>

                                            <form action="{{ url('/admin/docentes/' . $docente->id) }}" method="post"
                                                id="miFormulario{{ $docente->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="preguntar{{ $docente->id }}(event)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                        <script>
                                            function preguntar{{ $docente->id }}(event) {
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
                                                        document.getElementById('miFormulario{{ $docente->id }}').submit();
                                                    }
                                                });
                                            }
                                        </script>

                                        <div class="modal fade" id="ModalUpdate{{ $docente->id }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header"
                                                        style="background-color: #09ae5b; color: white;">
                                                        <h5 class="modal-title" id="exampleModalLabel">Editar docente</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="docente-update-form" action="{{ url('/admin/docentes/' . $docente->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Carnet de Identidad (CI)</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                                            </div>
                                                                            <input type="text" class="form-control"
                                                                                name="ci"
                                                                                value="{{ old('ci', $docente->ci) }}" required>
                                                                        </div>
                                                                        @error('ci')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Nombre Completo</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                                            </div>
                                                                            <input type="text" class="form-control"
                                                                                name="nombre"
                                                                                value="{{ old('nombre', $docente->nombre) }}" required>
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
                                                                        <label>Correo Electrónico</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                                            </div>
                                                                            <input type="email" class="form-control"
                                                                                name="correo"
                                                                                value="{{ old('correo', $docente->correo) }}" required>
                                                                        </div>
                                                                        @error('correo')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Especialidad</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                                                            </div>
                                                                            <input type="text" class="form-control"
                                                                                name="especialidad"
                                                                                value="{{ old('especialidad', $docente->especialidad) }}" required>
                                                                        </div>
                                                                        @error('especialidad')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Turno</label><b> (*)</b>
                                                                        <select class="form-control" name="turno_id" required>
                                                                            <option value="">Seleccione un turno</option>
                                                                            @foreach ($turnos as $turno)
                                                                                <option value="{{ $turno->id }}" {{ old('turno_id', $docente->turno_id) == $turno->id ? 'selected' : '' }}>
                                                                                    {{ $turno->nombre }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('turno_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Maestría</label><b> (*)</b>
                                                                        <select class="form-control" name="maestria" required>
                                                                            <option value="">Seleccione</option>
                                                                            <option value="1" {{ old('maestria', $docente->maestria) == '1' || old('maestria', $docente->maestria) == 1 ? 'selected' : '' }}>Sí</option>
                                                                            <option value="0" {{ old('maestria', $docente->maestria) == '0' || old('maestria', $docente->maestria) == 0 ? 'selected' : '' }}>No</option>
                                                                        </select>
                                                                        @error('maestria')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Diplomado Educativo</label><b> (*)</b>
                                                                        <select class="form-control" name="diplomado_edu" required>
                                                                            <option value="">Seleccione</option>
                                                                            <option value="1" {{ old('diplomado_edu', $docente->diplomado_edu) == '1' || old('diplomado_edu', $docente->diplomado_edu) == 1 ? 'selected' : '' }}>Sí</option>
                                                                            <option value="0" {{ old('diplomado_edu', $docente->diplomado_edu) == '0' || old('diplomado_edu', $docente->diplomado_edu) == 0 ? 'selected' : '' }}>No</option>
                                                                        </select>
                                                                        @error('diplomado_edu')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Estado</label><b> (*)</b>
                                                                        <select class="form-control" name="estado" required>
                                                                            <option value="">Seleccione estado</option>
                                                                            <option value="ACTIVO" {{ old('estado', $docente->estado) == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                                                                            <option value="NO ACTIVO" {{ old('estado', $docente->estado) == 'NO ACTIVO' ? 'selected' : '' }}>NO ACTIVO</option>
                                                                        </select>
                                                                        @error('estado')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <hr>
                                                                <div class="row" style="width: 100%; justify-content: flex-end; padding-right: 15px;">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit"
                                                                        class="btn btn-success">Actualizar</button>
                                                                </div>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No hay docentes registrados</td>
                                </tr>
                            @endforelse
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
    <script>
        $(document).ready(function() {
            // Búsqueda en tiempo real - lado del cliente
            $('#searchInput').keyup(function() {
                let search = $(this).val().toLowerCase();
                
                if (search === '') {
                    $('.docente-row').show();
                    return;
                }

                $('.docente-row').each(function() {
                    let searchData = $(this).data('search');
                    if (searchData.includes(search)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            @if ($errors->any())
                @if (session('modal_id'))
                    $('#ModalUpdate{{ session('modal_id') }}').modal('show');
                @else
                    $('#ModalCreate').modal('show');
                @endif
            @endif

            function validateDocenteForm(form) {
                const maestria = form.querySelector('[name="maestria"]') || form.querySelector('[name="maestria_create"]');
                const diplomado = form.querySelector('[name="diplomado_edu"]') || form.querySelector('[name="diplomado_edu_create"]');
                if (!maestria || !diplomado) return true;

                const maestriaVal = maestria.value;
                const diplomadoVal = diplomado.value;
                if (maestriaVal !== '1' || diplomadoVal !== '1') {
                    Swal.fire({
                        title: 'Validación',
                        text: 'Para continuar, Maestría y Diplomado Educativo deben ser Sí.',
                        icon: 'warning',
                        confirmButtonText: 'Entendido',
                    });
                    return false;
                }
                return true;
            }

            $('#docenteCreateForm').on('submit', function(event) {
                if (!validateDocenteForm(this)) {
                    event.preventDefault();
                }
            });

            $('.docente-update-form').on('submit', function(event) {
                if (!validateDocenteForm(this)) {
                    event.preventDefault();
                }
            });
        });
    </script>
@stop
