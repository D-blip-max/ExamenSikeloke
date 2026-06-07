@extends('adminlte::page')

@section('content_header')
    <h1><b>Docentes por grupos</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Docentes por grupos</h3>
        </div>
        <div class="card-body">
            @foreach($asignaciones as $grupoId => $items)
                <h5>{{ $items->first()->grupo->nombre ?? 'Grupo '.$grupoId }}</h5>
                <ul>
                    @foreach($items as $a)
                        <li>{{ $a->docente->nombre ?? 'Docente' }} - {{ $a->materia->nombre ?? '' }} ({{ $a->horario->horaInicio ?? '' }} - {{ $a->horario->horaFin ?? '' }})</li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
@stop
