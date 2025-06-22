<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'nombre' => 'Administrador',
                'descripcion' => 'Administrador del sistema'
            ],
            [
                'id' => 2,
                'nombre' => 'Evaluador',
                'descripcion' => 'Evaluador del sistema'
            ],
            [
                'id' => 3,
                'nombre' => 'Estudiante',
                'descripcion' => 'Estudiante del sistema'
            ],
            [
                'id' => 4,
                'nombre' => 'Gestor',
                'descripcion' => 'Gestor del sistema'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['id' => $role['id']],
                $role
            );
        }
    }
} 