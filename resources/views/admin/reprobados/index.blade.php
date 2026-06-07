@extends('adminlte::page')

@section('content_header')
    <h1><b>CU19 · Consultar Resultados (Reprobados)</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Reprobados</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalCreate">
                            Registrar reprobado
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="example" class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Postulante</th>
                                <th>Promedio final</th>
                                <th>Motivo</th>
                                <th>Detalle</th>
                                <th>Fecha registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reprobados as $reprobado)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $reprobado->postulante->nombres }} {{ $reprobado->postulante->apellidos }}</td>
                                    <td>{{ number_format($reprobado->promedio_final, 2) }}</td>
                                    <td>{{ $reprobado->motivo }}</td>
                                    <td>{{ $reprobado->detalle }}</td>
                                    <td>{{ $reprobado->fecha_registro }}</td>
                                    <td>
                                        <form action="{{ url('/admin/reprobados/' . $reprobado->id) }}" method="post" id="miFormulario{{ $reprobado->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="preguntar{{ $reprobado->id }}(event)">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </button>
                                        </form>

                                        <script>
                                            function preguntar{{ $reprobado->id }}(event) {
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
                                                        document.getElementById('miFormulario{{ $reprobado->id }}').submit();
                                                    }
                                                });
                                            }
                                        </script>
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
                <div class="modal-header" style="background-color: #dc3545; color: white;">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar reprobado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/admin/reprobados/create') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Postulante</label><b> (*)</b>
                                    <select class="form-control" name="postulante_id" required>
                                        <option value="">Seleccione un postulante</option>
                                        @foreach ($postulantes as $postulante)
                                            <option value="{{ $postulante->id }}" @if (old('postulante_id') == $postulante->id) selected @endif>
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
                                    <label for="">Promedio final</label><b> (*)</b>
                                    <input type="number" class="form-control" name="promedio_final" value="{{ old('promedio_final') }}" min="0" max="100" step="0.01" required>
                                    @error('promedio_final')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Motivo</label><b> (*)</b>
                                    <input type="text" class="form-control" name="motivo" value="{{ old('motivo') }}" required>
                                    @error('motivo')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Detalle</label>
                                    <textarea class="form-control" name="detalle" rows="3">{{ old('detalle') }}</textarea>
                                    @error('detalle')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <hr>
                            <div class="row">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Guardar</button>
                            </div>
                        </div>
                    </form>
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
                @if (session('modal_id') === 'create')
                    $('#ModalCreate').modal('show');
                @endif
            });
        </script>
    @endif
@stop
