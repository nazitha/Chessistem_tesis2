<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('torneo_id');
            $table->integer('ronda');
            $table->unsignedBigInteger('equipo_a_id');
            $table->unsignedBigInteger('equipo_b_id')->nullable(); // Puede ser null para BYE
            $table->float('puntos_equipo_a')->nullable();
            $table->float('puntos_equipo_b')->nullable();
            $table->tinyInteger('resultado_match')->nullable(); // 0: empate, 1: ganó A, 2: ganó B
            $table->integer('mesa')->nullable();
            $table->timestamps();

            $table->foreign('torneo_id')->references('id')->on('torneos')->onDelete('cascade');
            $table->foreign('equipo_a_id')->references('id')->on('equipos_torneo')->onDelete('cascade');
            $table->foreign('equipo_b_id')->references('id')->on('equipos_torneo')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos_matches');
    }
}; 