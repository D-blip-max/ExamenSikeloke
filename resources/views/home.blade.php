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
                <div class="card mb-4">
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Asignación de grupo</div>
                    <div class="card-body">
                        @if ($studentGroup)
                            <p><strong>Grupo asignado:</strong> {{ $studentGroup->nombre }}</p>
                            <p><strong>Turno:</strong> {{ $studentGroup->turno->nombre ?? 'No definido' }}</p>
                            @if ($studentGroupMaterias->isNotEmpty())
                                <p><strong>Materias asignadas en el grupo:</strong></p>
                                <ul>
                                    @foreach ($studentGroupMaterias as $asignacion)
                                        <li>{{ $asignacion->materia->nombre ?? 'Materia sin nombre' }}
                                            @if ($asignacion->dia || $asignacion->horario)
                                                — {{ $asignacion->dia->nombre ?? '' }} {{ $asignacion->horario->horaInicio ?? '' }} - {{ $asignacion->horario->horaFin ?? '' }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No hay materias asignadas todavía en este grupo.</p>
                            @endif
                        @else
                            <p class="text-muted">No tienes un grupo asignado.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif ($role === 'Docente')
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Datos del docente</div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $user->name }}</p>
                        <p><strong>Correo:</strong> {{ $user->email }}</p>
                        @if ($docente)
                            <p><strong>CI:</strong> {{ $docente->ci }}</p>
                            <p><strong>Categoría:</strong> {{ $docente->categoria ?? '-' }}</p>
                            <p><strong>Turno:</strong> {{ $docente->turno->nombre ?? 'No definido' }}</p>
                        @else
                            <p class="text-muted">No se encontró información adicional del docente.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Horario asignado</div>
                    <div class="card-body">
                        @if ($docente && $docenteSchedule->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Grupo</th>
                                            <th>Materia</th>
                                            <th>Día</th>
                                            <th>Horario</th>
                                            <th>Aula</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($docenteSchedule as $asignacion)
                                            <tr>
                                                <td>{{ $asignacion->grupo->nombre ?? '-' }}</td>
                                                <td>{{ $asignacion->materia->nombre ?? '-' }}</td>
                                                <td>{{ $asignacion->dia->nombre ?? '-' }}</td>
                                                <td>{{ $asignacion->horario ? ($asignacion->horario->horaInicio . ' - ' . $asignacion->horario->horaFin) : '-' }}</td>
                                                <td>{{ $asignacion->aula->nombre ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No hay horario asignado para este docente.</p>
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