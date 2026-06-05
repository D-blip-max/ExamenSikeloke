@extends('adminlte::page')

@section('content_header')
    <h1>Editar rol</h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">

            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Llene los datos del formulario</h3>
                </div>

                <div class="card-body">
                    <form action="{{ url('/admin/roles/'.$rol->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Nombre del rol</label><b> (*)</b>

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user-plus"></i></span>
                                        </div>
                                        <input type="text" class="form-control" value="{{ old('name',$rol->name) }}"
                                            name="name" placeholder="Escriba el nombre..." required>
                                    </div> @error('name')
                                        <small style="color: red;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ url('/admin/roles') }}" class="btn btn-default">
                                        <i class="fas fa-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div> @stop
@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop
{{--  hola --}}
@section('js')
  
@stop
