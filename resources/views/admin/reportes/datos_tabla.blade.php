@extends('adminlte::page')

@section('content_header')
    <h1><b>Datos por Tabla</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="card-title">Tabla: {{ $tables[$selectedTable] ?? $selectedTable }}</h3>
                <p class="text-muted mb-0">Selecciona columnas y visualiza los registros disponibles.</p>
            </div>
            <div class="d-flex">
                <button type="button" class="btn btn-secondary mr-2 no-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <a href="{{ url('admin/reportes') }}" class="btn btn-dark no-print">Volver</a>
            </div>
        </div>
        <div class="card-body">
            <form method="get" action="{{ route('admin.reportes.datos_tabla') }}">
                <div class="row mb-3 no-print">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Seleccionar tabla</label>
                            <select name="table" class="form-control" onchange="this.form.submit()">
                                @foreach($tables as $tableKey => $label)
                                    <option value="{{ $tableKey }}" {{ $selectedTable === $tableKey ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Buscar</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input id="searchInput" type="search" class="form-control" placeholder="Buscar...">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Actualizar tabla</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-outline card-secondary no-print">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Atributos</h5>
                            </div>
                            <div class="card-body p-2" style="max-height: 320px; overflow-y: auto;">
                                @foreach($columns as $column)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $column }}" id="col_{{ $column }}" {{ in_array($column, $selectedColumns) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="col_{{ $column }}">{{ $column }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        @foreach($selectedColumns as $column)
                                            <th>{{ $column }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $row)
                                        <tr>
                                            @foreach($selectedColumns as $column)
                                                <td>{{ data_get($row, $column, '') }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($selectedColumns) }}">No hay datos para esta tabla.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('searchInput');
            if (!input) return;
            const rows = Array.from(document.querySelectorAll('table tbody tr'));
            input.addEventListener('input', function () {
                const value = this.value.toLowerCase().trim();
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(value) ? '' : 'none';
                });
            });
        });
    </script>
@stop
