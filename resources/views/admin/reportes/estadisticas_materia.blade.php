@extends('adminlte::page')

@section('content_header')
    <h1><b>Estadísticas por materia</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Estadísticas por materia</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead><tr><th>Materia</th><th>Promedio</th><th>Total notas</th><th>Aprobados</th></tr></thead>
                    <tbody>
                        @foreach($stats as $mat => $s)
                            <tr>
                                <td>{{ $mat }}</td>
                                <td>{{ round($s['promedio'],2) }}</td>
                                <td>{{ $s['total'] }}</td>
                                <td>{{ $s['aprobados'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
