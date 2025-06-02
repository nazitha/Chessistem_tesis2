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
        Schema::create('federaciones', function (Blueprint $table) {
            $table->string('acronimo')->primary();
            $table->string('nombre_federacion');
            $table->foreignId('pais_id')->constrained('paises', 'id_pais');
            $table->boolean('federacion_estado')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('federaciones');
    }
};
