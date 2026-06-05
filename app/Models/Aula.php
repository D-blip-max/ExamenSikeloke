<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    //

    protected $table = 'aulas';
    protected $fillable = ['nombre', 'capacidad', 'tipo'];
}
