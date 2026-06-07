<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Postulante;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Reprobado;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public static function middleware(): array
    {
        return ['auth'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? '';

        $student = null;
        $docente = null;
        $metrics = [];

        if ($role === 'Estudiante') {
            $student = Postulante::where('correo', $user->email)->first();
        }

        if ($role === 'Docente') {
            $docente = Docente::where('correo', $user->email)->first();
        }

        if ($role === 'Administrador') {
            $totalInscritos = Postulante::count();
            $totalAprobados = DB::table('notas')
                ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                ->groupBy('postulante_id')
                ->havingRaw('AVG(nota) >= ?', [60])
                ->count();
            $totalReprobados = DB::table('notas')
                ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                ->groupBy('postulante_id')
                ->havingRaw('AVG(nota) < ?', [60])
                ->count();
            $totalGrupos = Grupo::count();
            $metrics = [
                'Total de inscritos' => $totalInscritos,
                'Total de aprobados' => $totalAprobados,
                'Total de reprobados' => $totalReprobados,
                'Total de grupos habilitados' => $totalGrupos,
            ];
        }

        return view('home', compact('user', 'role', 'student', 'docente', 'metrics'));
    }
}
