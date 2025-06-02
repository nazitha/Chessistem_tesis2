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
                $table->timestamps();
            });
        }

        DB::table('roles')->insert([
            [
                'id' => 1,
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'nombre' => 'Evaluador',
                'descripcion' => 'Acceso parcial al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'nombre' => 'Estudiante',   
                'descripcion' => 'Acceso limitado al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
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
