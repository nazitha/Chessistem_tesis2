<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
<<<<<<< HEAD
use App\Models\Emparejamiento;
=======
use Illuminate\Support\Facades\DB;
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b

class EmparejamientoSeeder extends Seeder
{
    public function run(): void
    {
        $sistemas = [
            [
                'id_emparejamiento' => 1,
                'sistema' => 'Sistema Suizo',
                'descripcion' => 'Sistema de emparejamiento suizo estándar'
            ],
            [
                'id_emparejamiento' => 2,
                'sistema' => 'Round Robin',
                'descripcion' => 'Todos contra todos'
            ],
            [
                'id_emparejamiento' => 3,
                'sistema' => 'Eliminación Directa',
<<<<<<< HEAD
                'descripcion' => 'Sistema de eliminación simple'
            ]
        ];

        foreach ($sistemas as $sistema) {
            Emparejamiento::firstOrCreate(
                ['id_emparejamiento' => $sistema['id_emparejamiento']],
                $sistema
            );
        }
=======
                'descripcion' => 'Sistema de eliminación directa'
            ]
        ];

        DB::table('sistemas_de_emparejamiento')->insert($sistemas);
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
    }
} 