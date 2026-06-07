<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Postulante;

class Reprobado extends Model
{
    protected $table = 'reprobados';

    protected $fillable = [
        'postulante_id',
        'promedio_final',
        'motivo',
        'detalle',
        'fecha_registro',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }
}
