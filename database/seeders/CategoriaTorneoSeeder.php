<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
<<<<<<< HEAD
use App\Models\CategoriaTorneo;
=======
use Illuminate\Support\Facades\DB;
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b

class CategoriaTorneoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'id_torneo_categoria' => 1,
<<<<<<< HEAD
                'categoria_torneo' => 'Sub-8',
                'descrip_categoria_torneo' => 'Categoría para jugadores menores de 8 años'
            ],
            [
                'id_torneo_categoria' => 2,
                'categoria_torneo' => 'Sub-10',
                'descrip_categoria_torneo' => 'Categoría para jugadores menores de 10 años'
            ],
            [
                'id_torneo_categoria' => 3,
                'categoria_torneo' => 'Sub-12',
                'descrip_categoria_torneo' => 'Categoría para jugadores menores de 12 años'
            ],
            [
                'id_torneo_categoria' => 4,
                'categoria_torneo' => 'Sub-14',
                'descrip_categoria_torneo' => 'Categoría para jugadores menores de 14 años'
            ],
            [
                'id_torneo_categoria' => 5,
                'categoria_torneo' => 'Sub-16',
                'descrip_categoria_torneo' => 'Categoría para jugadores menores de 16 años'
            ],
            [
                'id_torneo_categoria' => 6,
                'categoria_torneo' => 'Absoluto',
                'descrip_categoria_torneo' => 'Categoría abierta para todos los jugadores'
            ],
            [
                'id_torneo_categoria' => 7,
                'categoria_torneo' => 'Femenino',
                'descrip_categoria_torneo' => 'Categoría exclusiva para jugadoras'
            ],
            [
                'id_torneo_categoria' => 8,
                'categoria_torneo' => 'Senior',
                'descrip_categoria_torneo' => 'Categoría para jugadores mayores de 50 años'
            ]
        ];

        foreach ($categorias as $categoria) {
            CategoriaTorneo::firstOrCreate(
                ['id_torneo_categoria' => $categoria['id_torneo_categoria']],
                $categoria
            );
        }
=======
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
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
    }
} 