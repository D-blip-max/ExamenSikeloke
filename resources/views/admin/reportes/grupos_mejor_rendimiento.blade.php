@extends('adminlte::page')

@section('content_header')
    <h1><b>Grupos con mayor cantidad de aprobados</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Grupos con mayor cantidad de aprobados</h3>
            <div>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'grupos_mejor_rendimiento') . '?format=csv') }}" class="btn btn-sm btn-outline-primary">Exportar CSV</a>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'grupos_mejor_rendimiento') . '?format=pdf') }}" class="btn btn-sm btn-outline-secondary">Exportar PDF</a>
                <a href="{{ url('admin/reportes') }}" class="btn btn-sm btn-outline-dark">Volver</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead><tr><th>#</th><th>Grupo</th><th>Aprobados</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($grupos as $g)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $g['grupo'] }}</td>
                                <td>{{ $g['aprobados'] }}</td>
                                <td>{{ $g['total'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
