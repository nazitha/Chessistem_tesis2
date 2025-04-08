<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Crear roles si no existen
        $adminRole = Role::firstOrCreate(
            ['id' => 1],
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema'
            ]
        );

        // Crear usuario administrador
        User::firstOrCreate(
            ['correo' => 'admin@estrellasdelajedrez.com'],
            [
                'contrasena' => 'admin123',
                'rol_id' => $adminRole->id,
                'usuario_estado' => true
            ]
        );

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
