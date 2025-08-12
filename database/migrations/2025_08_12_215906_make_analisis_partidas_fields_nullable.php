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
        Schema::table('analisis_partidas', function (Blueprint $table) {
            $table->integer('partida_id')->nullable()->change();
            $table->string('jugador_blancas_id', 20)->nullable()->change();
            $table->string('jugador_negras_id', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisis_partidas', function (Blueprint $table) {
            $table->integer('partida_id')->nullable(false)->change();
            $table->string('jugador_blancas_id', 20)->nullable(false)->change();
            $table->string('jugador_negras_id', 20)->nullable(false)->change();
        });
    }
};
