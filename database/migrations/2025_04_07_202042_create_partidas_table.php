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
        Schema::create('partidas', function (Blueprint $table) {
            $table->integer('no_partida')->autoIncrement();
            $table->integer('ronda')->nullable();
            $table->string('participante_id', 20)->nullable();
            $table->unsignedBigInteger('torneo_id')->nullable();
            $table->integer('mesa')->nullable();
            $table->boolean('color')->nullable();
            $table->time('tiempo')->nullable();
            $table->integer('desempate_utilizado_id')->nullable();
            $table->boolean('estado_abandono')->nullable();
            $table->double('resultado')->nullable();

            $table->foreign('participante_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('set null');

            $table->foreign('torneo_id')
                  ->references('id')
                  ->on('torneos')
                  ->onDelete('set null');

            $table->foreign('desempate_utilizado_id')
                  ->references('id_sistema_desempate')
                  ->on('sistemas_desempate')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
