<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pago;
use App\Models\Postulante;

class PagoSeeder extends Seeder
{
    public function run()
    {
        $postulantes = Postulante::all();

        foreach ($postulantes as $postulante) {
            $comprobante = 'COMP-' . $postulante->ci . '-' . now()->format('YmdHis');

            Pago::create([
                'postulante_id' => $postulante->id,
                'comprobante' => $comprobante,
                'monto' => 200.00,
                'fecha' => now()->format('Y-m-d'),
                'estado' => 'CONFIRMADO',
            ]);

            $postulante->pago_confirmado = 'VERDADERO';
            $postulante->estado_inscripcion = 'INSCRITO';
            $postulante->save();
        }
    }
}
