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
        Schema::create('asignaciones_permisos', function (Blueprint $table) {
            $table->foreignId('rol_id')->constrained('roles');
            $table->integer('permiso_id');
            $table->primary(['rol_id', 'permiso_id']);

            $table->foreign('permiso_id')
                  ->references('id')
                  ->on('permisos')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_permisos');
    }
};
