<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ciudades = [
            // Boaco
            ['id_ciudad' => 1, 'depto_id' => 1, 'nombre_ciudad' => 'Boaco'],
            // Carazo
            ['id_ciudad' => 2, 'depto_id' => 2, 'nombre_ciudad' => 'Jinotepe'],
            // Chinandega
            ['id_ciudad' => 3, 'depto_id' => 3, 'nombre_ciudad' => 'Chinandega'],
            // Chontales
            ['id_ciudad' => 4, 'depto_id' => 4, 'nombre_ciudad' => 'Juigalpa'],
            // Costa Caribe Norte
            ['id_ciudad' => 5, 'depto_id' => 5, 'nombre_ciudad' => 'Puerto Cabezas'],
            // Costa Caribe Sur
            ['id_ciudad' => 6, 'depto_id' => 6, 'nombre_ciudad' => 'Bluefields'],
            // Estelí
            ['id_ciudad' => 7, 'depto_id' => 7, 'nombre_ciudad' => 'Estelí'],
            // Granada
            ['id_ciudad' => 8, 'depto_id' => 8, 'nombre_ciudad' => 'Granada'],
            // Jinotega
            ['id_ciudad' => 9, 'depto_id' => 9, 'nombre_ciudad' => 'Jinotega'],
            // León
            ['id_ciudad' => 10, 'depto_id' => 10, 'nombre_ciudad' => 'León'],
            // Madriz
            ['id_ciudad' => 11, 'depto_id' => 11, 'nombre_ciudad' => 'Somoto'],
            // Managua
            ['id_ciudad' => 12, 'depto_id' => 12, 'nombre_ciudad' => 'Managua'],
            // Masaya
            ['id_ciudad' => 13, 'depto_id' => 13, 'nombre_ciudad' => 'Masaya'],
            // Matagalpa
            ['id_ciudad' => 14, 'depto_id' => 14, 'nombre_ciudad' => 'Matagalpa'],
            // Nueva Segovia
            ['id_ciudad' => 15, 'depto_id' => 15, 'nombre_ciudad' => 'Ocotal'],
            // Río San Juan
            ['id_ciudad' => 16, 'depto_id' => 16, 'nombre_ciudad' => 'San Carlos'],
            // Rivas
            ['id_ciudad' => 17, 'depto_id' => 17, 'nombre_ciudad' => 'Rivas'],
        ];

        DB::table('ciudades')->insert($ciudades);
    }
}
