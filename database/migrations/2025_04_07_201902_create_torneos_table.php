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
        Schema::create('torneos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('categoriaTorneo_id')->nullable();
            $table->string('organizador_id', 20)->nullable();
            $table->unsignedInteger('control_tiempo_id')->nullable();
            $table->string('director_torneo_id', 20)->nullable();
            $table->string('arbitro_principal_id', 20)->nullable();
            $table->string('arbitro_id', 20)->nullable();
            $table->string('arbitro_adjunto_id', 20)->nullable();
            $table->string('federacion_id', 10)->nullable();
            $table->string('nombre_torneo', 100)->collation('utf8mb3_spanish_ci');
            $table->date('fecha_inicio')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->string('lugar', 100)->nullable()->collation('utf8mb3_spanish_ci');
            $table->integer('no_rondas')->nullable();
            $table->boolean('estado_torneo')->default(1)->nullable();
            $table->unsignedBigInteger('sistema_emparejamiento_id')->nullable();
            $table->boolean('usar_buchholz')->default(false);
            $table->boolean('usar_sonneborn_berger')->default(false);
            $table->boolean('usar_desempate_progresivo')->default(false);
            $table->integer('numero_minimo_participantes')->default(4);
            $table->boolean('permitir_bye')->default(true);
            $table->boolean('alternar_colores')->default(true);
            $table->boolean('evitar_emparejamientos_repetidos')->default(true);
            $table->integer('maximo_emparejamientos_repetidos')->default(1);

            $table->foreign('categoriaTorneo_id')
                  ->references('id_torneo_categoria')
                  ->on('categorias_torneo')
                  ->onDelete('set null');

            $table->foreign('control_tiempo_id')
                  ->references('id_control_tiempo')
                  ->on('controles_tiempo')
                  ->onDelete('set null');

            $table->foreign('organizador_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('set null');

            $table->foreign('director_torneo_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('set null');

            $table->foreign('arbitro_principal_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('set null');

            $table->foreign('arbitro_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('set null');

            $table->foreign('arbitro_adjunto_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('set null');

            $table->foreign('federacion_id')
                  ->references('acronimo')
                  ->on('federaciones')
                  ->onDelete('set null');

            $table->foreign('sistema_emparejamiento_id')
                  ->references('id_emparejamiento')
                  ->on('sistemas_de_emparejamiento')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torneos');
    }
};
