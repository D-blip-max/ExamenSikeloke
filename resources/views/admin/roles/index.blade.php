@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Roles</b></h1>
    <hr>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Roles Registrados</h3>

                    <div class="card-tools">
                        <a href="{{ url('/admin/roles/create') }}" class="btn btn-primary">Crear nuevo Rol</a>
                    </div>
                </div>
                <div class="card-body">

                    <table id="example" class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <div class="row d-flex justify-content-center">
                                            <a href="{{ url('/admin/roles/' . $role->id . '/permisos') }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-check-circle"></i> Permisos
                                            </a>
                                            <a href="{{ url('/admin/roles/' . $role->id . '/edit') }}"
                                                class="btn btn-success btn-sm">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </a>

                                            <form action="{{ url('/admin/roles/' . $role->id) }}" method="post"
                                                id="miFormulario{{ $role->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="preguntar{{ $role->id }}(event)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>



                                        <script>
                                            function preguntar{{ $role->id }}(event) {
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
                                                        // JavaScript puro para enviar el formulario
                                                        document.getElementById('miFormulario{{ $role->id }}').submit();
                                                    }
                                                });
                                            }
                                        </script>

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
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop
{{--  hola --}}
@section('js')

@stop
