@extends('adminlte::page')

@section('content_header')
    <h1><b>CU18 · Registrar Calificaciones</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Registrar calificaciones</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Registrar nueva nota
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="example" class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Postulante</th>
                                <th>Materia</th>
                                <th>Examen</th>
                                <th>Nota</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notas as $nota)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $nota->postulante->nombres }} {{ $nota->postulante->apellidos }}</td>
                                    <td>{{ $nota->materia->nombre }}</td>
                                    <td>{{ $nota->configExamen->numero_examen }}</td>
                                    <td>{{ $nota->nota }}</td>
                                    <td>
                                        <div class="row d-flex justify-content-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#ModalUpdate{{ $nota->id }}">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </button>

                                            <form action="{{ url('/admin/notas/' . $nota->id) }}" method="post"
                                                id="miFormulario{{ $nota->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="preguntar{{ $nota->id }}(event)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                        <script>
                                            function preguntar{{ $nota->id }}(event) {
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
                                                        document.getElementById('miFormulario{{ $nota->id }}').submit();
                                                    }
                                                });
                                            }
                                        </script>

                                        <div class="modal fade" id="ModalUpdate{{ $nota->id }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #09ae5b; color: white;">
                                                        <h5 class="modal-title" id="exampleModalLabel">Editar nota</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('/admin/notas/' . $nota->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="">Postulante</label><b> (*)</b>
                                                                        <select class="form-control" name="postulante_id" required>
                                                                            <option value="">Seleccione un postulante</option>
                                                                            @foreach ($postulantes as $postulante)
                                                                                <option value="{{ $postulante->id }}"
                                                                                    @if (old('postulante_id', $nota->postulante_id) == $postulante->id) selected @endif>
                                                                                    {{ $postulante->nombres }} {{ $postulante->apellidos }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('postulante_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="">Materia</label><b> (*)</b>
                                                                        <select class="form-control" name="materia_id" required>
                                                                            <option value="">Seleccione una materia</option>
                                                                            @foreach ($materias as $materia)
                                                                                <option value="{{ $materia->id }}"
                                                                                    @if (old('materia_id', $nota->materia_id) == $materia->id) selected @endif>
                                                                                    {{ $materia->nombre }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('materia_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="">Examen</label><b> (*)</b>
                                                                        <select class="form-control" name="config_examen_id" required>
                                                                            <option value="">Seleccione un examen</option>
                                                                            @foreach ($configPorcentajes as $config)
                                                                                <option value="{{ $config->id }}"
                                                                                    @if (old('config_examen_id', $nota->config_examen_id) == $config->id) selected @endif>
                                                                                    {{ $config->numero_examen }} - {{ $config->ponderacion }}%
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('config_examen_id')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="">Nota</label><b> (*)</b>
                                                                        <input type="number" class="form-control" name="nota"
                                                                            value="{{ old('nota', $nota->nota) }}"
                                                                            min="0" max="100" step="0.01" required>
                                                                        @error('nota')
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

    <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #007bff; color: white;">
                    <h5 class="modal-title" id="exampleModalLabel">Registro de una nueva nota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/admin/notas/create') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Postulante</label><b> (*)</b>
                                    <select class="form-control" name="postulante_id_create" required>
                                        <option value="">Seleccione un postulante</option>
                                        @foreach ($postulantes as $postulante)
                                            <option value="{{ $postulante->id }}"
                                                @if (old('postulante_id_create') == $postulante->id) selected @endif>
                                                {{ $postulante->nombres }} {{ $postulante->apellidos }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('postulante_id_create')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Materia</label><b> (*)</b>
                                    <select class="form-control" name="materia_id_create" required>
                                        <option value="">Seleccione una materia</option>
                                        @foreach ($materias as $materia)
                                            <option value="{{ $materia->id }}"
                                                @if (old('materia_id_create') == $materia->id) selected @endif>
                                                {{ $materia->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('materia_id_create')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Examen</label><b> (*)</b>
                                    <select class="form-control" name="config_examen_id_create" required>
                                        <option value="">Seleccione un examen</option>
                                        @foreach ($configPorcentajes as $config)
                                            <option value="{{ $config->id }}"
                                                @if (old('config_examen_id_create') == $config->id) selected @endif>
                                                {{ $config->numero_examen }} - {{ $config->ponderacion }}%
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('config_examen_id_create')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Nota</label><b> (*)</b>
                                    <input type="number" class="form-control" name="nota_create"
                                        value="{{ old('nota_create') }}" min="0" max="100" step="0.01" required>
                                    @error('nota_create')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <hr>
                            <div class="row">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($resumenPostulantes)
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Resumen de promedios por postulante</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Nro</th>
                                    <th>Postulante</th>
                                    <th>Promedio final</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resumenPostulantes as $postulanteId => $datos)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $datos['postulante']->nombres }} {{ $datos['postulante']->apellidos }}</td>
                                        <td>{{ number_format($datos['promedio_final'], 2) }}</td>
                                        <td>{{ $datos['estado'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
