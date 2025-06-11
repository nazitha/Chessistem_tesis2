<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
<<<<<<< HEAD
use App\Models\ControlTiempo;
=======
use Illuminate\Support\Facades\DB;
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b

class ControlTiempoSeeder extends Seeder
{
    public function run(): void
    {
        $controles = [
            [
                'id_control_tiempo' => 1,
                'formato' => 'Blitz',
<<<<<<< HEAD
                'control_tiempo' => '5+3',
                'descrip_control_tiempo' => 'Partidas rápidas de 5 minutos con incremento de 3 segundos'
=======
                'control_tiempo' => '3+2',
                'descrip_control_tiempo' => '3 minutos por jugador con 2 segundos de incremento por movimiento'
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
            ],
            [
                'id_control_tiempo' => 2,
                'formato' => 'Rápido',
<<<<<<< HEAD
                'control_tiempo' => '15+10',
                'descrip_control_tiempo' => 'Partidas rápidas de 15 minutos con incremento de 10 segundos'
=======
                'control_tiempo' => '10+5',
                'descrip_control_tiempo' => '10 minutos por jugador con 5 segundos de incremento por movimiento'
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
            ],
            [
                'id_control_tiempo' => 3,
                'formato' => 'Clásico',
                'control_tiempo' => '90+30',
<<<<<<< HEAD
                'descrip_control_tiempo' => 'Partidas clásicas de 90 minutos con incremento de 30 segundos'
            ],
            [
                'id_control_tiempo' => 4,
                'formato' => 'Escolar',
                'control_tiempo' => '30+0',
                'descrip_control_tiempo' => 'Partidas escolares de 30 minutos a finish'
            ]
        ];

        foreach ($controles as $control) {
            ControlTiempo::firstOrCreate(
                ['id_control_tiempo' => $control['id_control_tiempo']],
                $control
            );
        }
=======
                'descrip_control_tiempo' => '90 minutos por jugador con 30 segundos de incremento por movimiento'
            ],
            [
                'id_control_tiempo' => 4,
                'formato' => 'Semi-Rápido',
                'control_tiempo' => '15+10',
                'descrip_control_tiempo' => '15 minutos por jugador con 10 segundos de incremento por movimiento'
            ],
            [
                'id_control_tiempo' => 5,
                'formato' => 'Clásico FIDE',
                'control_tiempo' => '120+30',
                'descrip_control_tiempo' => '120 minutos por jugador con 30 segundos de incremento por movimiento'
            ]
        ];

        DB::table('controles_tiempo')->insert($controles);
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
    }
} 