@extends('adminlte::page')

@section('content_header')
    <h1><b>Gestión de Pagos</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Pagos Registrados</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control float-right" placeholder="Buscar pago...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        &nbsp;
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Crear pago
                        </button>
                    </div>

                    <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="ModalCreateLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="ModalCreateLabel">Registrar nuevo pago</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('/admin/pagos/create') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Postulante</label><b> (*)</b>
                                                    <select class="form-control" name="postulante_id" required>
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
                                                    <label>Comprobante</label><b> (*)</b>
                                                    <input type="text" class="form-control" name="comprobante" value="{{ old('comprobante') }}" required>
                                                    @error('comprobante')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Monto</label><b> (*)</b>
                                                    <input type="number" step="0.01" class="form-control" name="monto" value="{{ old('monto', 200) }}" required>
                                                    @error('monto')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Fecha</label><b> (*)</b>
                                                    <input type="date" class="form-control" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                                    @error('fecha')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Estado</label><b> (*)</b>
                                                    <select class="form-control" name="estado" required>
                                                        <option value="">Seleccione estado</option>
                                                        <option value="PENDIENTE" {{ old('estado') == 'PENDIENTE' ? 'selected' : '' }}>PENDIENTE</option>
                                                        <option value="CONFIRMADO" {{ old('estado') == 'CONFIRMADO' ? 'selected' : '' }}>CONFIRMADO</option>
                                                    </select>
                                                    @error('estado')
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
                    <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-bordered table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nro</th>
                                    <th>Postulante</th>
                                    <th>Comprobante</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="pagosBody">
                                @forelse($pagos as $pago)
                                    <tr class="pago-row" data-search="{{ strtolower($pago->comprobante . ' ' . $pago->postulante->nombres . ' ' . $pago->postulante->apellidos) }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pago->postulante->ci ?? '-' }} - {{ $pago->postulante->nombres ?? '-' }} {{ $pago->postulante->apellidos ?? '' }}</td>
                                        <td>{{ $pago->comprobante }}</td>
                                        <td>{{ number_format($pago->monto, 2, '.', ',') }}</td>
                                        <td>{{ $pago->fecha }}</td>
                                        <td>{{ $pago->estado }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ModalUpdate{{ $pago->id }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <form action="{{ url('/admin/pagos/' . $pago->id) }}" method="POST" class="d-inline" id="deletePagoForm{{ $pago->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeletePago{{ $pago->id }}(event)">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <script>
                                                function confirmDeletePago{{ $pago->id }}(event) {
                                                    event.preventDefault();
                                                    Swal.fire({
                                                        title: '¿Eliminar este pago?',
                                                        icon: 'question',
                                                        showDenyButton: true,
                                                        confirmButtonText: 'Eliminar',
                                                        denyButtonText: 'Cancelar',
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            document.getElementById('deletePagoForm{{ $pago->id }}').submit();
                                                        }
                                                    });
                                                }
                                            </script>

                                            <div class="modal fade" id="ModalUpdate{{ $pago->id }}" tabindex="-1" aria-labelledby="ModalUpdateLabel{{ $pago->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title" id="ModalUpdateLabel{{ $pago->id }}">Editar pago</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ url('/admin/pagos/' . $pago->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Postulante</label><b> (*)</b>
                                                                            <select class="form-control" name="postulante_id" required>
                                                                                <option value="">Seleccione postulante</option>
                                                                                @foreach($postulantes as $postulante)
                                                                                    <option value="{{ $postulante->id }}" {{ old('postulante_id', $pago->postulante_id) == $postulante->id ? 'selected' : '' }}>{{ $postulante->ci }} - {{ $postulante->nombres }} {{ $postulante->apellidos }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('postulante_id')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Comprobante</label><b> (*)</b>
                                                                            <input type="text" class="form-control" name="comprobante" value="{{ old('comprobante', $pago->comprobante) }}" required>
                                                                            @error('comprobante')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Monto</label><b> (*)</b>
                                                                            <input type="number" step="0.01" class="form-control" name="monto" value="{{ old('monto', $pago->monto) }}" required>
                                                                            @error('monto')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Fecha</label><b> (*)</b>
                                                                            <input type="date" class="form-control" name="fecha" value="{{ old('fecha', $pago->fecha) }}" required>
                                                                            @error('fecha')
                                                                                <small class="text-danger">{{ $message }}</small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Estado</label><b> (*)</b>
                                                                            <select class="form-control" name="estado" required>
                                                                                <option value="">Seleccione estado</option>
                                                                                <option value="PENDIENTE" {{ old('estado', $pago->estado) == 'PENDIENTE' ? 'selected' : '' }}>PENDIENTE</option>
                                                                                <option value="CONFIRMADO" {{ old('estado', $pago->estado) == 'CONFIRMADO' ? 'selected' : '' }}>CONFIRMADO</option>
                                                                            </select>
                                                                            @error('estado')
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
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay pagos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                    $('.pago-row').show();
                    return;
                }
                $('.pago-row').each(function() {
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
