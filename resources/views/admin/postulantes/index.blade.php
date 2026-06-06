@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Postulantes</b></h1>
    <hr>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Postulantes Registrados</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control float-right" placeholder="Buscar postulante...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        &nbsp;
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Crear nuevo postulante
                        </button>

                        <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="ModalCreateLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="ModalCreateLabel">Registro de nuevo postulante</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ url('/admin/postulantes/create') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Carnet de Identidad (CI)</label><b> (*)</b>
                                                        <input type="text" class="form-control" name="ci_create" value="{{ old('ci_create') }}" required>
                                                        @error('ci_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nombres</label><b> (*)</b>
                                                        <input type="text" class="form-control" name="nombres_create" value="{{ old('nombres_create') }}" required>
                                                        @error('nombres_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Apellidos</label><b> (*)</b>
                                                        <input type="text" class="form-control" name="apellidos_create" value="{{ old('apellidos_create') }}" required>
                                                        @error('apellidos_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Sexo</label><b> (*)</b>
                                                        <select class="form-control" name="sexo_create" required>
                                                            <option value="">Seleccione sexo</option>
                                                            <option value="M" {{ old('sexo_create') == 'M' ? 'selected' : '' }}>M</option>
                                                            <option value="F" {{ old('sexo_create') == 'F' ? 'selected' : '' }}>F</option>
                                                        </select>
                                                        @error('sexo_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Correo Electrónico</label><b> (*)</b>
                                                        <input type="email" class="form-control" name="correo_create" value="{{ old('correo_create') }}" required>
                                                        @error('correo_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Teléfono</label><b> (*)</b>
                                                        <input type="text" class="form-control" name="telefono_create" value="{{ old('telefono_create') }}" required>
                                                        @error('telefono_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Ciudad</label><b> (*)</b>
                                                        <input type="text" class="form-control" name="ciudad_create" value="{{ old('ciudad_create') }}" required>
                                                        @error('ciudad_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Colegio</label><b> (*)</b>
                                                        <input type="text" class="form-control" name="colegio_create" value="{{ old('colegio_create') }}" required>
                                                        @error('colegio_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Fecha de Nacimiento</label><b> (*)</b>
                                                        <input type="date" class="form-control" name="fecha_nac_create" value="{{ old('fecha_nac_create') }}" required>
                                                        @error('fecha_nac_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Título Bachiller</label><b> (*)</b>
                                                        <select class="form-control" name="titulo_bachiller_create" required>
                                                            <option value="">Seleccione</option>
                                                            <option value="1" {{ old('titulo_bachiller_create') == '1' ? 'selected' : '' }}>Sí</option>
                                                            <option value="0" {{ old('titulo_bachiller_create') == '0' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                        @error('titulo_bachiller_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Carrera - Primera Opción</label><b> (*)</b>
                                                        <select class="form-control" name="carrera1_id_create" required>
                                                            <option value="">Seleccione carrera</option>
                                                            @foreach($carreras as $carrera)
                                                                <option value="{{ $carrera->id }}" {{ old('carrera1_id_create') == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('carrera1_id_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Carrera - Segunda Opción</label><b> (*)</b>
                                                        <select class="form-control" name="carrera2_id_create" required>
                                                            <option value="">Seleccione carrera</option>
                                                            @foreach($carreras as $carrera)
                                                                <option value="{{ $carrera->id }}" {{ old('carrera2_id_create') == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('carrera2_id_create')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="alert alert-info">
                                                        <strong>Notas:</strong> Al registrar un postulante se crea automáticamente un pago en estado <strong>PENDIENTE</strong>. El postulante quedará con pago = <strong>FALSO</strong> y estado de inscripción = <strong>PENDIENTE_PAGO</strong>.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Gestión</label><b> (*)</b>
                                                        <select class="form-control" name="gestion_id_create" required>
                                                            <option value="">Seleccione gestión</option>
                                                            @foreach($gestiones as $gestion)
                                                                <option value="{{ $gestion->id }}" {{ old('gestion_id_create') == $gestion->id ? 'selected' : '' }}>{{ $gestion->nombre ?? $gestion->id }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('gestion_id_create')
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
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                        <table id="postulantesTable" class="table table-bordered table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nro</th>
                                    <th>CI</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Carrera 1</th>
                                    <th>Carrera 2</th>
                                    <th>Pago</th>
                                    <th>Inscripción</th>
                                    <th>Gestión</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="postulantesBody">
                                @forelse($postulantes as $postulante)
                                    <tr class="postulante-row" data-search="{{ strtolower($postulante->nombres . ' ' . $postulante->apellidos . ' ' . $postulante->correo . ' ' . $postulante->telefono) }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $postulante->ci }}</td>
                                        <td>{{ $postulante->nombres }}</td>
                                        <td>{{ $postulante->apellidos }}</td>
                                        <td>{{ $postulante->correo }}</td>
                                        <td>{{ $postulante->telefono }}</td>
                                        <td>{{ $postulante->carrera1->nombre ?? '-' }}</td>
                                        <td>{{ $postulante->carrera2->nombre ?? '-' }}</td>
                                        <td>{{ $postulante->pago_confirmado }}</td>
                                        <td>{{ $postulante->estado_inscripcion }}</td>
                                        <td>{{ $postulante->gestion->nombre ?? $postulante->gestion_id }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ModalUpdate{{ $postulante->id }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <form action="{{ url('/admin/postulantes/' . $postulante->id) }}" method="POST" class="d-inline" id="deleteForm{{ $postulante->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete{{ $postulante->id }}(event)">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <script>
                                                function confirmDelete{{ $postulante->id }}(event) {
                                                    event.preventDefault();
                                                    Swal.fire({
                                                        title: '¿Eliminar este postulante?',
                                                        icon: 'question',
                                                        showDenyButton: true,
                                                        confirmButtonText: 'Eliminar',
                                                        denyButtonText: 'Cancelar',
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            document.getElementById('deleteForm{{ $postulante->id }}').submit();
                                                        }
                                                    });
                                                }
                                            </script>

                                            <div class="modal fade" id="ModalUpdate{{ $postulante->id }}" tabindex="-1" aria-labelledby="ModalUpdateLabel{{ $postulante->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title" id="ModalUpdateLabel{{ $postulante->id }}">Editar postulante</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ url('/admin/postulantes/' . $postulante->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Carnet de Identidad (CI)</label><b> (*)</b>
                                                                            <input type="text" class="form-control" name="ci" value="{{ old('ci', $postulante->ci) }}" required>
                                                                            @error('ci')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Nombres</label><b> (*)</b>
                                                                            <input type="text" class="form-control" name="nombres" value="{{ old('nombres', $postulante->nombres) }}" required>
                                                                            @error('nombres')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Apellidos</label><b> (*)</b>
                                                                            <input type="text" class="form-control" name="apellidos" value="{{ old('apellidos', $postulante->apellidos) }}" required>
                                                                            @error('apellidos')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Sexo</label><b> (*)</b>
                                                                            <select class="form-control" name="sexo" required>
                                                                                <option value="">Seleccione sexo</option>
                                                                                <option value="M" {{ old('sexo', $postulante->sexo) == 'M' ? 'selected' : '' }}>M</option>
                                                                                <option value="F" {{ old('sexo', $postulante->sexo) == 'F' ? 'selected' : '' }}>F</option>
                                                                            </select>
                                                                            @error('sexo')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Correo Electrónico</label><b> (*)</b>
                                                                            <input type="email" class="form-control" name="correo" value="{{ old('correo', $postulante->correo) }}" required>
                                                                            @error('correo')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Teléfono</label><b> (*)</b>
                                                                            <input type="text" class="form-control" name="telefono" value="{{ old('telefono', $postulante->telefono) }}" required>
                                                                            @error('telefono')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Ciudad</label><b> (*)</b>
                                                                            <input type="text" class="form-control" name="ciudad" value="{{ old('ciudad', $postulante->ciudad) }}" required>
                                                                            @error('ciudad')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Colegio</label><b> (*)</b>
                                                                            <input type="text" class="form-control" name="colegio" value="{{ old('colegio', $postulante->colegio) }}" required>
                                                                            @error('colegio')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Fecha de Nacimiento</label><b> (*)</b>
                                                                            <input type="date" class="form-control" name="fecha_nac" value="{{ old('fecha_nac', $postulante->fecha_nac) }}" required>
                                                                            @error('fecha_nac')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Título Bachiller</label><b> (*)</b>
                                                                            <select class="form-control" name="titulo_bachiller" required>
                                                                                <option value="">Seleccione</option>
                                                                                <option value="1" {{ old('titulo_bachiller', $postulante->titulo_bachiller) == '1' || old('titulo_bachiller', $postulante->titulo_bachiller) == 1 ? 'selected' : '' }}>Sí</option>
                                                                                <option value="0" {{ old('titulo_bachiller', $postulante->titulo_bachiller) == '0' ? 'selected' : '' }}>No</option>
                                                                            </select>
                                                                            @error('titulo_bachiller')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Carrera - Primera Opción</label><b> (*)</b>
                                                                            <select class="form-control" name="carrera1_id" required>
                                                                                <option value="">Seleccione carrera</option>
                                                                                @foreach($carreras as $carrera)
                                                                                    <option value="{{ $carrera->id }}" {{ old('carrera1_id', $postulante->carrera1_id) == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('carrera1_id')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Carrera - Segunda Opción</label><b> (*)</b>
                                                                            <select class="form-control" name="carrera2_id" required>
                                                                                <option value="">Seleccione carrera</option>
                                                                                @foreach($carreras as $carrera)
                                                                                    <option value="{{ $carrera->id }}" {{ old('carrera2_id', $postulante->carrera2_id) == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('carrera2_id')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Pago Confirmado</label><b> (*)</b>
                                                                            <select class="form-control" name="pago_confirmado" required>
                                                                                <option value="">Seleccione</option>
                                                                                <option value="VERDADERO" {{ old('pago_confirmado', $postulante->pago_confirmado) == 'VERDADERO' ? 'selected' : '' }}>Verdadero</option>
                                                                                <option value="FALSO" {{ old('pago_confirmado', $postulante->pago_confirmado) == 'FALSO' ? 'selected' : '' }}>Falso</option>
                                                                            </select>
                                                                            @error('pago_confirmado')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Estado de Inscripción</label><b> (*)</b>
                                                                            <select class="form-control" name="estado_inscripcion" required>
                                                                                <option value="">Seleccione estado</option>
                                                                                <option value="INSCRITO" {{ old('estado_inscripcion', $postulante->estado_inscripcion) == 'INSCRITO' ? 'selected' : '' }}>INSCRITO</option>
                                                                                <option value="PENDIENTE_PAGO" {{ old('estado_inscripcion', $postulante->estado_inscripcion) == 'PENDIENTE_PAGO' ? 'selected' : '' }}>PENDIENTE_PAGO</option>
                                                                                <option value="BLOQUEADO" {{ old('estado_inscripcion', $postulante->estado_inscripcion) == 'BLOQUEADO' ? 'selected' : '' }}>BLOQUEADO</option>
                                                                            </select>
                                                                            @error('estado_inscripcion')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Gestión</label><b> (*)</b>
                                                                            <select class="form-control" name="gestion_id" required>
                                                                                <option value="">Seleccione gestión</option>
                                                                                @foreach($gestiones as $gestion)
                                                                                    <option value="{{ $gestion->id }}" {{ old('gestion_id', $postulante->gestion_id) == $gestion->id ? 'selected' : '' }}>{{ $gestion->nombre ?? $gestion->id }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('gestion_id')
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
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center">No hay postulantes registrados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
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
                if (search === '') {
                    $('.postulante-row').show();
                    return;
                }
                $('.postulante-row').each(function() {
                    let searchData = $(this).data('search');
                    if (searchData.includes(search)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
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
