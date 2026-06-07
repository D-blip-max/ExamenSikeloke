<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $fillable = [
        'ci',
        'nombre',
        'correo',
        'especialidad',
        'turno_id',
        'maestria',
        'diplomado_edu',
        'estado',
    ];

    protected $casts = [
        'maestria' => 'boolean',
        'diplomado_edu' => 'boolean',
    ];

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'email', 'correo');
    }

    public function asignaciones()
    {
        return $this->hasMany(\App\Models\Asignacion::class, 'docente_id');
    }
}
