<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaTorneo;

class CategoriaTorneoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'id_torneo_categoria' => 1,
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
    }
} 