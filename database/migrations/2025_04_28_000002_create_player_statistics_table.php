<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participante_id')->constrained('participantes_torneo')->onDelete('cascade');
            $table->foreignId('torneo_id')->constrained()->onDelete('cascade');
            $table->integer('partidas_jugadas')->default(0);
            $table->integer('partidas_blancas')->default(0);
            $table->integer('partidas_negras')->default(0);
            $table->integer('byes_recibidos')->default(0);
            $table->integer('flotamientos')->default(0);
            $table->float('porcentaje_blancas')->default(0);
            $table->float('porcentaje_negras')->default(0);
            $table->float('porcentaje_emparejamientos_repetidos')->default(0);
            $table->timestamps();

            $table->unique(['participante_id', 'torneo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_statistics');
    }
}; 