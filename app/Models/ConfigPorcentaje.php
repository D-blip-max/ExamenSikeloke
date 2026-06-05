<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigPorcentaje extends Model
{
    use HasFactory;

    protected $table = 'config_porcentaje';

    protected $fillable = [
        'numero_examen',
        'ponderacion',
    ];
}
