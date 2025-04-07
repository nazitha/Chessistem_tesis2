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
        Schema::create('sistemas_desempate', function (Blueprint $table) {
            $table->integer('id_sistema_desempate')->autoIncrement();
            $table->string('nombre_sistema_desempate', 50)->collation('utf8mb3_spanish_ci');
            $table->string('descrip_sistema_desempate', 255)->nullable()->collation('utf8mb3_spanish_ci');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistemas_desempate');
    }
};
