@extends('adminlte::page')

@section('content_header')
    <h1><b>Reportes</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU21 · Generar Reporte</h3>
        </div>
        <div class="card-body">
            <div class="list-group">
                <a href="{{ url('admin/reportes/generar/lista') }}" class="list-group-item list-group-item-action">Lista general de postulantes</a>
                <a href="{{ url('admin/reportes/generar/datos_tabla') }}" class="list-group-item list-group-item-action">Datos por Tabla</a>
                <a href="{{ url('admin/reportes/generar/aprobados') }}" class="list-group-item list-group-item-action">Postulantes aprobados (>=60)</a>
                <a href="{{ url('admin/reportes/generar/reprobados') }}" class="list-group-item list-group-item-action">Postulantes reprobados (&lt;60)</a>
                <a href="{{ url('admin/reportes/generar/promedios') }}" class="list-group-item list-group-item-action">Promedios generales</a>
                <a href="{{ url('admin/reportes/generar/cantidad_grupos') }}" class="list-group-item list-group-item-action">Cantidad de grupos habilitados</a>
                <a href="{{ url('admin/reportes/voz') }}" class="list-group-item list-group-item-action">Consulta por voz</a>
                <a href="{{ url('admin/reportes/generar/estadisticas_materia') }}" class="list-group-item list-group-item-action">Estadísticas por materia</a>
                <a href="{{ url('admin/reportes/generar/docentes_por_grupos') }}" class="list-group-item list-group-item-action">Docentes por grupos</a>
                <a href="{{ url('admin/reportes/generar/grupos_mejor_rendimiento') }}" class="list-group-item list-group-item-action">Grupos con mayor cantidad de aprobados</a>
            </div>
        </div>
    </div>
@stop
