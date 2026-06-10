<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Postulante;
use App\Models\Asignacion;
use App\Models\PostGrupo;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Bitacora;
use App\Models\Admitido;
use App\Models\Reprobado;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    public function datosTabla(Request $request)
    {
        $tables = [
            'users' => 'Usuarios',
            'grupos' => 'Grupos',
            'aulas' => 'Aulas',
            'carreras' => 'Carreras',
            'horarios' => 'Horarios',
            'dias' => 'Días',
            'gestiones' => 'Gestiones',
            'materias' => 'Materias',
            'turnos' => 'Turnos',
            'docentes' => 'Docentes',
            'postulantes' => 'Postulantes',
            'pagos' => 'Pagos',
            'notas' => 'Notas',
            'admitidos' => 'Admitidos',
            'reprobados' => 'Reprobados',
            'post_grupos' => 'Post Grupos',
        ];

        $selectedTable = $request->query('table', array_key_first($tables));
        if (!isset($tables[$selectedTable])) {
            $selectedTable = array_key_first($tables);
        }

        $columns = Schema::getColumnListing($selectedTable);
        $selectedColumns = $request->query('columns', $columns);
        $selectedColumns = array_values(array_intersect($selectedColumns, $columns));
        if (empty($selectedColumns)) {
            $selectedColumns = $columns;
        }

        $rows = DB::table($selectedTable)->select($selectedColumns)->get();

        return view('admin.reportes.datos_tabla', compact('tables', 'selectedTable', 'columns', 'selectedColumns', 'rows'))->with('tipo', 'datos_tabla');
    }

    public function generar(Request $request, $tipo)
    {
        switch ($tipo) {
            case 'lista':
                $postulantes = Postulante::orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'tipo' => 'lista']);

            case 'aprobados':
                // Obtener aprobados desde la tabla Admitido y permitir búsqueda por postulante
                $aprobQuery = Admitido::with('postulante');
                if ($request->filled('q')) {
                    $term = $request->get('q');
                    $aprobQuery->whereHas('postulante', function ($q) use ($term) {
                        $q->where('nombres', 'like', "%{$term}%")
                          ->orWhere('apellidos', 'like', "%{$term}%")
                          ->orWhere('ci', 'like', "%{$term}%");
                    });
                }
                $aprobados = $aprobQuery->get()->pluck('promedio_final', 'postulante_id');
                $postulantes = Postulante::whereIn('id', $aprobados->keys())->orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'titulo' => 'Postulantes aprobados', 'promedios' => $aprobados, 'tipo' => 'aprobados']);

            case 'reprobados':
                // Obtener reprobados desde la tabla Reprobado y aplicar filtro de búsqueda si viene q
                $reprobadosQuery = Reprobado::with('postulante');
                if ($request->filled('q')) {
                    $term = $request->get('q');
                    $reprobadosQuery->whereHas('postulante', function ($q) use ($term) {
                        $q->where('nombres', 'like', "%{$term}%")
                          ->orWhere('apellidos', 'like', "%{$term}%")
                          ->orWhere('ci', 'like', "%{$term}%");
                    });
                }
                $reprobados = $reprobadosQuery->get()->pluck('promedio_final', 'postulante_id');
                $postulantes = Postulante::whereIn('id', $reprobados->keys())->orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'titulo' => 'Postulantes reprobados', 'promedios' => $reprobados, 'tipo' => 'reprobados']);

            case 'promedios':
                $promedioGlobal = DB::table('notas')->avg('nota');
                $promediosPorPost = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->get();

                return view('admin.reportes.promedios', compact('promedioGlobal', 'promediosPorPost'))->with('tipo', 'promedios');

            case 'cantidad_grupos':
                $total = Postulante::count();
                $capacidad = Grupo::max('cupo_maximo') ?: 1;
                $gruposNecesarios = (int) ceil($total / $capacidad);
                return view('admin.reportes.cantidad_grupos', compact('total', 'capacidad', 'gruposNecesarios'))->with('tipo', 'cantidad_grupos');

            case 'estadisticas_materia':
                $materias = ['Computación', 'Matemáticas', 'Inglés', 'Física'];
                $stats = [];
                foreach ($materias as $mat) {
                    $q = DB::table('notas')
                        ->join('materias', 'notas.materia_id', '=', 'materias.id')
                        ->where('materias.nombre', 'like', "%{$mat}%");

                    $avg = $q->avg('notas.nota');
                    $total = $q->count();
                    $aprob = $q->where('notas.nota', '>=', 60)->count();
                    $stats[$mat] = ['promedio' => $avg ?: 0, 'total' => $total, 'aprobados' => $aprob];
                }
                return view('admin.reportes.estadisticas_materia', compact('stats'))->with('tipo', 'estadisticas_materia');

            case 'docentes_por_grupos':
                $asignaciones = Asignacion::with(['docente', 'grupo', 'materia', 'horario', 'aula', 'dia'])->get()->groupBy('grupo_id');
                return view('admin.reportes.docentes_por_grupos', compact('asignaciones'))->with('tipo', 'docentes_por_grupos');

            case 'grupos_mejor_rendimiento':
                $postGrupos = PostGrupo::with('postulante', 'grupo')->get();
                $grupos = [];
                foreach ($postGrupos as $pg) {
                    $pid = $pg->postulante_id;
                    $gid = $pg->grupo_id;
                    if (!isset($grupos[$gid])) {
                        $grupos[$gid] = ['grupo' => $pg->grupo->nombre ?? 'Grupo '.$gid, 'aprobados' => 0, 'total' => 0];
                    }
                    // Considerar aprobado si existe entrada en Admitido
                    if (Admitido::where('postulante_id', $pid)->exists()) {
                        $grupos[$gid]['aprobados']++;
                    }
                    $grupos[$gid]['total']++;
                }
                usort($grupos, function ($a, $b) { return $b['aprobados'] <=> $a['aprobados']; });
                return view('admin.reportes.grupos_mejor_rendimiento', compact('grupos'))->with('tipo', 'grupos_mejor_rendimiento');

            default:
                abort(404);
        }
    }

    public function export(Request $request, $tipo)
    {
        $format = $request->query('format', 'csv');

        // Reuse generar to get data via internal call-type handling
        // For simplicity call same logic and then export the rendered view or CSV
        $headers = [];
        $rows = [];
        $titulo = ucfirst(str_replace('_', ' ', $tipo));

        switch ($tipo) {
            case 'lista':
                $postulantes = Postulante::orderBy('apellidos')->get();
                $headers = ['ID', 'Nombres', 'Apellidos', 'CI'];
                $rows = $postulantes->map(function ($p) {
                    return [$p->id, $p->nombres ?? '', $p->apellidos ?? '', $p->ci ?? ''];
                })->toArray();
                break;

            case 'aprobados':
            case 'reprobados':
                $proms = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->get();
                $filtered = $proms->filter(function ($r) use ($tipo) {
                    return $tipo == 'aprobados' ? $r->promedio >= 60 : $r->promedio < 60;
                });
                $postulantes = Postulante::whereIn('id', $filtered->pluck('postulante_id'))->orderBy('apellidos')->get();
                $headers = ['ID', 'Nombres', 'Apellidos', 'Promedio'];
                $rows = $postulantes->map(function ($p) use ($filtered) {
                    $prom = $filtered->firstWhere('postulante_id', $p->id)->promedio ?? 0;
                    return [$p->id, $p->nombres ?? '', $p->apellidos ?? '', round($prom, 2)];
                })->toArray();
                break;

            case 'promedios':
                $promediosPorPost = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->get();
                $headers = ['ID', 'Nombres', 'Apellidos', 'Promedio'];
                $rows = $promediosPorPost->map(function ($item) {
                    $post = Postulante::find($item->postulante_id);
                    return [
                        optional($post)->id ?? $item->postulante_id,
                        optional($post)->nombres ?? '',
                        optional($post)->apellidos ?? '',
                        round($item->promedio, 2),
                    ];
                })->toArray();
                break;

            case 'cantidad_grupos':
                $total = Postulante::count();
                $capacidad = Grupo::max('cupo_maximo') ?: 1;
                $gruposNecesarios = (int) ceil($total / $capacidad);
                $headers = ['Total inscritos', 'Capacidad por grupo', 'Grupos necesarios'];
                $rows = [[$total, $capacidad, $gruposNecesarios]];
                break;

            case 'estadisticas_materia':
                $materias = ['Computación', 'Matemáticas', 'Inglés', 'Física'];
                $headers = ['Materia', 'Promedio', 'Total notas', 'Aprobados'];
                foreach ($materias as $mat) {
                    $q = DB::table('notas')
                        ->join('materias', 'notas.materia_id', '=', 'materias.id')
                        ->where('materias.nombre', 'like', "%{$mat}%");
                    $avg = $q->avg('notas.nota');
                    $total = $q->count();
                    $aprob = $q->where('notas.nota', '>=', 60)->count();
                    $rows[] = [
                        $mat,
                        round($avg ?: 0, 2),
                        $total,
                        $aprob,
                    ];
                }
                break;

            case 'docentes_por_grupos':
                $asignaciones = Asignacion::with(['docente', 'grupo', 'materia', 'aula', 'dia', 'horario'])->get();
                $headers = ['Grupo', 'Docente', 'Materia', 'Aula', 'Día', 'Horario'];
                foreach ($asignaciones as $a) {
                    $rows[] = [
                        optional($a->grupo)->nombre ?? '',
                        optional($a->docente)->nombre ?? '',
                        optional($a->materia)->nombre ?? '',
                        optional($a->aula)->nombre ?? '',
                        optional($a->dia)->nombre ?? '',
                        (optional($a->horario)->horaInicio ?? '') . ' - ' . (optional($a->horario)->horaFin ?? ''),
                    ];
                }
                break;

            case 'grupos_mejor_rendimiento':
                $postGrupos = PostGrupo::with('postulante', 'grupo')->get();
                $grupos = [];
                foreach ($postGrupos as $pg) {
                    $pid = $pg->postulante_id;
                    $gid = $pg->grupo_id;
                    $prom = DB::table('notas')->where('postulante_id', $pid)->avg('nota');
                    if (!isset($grupos[$gid])) {
                        $grupos[$gid] = ['grupo' => $pg->grupo->nombre ?? 'Grupo '.$gid, 'aprobados' => 0, 'total' => 0];
                    }
                    if ($prom >= 60) {
                        $grupos[$gid]['aprobados']++;
                    }
                    $grupos[$gid]['total']++;
                }
                usort($grupos, function ($a, $b) { return $b['aprobados'] <=> $a['aprobados']; });
                $headers = ['Grupo', 'Aprobados', 'Total'];
                foreach ($grupos as $g) {
                    $rows[] = [$g['grupo'], $g['aprobados'], $g['total']];
                }
                break;

            default:
                return back()->with('error', 'Tipo de reporte no soportado');
        }

        if ($format === 'csv') {
            $filename = "report_{$tipo}_".date('Ymd_His').".csv";
            $callback = function() use ($rows, $headers) {
                $f = fopen('php://output', 'w');
                fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($f, $headers);
                foreach ($rows as $row) {
                    fputcsv($f, $row);
                }
                fclose($f);
            };
            return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        if ($format === 'pdf') {
            $data = ['headers' => $headers, 'rows' => $rows, 'titulo' => $titulo];
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                return \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes.pdf_simple', $data)->download("report_{$tipo}_".date('Ymd_His').".pdf");
            }
            if (app()->bound('dompdf.wrapper')) {
                $pdf = app('dompdf.wrapper')->loadView('admin.reportes.pdf_simple', $data);
                return $pdf->download("report_{$tipo}_".date('Ymd_His').".pdf");
            }
            return back()->with('error', 'PDF no disponible en este entorno.');
        }

        return back()->with('error', 'Formato no soportado');
    }
}
