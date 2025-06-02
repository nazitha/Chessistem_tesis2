<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos_torneo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('torneo_id');
            $table->string('nombre');
            $table->string('capitan_id', 20)->nullable()->collation('utf8mb3_spanish_ci');
            $table->float('elo_medio')->nullable();
            $table->string('federacion', 10)->nullable();
            $table->string('logo')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('torneo_id')->references('id')->on('torneos')->onDelete('cascade');
            // $table->foreign('capitan_id')->references('cedula')->on('miembros')->onDelete('set null');
        });

        // Forzar el collation de la tabla
        // DB::statement("ALTER TABLE equipos_torneo CONVERT TO CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci");
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos_torneo');
    }
}; 