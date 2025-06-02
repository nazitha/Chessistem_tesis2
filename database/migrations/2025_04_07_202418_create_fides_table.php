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
        Schema::create('fides', function (Blueprint $table) {
            $table->string('fide_id')->primary();
            $table->string('cedula_ajedrecista_id');
            $table->string('fed_id');
            $table->string('titulo')->nullable();
            $table->boolean('fide_estado')->default(true);
            
            $table->foreign('cedula_ajedrecista_id')->references('cedula')->on('miembros');
            $table->foreign('fed_id')->references('acronimo')->on('federaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fides');
    }
};
