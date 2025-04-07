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
        Schema::create('miembros', function (Blueprint $table) {
            $table->string('cedula', 20)->primary();
            $table->string('nombres', 50)->nullable();
            $table->string('apellidos', 50)->nullable();
            $table->char('sexo', 1)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('telefono')->nullable();
            $table->date('fecha_inscripcion')->nullable();
            $table->string('club', 30)->nullable();
            $table->string('correo_sistema_id', 40)->nullable();
            $table->unsignedBigInteger('ciudad_id')->nullable();
            $table->string('academia_id', 50)->nullable();
            $table->boolean('estado_miembro')->nullable();

            $table->foreign('correo_sistema_id')
                  ->references('correo')
                  ->on('usuarios')
                  ->onDelete('set null');

            $table->foreign('ciudad_id')
                  ->references('id_ciudad')
                  ->on('ciudades')
                  ->onDelete('set null');

            $table->foreign('academia_id')
                  ->references('id')
                  ->on('academias')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miembros');
    }
};
