<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Docente;
use App\Models\Turno;
use App\Models\Materia;

class DocenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $turno = Turno::firstWhere('nombre', 'Mañana') ?? Turno::first();

        $docentes = [
            [
                'ci' => '9000001',
                'nombre' => 'Carlos Fisica',
                'correo' => 'fisica.docente@example.com',
                'especialidad' => 'Fisica',
                'maestria' => true,
                'diplomado_edu' => false,
                'estado' => 'ACTIVO',
            ],
            [
                'ci' => '9000002',
                'nombre' => 'Ana Computacion',
                'correo' => 'computacion.docente@example.com',
                'especialidad' => 'Computacion',
                'maestria' => true,
                'diplomado_edu' => false,
                'estado' => 'ACTIVO',
            ],
            [
                'ci' => '9000003',
                'nombre' => 'Luis Matematica',
                'correo' => 'matematica.docente@example.com',
                'especialidad' => 'Matematicas',
                'maestria' => false,
                'diplomado_edu' => true,
                'estado' => 'ACTIVO',
            ],
            [
                'ci' => '9000004',
                'nombre' => 'Sofia Ingles',
                'correo' => 'ingles.docente@example.com',
                'especialidad' => 'Ingles',
                'maestria' => false,
                'diplomado_edu' => true,
                'estado' => 'ACTIVO',
            ],
        ];

        foreach ($docentes as $docente) {
            Docente::updateOrCreate(
                ['ci' => $docente['ci']],
                array_merge($docente, ['turno_id' => $turno->id])
            );
        }
    }
}
