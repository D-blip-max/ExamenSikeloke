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
        $grupos = Grupo::orderBy('id')->take(4)->get();
        if ($grupos->count() < 4) {
            return;
        }

        $postulantes = Postulante::all();
        if ($postulantes->isEmpty()) {
            return;
        }

        $grupoIndex = 0;
        foreach ($postulantes as $index => $postulante) {
            $grupo = $grupos[floor($index / 70)];
            PostGrupo::updateOrCreate(
                ['postulante_id' => $postulante->id],
                ['grupo_id' => $grupo->id]
            );
        }

        foreach ($grupos as $grupo) {
            $grupo->inscritos = PostGrupo::where('grupo_id', $grupo->id)->count();
            $grupo->save();
        }
    }
}
