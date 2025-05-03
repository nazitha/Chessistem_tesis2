<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            $table->integer('max_byes_por_jugador')->default(1)->after('permitir_bye');
            $table->integer('diferencia_maxima_puntos')->default(2)->after('max_byes_por_jugador');
        });
    }

    public function down(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            $table->dropColumn('max_byes_por_jugador');
            $table->dropColumn('diferencia_maxima_puntos');
        });
    }
}; 