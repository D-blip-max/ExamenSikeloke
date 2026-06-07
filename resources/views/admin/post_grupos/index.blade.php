@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Asignaciones Grupo</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h3 class="card-title">Asignaciones de Postulantes a Grupos</h3>
                        <p class="text-muted mb-0">Usa la búsqueda para filtrar por grupo o postulante.</p>
                    </div>
                    <button type="button" class="btn btn-primary mt-3 mt-md-0" data-toggle="modal" data-target="#ModalCreate">
                        Crear asignación
                    </button>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar asignación...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Grupo</th>
                                    <th>Postulante</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 0; @endphp
                                @foreach($postGruposByGrupo as $grupoAsignaciones)
                                    @foreach($grupoAsignaciones as $postGrupo)
                                        @php $counter++; @endphp
                                        <tr class="postGrupo-row" data-search="{{ strtolower(($postGrupo->grupo->nombre ?? '') . ' ' . ($postGrupo->postulante->nombres ?? '') . ' ' . ($postGrupo->postulante->apellidos ?? '')) }}">
                                            <td>{{ $counter }}</td>
                                            <td>{{ $postGrupo->grupo->nombre ?? 'Sin grupo' }}</td>
                                            <td>{{ $postGrupo->postulante->ci ?? '-' }} - {{ $postGrupo->postulante->nombres ?? '-' }} {{ $postGrupo->postulante->apellidos ?? '' }}</td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-success btn-edit-postgrupo"
                                                    data-id="{{ $postGrupo->id }}"
                                                    data-postulante-id="{{ $postGrupo->postulante_id }}"
                                                    data-grupo-id="{{ $postGrupo->grupo_id }}"
                                                    data-postulante-name="{{ $postGrupo->postulante->nombres ?? '' }} {{ $postGrupo->postulante->apellidos ?? '' }}"
                                                    data-postulante-ci="{{ $postGrupo->postulante->ci ?? '' }}"
                                                    data-toggle="modal"
                                                    data-target="#ModalEdit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <form action="{{ url('/admin/post_grupos/' . $postGrupo->id) }}" method="POST" class="d-inline delete-form" id="deleteForm{{ $postGrupo->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $postGrupo->id }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($counter === 0)
                        <p class="text-center text-muted">No hay asignaciones registradas.</p>
                    @endif
                </div>
            </div>
        </div>
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
                    <form action="{{ route('admin.post_grupos.create') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Postulante</label><b> (*)</b>
                                    <input type="text" id="searchPostulanteCreate" class="form-control mb-2" placeholder="Buscar postulante...">
                                    <select class="form-control filterable-postulante-select" name="postulante_id" required>
                                        <option value="">Seleccione postulante</option>
                                        @foreach($postulantes as $postulante)
                                            @php $asignado = $postulante->postGrupos()->exists(); @endphp
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
                                            @php $full = $grupo->inscritos >= $grupo->cupo_maximo; @endphp
                                            <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }} {{ $full ? 'disabled' : '' }}>{{ $grupo->nombre }} ({{ $grupo->inscritos }}/{{ $grupo->cupo_maximo }}){{ $full ? ' - Completo' : '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('grupo_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="ModalEditLabel">Editar asignación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editPostGrupoForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Postulante</label><b> (*)</b>
                                    <input type="text" class="form-control mb-2 searchPostulanteUpdate" placeholder="Buscar postulante...">
                                    <select class="form-control filterable-postulante-select" id="editPostulanteSelect" name="postulante_id" required>
                                        <option value="">Seleccione postulante</option>
                                        @foreach($postulantes as $postulante)
                                            <option value="{{ $postulante->id }}" {{ old('postulante_id') == $postulante->id ? 'selected' : '' }}>{{ $postulante->ci }} - {{ $postulante->nombres }} {{ $postulante->apellidos }}</option>
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
                                    <select class="form-control" id="editGrupoSelect" name="grupo_id" required>
                                        <option value="">Seleccione grupo</option>
                                        @foreach($grupos as $grupoOption)
                                            @php $full = $grupoOption->inscritos >= $grupoOption->cupo_maximo; @endphp
                                            <option value="{{ $grupoOption->id }}" {{ old('grupo_id') == $grupoOption->id ? 'selected' : '' }} {{ $full ? 'disabled' : '' }}>{{ $grupoOption->nombre }} ({{ $grupoOption->inscritos }}/{{ $grupoOption->cupo_maximo }}){{ $full ? ' - Completo' : '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('grupo_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
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
@stop

@section('js')
    <script>
        $(document).ready(function() {
            const postGruposUpdateBaseUrl = "{{ url('admin/post_grupos') }}";

            function filtrarOpcionesPostulante($input) {
                const query = $input.val().toLowerCase();
                const $select = $input.closest('.form-group').find('.filterable-postulante-select');
                $select.find('option').each(function() {
                    const text = $(this).text().toLowerCase();
                    const isDefault = $(this).val() === '';
                    $(this).toggle(isDefault || text.includes(query));
                });
            }

            $('#searchInput').on('input', function() {
                const search = $(this).val().toLowerCase();
                $('.postGrupo-row').each(function() {
                    const searchData = $(this).data('search');
                    $(this).toggle(search === '' || searchData.includes(search));
                });
            });

            $('#searchPostulanteCreate').on('input', function() {
                filtrarOpcionesPostulante($(this));
            });

            $(document).on('input', '.searchPostulanteUpdate', function() {
                filtrarOpcionesPostulante($(this));
            });

            $(document).on('click', '.btn-edit-postgrupo', function() {
                const id = $(this).data('id');
                const postulanteId = $(this).data('postulante-id');
                const grupoId = $(this).data('grupo-id');
                const postulanteName = $(this).data('postulante-name');
                const postulanteCi = $(this).data('postulante-ci');

                $('#ModalEditLabel').text('Editar asignación: ' + postulanteCi + ' - ' + postulanteName);
                $('#editPostGrupoForm').attr('action', postGruposUpdateBaseUrl + '/' + id);
                $('#editPostulanteSelect').val(postulanteId);
                $('#editGrupoSelect').val(grupoId);
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                Swal.fire({
                    title: '¿Eliminar esta asignación?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: 'Eliminar',
                    denyButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#deleteForm' + id).submit();
                    }
                });
            });

            @if ($errors->any())
                @if (session('modal_id'))
                    const editButton = $('.btn-edit-postgrupo[data-id="{{ session('modal_id') }}"]');
                    if (editButton.length) {
                        editButton.trigger('click');
                    }
                    $('#ModalEdit').modal('show');
                @else
                    $('#ModalCreate').modal('show');
                @endif
            @endif
        });
    </script>
@stop
