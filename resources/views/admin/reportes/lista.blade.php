@extends('adminlte::page')

@section('content_header')
    <h1><b>{{ $titulo ?? 'Lista general de postulantes' }}</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ $titulo ?? 'Lista general de postulantes' }}</h3>
            <div>
                @if(isset($tipo) && ($tipo === 'reprobados' || $tipo === 'aprobados'))
                    <div class="input-group input-group-sm mr-2" style="width: 250px; display: inline-flex;">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="searchInput{{ $tipo }}" class="form-control" placeholder="Buscar...">
                    </div>
                @endif
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'lista') . '?format=csv') }}" class="btn btn-sm btn-outline-primary">Exportar CSV</a>
                <a href="{{ url('admin/reportes/export/' . ($tipo ?? 'lista') . '?format=pdf') }}" class="btn btn-sm btn-outline-secondary">Exportar PDF</a>
                <a href="{{ url('admin/reportes') }}" class="btn btn-sm btn-outline-dark">Volver</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>CI</th>
                            @if(isset($promedios))<th>Promedio</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($postulantes as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nombres ?? '' }}</td>
                                <td>{{ $p->apellidos ?? '' }}</td>
                                <td>{{ $p->ci ?? '' }}</td>
                                @if(isset($promedios))<td>{{ round($promedios[$p->id] ?? 0, 2) }}</td>@endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        (function(){
            const tipos = ['aprobados', 'reprobados'];
            tipos.forEach(function(tipo){
                const input = document.getElementById('searchInput' + tipo);
                if (!input) return;
                const rows = Array.from(document.querySelectorAll('table tbody tr'));
                input.addEventListener('input', function(){
                    const v = this.value.toLowerCase().trim();
                    rows.forEach(r => {
                        const nombres = (r.cells[1]?.textContent || '').toLowerCase();
                        const apellidos = (r.cells[2]?.textContent || '').toLowerCase();
                        const ci = (r.cells[3]?.textContent || '').toLowerCase();
                        const match = nombres.includes(v) || apellidos.includes(v) || ci.includes(v);
                        r.style.display = match ? '' : 'none';
                    });
                });
            });
        })();
    </script>
@stop
