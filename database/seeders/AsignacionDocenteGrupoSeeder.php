<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asignacion;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Docente;
use App\Models\Aula;
use App\Models\Dia;
use App\Models\Horario;

class AsignacionDocenteGrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupo = Grupo::first();
        if (! $grupo) {
            return;
        }

        $dias = Dia::whereIn('nombre', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'])->get()->pluck('id', 'nombre');

        $horarios = [
            'Fisica' => ['horaInicio' => '07:00', 'horaFin' => '07:45'],
            'Computacion' => ['horaInicio' => '07:45', 'horaFin' => '08:30'],
            'Matematicas' => ['horaInicio' => '08:30', 'horaFin' => '09:15'],
            'Ingles' => ['horaInicio' => '09:15', 'horaFin' => '10:00'],
        ];

        $aulaNombres = [
            'Fisica' => 'Aula 1',
            'Computacion' => 'Aula 2',
            'Matematicas' => 'Aula 3',
            'Ingles' => 'Aula 4',
        ];

        foreach ($aulaNombres as $nombre) {
            Aula::firstOrCreate(
                ['nombre' => $nombre],
                ['capacidad' => 30, 'tipo' => 'Presencial']
            );
        }

        foreach ($horarios as $especialidad => $horaData) {
            $docente = Docente::where('especialidad', $especialidad)->first();
            $materia = Materia::where('nombre', $especialidad)->first();
            $aula = Aula::firstWhere('nombre', $aulaNombres[$especialidad]);
            $horario = Horario::where($horaData)->first();

            if (! $docente || ! $materia || ! $aula || ! $horario) {
                continue;
            }

            foreach ($dias as $diaId) {
                Asignacion::updateOrCreate(
                    [
                        'grupo_id' => $grupo->id,
                        'materia_id' => $materia->id,
                        'docente_id' => $docente->id,
                        'dia_id' => $diaId,
                        'horario_id' => $horario->id,
                    ],
                    [
                        'aula_id' => $aula->id,
                    ]
                );
            }
        }
    }
}
