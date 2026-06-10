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
        $grupos = Grupo::all();
        if ($grupos->isEmpty()) {
            return;
        }

        $dias = Dia::whereIn('nombre', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'])->get()->pluck('id', 'nombre');

        // Bloques de horarios por grupo para evitar cruces
        $blouquesHorarios = [
            1 => [ // Grupo 1 - Turno Mañana (07:00-10:00)
                'Fisica' => ['horaInicio' => '07:00', 'horaFin' => '07:45'],
                'Computacion' => ['horaInicio' => '07:45', 'horaFin' => '08:30'],
                'Matematicas' => ['horaInicio' => '08:30', 'horaFin' => '09:15'],
                'Ingles' => ['horaInicio' => '09:15', 'horaFin' => '10:00'],
            ],
            2 => [ // Grupo 2 - Turno Tarde (13:00-16:00)
                'Fisica' => ['horaInicio' => '13:00', 'horaFin' => '13:45'],
                'Computacion' => ['horaInicio' => '13:45', 'horaFin' => '14:30'],
                'Matematicas' => ['horaInicio' => '14:30', 'horaFin' => '15:15'],
                'Ingles' => ['horaInicio' => '15:15', 'horaFin' => '16:00'],
            ],
            3 => [ // Grupo 3 - Turno Noche (19:00-21:45)
                'Fisica' => ['horaInicio' => '19:00', 'horaFin' => '19:45'],
                'Computacion' => ['horaInicio' => '19:45', 'horaFin' => '20:30'],
                'Matematicas' => ['horaInicio' => '20:30', 'horaFin' => '21:15'],
                'Ingles' => ['horaInicio' => '21:15', 'horaFin' => '22:00'],
            ],
            4 => [ // Grupo 4 - Media mañana-tarde (10:45-13:00)
                'Fisica' => ['horaInicio' => '10:45', 'horaFin' => '11:30'],
                'Computacion' => ['horaInicio' => '11:30', 'horaFin' => '12:15'],
                'Matematicas' => ['horaInicio' => '12:15', 'horaFin' => '13:00'],
                'Ingles' => ['horaInicio' => '13:00', 'horaFin' => '13:45'],
            ],
        ];

        $aulaNombres = [
            'Fisica' => 'Aula 101',
            'Computacion' => 'Aula 102',
            'Matematicas' => 'Aula 103',
            'Ingles' => 'Aula 104',
        ];

        // Crear aulas si no existen
        foreach ($aulaNombres as $nombre) {
            Aula::firstOrCreate(
                ['nombre' => $nombre],
                ['capacidad' => 30, 'tipo' => 'Presencial']
            );
        }

        // Iterar sobre los 4 grupos y asignar horarios diferentes
        foreach ($grupos as $index => $grupo) {
            $numGrupo = $grupo->id;
            if (!isset($blouquesHorarios[$numGrupo])) {
                continue;
            }

            $horarios = $blouquesHorarios[$numGrupo];

            foreach ($horarios as $especialidad => $horaData) {
                $docente = Docente::where('especialidad', $especialidad)->first();
                $materia = Materia::where('nombre', $especialidad)->first();
                $aula = Aula::firstWhere('nombre', $aulaNombres[$especialidad]);
                $horario = Horario::where($horaData)->first();

                if (!$docente || !$materia || !$aula || !$horario) {
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
}
