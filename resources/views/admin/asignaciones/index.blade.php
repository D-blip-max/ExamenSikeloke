@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Asignaciones</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Asignaciones de grupos, materias y docentes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Crear nueva asignación
                        </button>

                        <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="ModalCreateLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #007bff; color: white;">
                                        <h5 class="modal-title" id="ModalCreateLabel">Nueva asignación</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ url('/admin/asignaciones/create') }}" method="POST">
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Grupo</label><b> (*)</b>
                                                        <select class="form-control" name="grupo_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($grupos as $grupo)
                                                                <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                                                    {{ $grupo->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('grupo_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Materia</label><b> (*)</b>
                                                        <select class="form-control" name="materia_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($materias as $materia)
                                                                <option value="{{ $materia->id }}" {{ old('materia_id') == $materia->id ? 'selected' : '' }}>
                                                                    {{ $materia->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('materia_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Docente</label><b> (*)</b>
                                                        <select class="form-control" name="docente_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($docentes as $docente)
                                                                <option value="{{ $docente->id }}" {{ old('docente_id') == $docente->id ? 'selected' : '' }}>
                                                                    {{ $docente->nombre }} - {{ $docente->especialidad }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('docente_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Aula</label><b> (*)</b>
                                                        <select class="form-control" name="aula_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($aulas as $aula)
                                                                <option value="{{ $aula->id }}" {{ old('aula_id') == $aula->id ? 'selected' : '' }}>
                                                                    {{ $aula->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('aula_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Día</label><b> (*)</b>
                                                        <select class="form-control" name="dia_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($dias as $dia)
                                                                <option value="{{ $dia->id }}" {{ old('dia_id') == $dia->id ? 'selected' : '' }}>
                                                                    {{ $dia->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('dia_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Horario</label><b> (*)</b>
                                                        <select class="form-control" name="horario_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($horarios as $horario)
                                                                <option value="{{ $horario->id }}" {{ old('horario_id') == $horario->id ? 'selected' : '' }}>
                                                                    {{ $horario->horaInicio }} - {{ $horario->horaFin }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('horario_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <hr>
                                                <div class="col-md-12 d-flex justify-content-end">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary ml-2">Guardar</button>
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
                    <table id="example" class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Grupo</th>
                                <th>Materia</th>
                                <th>Docente</th>
                                <th>Aula</th>
                                <th>Día</th>
                                <th>Horario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($asignaciones as $asignacion)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $asignacion->grupo->nombre }}</td>
                                    <td>{{ $asignacion->materia->nombre }}</td>
                                    <td>{{ $asignacion->docente->nombre }}</td>
                                    <td>{{ $asignacion->aula->nombre }}</td>
                                    <td>{{ $asignacion->dia->nombre }}</td>
                                    <td>{{ $asignacion->horario->horaInicio }} - {{ $asignacion->horario->horaFin }}</td>
                                    <td>
                                        <div class="row d-flex justify-content-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ModalUpdate{{ $asignacion->id }}">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </button>

                                            <form action="{{ url('/admin/asignaciones/' . $asignacion->id) }}" method="POST" id="deleteForm{{ $asignacion->id }}" class="ml-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="confirmDelete{{ $asignacion->id }}(event)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                        <script>
                                            function confirmDelete{{ $asignacion->id }}(event) {
                                                event.preventDefault();
                                                Swal.fire({
                                                    title: '¿Eliminar esta asignación?',
                                                    text: '',
                                                    icon: 'question',
                                                    showDenyButton: true,
                                                    confirmButtonText: 'Eliminar',
                                                    confirmButtonColor: '#a5161d',
                                                    denyButtonColor: '#270a0a',
                                                    denyButtonText: 'Cancelar',
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById('deleteForm{{ $asignacion->id }}').submit();
                                                    }
                                                });
                                            }
                                        </script>

                                        <div class="modal fade" id="ModalUpdate{{ $asignacion->id }}" tabindex="-1" aria-labelledby="ModalUpdateLabel{{ $asignacion->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #09ae5b; color: white;">
                                                        <h5 class="modal-title" id="ModalUpdateLabel{{ $asignacion->id }}">Editar asignación</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('/admin/asignaciones/' . $asignacion->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Grupo</label><b> (*)</b>
                                                                        <select class="form-control" name="grupo_id" required>
                                                                            <option value="">Seleccione...</option>
                                                                            @foreach ($grupos as $grupo)
                                                                                <option value="{{ $grupo->id }}" {{ old('grupo_id', $asignacion->grupo_id) == $grupo->id ? 'selected' : '' }}>
                                                                                    {{ $grupo->nombre }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('grupo_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Materia</label><b> (*)</b>
                                                                        <select class="form-control" name="materia_id" required>
                                                                            <option value="">Seleccione...</option>
                                                                            @foreach ($materias as $materia)
                                                                                <option value="{{ $materia->id }}" {{ old('materia_id', $asignacion->materia_id) == $materia->id ? 'selected' : '' }}>
                                                                                    {{ $materia->nombre }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('materia_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Docente</label><b> (*)</b>
                                                                        <select class="form-control" name="docente_id" required>
                                                                            <option value="">Seleccione...</option>
                                                                            @foreach ($docentes as $docente)
                                                                                <option value="{{ $docente->id }}" {{ old('docente_id', $asignacion->docente_id) == $docente->id ? 'selected' : '' }}>
                                                                                    {{ $docente->nombre }} - {{ $docente->especialidad }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('docente_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Aula</label><b> (*)</b>
                                                                        <select class="form-control" name="aula_id" required>
                                                                            <option value="">Seleccione...</option>
                                                                            @foreach ($aulas as $aula)
                                                                                <option value="{{ $aula->id }}" {{ old('aula_id', $asignacion->aula_id) == $aula->id ? 'selected' : '' }}>
                                                                                    {{ $aula->nombre }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('aula_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Día</label><b> (*)</b>
                                                                        <select class="form-control" name="dia_id" required>
                                                                            <option value="">Seleccione...</option>
                                                                            @foreach ($dias as $dia)
                                                                                <option value="{{ $dia->id }}" {{ old('dia_id', $asignacion->dia_id) == $dia->id ? 'selected' : '' }}>
                                                                                    {{ $dia->nombre }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('dia_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Horario</label><b> (*)</b>
                                                                        <select class="form-control" name="horario_id" required>
                                                                            <option value="">Seleccione...</option>
                                                                            @foreach ($horarios as $horario)
                                                                                <option value="{{ $horario->id }}" {{ old('horario_id', $asignacion->horario_id) == $horario->id ? 'selected' : '' }}>
                                                                                    {{ $horario->horaInicio }} - {{ $horario->horaFin }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('horario_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <hr>
                                                                <div class="col-md-12 d-flex justify-content-end">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-success ml-2">Actualizar</button>
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
