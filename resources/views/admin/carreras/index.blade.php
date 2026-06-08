@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Carreras</b></h1>
    <hr>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Carreras Registradas</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCreate">
                            Crear nueva carrera
                        </button>

                        <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #007bff; color: white;">
                                        <h5 class="modal-title" id="exampleModalLabel">Registro de una nueva carrera</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ url('/admin/carreras/create') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="">Nombre de la carrera</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" name="nombre_create"
                                                                value="{{ old('nombre_create') }}"
                                                                placeholder="Escriba aquí..." required>
                                                        </div>
                                                        @error('nombre_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Nota mínima</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-star"></i></span>
                                                            </div>
                                                            <input type="number" step="0.01" class="form-control" name="nota_minima_create"
                                                                value="{{ old('nota_minima_create') }}"
                                                                min="0" placeholder="Nota mínima" required>
                                                        </div>
                                                        @error('nota_minima_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Cupo máximo</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="cupo_maximo_create"
                                                                value="{{ old('cupo_maximo_create') }}"
                                                                min="1" placeholder="Número máximo" required>
                                                        </div>
                                                        @error('cupo_maximo_create')
                                                            <small style="color: red;">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="">Cupo disponible</label><b> (*)</b>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="cupo_disponible_create"
                                                                value="{{ old('cupo_disponible_create') }}"
                                                                min="0" placeholder="Cupo disponible" required>
                                                        </div>
                                                        @error('cupo_disponible_create')
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
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="example" class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Nombre</th>
                                <th>Nota mínima</th>
                                <th>Cupo máximo</th>
                                <th>Cupo disponible</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($carreras as $carrera)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $carrera->nombre }}</td>
                                    <td>{{ $carrera->nota_minima }}</td>
                                    <td>{{ $carrera->cupo_maximo }}</td>
                                    <td>{{ $carrera->cupo_disponible }}</td>
                                    <td>
                                        <div class="row d-flex justify-content-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#ModalUpdate{{ $carrera->id }}">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </button>

                                            <form action="{{ url('/admin/carreras/' . $carrera->id) }}" method="post"
                                                id="miFormulario{{ $carrera->id }}" class="ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="preguntar{{ $carrera->id }}(event)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                        <script>
                                            function preguntar{{ $carrera->id }}(event) {
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
                                                        document.getElementById('miFormulario{{ $carrera->id }}').submit();
                                                    }
                                                });
                                            }
                                        </script>

                                        <div class="modal fade" id="ModalUpdate{{ $carrera->id }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #09ae5b; color: white;">
                                                        <h5 class="modal-title" id="exampleModalLabel">Editar carrera</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ url('/admin/carreras/' . $carrera->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="">Nombre de la carrera</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                                                            </div>
                                                                            <input type="text" class="form-control"
                                                                                name="nombre"
                                                                                value="{{ old('nombre', $carrera->nombre) }}"
                                                                                placeholder="Escriba aquí..." required>
                                                                        </div>
                                                                        @error('nombre')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="">Nota mínima</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-star"></i></span>
                                                                            </div>
                                                                            <input type="number" step="0.01" class="form-control"
                                                                                name="nota_minima"
                                                                                value="{{ old('nota_minima', $carrera->nota_minima) }}"
                                                                                min="0" placeholder="Nota mínima" required>
                                                                        </div>
                                                                        @error('nota_minima')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="">Cupo máximo</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                                            </div>
                                                                            <input type="number" class="form-control"
                                                                                name="cupo_maximo"
                                                                                value="{{ old('cupo_maximo', $carrera->cupo_maximo) }}"
                                                                                min="1" placeholder="Número máximo" required>
                                                                        </div>
                                                                        @error('cupo_maximo')
                                                                            <small style="color: red;">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="">Cupo disponible</label><b> (*)</b>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                                                            </div>
                                                                            <input type="number" class="form-control"
                                                                                name="cupo_disponible"
                                                                                value="{{ old('cupo_disponible', $carrera->cupo_disponible) }}"
                                                                                min="0" placeholder="Cupo disponible" required>
                                                                        </div>
                                                                        @error('cupo_disponible')
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

@stop

@section('css')
@stop

@section('js')
    @if ($errors->any())
        <script>
            $(document).ready(function() {
                @if (session('modal_id'))
                    $('#ModalUpdate{{ session('modal_id') }}').modal('show');
                @else
                    $('#ModalCreate').modal('show');
                @endif
            });
        </script>
    @endif
@stop
