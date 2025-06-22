<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            ['id_depto' => 1, 'pais_id' => 1, 'nombre_depto' => 'Boaco'],
            ['id_depto' => 2, 'pais_id' => 1, 'nombre_depto' => 'Carazo'],
            ['id_depto' => 3, 'pais_id' => 1, 'nombre_depto' => 'Chinandega'],
            ['id_depto' => 4, 'pais_id' => 1, 'nombre_depto' => 'Chontales'],
            ['id_depto' => 5, 'pais_id' => 1, 'nombre_depto' => 'Costa Caribe Norte'],
            ['id_depto' => 6, 'pais_id' => 1, 'nombre_depto' => 'Costa Caribe Sur'],
            ['id_depto' => 7, 'pais_id' => 1, 'nombre_depto' => 'Estelí'],
            ['id_depto' => 8, 'pais_id' => 1, 'nombre_depto' => 'Granada'],
            ['id_depto' => 9, 'pais_id' => 1, 'nombre_depto' => 'Jinotega'],
            ['id_depto' => 10, 'pais_id' => 1, 'nombre_depto' => 'León'],
            ['id_depto' => 11, 'pais_id' => 1, 'nombre_depto' => 'Madriz'],
            ['id_depto' => 12, 'pais_id' => 1, 'nombre_depto' => 'Managua'],
            ['id_depto' => 13, 'pais_id' => 1, 'nombre_depto' => 'Masaya'],
            ['id_depto' => 14, 'pais_id' => 1, 'nombre_depto' => 'Matagalpa'],
            ['id_depto' => 15, 'pais_id' => 1, 'nombre_depto' => 'Nueva Segovia'],
            ['id_depto' => 16, 'pais_id' => 1, 'nombre_depto' => 'Río San Juan'],
            ['id_depto' => 17, 'pais_id' => 1, 'nombre_depto' => 'Rivas'],
        ];

        DB::table('departamentos')->insert($departamentos);
    }
}
