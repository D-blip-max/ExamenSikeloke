@extends('adminlte::page')

@section('content_header')
    <h1><b>Cantidad de grupos habilitados</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Cantidad de grupos habilitados</h3>
            <div>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'cantidad_grupos') . '?format=csv') }}" class="btn btn-sm btn-outline-primary">Exportar CSV</a>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'cantidad_grupos') . '?format=pdf') }}" class="btn btn-sm btn-outline-secondary">Exportar PDF</a>
                <a href="{{ url('admin/reportes') }}" class="btn btn-sm btn-outline-dark">Volver</a>
            </div>
        </div>
        <div class="card-body">
            <p>Total inscritos: <strong>{{ $total }}</strong></p>
            <p>Capacidad por grupo (asumida): <strong>{{ $capacidad }}</strong></p>
            <p>Grupos necesarios: <strong>{{ $gruposNecesarios }}</strong></p>
        </div>
    </div>
@stop
