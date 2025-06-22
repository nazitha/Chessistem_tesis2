<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academia;

class AcademiaSeeder extends Seeder
{
    public function run()
    {
        Academia::firstOrCreate([
            'nombre_academia' => 'Estrellas del Ajedrez',
        ], [
            'estado_academia' => 1
        ]);
    }
} 