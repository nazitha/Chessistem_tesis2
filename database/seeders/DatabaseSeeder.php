<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->call([
            PaisSeeder::class,
            DepartamentoSeeder::class,
            CiudadSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CategoriaTorneoSeeder::class,
            ControlTiempoSeeder::class,
            EmparejamientoSeeder::class,
            ParticipantesSeeder::class,
            FideEloSeeder::class,
            AcademiaSeeder::class,
            AnalisisTestDataSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
