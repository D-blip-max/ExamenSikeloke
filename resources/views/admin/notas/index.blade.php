@extends('adminlte::page')

@section('content_header')
    <h1><b>CU18 · Registrar Calificaciones</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Registrar calificaciones</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Registrar nueva nota
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if ($notas->count())
                        @php $notasPorPostulante = $notas->groupBy('postulante_id'); @endphp
                        <div class="accordion" id="notasAccordion">
                            @foreach ($notasPorPostulante as $postulanteId => $notasGrupo)
                                @php $postulante = $notasGrupo->first()->postulante; @endphp
                                <div class="card">
                                    <div class="card-header p-0" id="heading{{ $postulanteId }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapse{{ $postulanteId }}" aria-expanded="false" aria-controls="collapse{{ $postulanteId }}">
                                                <span>{{ $postulante->nombres }} {{ $postulante->apellidos }} ({{ $notasGrupo->count() }} notas)</span>
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapse{{ $postulanteId }}" class="collapse" aria-labelledby="heading{{ $postulanteId }}" data-parent="#notasAccordion">
                                        <div class="card-body">
                                            @foreach ($configPorcentajes as $config)
                                                @php $notasParcial = $notasGrupo->where('config_examen_id', $config->id); @endphp
                                                <h5 class="mt-3">{{ $config->numero_examen }}</h5>
                                                @if ($notasParcial->count())
                                                    <table class="table table-sm table-bordered mb-4">
                                                        <thead>
                                                            <tr>
                                                                <th>Materia</th>
                                                                <th>Nota</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($notasParcial as $nota)
                                                                <tr>
                                                                    <td>{{ $nota->materia->nombre }}</td>
                                                                    <td>{{ $nota->nota }}</td>
                                                                    <td>
                                                                        <div class="row d-flex justify-content-center">
                                                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ModalUpdate{{ $nota->id }}">
                                                                                <i class="fas fa-pencil-alt"></i> Editar
                                                                            </button>
                                                                            <form action="{{ url('/admin/notas/' . $nota->id) }}" method="post" id="miFormulario{{ $nota->id }}">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-danger btn-sm" onclick="preguntar{{ $nota->id }}(event)">
                                                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <script>
                                                                    function preguntar{{ $nota->id }}(event) {
                                                                        event.preventDefault();
                                                                        Swal.fire({
                                                                            title: '¿Desea eliminar este registro?',
                                                                            text: '',
                                                                            icon: 'question',
                                                                            showDenyButton: true,
                                                                            confirmButtonText: 'Eliminar',
                                                                            confirmButtonColor: '#a5161d',
                                                                            denyButtonColor: '#270a0a',
                                                                            denyButtonText: 'Cancelar',
                                                                        }).then((result) => {
                                                                            if (result.isConfirmed) {
                                                                                document.getElementById('miFormulario{{ $nota->id }}').submit();
                                                                            }
                                                                        });
                                                                    }
                                                                </script>
                                                                <div class="modal fade" id="ModalUpdate{{ $nota->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header" style="background-color: #09ae5b; color: white;">
                                                                                <h5 class="modal-title" id="exampleModalLabel">Editar nota</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form action="{{ url('/admin/notas/' . $nota->id) }}" method="POST">
                                                                                    @csrf
                                                                                    @method('PUT')
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="">Postulante</label><b> (*)</b>
                                                                                                <select class="form-control" name="postulante_id" required>
                                                                                                    <option value="">Seleccione un postulante</option>
                                                                                                    @foreach ($postulantes as $postulante)
                                                                                                        <option value="{{ $postulante->id }}" @if (old('postulante_id', $nota->postulante_id) == $postulante->id) selected @endif>
                                                                                                            {{ $postulante->nombres }} {{ $postulante->apellidos }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                                @error('postulante_id')
                                                                                                    <small style="color: red;">{{ $message }}</small>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <div class="form-group">
                                                                                                <label for="">Materia</label><b> (*)</b>
                                                                                                <select class="form-control" name="materia_id" required>
                                                                                                    <option value="">Seleccione una materia</option>
                                                                                                    @foreach ($materias as $materia)
                                                                                                        <option value="{{ $materia->id }}" @if (old('materia_id', $nota->materia_id) == $materia->id) selected @endif>
                                                                                                            {{ $materia->nombre }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                                @error('materia_id')
                                                                                                    <small style="color: red;">{{ $message }}</small>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <div class="form-group">
                                                                                                <label for="">Examen</label><b> (*)</b>
                                                                                                <select class="form-control" name="config_examen_id" required>
                                                                                                    <option value="">Seleccione un examen</option>
                                                                                                    @foreach ($configPorcentajes as $config)
                                                                                                        <option value="{{ $config->id }}" @if (old('config_examen_id', $nota->config_examen_id) == $config->id) selected @endif>
                                                                                                            {{ $config->numero_examen }} - {{ $config->ponderacion }}%
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                                @error('config_examen_id')
                                                                                                    <small style="color: red;">{{ $message }}</small>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="">Nota</label><b> (*)</b>
                                                                                                <input type="number" class="form-control" name="nota" value="{{ old('nota', $nota->nota) }}" min="0" max="100" step="0.01" required>
                                                                                                @error('nota')
                                                                                                    <small style="color: red;">{{ $message }}</small>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <hr>
                                                                                        <div class="row">
                                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                                            <button type="submit" class="btn btn-success">Actualizar</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <p class="text-muted">Aún no hay notas registradas para este parcial.</p>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>No hay notas registradas aún.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #007bff; color: white;">
                    <h5 class="modal-title" id="exampleModalLabel">Registro de una nueva nota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/admin/notas/create') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Postulante</label><b> (*)</b>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input type="text" id="postulanteSearch" class="form-control" placeholder="Buscar postulante...">
                                    </div>
                                    <select class="form-control" id="postulante_id_create" name="postulante_id_create" required>
                                        <option value="">Seleccione un postulante</option>
                                        @foreach ($postulantesRegistrables as $postulante)
                                            <option value="{{ $postulante['id'] }}"
                                                @if (old('postulante_id_create') == $postulante['id']) selected @endif>
                                                {{ $postulante['nombre'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small id="notaStageInfo" class="form-text text-muted">Seleccione un postulante para ver el parcial disponible.</small>
                                    @error('postulante_id_create')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Materia</label><b> (*)</b>
                                    <select class="form-control" id="materia_id_create" name="materia_id_create" disabled required>
                                        <option value="">Seleccione un postulante primero</option>
                                    </select>
                                    @error('materia_id_create')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Examen</label><b> (*)</b>
                                    <select class="form-control" id="config_examen_id_create" name="config_examen_id_create" disabled required>
                                        <option value="">Seleccione un postulante primero</option>
                                    </select>
                                    @error('config_examen_id_create')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Nota</label><b> (*)</b>
                                    <input type="number" class="form-control" name="nota_create"
                                        value="{{ old('nota_create') }}" min="0" max="100" step="0.01" required>
                                    @error('nota_create')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <hr>
                            <div class="row">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($resumenPostulantes)
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Resumen de promedios por postulante</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Nro</th>
                                    <th>Postulante</th>
                                    <th>Promedio final</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resumenPostulantes as $postulanteId => $datos)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $datos['postulante']->nombres }} {{ $datos['postulante']->apellidos }}</td>
                                        <td>{{ number_format($datos['promedio_final'], 2) }}</td>
                                        <td>{{ $datos['estado'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
@stop

@section('js')
    <script>
        const postulantesRegistrablesData = {!! json_encode($postulantesRegistrables) !!};

        function mostrarMensajeEtapa(postulante) {
            const info = document.getElementById('notaStageInfo');
            if (!postulante) {
                info.textContent = 'Seleccione un postulante para ver el parcial disponible.';
                return;
            }
            if (postulante.exam_label) {
                info.textContent = `Etapa disponible: ${postulante.exam_label} (${postulante.exam_ponderacion}%); debe registrar todas las materias del parcial anterior.`;
            } else {
                info.textContent = 'Este postulante no tiene parciales asignados o ya completó las 12 notas.';
            }
        }

        function actualizarPostulanteSeleccionado() {
            const postulanteId = $('#postulante_id_create').val();
            const materiaSelect = $('#materia_id_create');
            const examenSelect = $('#config_examen_id_create');

            materiaSelect.empty();
            examenSelect.empty();

            if (!postulanteId) {
                materiaSelect.prop('disabled', true).append('<option value="">Seleccione un postulante primero</option>');
                examenSelect.prop('disabled', true).append('<option value="">Seleccione un postulante primero</option>');
                mostrarMensajeEtapa(null);
                return;
            }

            const postulante = postulantesRegistrablesData.find(p => p.id == postulanteId);
            if (!postulante) {
                materiaSelect.prop('disabled', true).append('<option value="">Seleccione un postulante válido</option>');
                examenSelect.prop('disabled', true).append('<option value="">Seleccione un postulante válido</option>');
                mostrarMensajeEtapa(null);
                return;
            }

            if (postulante.materias.length === 0) {
                materiaSelect.prop('disabled', true).append('<option value="">No hay materias disponibles</option>');
            } else {
                materiaSelect.prop('disabled', false).append('<option value="">Seleccione una materia</option>');
                postulante.materias.forEach(materia => {
                    materiaSelect.append(`<option value="${materia.id}">${materia.nombre}</option>`);
                });
            }

            if (postulante.exam_id) {
                examenSelect.prop('disabled', false).append('<option value="">Seleccione un examen</option>');
                examenSelect.append(`<option value="${postulante.exam_id}">${postulante.exam_label} - ${postulante.exam_ponderacion}%</option>`);
                examenSelect.val(postulante.exam_id);
            } else {
                examenSelect.prop('disabled', true).append('<option value="">No hay examen disponible</option>');
            }

            mostrarMensajeEtapa(postulante);
        }

        function filtrarPostulantes() {
            const filtro = $('#postulanteSearch').val().toLowerCase();
            $('#postulante_id_create option').each(function() {
                const text = $(this).text().toLowerCase();
                const isDefault = $(this).val() === '';
                $(this).toggle(isDefault || text.includes(filtro));
            });
        }

        $(document).ready(function() {
            $('#postulante_id_create').on('change', actualizarPostulanteSeleccionado);
            $('#postulanteSearch').on('input', filtrarPostulantes);
            actualizarPostulanteSeleccionado();

            @if ($errors->any())
                @if (session('modal_id'))
                    $('#ModalUpdate{{ session('modal_id') }}').modal('show');
                @else
                    $('#ModalCreate').modal('show');
                @endif
            @endif

            const oldPostulanteId = '{{ old('postulante_id_create') }}';
            if (oldPostulanteId) {
                $('#postulante_id_create').val(oldPostulanteId);
                actualizarPostulanteSeleccionado();
                const oldMateria = '{{ old('materia_id_create') }}';
                const oldExamen = '{{ old('config_examen_id_create') }}';
                if (oldMateria) {
                    $('#materia_id_create').val(oldMateria);
                }
                if (oldExamen) {
                    $('#config_examen_id_create').val(oldExamen);
                }
            }
        });
    </script>
@stop
