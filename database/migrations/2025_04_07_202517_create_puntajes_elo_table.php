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
        Schema::create('puntajes_elo', function (Blueprint $table) {
            $table->string('fide_id_miembro');
            $table->foreignId('no_categoria_elo')->constrained('elo_categorias', 'no_elo');
            $table->integer('elo');
            
            $table->foreign('fide_id_miembro')->references('fide_id')->on('fides');
            $table->primary(['fide_id_miembro', 'no_categoria_elo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntajes_elo');
    }
};
