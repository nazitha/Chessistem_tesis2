<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Emparejamiento;

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
                'descripcion' => 'Sistema de eliminación simple'
            ]
        ];

        foreach ($sistemas as $sistema) {
            Emparejamiento::firstOrCreate(
                ['id_emparejamiento' => $sistema['id_emparejamiento']],
                $sistema
            );
        }
    }
} 