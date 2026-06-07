<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Postulante;
use App\Models\Asignacion;
use App\Models\PostGrupo;
use App\Models\Materia;
use App\Models\Bitacora;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    public function generar(Request $request, $tipo)
    {
        switch ($tipo) {
            case 'lista':
                $postulantes = Postulante::orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'tipo' => 'lista']);

            case 'aprobados':
                $aprobados = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->havingRaw('AVG(nota) >= ?', [60])
                    ->pluck('promedio', 'postulante_id');

                $postulantes = Postulante::whereIn('id', $aprobados->keys())->orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'titulo' => 'Postulantes aprobados', 'promedios' => $aprobados, 'tipo' => 'aprobados']);

            case 'reprobados':
                $reprobados = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->havingRaw('AVG(nota) < ?', [60])
                    ->pluck('promedio', 'postulante_id');

                $postulantes = Postulante::whereIn('id', $reprobados->keys())->orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'titulo' => 'Postulantes reprobados', 'promedios' => $reprobados, 'tipo' => 'reprobados']);

            case 'promedios':
                $promedioGlobal = DB::table('notas')->avg('nota');
                $promediosPorPost = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->get();

                return view('admin.reportes.promedios', compact('promedioGlobal', 'promediosPorPost'));

            case 'cantidad_grupos':
                $total = Postulante::count();
                $capacidad = 30; // capacidad por grupo asumida
                $gruposNecesarios = (int) ceil($total / $capacidad);
                return view('admin.reportes.cantidad_grupos', compact('total', 'capacidad', 'gruposNecesarios'));

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
                return view('admin.reportes.estadisticas_materia', compact('stats'));

            case 'docentes_por_grupos':
                $asignaciones = Asignacion::with(['docente', 'grupo', 'materia', 'horario'])->get()->groupBy('grupo_id');
                return view('admin.reportes.docentes_por_grupos', compact('asignaciones'));

            case 'grupos_mejor_rendimiento':
                $postGrupos = PostGrupo::with('postulante', 'grupo')->get();
                $grupos = [];
                foreach ($postGrupos as $pg) {
                    $pid = $pg->postulante_id;
                    $gid = $pg->grupo_id;
                    $prom = DB::table('notas')->where('postulante_id', $pid)->avg('nota');
                    if (!isset($grupos[$gid])) $grupos[$gid] = ['grupo' => $pg->grupo->nombre ?? 'Grupo '.$gid, 'aprobados' => 0, 'total' => 0];
                    if ($prom >= 60) $grupos[$gid]['aprobados']++;
                    $grupos[$gid]['total']++;
                }
                usort($grupos, function ($a, $b) { return $b['aprobados'] <=> $a['aprobados']; });
                return view('admin.reportes.grupos_mejor_rendimiento', compact('grupos'));

            default:
                abort(404);
        }
    }

    public function export(Request $request, $tipo)
    {
        $format = $request->query('format', 'csv');

        // Reuse generar to get data via internal call-type handling
        // For simplicity call same logic and then export the rendered view or CSV
        if ($tipo == 'lista' || $tipo == 'aprobados' || $tipo == 'reprobados') {
            // build the data similar to generar
            if ($tipo == 'lista') {
                $postulantes = Postulante::orderBy('apellidos')->get();
                $rows = $postulantes->map(function ($p) { return [$p->id, $p->nombres ?? '', $p->apellidos ?? '', $p->ci ?? '']; })->toArray();
                $headers = ['ID', 'Nombres', 'Apellidos', 'CI'];
            } else {
                $op = $tipo == 'aprobados' ? '>=60' : '<60';
                $proms = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->get();
                $filtered = $proms->filter(function ($r) use ($tipo) { return ($tipo=='aprobados') ? $r->promedio >= 60 : $r->promedio < 60; });
                $postulantes = Postulante::whereIn('id', $filtered->pluck('postulante_id'))->orderBy('apellidos')->get();
                $rows = $postulantes->map(function ($p) use ($filtered) { $prom = $filtered->firstWhere('postulante_id', $p->id)->promedio ?? null; return [$p->id, $p->nombres ?? '', $p->apellidos ?? '', round($prom,2)]; })->toArray();
                $headers = ['ID', 'Nombres', 'Apellidos', 'Promedio'];
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
                $data = ['headers' => $headers, 'rows' => $rows, 'titulo' => ucfirst($tipo)];
                if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                    return \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes.pdf_simple', $data)->download("report_{$tipo}_".date('Ymd_His').".pdf");
                }
                if (app()->bound('dompdf.wrapper')) {
                    $pdf = app('dompdf.wrapper')->loadView('admin.reportes.pdf_simple', $data);
                    return $pdf->download("report_{$tipo}_".date('Ymd_His').".pdf");
                }
                return back()->with('error', 'PDF no disponible en este entorno.');
            }
        }

        return back()->with('error', 'Formato no soportado');
    }
}
