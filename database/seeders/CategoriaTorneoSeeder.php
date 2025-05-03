<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaTorneoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'id_torneo_categoria' => 1,
                'categoria_torneo' => 'Absoluto',
                'descrip_categoria_torneo' => 'Torneo abierto para todas las categorías'
            ],
            [
                'id_torneo_categoria' => 2,
                'categoria_torneo' => 'Sub-2000',
                'descrip_categoria_torneo' => 'Torneo para jugadores con ELO menor a 2000'
            ],
            [
                'id_torneo_categoria' => 3,
                'categoria_torneo' => 'Sub-1800',
                'descrip_categoria_torneo' => 'Torneo para jugadores con ELO menor a 1800'
            ],
            [
                'id_torneo_categoria' => 4,
                'categoria_torneo' => 'Sub-1600',
                'descrip_categoria_torneo' => 'Torneo para jugadores con ELO menor a 1600'
            ],
            [
                'id_torneo_categoria' => 5,
                'categoria_torneo' => 'Juvenil',
                'descrip_categoria_torneo' => 'Torneo para jugadores menores de 20 años'
            ],
            [
                'id_torneo_categoria' => 6,
                'categoria_torneo' => 'Femenino',
                'descrip_categoria_torneo' => 'Torneo exclusivo para jugadoras'
            ]
        ];

        DB::table('categorias_torneo')->insert($categorias);
    }
} 