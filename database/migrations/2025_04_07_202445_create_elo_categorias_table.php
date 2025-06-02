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
        Schema::create('elo_categorias', function (Blueprint $table) {
            $table->id('no_elo');
            $table->string('categoria_elo', 10)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('elo_categorias_estado')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elo_categorias');
    }
};
