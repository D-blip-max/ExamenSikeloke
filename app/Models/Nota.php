<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'notas';

    protected $fillable = [
        'postulante_id',
        'materia_id',
        'config_examen_id',
        'nota',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function configExamen()
    {
        return $this->belongsTo(ConfigPorcentaje::class, 'config_examen_id');
    }
}
