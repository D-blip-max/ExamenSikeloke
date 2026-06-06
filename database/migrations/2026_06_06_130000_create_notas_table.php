<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('config_examen_id')->constrained('config_porcentaje')->onDelete('restrict');
            $table->integer('nota');
            $table->timestamps();
            $table->unique(['postulante_id', 'materia_id', 'config_examen_id'], 'notas_unq_postulante_materia_examen');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notas');
    }
};
