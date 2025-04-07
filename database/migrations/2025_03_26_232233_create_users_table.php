<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_email'); 
            $table->string('correo', 40)->unique(); 
            $table->string('contrasena', 80);
            $table->unsignedBigInteger('rol_id')->nullable();
            $table->boolean('usuario_estado')->default(true); 
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('rol_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};