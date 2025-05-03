<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data
        DB::table('roles')->delete();
        DB::table('usuarios')->delete();

        // Create roles
        $roles = [
            ['id' => 1, 'nombre' => 'Administrador', 'descripcion' => 'Administrador del sistema'],
            ['id' => 2, 'nombre' => 'Evaluador', 'descripcion' => 'Evaluador de torneos'],
            ['id' => 3, 'nombre' => 'Estudiante', 'descripcion' => 'Estudiante participante'],
            ['id' => 4, 'nombre' => 'Gestor', 'descripcion' => 'Gestor de torneos']
        ];

        DB::table('roles')->insert($roles);

        // Create users
        $users = [
            [
                'correo' => 'nazarethgarcia53@gmail.com',
                'contrasena' => Hash::make('Test1234'),
                'rol_id' => 1,
                'usuario_estado' => true
            ],
            [
                'correo' => 'evaluador@chessistem.com',
                'contrasena' => Hash::make('Eval1234'),
                'rol_id' => 2,
                'usuario_estado' => true
            ],
            [
                'correo' => 'estudiante@chessistem.com',
                'contrasena' => Hash::make('Est1234'),
                'rol_id' => 3,
                'usuario_estado' => true
            ],
            [
                'correo' => 'gestor@chessistem.com',
                'contrasena' => Hash::make('Gest1234'),
                'rol_id' => 4,
                'usuario_estado' => true
            ]
        ];

        DB::table('usuarios')->insert($users);

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Run other seeders
        $this->call([
            CategoriaTorneoSeeder::class,
            ControlTiempoSeeder::class,
            EmparejamientoSeeder::class,
            ParticipantesSeeder::class,
            FideEloSeeder::class,
        ]);
    }
}
