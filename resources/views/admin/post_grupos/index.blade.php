@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Asignaciones Grupo</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Asignaciones de Postulantes a Grupos</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control float-right" placeholder="Buscar asignación...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        &nbsp;
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Crear asignación
                        </button>
                    </div>

                    <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="ModalCreateLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="ModalCreateLabel">Nueva asignación de grupo</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('/admin/post_grupos/create') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Postulante</label><b> (*)</b>
                                                    <input type="text" id="searchPostulanteCreate" class="form-control mb-2" placeholder="Buscar postulante...">
                                                    <select class="form-control filterable-postulante-select" name="postulante_id" required>
                                                        <option value="">Seleccione postulante</option>
                                                        @foreach($postulantes as $postulante)
                                                            @php
                                                                $asignado = $postulante->postGrupos()->exists();
                                                            @endphp
                                                            @if(!$asignado)
                                                                <option value="{{ $postulante->id }}" {{ old('postulante_id') == $postulante->id ? 'selected' : '' }}>{{ $postulante->ci }} - {{ $postulante->nombres }} {{ $postulante->apellidos }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('postulante_id')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Grupo</label><b> (*)</b>
                                                    <select class="form-control" name="grupo_id" required>
                                                        <option value="">Seleccione grupo</option>
                                                        @foreach($grupos as $grupo)
                                                            @php
                                                                $full = $grupo->inscritos >= $grupo->cupo_maximo;
                                                            @endphp
                                                            <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }} {{ $full ? 'disabled' : '' }}>{{ $grupo->nombre }} ({{ $grupo->inscritos }}/{{ $grupo->cupo_maximo }}){{ $full ? ' - Completo' : '' }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('grupo_id')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-end">
                                            <div class="col-md-12 text-right">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($postGruposByGrupo->count())
                        <div class="accordion" id="gruposAccordion">
                            @foreach($postGruposByGrupo as $grupoId => $grupoAsignaciones)
                                @php $grupo = $grupoAsignaciones->first()->grupo; @endphp
                                <div class="card grupo-card" data-grupo-id="{{ $grupoId }}">
                                    <div class="card-header p-0" id="headingGrupo{{ $grupoId }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseGrupo{{ $grupoId }}" aria-expanded="false" aria-controls="collapseGrupo{{ $grupoId }}">
                                                <span>{{ $grupo->nombre ?? 'Grupo desconocido' }} ({{ $grupoAsignaciones->count() }} postulantes)</span>
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseGrupo{{ $grupoId }}" class="collapse" aria-labelledby="headingGrupo{{ $grupoId }}" data-parent="#gruposAccordion">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm table-hover mb-0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Postulante</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($grupoAsignaciones as $postGrupo)
                                                            <tr class="postGrupo-row" data-search="{{ strtolower(($postGrupo->postulante->nombres ?? '') . ' ' . ($postGrupo->postulante->apellidos ?? '') . ' ' . ($grupo->nombre ?? '')) }}" data-grupo-id="{{ $grupoId }}">
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $postGrupo->postulante->ci ?? '-' }} - {{ $postGrupo->postulante->nombres ?? '-' }} {{ $postGrupo->postulante->apellidos ?? '' }}</td>
                                                                <td>
                                                                    <div class="btn-group" role="group">
                                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ModalUpdate{{ $postGrupo->id }}">
                                                                            <i class="fas fa-pencil-alt"></i>
                                                                        </button>
                                                                        <form action="{{ url('/admin/post_grupos/' . $postGrupo->id) }}" method="POST" class="d-inline" id="deleteForm{{ $postGrupo->id }}">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete{{ $postGrupo->id }}(event)">
                                                                                <i class="fas fa-trash-alt"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>

                                                                    <script>
                                                                        function confirmDelete{{ $postGrupo->id }}(event) {
                                                                            event.preventDefault();
                                                                            Swal.fire({
                                                                                title: '¿Eliminar esta asignación?',
                                                                                icon: 'question',
                                                                                showDenyButton: true,
                                                                                confirmButtonText: 'Eliminar',
                                                                                denyButtonText: 'Cancelar',
                                                                            }).then((result) => {
                                                                                if (result.isConfirmed) {
                                                                                    document.getElementById('deleteForm{{ $postGrupo->id }}').submit();
                                                                                }
                                                                            });
                                                                        }
                                                                    </script>

                                                                    <div class="modal fade" id="ModalUpdate{{ $postGrupo->id }}" tabindex="-1" aria-labelledby="ModalUpdateLabel{{ $postGrupo->id }}" aria-hidden="true">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-success text-white">
                                                                                    <h5 class="modal-title" id="ModalUpdateLabel{{ $postGrupo->id }}">Editar asignación de grupo</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <form action="{{ url('/admin/post_grupos/' . $postGrupo->id) }}" method="POST">
                                                                                        @csrf
                                                                                        @method('PUT')
                                                                                        <div class="row">
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Postulante</label><b> (*)</b>
                                                                                                    <input type="text" class="form-control mb-2 searchPostulanteUpdate" placeholder="Buscar postulante...">
                                                                                                    <select class="form-control filterable-postulante-select" name="postulante_id" required>
                                                                                                        <option value="">Seleccione postulante</option>
                                                                                                        @foreach($postulantes as $postulante)
                                                                                                            @php
                                                                                                                $otroAsignado = $postulante->postGrupos()->where('id', '!=', $postGrupo->id)->exists();
                                                                                                            @endphp
                                                                                                            @if(!$otroAsignado)
                                                                                                                <option value="{{ $postulante->id }}" {{ old('postulante_id', $postGrupo->postulante_id) == $postulante->id ? 'selected' : '' }}>{{ $postulante->ci }} - {{ $postulante->nombres }} {{ $postulante->apellidos }}</option>
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('postulante_id')
                                                                                                        <small class="text-danger">{{ $message }}</small>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Grupo</label><b> (*)</b>
                                                                                                    <select class="form-control" name="grupo_id" required>
                                                                                                        <option value="">Seleccione grupo</option>
                                                                                                        @foreach($grupos as $grupoOption)
                                                                                                            @php
                                                                                                                $full = $grupoOption->inscritos >= $grupoOption->cupo_maximo;
                                                                                                                $selected = old('grupo_id', $postGrupo->grupo_id) == $grupoOption->id;
                                                                                                            @endphp
                                                                                                            <option value="{{ $grupoOption->id }}" {{ $selected ? 'selected' : '' }} {{ $full && !$selected ? 'disabled' : '' }}>{{ $grupoOption->nombre }} ({{ $grupoOption->inscritos }}/{{ $grupoOption->cupo_maximo }}){{ $full ? ' - Completo' : '' }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('grupo_id')
                                                                                                        <small class="text-danger">{{ $message }}</small>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="row justify-content-end">
                                                                                            <div class="col-md-12 text-right">
                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                                                <button type="submit" class="btn btn-success">Actualizar</button>
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
                        </div>
                    @else
                        <p class="text-center">No hay asignaciones registradas</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#searchInput').keyup(function() {
                let search = $(this).val().toLowerCase();

                $('.postGrupo-row').each(function() {
                    let searchData = $(this).data('search');
                    if (search === '' || searchData.includes(search)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                $('.grupo-card').each(function() {
                    const visibleRows = $(this).find('.postGrupo-row:visible').length;
                    $(this).toggle(visibleRows > 0);
                });
            });

            function filtrarOpcionesPostulante($input) {
                const query = $input.val().toLowerCase();
                const $select = $input.closest('.form-group').find('.filterable-postulante-select');
                $select.find('option').each(function() {
                    const text = $(this).text().toLowerCase();
                    const isDefault = $(this).val() === '';
                    $(this).toggle(isDefault || text.includes(query));
                });
            }

            $('#searchPostulanteCreate').on('input', function() {
                filtrarOpcionesPostulante($(this));
            });

            $(document).on('input', '.searchPostulanteUpdate', function() {
                filtrarOpcionesPostulante($(this));
            });

            @if ($errors->any())
                @if (session('modal_id'))
                    $('#ModalUpdate{{ session('modal_id') }}').modal('show');
                @else
                    $('#ModalCreate').modal('show');
                @endif
            @endif
        });
    </script>
@stop
