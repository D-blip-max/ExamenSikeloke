@extends('adminlte::page')

@section('content_header')
    <h1><b>{{ $titulo ?? 'Lista general de postulantes' }}</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ $titulo ?? 'Lista general de postulantes' }}</h3>
            <div>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'lista') . '?format=csv') }}" class="btn btn-sm btn-outline-primary">Exportar CSV</a>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'lista') . '?format=pdf') }}" class="btn btn-sm btn-outline-secondary">Exportar PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>CI</th>
                            @if(isset($promedios))<th>Promedio</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($postulantes as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nombres ?? '' }}</td>
                                <td>{{ $p->apellidos ?? '' }}</td>
                                <td>{{ $p->ci ?? '' }}</td>
                                @if(isset($promedios))<td>{{ round($promedios[$p->id] ?? 0, 2) }}</td>@endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
