<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate the table first
        DB::table('roles')->truncate();

        $roles = [
            [
                'id' => 1,
                'nombre' => 'Administrador',
                'descripcion' => 'Administrador del sistema'
            ],
            [
                'id' => 2,
                'nombre' => 'Evaluador',
                'descripcion' => 'Evaluador de torneos'
            ],
            [
                'id' => 3,
                'nombre' => 'Estudiante',
                'descripcion' => 'Estudiante participante'
            ],
            [
                'id' => 4,
                'nombre' => 'Gestor',
                'descripcion' => 'Gestor de torneos'
            ]
        ];

        DB::table('roles')->insert($roles);
    }
} 