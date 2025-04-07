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
            $table->integer('id_torneo')->autoIncrement();
            $table->unsignedInteger('categoriaTorneo_id');
            $table->string('organizador_id', 20);
            $table->unsignedInteger('control_tiempo_id')->nullable();
            $table->string('director_torneo_id', 20);
            $table->string('arbitro_principal_id', 20);
            $table->string('arbitro_id', 20);
            $table->string('arbitro_adjunto_id', 20);
            $table->string('federacion_id', 10)->nullable();
            $table->string('nombre_torneo', 100)->collation('utf8mb3_spanish_ci');
            $table->date('fecha_inicio')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->string('lugar', 100)->nullable()->collation('utf8mb3_spanish_ci');
            $table->integer('no_rondas');
            $table->boolean('estado_torneo')->default(1)->nullable();
            $table->unsignedInteger('sistema_emparejamiento_id')->nullable();

            $table->foreign('categoriaTorneo_id')
                  ->references('id_torneo_categoria')
                  ->on('categorias_torneo')
                  ->onDelete('cascade');

            $table->foreign('control_tiempo_id')
                  ->references('id_control_tiempo')
                  ->on('controles_tiempo')
                  ->onDelete('set null');

            $table->foreign('organizador_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');

            $table->foreign('director_torneo_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');

            $table->foreign('arbitro_principal_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');

            $table->foreign('arbitro_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');

            $table->foreign('arbitro_adjunto_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');

            $table->foreign('federacion_id')
                  ->references('acronimo')
                  ->on('federaciones')
                  ->onDelete('set null');

            $table->foreign('sistema_emparejamiento_id')
                  ->references('id')
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
