@extends('adminlte::page')
@section('title', 'Bitácora')
@section('content_header')
    <h1>Registro de Actividades (Bitácora)</h1>
    <hr>
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Actividades Registradas</h3>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bitacoras as $bitacora)
                                <tr>
                                    <td>{{ $bitacora->usuario->name }}</td>
                                    <td>{{ $bitacora->accion }}</td>
                                    <td>{{ $bitacora->hora ? \Illuminate\Support\Carbon::parse($bitacora->hora)->format('d/m/Y H:i:s') : '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay actividades registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop