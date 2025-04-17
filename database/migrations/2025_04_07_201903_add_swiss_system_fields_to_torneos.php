<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            // Campos de tiempo
            $table->integer('tiempo_por_partida')->after('no_rondas')->nullable();
            $table->integer('incremento_tiempo')->after('tiempo_por_partida')->nullable()->default(0);
            
            // Campos de puntuación
            $table->decimal('puntos_victoria', 3, 1)->after('incremento_tiempo')->default(1.0);
            $table->decimal('puntos_empate', 3, 1)->after('puntos_victoria')->default(0.5);
            $table->decimal('puntos_derrota', 3, 1)->after('puntos_empate')->default(0.0);
            
            // Fecha de finalización
            $table->date('fecha_fin')->after('fecha_inicio')->nullable();
            
            // Criterios de desempate como JSON
            $table->json('criterios_desempate')->after('puntos_derrota')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            $table->dropColumn([
                'tiempo_por_partida',
                'incremento_tiempo',
                'puntos_victoria',
                'puntos_empate',
                'puntos_derrota',
                'fecha_fin',
                'criterios_desempate'
            ]);
        });
    }
}; 