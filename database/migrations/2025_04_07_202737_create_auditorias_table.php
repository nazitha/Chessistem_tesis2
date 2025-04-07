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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->string('correo_id', 40);
            $table->string('tabla_afectada', 30);
            $table->string('accion', 20);
            $table->text('valor_previo')->nullable();
            $table->text('valor_posterior')->nullable();
            $table->date('fecha');
            $table->time('hora');
            $table->string('equipo', 50);
            
            $table->foreign('correo_id')->references('correo')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
