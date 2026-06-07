@extends('adminlte::page')

@section('content_header')
    <h1><b>CU19 · Consultar Resultados (Admitidos)</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Admitidos</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalAssign">
                            Asignar carrera a postulante
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Postulante</th>
                                <th>Carrera asignada</th>
                                <th>Opción</th>
                                <th>Promedio final</th>
                                <th>Fecha asignación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admitidos as $admitido)
                                <tr @if ($admitido->opcion_asignada == 'PENDIENTE') style="background-color: #fff3cd;" @endif>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $admitido->postulante->nombres }} {{ $admitido->postulante->apellidos }}</td>
                                    <td>
                                        @if ($admitido->carrera_id)
                                            {{ $admitido->carrera->nombre }}
                                        @else
                                            <span class="badge badge-warning">PENDIENTE</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $admitido->opcion_asignada }}
                                        @if ($admitido->opcion_asignada == 'PENDIENTE')
                                            <br>
                                            <small class="text-muted">(Automático)</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($admitido->promedio_final, 2) }}</td>
                                    <td>{{ $admitido->fecha_asignacion }}</td>
                                    <td>
                                        <form action="{{ url('/admin/admitidos/' . $admitido->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalAssign" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #007bff; color: white;">
                    <h5 class="modal-title" id="exampleModalLabel">Asignar carrera</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/admin/admitidos/asignar') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="postulante">Postulante</label>
                            <select name="postulante_id" class="form-control" required>
                                <option value="">Seleccione un postulante</option>
                                @foreach ($postulantes as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombres }} {{ $p->apellidos }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <hr>
                            <div class="row">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Asignar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('mensaje'))
        <script>
            $(function() {
                Swal.fire({
                    icon: '{{ session('icono') ?? 'info' }}',
                    title: '{{ session('mensaje') }}',
                });
            });
        </script>
    @endif

@stop
