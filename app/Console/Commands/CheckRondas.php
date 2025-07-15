<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\PartidaTorneo;

class CheckRondas extends Command
{
    protected $signature = 'check:rondas {torneo_id}';
    protected $description = 'Verificar rondas y partidas de un torneo';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        
        $torneo = Torneo::find($torneoId);
        if (!$torneo) {
            $this->error("Torneo {$torneoId} no encontrado");
            return 1;
        }
        
        $this->info("Torneo: {$torneo->nombre_torneo}");
        $this->info("Sistema de emparejamiento: " . ($torneo->emparejamiento->sistema ?? 'No definido'));
        $this->info("Es por equipos: " . ($torneo->es_por_equipos ? 'Sí' : 'No'));
        $this->info("Número de rondas configurado: {$torneo->no_rondas}");
        
        $rondas = $torneo->rondas;
        $this->info("\nRondas existentes: {$rondas->count()}");
        
        foreach ($rondas as $ronda) {
            $partidas = $ronda->partidas;
            $this->info("Ronda {$ronda->numero_ronda}: {$partidas->count()} partidas");
            
            foreach ($partidas as $partida) {
                $blancas = $partida->jugadorBlancas ? $partida->jugadorBlancas->nombres : 'BYE';
                $negras = $partida->jugadorNegras ? $partida->jugadorNegras->nombres : 'BYE';
                $this->line("  Mesa {$partida->mesa}: {$blancas} vs {$negras}");
            }
        }
        
        return 0;
    }
} 