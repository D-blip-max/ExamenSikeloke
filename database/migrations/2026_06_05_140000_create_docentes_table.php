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
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->unique();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->string('especialidad');
            $table->foreignId('turno_id')->constrained('turnos');
            $table->boolean('maestria')->default(false);
            $table->boolean('diplomado_edu')->default(false);
            $table->enum('estado', ['ACTIVO', 'NO ACTIVO'])->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
