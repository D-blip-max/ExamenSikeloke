@extends('adminlte::page')

@section('content_header')
    <h1><b>Promedios generales</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Promedios generales</h3>
            <div>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'promedios') . '?format=csv') }}" class="btn btn-sm btn-outline-primary">Exportar CSV</a>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'promedios') . '?format=pdf') }}" class="btn btn-sm btn-outline-secondary">Exportar PDF</a>
                <a href="{{ url('admin/reportes') }}" class="btn btn-sm btn-outline-dark">Volver</a>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Promedio global:</strong> {{ round($promedioGlobal,2) }}</p>
            <h5>Promedios por postulante</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr><th>#</th><th>Postulante</th><th>Promedio</th></tr>
                    </thead>
                    <tbody>
                        @foreach($promediosPorPost as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional(\App\Models\Postulante::find($row->postulante_id))->apellidos ?? 'N/A' }}, {{ optional(\App\Models\Postulante::find($row->postulante_id))->nombres ?? '' }}</td>
                                <td>{{ round($row->promedio,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
