<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admitidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
            $table->foreignId('carrera_id')->nullable()->constrained('carreras')->onDelete('set null');
            $table->string('opcion_asignada');
            $table->decimal('promedio_final', 5, 2);
            $table->date('fecha_asignacion');
            $table->timestamps();

            $table->unique('postulante_id', 'admitidos_unq_postulante');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admitidos');
    }
};
