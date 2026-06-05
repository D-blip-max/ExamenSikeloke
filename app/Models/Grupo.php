<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    //
    protected $table = 'grupos';
    protected $fillable = ['nombre', 'cupo_maximo', 'inscritos', 'turno_id'];

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }
}
