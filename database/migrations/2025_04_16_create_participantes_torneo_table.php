<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participantes_torneo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('torneo_id');
            $table->string('miembro_id', 20);
            $table->integer('puntos')->default(0);
            $table->integer('posicion')->nullable();
            $table->float('buchholz')->default(0);
            $table->float('sonneborn_berger')->default(0);
            $table->float('progresivo')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('torneo_id')
                  ->references('id')
                  ->on('torneos')
                  ->onDelete('cascade');

            $table->foreign('miembro_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');

            $table->unique(['torneo_id', 'miembro_id']);
        });

        Schema::create('rondas_torneo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('torneo_id');
            $table->integer('numero_ronda');
            $table->dateTime('fecha_hora');
            $table->boolean('completada')->default(false);
            $table->timestamps();

            $table->foreign('torneo_id')
                  ->references('id')
                  ->on('torneos')
                  ->onDelete('cascade');

            $table->unique(['torneo_id', 'numero_ronda']);
        });

        Schema::create('partidas_torneo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ronda_id');
            $table->string('jugador_blancas_id', 20);
            $table->string('jugador_negras_id', 20)->nullable(); // Nullable para permitir "bye"
            $table->float('resultado', 3, 1)->nullable(); // 0=pendiente, 1=victoria blancas, 2=victoria negras, 3=tablas
            $table->integer('mesa')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('ronda_id')
                  ->references('id')
                  ->on('rondas_torneo')
                  ->onDelete('cascade');

            $table->foreign('jugador_blancas_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');

            $table->foreign('jugador_negras_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas_torneo');
        Schema::dropIfExists('rondas_torneo');
        Schema::dropIfExists('participantes_torneo');
    }
}; 