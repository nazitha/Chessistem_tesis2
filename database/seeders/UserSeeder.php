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
            [
                'correo' => 'nazarethgarcia53@gmail.com',
                'contrasena' => Hash::make('Test1234'),
                'rol_id' => 1, // Administrador
                'usuario_estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'correo' => 'evaluador@chessistem.com',
                'contrasena' => Hash::make('Eval1234'),
                'rol_id' => 2, // Evaluador
                'usuario_estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'correo' => 'estudiante@chessistem.com',
                'contrasena' => Hash::make('Est1234'),
                'rol_id' => 3, // Estudiante
                'usuario_estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'correo' => 'gestor@chessistem.com',
                'contrasena' => Hash::make('Gest1234'),
                'rol_id' => 4, // Gestor
                'usuario_estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
} 