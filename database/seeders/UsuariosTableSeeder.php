<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuariosTableSeeder extends Seeder
{
    public function run()
{
    DB::table('usuarios')->insert([
        'nombre' => 'Admin',
        'correo' => 'admin@example.com',
        'contrasena' => bcrypt('password'),
        'rol_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
}
