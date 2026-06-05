<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    //
    protected $table = 'turnos';
    protected $fillable = ['nombre'];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'turno_id');
    }
}
