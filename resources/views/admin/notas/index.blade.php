@extends('adminlte::page')

@section('content_header')
    <h1><b>CU18 · Registrar Calificaciones</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h3 class="card-title">Registrar calificaciones</h3>
                        <p class="text-muted mb-0">Usa la búsqueda para filtrar por postulante, materia o examen.</p>
                    </div>
                    <button type="button" class="btn btn-primary mt-3 mt-md-0" data-toggle="modal" data-target="#ModalCreate">
                        Registrar nueva nota
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar nota...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Postulante</th>
                                    <th>Materia</th>
                                    <th>Examen</th>
                                    <th>Nota</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notas as $nota)
                                    <tr class="nota-row" data-search="{{ strtolower($nota->postulante->nombres . ' ' . $nota->postulante->apellidos . ' ' . $nota->materia->nombre . ' ' . $nota->configExamen->numero_examen) }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $nota->postulante->nombres }} {{ $nota->postulante->apellidos }}</td>
                                        <td>{{ $nota->materia->nombre }}</td>
                                        <td>{{ $nota->configExamen->numero_examen }} ({{ $nota->configExamen->ponderacion }}%)</td>
                                        <td>{{ $nota->nota }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success btn-sm btn-edit-note" 
                                                data-id="{{ $nota->id }}"
                                                data-postulante-id="{{ $nota->postulante_id }}"
                                                data-postulante-name="{{ $nota->postulante->nombres }} {{ $nota->postulante->apellidos }}"
                                                data-postulante-ci="{{ $nota->postulante->ci }}"
                                                data-materia-id="{{ $nota->materia_id }}"
                                                data-config-id="{{ $nota->config_examen_id }}"
                                                data-nota="{{ $nota->nota }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ url('/admin/notas/' . $nota->id) }}" method="POST" class="d-inline" id="deleteForm{{ $nota->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm btn-delete-note" data-id="{{ $nota->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($notas->isEmpty())
                        <p class="text-center text-muted mt-3">No hay notas registradas aún.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="ModalCreateLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="ModalCreateLabel">Registrar nueva nota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.notas.create') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Postulante</label><b> (*)</b>
                            <input type="text" id="postulanteSearch" class="form-control mb-2" placeholder="Buscar postulante...">
                            <select class="form-control" id="postulante_id_create" name="postulante_id_create" required>
                                <option value="">Seleccione un postulante</option>
                                @foreach ($postulantesRegistrables as $postulante)
                                    <option value="{{ $postulante['id'] }}" @if(old('postulante_id_create') == $postulante['id']) selected @endif>{{ $postulante['nombre'] }}</option>
                                @endforeach
                            </select>
                            <div id="notaStageInfo" class="small text-muted mt-2">Seleccione un postulante para ver el examen disponible.</div>
                            @error('postulante_id_create')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Materia</label><b> (*)</b>
                                <select class="form-control" id="materia_id_create" name="materia_id_create" disabled required>
                                    <option value="">Seleccione un postulante primero</option>
                                </select>
                                @error('materia_id_create')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Examen</label><b> (*)</b>
                                <select class="form-control" id="config_examen_id_create" name="config_examen_id_create" disabled required>
                                    <option value="">Seleccione un postulante primero</option>
                                </select>
                                @error('config_examen_id_create')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Nota</label><b> (*)</b>
                            <input type="number" class="form-control" name="nota_create" value="{{ old('nota_create') }}" min="0" max="100" step="0.01" required>
                            @error('nota_create')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalEdit" tabindex="-1" aria-labelledby="ModalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="ModalEditLabel">Editar nota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editNotaForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Postulante</label><b> (*)</b>
                            <select class="form-control" id="editPostulanteSelect" name="postulante_id" required>
                                <option value="">Seleccione un postulante</option>
                                @foreach ($postulantes as $postulante)
                                    <option value="{{ $postulante->id }}">{{ $postulante->nombres }} {{ $postulante->apellidos }}</option>
                                @endforeach
                            </select>
                            @error('postulante_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Materia</label><b> (*)</b>
                                <select class="form-control" id="editMateriaSelect" name="materia_id" required>
                                    <option value="">Seleccione una materia</option>
                                    @foreach ($materias as $materia)
                                        <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('materia_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Examen</label><b> (*)</b>
                                <select class="form-control" id="editConfigSelect" name="config_examen_id" required>
                                    <option value="">Seleccione un examen</option>
                                    @foreach ($configPorcentajes as $config)
                                        <option value="{{ $config->id }}">{{ $config->numero_examen }} - {{ $config->ponderacion }}%</option>
                                    @endforeach
                                </select>
                                @error('config_examen_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nota</label><b> (*)</b>
                            <input type="number" class="form-control" id="editNotaInput" name="nota" min="0" max="100" step="0.01" required>
                            @error('nota')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($resumenPostulantes)
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Resumen de promedios por postulante</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm mb-0">
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
        </div>
    @endif
@stop

@section('css')
@stop

@section('js')
    <script>
        const postulantesRegistrablesData = {!! json_encode($postulantesRegistrables) !!};
        const notasUpdateBaseUrl = "{{ url('admin/notas') }}";

        function mostrarMensajeEtapa(postulante) {
            const info = document.getElementById('notaStageInfo');
            if (!info) return;
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

            if (!postulante.materias || postulante.materias.length === 0) {
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

        function iniciarEdicionNota(button) {
            const id = button.data('id');
            const postulanteId = button.data('postulante-id');
            const materiaId = button.data('materia-id');
            const configId = button.data('config-id');
            const nota = button.data('nota');
            const postulanteName = button.data('postulante-name');
            const postulanteCi = button.data('postulante-ci');

            $('#ModalEditLabel').text(`Editar nota: ${postulanteCi} - ${postulanteName}`);
            $('#editNotaForm').attr('action', notasUpdateBaseUrl + '/' + id);
            $('#editPostulanteSelect').val(postulanteId);
            $('#editMateriaSelect').val(materiaId);
            $('#editConfigSelect').val(configId);
            $('#editNotaInput').val(nota);
        }

        $(document).ready(function() {
            $('#postulante_id_create').on('change', actualizarPostulanteSeleccionado);
            $('#postulanteSearch').on('input', filtrarPostulantes);
            actualizarPostulanteSeleccionado();

            $('#searchInput').on('input', function() {
                const search = $(this).val().toLowerCase();
                $('.nota-row').each(function() {
                    const text = $(this).data('search');
                    $(this).toggle(search === '' || text.includes(search));
                });
            });

            $(document).on('click', '.btn-delete-note', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                Swal.fire({
                    title: '¿Eliminar esta nota?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: 'Eliminar',
                    denyButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#deleteForm${id}`).submit();
                    }
                });
            });

            $(document).on('click', '.btn-edit-note', function() {
                iniciarEdicionNota($(this));
                $('#ModalEdit').modal('show');
            });

            @if ($errors->any())
                @if (session('modal_id'))
                    const button = $(`.btn-edit-note[data-id="{{ session('modal_id') }}"]`);
                    if (button.length) {
                        iniciarEdicionNota(button);
                    }
                    $('#ModalEdit').modal('show');
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
