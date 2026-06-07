@extends('adminlte::page')

@section('content_header')
    <h1><b>Cantidad de grupos habilitados</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Cantidad de grupos habilitados</h3>
        </div>
        <div class="card-body">
            <p>Total inscritos: <strong>{{ $total }}</strong></p>
            <p>Capacidad por grupo (asumida): <strong>{{ $capacidad }}</strong></p>
            <p>Grupos necesarios: <strong>{{ $gruposNecesarios }}</strong></p>
        </div>
    </div>
@stop
