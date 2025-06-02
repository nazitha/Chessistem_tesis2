<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('control_tiempo_torneos', function (Blueprint $table) {
            $table->unsignedInteger('control_tiempo_id');
            $table->unsignedInteger('categorias_torneo_id');
            
            $table->foreign('control_tiempo_id')
                  ->references('id_control_tiempo')
                  ->on('controles_tiempo')
                  ->onDelete('cascade');
                  
            $table->foreign('categorias_torneo_id')
                  ->references('id_torneo_categoria')
                  ->on('categorias_torneo')
                  ->onDelete('cascade');
                  
            $table->primary(['control_tiempo_id', 'categorias_torneo_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('control_tiempo_torneos');
    }
}; 