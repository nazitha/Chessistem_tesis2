<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;

class ListarTorneos extends Command
{
    protected $signature = 'torneos:listar';
    protected $description = 'Listar todos los torneos disponibles';

    public function handle()
    {
        $torneos = Torneo::all(['id', 'nombre_torneo', 'es_por_equipos', 'no_rondas', 'estado_torneo']);

        if ($torneos->isEmpty()) {
            $this->info('No hay torneos en la base de datos.');
            return 0;
        }

        $this->info('=== TORNEOS DISPONIBLES ===');
        $this->table(
            ['ID', 'Nombre', 'Tipo', 'Rondas', 'Estado'],
            $torneos->map(function ($torneo) {
                return [
                    $torneo->id,
                    $torneo->nombre_torneo,
                    $torneo->es_por_equipos ? 'Equipos' : 'Individual',
                    $torneo->no_rondas,
                    $torneo->estado_torneo ? 'Activo' : 'Inactivo'
                ];
            })
        );

        return 0;
    }
} 