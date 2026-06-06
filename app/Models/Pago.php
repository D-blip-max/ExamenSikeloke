<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Postulante;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'postulante_id',
        'comprobante',
        'monto',
        'fecha',
        'estado',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }
}
