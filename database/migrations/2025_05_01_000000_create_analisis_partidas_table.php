<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('analisis_partidas')) {
            Schema::drop('analisis_partidas');
        }
        Schema::create('analisis_partidas', function (Blueprint $table) {
            $table->id();
            $table->integer('partida_id')->nullable(); // Nullable para PGN manual
            $table->longText('movimientos');
            $table->string('jugador_blancas_id', 20)->nullable(); // Nullable para PGN manual
            $table->string('jugador_negras_id', 20)->nullable(); // Nullable para PGN manual
            $table->text('evaluacion_general');
            $table->integer('errores_blancas');
            $table->integer('errores_negras');
            $table->integer('brillantes_blancas');
            $table->integer('brillantes_negras');
            $table->integer('blunders_blancas');
            $table->integer('blunders_negras');
            $table->timestamps();

            $table->foreign('partida_id')->references('no_partida')->on('partidas')->onDelete('cascade');
            $table->foreign('jugador_blancas_id')->references('cedula')->on('miembros')->onDelete('cascade');
            $table->foreign('jugador_negras_id')->references('cedula')->on('miembros')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('analisis_partidas');
    }
}; 