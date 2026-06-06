<?php

namespace App\Models;

use App\Models\Carrera;
use App\Models\Gestion;
use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    protected $table = 'postulantes';

    protected $fillable = [
        'ci',
        'nombres',
        'apellidos',
        'sexo',
        'correo',
        'telefono',
        'ciudad',
        'colegio',
        'fecha_nac',
        'titulo_bachiller',
        'carrera1_id',
        'carrera2_id',
        'pago_confirmado',
        'estado_inscripcion',
        'gestion_id',
    ];

    protected $casts = [
        'titulo_bachiller' => 'boolean',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function carrera1()
    {
        return $this->belongsTo(Carrera::class, 'carrera1_id');
    }

    public function carrera2()
    {
        return $this->belongsTo(Carrera::class, 'carrera2_id');
    }
}
