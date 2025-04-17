<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ControlTiempo;

class ControlTiempoSeeder extends Seeder
{
    public function run(): void
    {
        $controles = [
            [
                'id_control_tiempo' => 1,
                'formato' => 'Blitz',
                'control_tiempo' => '5+3',
                'descrip_control_tiempo' => 'Partidas rápidas de 5 minutos con incremento de 3 segundos'
            ],
            [
                'id_control_tiempo' => 2,
                'formato' => 'Rápido',
                'control_tiempo' => '15+10',
                'descrip_control_tiempo' => 'Partidas rápidas de 15 minutos con incremento de 10 segundos'
            ],
            [
                'id_control_tiempo' => 3,
                'formato' => 'Clásico',
                'control_tiempo' => '90+30',
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
    }
} 