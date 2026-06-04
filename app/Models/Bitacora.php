<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Bitacora extends Model
{
    //
        protected $table = 'bitacoras';
        protected $fillable = ['accion', 'user_id', 'hora'];
        
        protected $casts = [
            'hora' => 'datetime',
        ];
        
        public function usuario()
        {
            return $this->belongsTo(User::class, 'user_id', 'id');
        }
        
}
