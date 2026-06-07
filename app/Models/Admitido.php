<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admitido extends Model
{
    use HasFactory;

    protected $table = 'admitidos';

    protected $fillable = [
        'postulante_id',
        'carrera_id',
        'opcion_asignada',
        'promedio_final',
        'fecha_asignacion',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }
}
