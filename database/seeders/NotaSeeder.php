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

        if ($carrera1 && $promedioFinal >= ($carrera1->nota_minima ?? 60)) {
            $this->crearAdmitido($postulante->id, $carrera1->id, '1RA OPCIÓN', $promedioFinal);
            return;
        }

        if ($carrera2 && $promedioFinal >= ($carrera2->nota_minima ?? 60)) {
            $this->crearAdmitido($postulante->id, $carrera2->id, '2DA OPCIÓN', $promedioFinal);
            return;
        }

        Reprobado::create([
            'postulante_id' => $postulante->id,
            'promedio_final' => $promedioFinal,
            'motivo' => 'NO CUMPLE OPCIONES',
            'detalle' => "Promedio final {$promedioFinal} suficiente, pero no alcanza ninguna opción de carrera",
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
