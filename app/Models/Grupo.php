<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Turno;
use App\Models\Postulante;

class Grupo extends Model
{
    const DEFAULT_CAPACITY = 70;

    protected $table = 'grupos';
    protected $fillable = ['nombre', 'cupo_maximo', 'inscritos', 'turno_id'];

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function postGrupos()
    {
        return $this->hasMany(PostGrupo::class, 'grupo_id');
    }

    public static function ensureAutoGroups(): void
    {
        $postulanteCount = Postulante::count();
        $neededGroups = $postulanteCount > 0 ? (int) ceil($postulanteCount / self::DEFAULT_CAPACITY) : 0;
        $existingCount = self::count();

        if ($neededGroups <= $existingCount) {
            return;
        }

        $turnoManana = Turno::firstWhere('nombre', 'Mañana') ?: Turno::first();
        if (! $turnoManana) {
            return;
        }

        $startIndex = $existingCount + 1;

        for ($i = $startIndex; $i <= $neededGroups; $i++) {
            self::create([
                'nombre' => 'Grupo ' . $i,
                'cupo_maximo' => self::DEFAULT_CAPACITY,
                'inscritos' => 0,
                'turno_id' => $turnoManana->id,
            ]);
        }
    }
}
