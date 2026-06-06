<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulante_id')->constrained('postulantes')->cascadeOnDelete();
            $table->string('comprobante')->unique();
            $table->decimal('monto', 10, 2)->default(200);
            $table->date('fecha');
            $table->enum('estado', ['PENDIENTE', 'CONFIRMADO'])->default('PENDIENTE');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
