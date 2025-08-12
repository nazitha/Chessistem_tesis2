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
            // Eliminar las claves foráneas problemáticas
            $table->dropForeign(['jugador_blancas_id']);
            $table->dropForeign(['jugador_negras_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisis_partidas', function (Blueprint $table) {
            // Restaurar las claves foráneas si es necesario
            $table->foreign('jugador_blancas_id')->references('cedula')->on('miembros')->onDelete('cascade');
            $table->foreign('jugador_negras_id')->references('cedula')->on('miembros')->onDelete('cascade');
        });
    }
};
