<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 50);
                $table->text('descripcion')->nullable();
            });
        }

        DB::table('roles')->insert([
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema'
            ],
            [
                'nombre' => 'Evaluador',
                'descripcion' => 'Acceso parcial al sistema'
            ],
            [
                'nombre' => 'Estudiante',   
                'descripcion' => 'Acceso limitado al sistema'
            ],
            [
                'nombre' => 'Gestor',
                'descripcion' => 'Acceso a funciones de gestión'
            ]
        ]);   
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
    
};
