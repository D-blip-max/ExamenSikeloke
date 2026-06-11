@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Asignaciones</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Asignaciones de grupos, materias y docentes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Crear nueva asignación
                        </button>

                        <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="ModalCreateLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #007bff; color: white;">
                                        <h5 class="modal-title" id="ModalCreateLabel">Nueva asignación</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ url('/admin/asignaciones/create') }}" method="POST">
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Grupo</label><b> (*)</b>
                                                        <select class="form-control" name="grupo_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($grupos as $grupo)
                                                                <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                                                    {{ $grupo->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('grupo_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Materia</label><b> (*)</b>
                                                        <select class="form-control" name="materia_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($materias as $materia)
                                                                <option value="{{ $materia->id }}" {{ old('materia_id') == $materia->id ? 'selected' : '' }}>
                                                                    {{ $materia->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('materia_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Docente</label><b> (*)</b>
                                                        <select class="form-control" name="docente_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($docentes as $docente)
                                                                <option value="{{ $docente->id }}" {{ old('docente_id') == $docente->id ? 'selected' : '' }}>
                                                                    {{ $docente->nombre }} - {{ $docente->especialidad }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('docente_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Aula</label><b> (*)</b>
                                                        <select class="form-control" name="aula_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($aulas as $aula)
                                                                <option value="{{ $aula->id }}" {{ old('aula_id') == $aula->id ? 'selected' : '' }}>
                                                                    {{ $aula->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('aula_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Día</label><b> (*)</b>
                                                        <select class="form-control" name="dia_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($dias as $dia)
                                                                <option value="{{ $dia->id }}" {{ old('dia_id') == $dia->id ? 'selected' : '' }}>
                                                                    {{ $dia->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('dia_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Horario</label><b> (*)</b>
                                                        <select class="form-control" name="horario_id" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach ($horarios as $horario)
                                                                <option value="{{ $horario->id }}" {{ old('horario_id') == $horario->id ? 'selected' : '' }}>
                                                                    {{ $horario->horaInicio }} - {{ $horario->horaFin }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('horario_id')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <hr>
                                                <div class="col-md-12 d-flex justify-content-end">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary ml-2">Guardar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="accordion" id="asignacionesAccordion">
                        @if(isset($asignacionesByGrupo) && $asignacionesByGrupo->count())
                            @foreach($asignacionesByGrupo as $grupoId => $grupoAsignaciones)
                                @php $grupo = $grupoAsignaciones->first()->grupo; @endphp
                                <div class="card grupo-card" data-grupo-id="{{ $grupoId }}">
                                    <div class="card-header p-0" id="headingAsig{{ $grupoId }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseAsig{{ $grupoId }}" aria-expanded="false" aria-controls="collapseAsig{{ $grupoId }}">
                                                <span>{{ $grupo->nombre ?? 'Grupo' }} ({{ $grupoAsignaciones->count() }} asignaciones)</span>
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseAsig{{ $grupoId }}" class="collapse" aria-labelledby="headingAsig{{ $grupoId }}" data-parent="#asignacionesAccordion">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm table-hover mb-0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Materia</th>
                                                            <th>Docente</th>
                                                            <th>Aula</th>
                                                            <th>Día</th>
                                                            <th>Horario</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($grupoAsignaciones as $asignacion)
                                                            <tr class="asignacion-row" data-asignacion-id="{{ $asignacion->id }}" data-search="{{ strtolower(($asignacion->materia->nombre ?? '') . ' ' . ($asignacion->docente->nombre ?? '') . ' ' . ($grupo->nombre ?? '')) }}" data-grupo-id="{{ $grupoId }}">
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $asignacion->materia->nombre ?? '-' }}</td>
                                                                <td>{{ $asignacion->docente->nombre ?? '-' }}</td>
                                                                <td>{{ $asignacion->aula->nombre ?? '-' }}</td>
                                                                <td>{{ $asignacion->dia->nombre ?? '-' }}</td>
                                                                <td>{{ $asignacion->horario->horaInicio ?? '' }} - {{ $asignacion->horario->horaFin ?? '' }}</td>
                                                                <td>
                                                                    <div class="row d-flex justify-content-center">
                                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ModalUpdate{{ $asignacion->id }}">
                                                                            <i class="fas fa-pencil-alt"></i> Editar
                                                                        </button>

                                                                        <form action="{{ url('/admin/asignaciones/' . $asignacion->id) }}" method="POST" id="deleteForm{{ $asignacion->id }}" class="ml-1">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="confirmDelete{{ $asignacion->id }}(event)">
                                                                                <i class="fas fa-trash-alt"></i> Eliminar
                                                                            </button>
                                                                        </form>
                                                                    </div>

                                                                    <script>
                                                                        function confirmDelete{{ $asignacion->id }}(event) {
                                                                            event.preventDefault();
                                                                            Swal.fire({
                                                                                title: '¿Eliminar esta asignación?',
                                                                                text: '',
                                                                                icon: 'question',
                                                                                showDenyButton: true,
                                                                                confirmButtonText: 'Eliminar',
                                                                                confirmButtonColor: '#a5161d',
                                                                                denyButtonColor: '#270a0a',
                                                                                denyButtonText: 'Cancelar',
                                                                            }).then((result) => {
                                                                                if (result.isConfirmed) {
                                                                                    document.getElementById('deleteForm{{ $asignacion->id }}').submit();
                                                                                }
                                                                            });
                                                                        }
                                                                    </script>

                                                                    {{-- mantener modal de edición existente --}}
                                                                    <div class="modal fade" id="ModalUpdate{{ $asignacion->id }}" tabindex="-1" aria-labelledby="ModalUpdateLabel{{ $asignacion->id }}" aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" style="background-color: #09ae5b; color: white;">
                                                                                    <h5 class="modal-title" id="ModalUpdateLabel{{ $asignacion->id }}">Editar asignación</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <form action="{{ url('/admin/asignaciones/' . $asignacion->id) }}" method="POST">
                                                                                        @csrf
                                                                                        @method('PUT')
                                                                                        <input type="hidden" name="modal_id" value="{{ $asignacion->id }}">

                                                                                        <div class="row">
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Grupo</label><b> (*)</b>
                                                                                                    <select class="form-control" name="grupo_id" required>
                                                                                                        <option value="">Seleccione...</option>
                                                                                                        @foreach ($grupos as $g)
                                                                                                            <option value="{{ $g->id }}" {{ (old('modal_id') == $asignacion->id ? old('grupo_id', $asignacion->grupo_id) : $asignacion->grupo_id) == $g->id ? 'selected' : '' }}>
                                                                                                                {{ $g->nombre }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('grupo_id')
                                                                                                        <small style="color: red;">{{ $message }}</small>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Materia</label><b> (*)</b>
                                                                                                    <select class="form-control" name="materia_id" required>
                                                                                                        <option value="">Seleccione...</option>
                                                                                                        @foreach ($materias as $mat)
                                                                                                            <option value="{{ $mat->id }}" {{ (old('modal_id') == $asignacion->id ? old('materia_id', $asignacion->materia_id) : $asignacion->materia_id) == $mat->id ? 'selected' : '' }}>
                                                                                                                {{ $mat->nombre }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('materia_id')
                                                                                                        <small style="color: red;">{{ $message }}</small>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="row">
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Docente</label><b> (*)</b>
                                                                                                    <select class="form-control" name="docente_id" required>
                                                                                                        <option value="">Seleccione...</option>
                                                                                                        @foreach ($docentes as $doc)
                                                                                                            <option value="{{ $doc->id }}" {{ (old('modal_id') == $asignacion->id ? old('docente_id', $asignacion->docente_id) : $asignacion->docente_id) == $doc->id ? 'selected' : '' }}>
                                                                                                                {{ $doc->nombre }} - {{ $doc->especialidad }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('docente_id')
                                                                                                        <small style="color: red;">{{ $message }}</small>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Aula</label><b> (*)</b>
                                                                                                    <select class="form-control" name="aula_id" required>
                                                                                                        <option value="">Seleccione...</option>
                                                                                                        @foreach ($aulas as $aul)
                                                                                                            <option value="{{ $aul->id }}" {{ (old('modal_id') == $asignacion->id ? old('aula_id', $asignacion->aula_id) : $asignacion->aula_id) == $aul->id ? 'selected' : '' }}>
                                                                                                                {{ $aul->nombre }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('aula_id')
                                                                                                        <small style="color: red;">{{ $message }}</small>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="row">
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Día</label><b> (*)</b>
                                                                                                    <select class="form-control" name="dia_id" required>
                                                                                                        <option value="">Seleccione...</option>
                                                                                                        @foreach ($dias as $d)
                                                                                                            <option value="{{ $d->id }}" {{ (old('modal_id') == $asignacion->id ? old('dia_id', $asignacion->dia_id) : $asignacion->dia_id) == $d->id ? 'selected' : '' }}>
                                                                                                                {{ $d->nombre }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('dia_id')
                                                                                                        <small style="color: red;">{{ $message }}</small>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Horario</label><b> (*)</b>
                                                                                                    <select class="form-control" name="horario_id" required>
                                                                                                        <option value="">Seleccione...</option>
                                                                                                        @foreach ($horarios as $hor)
                                                                                                            <option value="{{ $hor->id }}" {{ (old('modal_id') == $asignacion->id ? old('horario_id', $asignacion->horario_id) : $asignacion->horario_id) == $hor->id ? 'selected' : '' }}>
                                                                                                                {{ $hor->horaInicio }} - {{ $hor->horaFin }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('horario_id')
                                                                                                        <small style="color: red;">{{ $message }}</small>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="row">
                                                                                            <hr>
                                                                                            <div class="col-md-12 d-flex justify-content-end">
                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                                                <button type="submit" class="btn btn-success ml-2">Actualizar</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center">No hay asignaciones registradas</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // search filter for accordion: hides assignments and groups without matches
            const $search = $('<div class="input-group input-group-sm mb-2" style="width:300px;"></div>');
            $search.append('<input type="text" id="asigSearchInput" class="form-control" placeholder="Buscar asignación...">');
            $('.card-tools').first().prepend($search);

            $(document).on('input', '#asigSearchInput', function() {
                const q = $(this).val().toLowerCase();
                $('.asignacion-row').each(function() {
                    const s = $(this).data('search') || '';
                    $(this).toggle(q === '' || s.includes(q));
                });

                $('.grupo-card').each(function() {
                    const visible = $(this).find('.asignacion-row:visible').length;
                    $(this).toggle(visible > 0);
                });
            });

            @if ($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const modalId = '{{ old('modal_id') ? old('modal_id') : (session('modal_id') ? session('modal_id') : '') }}';
                        if (!modalId) {
                            $('#ModalCreate').modal('show');
                            return;
                        }

                        const $row = $(`.asignacion-row[data-asignacion-id="${modalId}"]`);
                        if ($row.length) {
                            const grupoId = $row.data('grupo-id');
                            const $collapse = $(`#collapseAsig${grupoId}`);

                            function showModal() {
                                $('#ModalUpdate' + modalId).modal('show');
                            }

                            if (!$collapse.hasClass('show')) {
                                $collapse.one('shown.bs.collapse', function () {
                                    showModal();
                                });
                                $collapse.collapse('show');
                            } else {
                                showModal();
                            }
                        } else {
                            // fallback: open modal directly if row not found
                            $('#ModalUpdate' + modalId).modal('show');
                        }
                    });
                </script>
            @endif
        });
    </script>
@stop
