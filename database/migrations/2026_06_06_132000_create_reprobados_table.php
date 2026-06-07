<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reprobados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
            $table->decimal('promedio_final', 5, 2);
            $table->string('motivo');
            $table->text('detalle')->nullable();
            $table->date('fecha_registro');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reprobados');
    }
};
