<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Postulante;
use App\Models\PostGrupo;
use App\Models\Grupo;

class PostulanteGrupoSeeder extends Seeder
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

        $postulantes = Postulante::all();
        foreach ($postulantes as $postulante) {
            PostGrupo::updateOrCreate(
                ['postulante_id' => $postulante->id],
                ['grupo_id' => $grupo->id]
            );
        }

        $grupo->inscritos = $postulantes->count();
        $grupo->save();
    }
}
