<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
{
    Schema::create('usuarios', function (Blueprint $table) {
        $table->string('correo')->primary(); // Clave primaria personalizada
        $table->string('contrasena');
        $table->unsignedBigInteger('rol_id');
        $table->boolean('usuario_estado')->default(true); // Estado activo/inactivo
        $table->rememberToken();
        $table->timestamps();

        // Clave foránea
        $table->foreign('rol_id')
              ->references('id')
              ->on('roles') 
              ->onDelete('cascade');
    });
}

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};