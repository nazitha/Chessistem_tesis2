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
        Schema::create('categorias_torneo', function (Blueprint $table) {
            $table->unsignedInteger('id_torneo_categoria')->primary();
            $table->string('categoria_torneo', 50);
            $table->string('descrip_categoria_torneo', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_torneo');
    }
};
