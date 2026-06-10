<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nota;
use App\Models\Postulante;
use App\Models\Materia;
use App\Models\ConfigPorcentaje;
use App\Models\Carrera;
use App\Models\Admitido;
use App\Models\Reprobado;

class NotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $postulantes = Postulante::all();
        $materias = Materia::orderBy('id')->take(4)->get();
        $configExamenes = ConfigPorcentaje::orderBy('id')->get();

        if ($postulantes->isEmpty() || $materias->count() < 4 || $configExamenes->count() < 3) {
            return;
        }

        foreach ($postulantes as $postulante) {
            $this->seedNotasYResultado($postulante, $materias, $configExamenes);
        }
    }

    protected function seedNotasYResultado(Postulante $postulante, $materias, $configExamenes): void
    {
        $baseScore = $this->getBaseScore($postulante->id);
        $materiaPromedios = [];

        foreach ($materias as $materia) {
            $materiaTotal = 0;

            foreach ($configExamenes as $config) {
                $nota = $this->generateNota($baseScore);

                Nota::updateOrCreate(
                    [
                        'postulante_id' => $postulante->id,
                        'materia_id' => $materia->id,
                        'config_examen_id' => $config->id,
                    ],
                    ['nota' => $nota]
                );

                $materiaTotal += $nota * ($config->ponderacion / 100);
            }

            $materiaPromedios[] = $materiaTotal;
        }

        $promedioFinal = round(array_sum($materiaPromedios) / count($materias), 2);

        Admitido::where('postulante_id', $postulante->id)->delete();
        Reprobado::where('postulante_id', $postulante->id)->delete();

        $notaBaja = Nota::where('postulante_id', $postulante->id)
            ->where('nota', '<', 60)
            ->exists();

        if ($notaBaja) {
            Reprobado::create([
                'postulante_id' => $postulante->id,
                'promedio_final' => $promedioFinal,
                'motivo' => 'NOTA INSUFICIENTE EN EXAMEN',
                'detalle' => "Al menos un examen con nota inferior a 60. Promedio final {$promedioFinal}",
                'fecha_registro' => now()->format('Y-m-d'),
            ]);
            return;
        }

        if ($promedioFinal < 60) {
            Reprobado::create([
                'postulante_id' => $postulante->id,
                'promedio_final' => $promedioFinal,
                'motivo' => 'PROMEDIO INSUFICIENTE',
                'detalle' => "Promedio final {$promedioFinal} menor a 60",
                'fecha_registro' => now()->format('Y-m-d'),
            ]);
            return;
        }

        $carrera1 = Carrera::find($postulante->carrera1_id);
        $carrera2 = Carrera::find($postulante->carrera2_id);

        $alternativa = Carrera::where('cupo_disponible', '>', 0)
            ->where('nota_minima', '<=', $promedioFinal)
            ->whereNotIn('id', array_filter([$postulante->carrera1_id, $postulante->carrera2_id]))
            ->orderBy('nota_minima', 'asc')
            ->first();

        if ($carrera1 && $promedioFinal >= ($carrera1->nota_minima ?? 60) && $carrera1->cupo_disponible > 0) {
            $this->crearAdmitido($postulante->id, $carrera1->id, '1RA OPCIÓN', $promedioFinal);
            return;
        }

        if ($carrera2 && $promedioFinal >= ($carrera2->nota_minima ?? 60) && $carrera2->cupo_disponible > 0) {
            $this->crearAdmitido($postulante->id, $carrera2->id, '2DA OPCIÓN', $promedioFinal);
            return;
        }

        if ($alternativa) {
            $this->crearAdmitido($postulante->id, $alternativa->id, 'ALTERNATIVA', $promedioFinal);
            return;
        }

        if ($carrera2) {
            $this->crearAdmitido($postulante->id, $carrera2->id, '2DA OPCIÓN (SIN CUPO)', $promedioFinal);
            return;
        }

        if ($carrera1) {
            $this->crearAdmitido($postulante->id, $carrera1->id, '1RA OPCIÓN (SIN CUPO)', $promedioFinal);
            return;
        }

        Reprobado::create([
            'postulante_id' => $postulante->id,
            'promedio_final' => $promedioFinal,
            'motivo' => 'NO HAY OPCIONES DISPONIBLES',
            'detalle' => "Promedio final {$promedioFinal} suficiente, pero no hay carreras disponibles.",
            'fecha_registro' => now()->format('Y-m-d'),
        ]);
    }

    protected function crearAdmitido(int $postulanteId, int $carreraId, string $opcion, float $promedioFinal): void
    {
        Admitido::updateOrCreate(
            ['postulante_id' => $postulanteId],
            [
                'carrera_id' => $carreraId,
                'opcion_asignada' => $opcion,
                'promedio_final' => $promedioFinal,
                'fecha_asignacion' => now()->format('Y-m-d'),
            ]
        );
    }

    protected function generateNota(float $baseScore): int
    {
        $random = rand(-15, 15);
        $nota = (int) round($baseScore + $random);
        return max(0, min(100, $nota));
    }

    protected function getBaseScore(int $postulanteId): float
    {
        $bucket = $postulanteId % 10;

        return match (true) {
            $bucket <= 2 => rand(80, 95),
            $bucket <= 5 => rand(65, 75),
            $bucket <= 7 => rand(60, 69),
            default => rand(35, 58),
        };
    }
}
