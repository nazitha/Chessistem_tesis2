<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EquipoMatch;
use App\Models\Torneo;
use App\Models\RondaTorneo;

class CheckMatches extends Command
{
    protected $signature = 'check:matches {torneo_id}';
    protected $description = 'Verificar matches de un torneo';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        
        $this->info("Verificando matches para el torneo ID: {$torneoId}");
        
        // Verificar torneo
        $torneo = Torneo::find($torneoId);
        if (!$torneo) {
            $this->error("Torneo no encontrado");
            return;
        }
        
        $this->info("Torneo: {$torneo->nombre_torneo}");
        $this->info("Tipo: " . ($torneo->es_por_equipos ? 'Por equipos' : 'Individual'));
        
        // Verificar rondas
        $rondas = $torneo->rondas;
        $this->info("Rondas encontradas: " . $rondas->count());
        
        foreach ($rondas as $ronda) {
            $this->info("Ronda {$ronda->numero_ronda} (ID: {$ronda->id})");
            
            // Verificar matches
            $matches = EquipoMatch::where('torneo_id', $torneoId)
                ->where('ronda', $ronda->numero_ronda)
                ->get();
                
            $this->info("  Matches encontrados: " . $matches->count());
            
            foreach ($matches as $match) {
                $this->info("    Match ID: {$match->id}");
                $this->info("      Equipo A: {$match->equipo_a_id}");
                $this->info("      Equipo B: {$match->equipo_b_id}");
                $this->info("      Mesa: {$match->mesa}");
                $this->info("      Resultado: {$match->resultado_match}");
                $this->info("      Partidas: " . $match->partidas->count());
                
                foreach ($match->partidas as $partida) {
                    $this->info("        Partida ID: {$partida->id}");
                    $this->info("          Jugador A: {$partida->jugador_a_id}");
                    $this->info("          Jugador B: {$partida->jugador_b_id}");
                    $this->info("          Tablero: {$partida->tablero}");
                    $this->info("          Resultado: {$partida->resultado}");
                }
            }
        }
        
        // Verificar todos los matches del torneo
        $allMatches = EquipoMatch::where('torneo_id', $torneoId)->get();
        $this->info("Total de matches en el torneo: " . $allMatches->count());
        
        foreach ($allMatches as $match) {
            $this->info("Match {$match->id}: Ronda {$match->ronda}, Mesa {$match->mesa}");
        }
    }
} 