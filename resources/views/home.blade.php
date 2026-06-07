@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="container-fluid">
    @php
        $renderCarrera = function($c) {
            if (is_null($c) || $c === '') return '-';
            if (is_string($c)) {
                $d = json_decode($c, true);
                if (is_array($d) && isset($d['nombre'])) return $d['nombre'];
                return $c;
            }
            if (is_object($c) && isset($c->nombre)) return $c->nombre;
            if (is_array($c) && isset($c['nombre'])) return $c['nombre'];
            return (string) $c;
        };
    @endphp
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-white shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">Bienvenido, {{ $user->name }}</h2>
                    <p class="card-text text-muted">Rol asignado: <strong>{{ $role ?: 'Usuario' }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    @if ($role === 'Administrador')
        <div class="row">
            @foreach ($metrics as $label => $value)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $value }}</h3>
                            <p>{{ $label }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Resumen administrativo</div>
                    <div class="card-body">
                        <p>Accede a los módulos de gestión para administrar grupos, docentes y postulantes.</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($role === 'Estudiante')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Datos del estudiante</div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $user->name }}</p>
                        <p><strong>Correo:</strong> {{ $user->email }}</p>
                        @if ($student)
                            <p><strong>CI:</strong> {{ $student->ci }}</p>
                            <p><strong>Carrera 1:</strong> {{ $renderCarrera($student->carrera1) }}</p>
                            <p><strong>Carrera 2:</strong> {{ $renderCarrera($student->carrera2) }}</p>
                        @else
                            <p class="text-muted">No se encontró información adicional del postulante.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif ($role === 'Docente')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Datos del docente</div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $user->name }}</p>
                        <p><strong>Correo:</strong> {{ $user->email }}</p>
                        @if ($docente)
                            <p><strong>CI:</strong> {{ $docente->ci }}</p>
                            <p><strong>Categoría:</strong> {{ $docente->categoria }}</p>
                            <p><strong>Turno:</strong> {{ $docente->turno }}</p>
                        @else
                            <p class="text-muted">No se encontró información adicional del docente.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>
                    <div class="card-body">
                        <p>Accede al menú para ver las opciones disponibles según tu rol.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@stop

@section('css')
@stop

@section('js')
@stop