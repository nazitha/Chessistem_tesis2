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
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->integer('torneo_id');
            $table->string('miembro_id');
            $table->integer('puntos')->default(0);
            $table->integer('posicion')->nullable();
            $table->timestamps();
            
            $table->foreign('torneo_id')
                  ->references('id_torneo')
                  ->on('torneos')
                  ->onDelete('cascade');
                  
            $table->foreign('miembro_id')
                  ->references('cedula')
                  ->on('miembros')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participantes');
    }
};
