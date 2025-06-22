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
        Schema::create('academias', function (Blueprint $table) {
            $table->id('id_academia');
            $table->string('nombre_academia', 100);
            $table->string('correo_academia', 100)->nullable();
            $table->string('telefono_academia', 20)->nullable();
            $table->string('representante_academia')->nullable();
            $table->string('direccion_academia', 255)->nullable();
            $table->unsignedBigInteger('ciudad_id')->nullable();
            $table->boolean('estado_academia')->default(true);

            $table->foreign('ciudad_id')
                  ->references('id_ciudad')
                  ->on('ciudades')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academias');
    }
};
