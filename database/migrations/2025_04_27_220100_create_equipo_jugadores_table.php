<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo_jugadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipo_id');
            $table->string('miembro_id', 20);
            $table->integer('tablero')->nullable();
            $table->timestamps();

            $table->foreign('equipo_id')->references('id')->on('equipos_torneo')->onDelete('cascade');
            $table->foreign('miembro_id')->references('cedula')->on('miembros')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_jugadores');
    }
}; 