<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidas_individuales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipo_match_id');
            $table->string('jugador_a_id', 20);
            $table->string('jugador_b_id', 20)->nullable(); // Permitir NULL para BYE
            $table->integer('tablero');
            $table->float('resultado')->nullable(); // 0: gana B, 1: gana A, 0.5: empate
            $table->timestamps();

            $table->foreign('equipo_match_id')->references('id')->on('equipos_matches')->onDelete('cascade');
            $table->foreign('jugador_a_id')->references('cedula')->on('miembros')->onDelete('cascade');
            $table->foreign('jugador_b_id')->references('cedula')->on('miembros')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas_individuales');
    }
}; 