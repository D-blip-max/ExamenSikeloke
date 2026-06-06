<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('postulantes', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->enum('sexo', ['M', 'F'])->nullable(false);
            $table->string('correo')->unique();
            $table->string('telefono');
            $table->string('ciudad');
            $table->string('colegio');
            $table->date('fecha_nac');
            $table->boolean('titulo_bachiller')->default(true);
            $table->foreignId('carrera1_id')->constrained('carreras');
            $table->foreignId('carrera2_id')->constrained('carreras');
            $table->enum('pago_confirmado', ['VERDADERO', 'FALSO'])->default('FALSO');
            $table->enum('estado_inscripcion', ['INSCRITO', 'PENDIENTE_PAGO', 'BLOQUEADO'])->default('PENDIENTE_PAGO');
            $table->foreignId('gestion_id')->constrained('gestions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulantes');
    }
};
