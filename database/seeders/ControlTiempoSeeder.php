<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ControlTiempoSeeder extends Seeder
{
    public function run(): void
    {
        $controles = [
            [
                'id_control_tiempo' => 1,
                'formato' => 'Blitz',
                'control_tiempo' => '3+2',
                'descrip_control_tiempo' => '3 minutos por jugador con 2 segundos de incremento por movimiento'
            ],
            [
                'id_control_tiempo' => 2,
                'formato' => 'Rápido',
                'control_tiempo' => '10+5',
                'descrip_control_tiempo' => '10 minutos por jugador con 5 segundos de incremento por movimiento'
            ],
            [
                'id_control_tiempo' => 3,
                'formato' => 'Clásico',
                'control_tiempo' => '90+30',
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
    }
} 