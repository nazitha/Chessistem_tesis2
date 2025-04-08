<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            'correo' => 'nazarethgarcia53@gmail.com',
            'contrasena' => Hash::make('Test1234'),
            'rol_id' => 1, // Asumiendo que 1 es el rol de administrador
            'usuario_estado' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
} 