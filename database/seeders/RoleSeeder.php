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
                'nombre' => 'Usuario',
                'descripcion' => 'Usuario regular'
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