<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 50);
                $table->text('descripcion')->nullable();
                $table->timestamps();
            });
        }

        DB::table('roles')->insert([
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Evaluador',
                'descripcion' => 'Acceso parcial al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Estudiante',   
                'descripcion' => 'Acceso limitado al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Gestor',
                'descripcion' => 'Acceso a funciones de gestión',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);   
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
    
};
