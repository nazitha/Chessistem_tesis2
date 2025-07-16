<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\PartidaTorneo;
use App\Models\EquipoMatch;
use App\Models\PartidaIndividual;
use Illuminate\Support\Facades\Log;

class TestClasificacionFinal extends Command
{
    protected $signature = 'test:clasificacion-final {torneo_id}';
    protected $description = 'Probar la clasificación final de un torneo';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        $torneo = Torneo::find($torneoId);

        if (!$torneo) {
            $this->error("Torneo con ID {$torneoId} no encontrado.");
            return 1;
        }

        $this->info("=== PRUEBA DE CLASIFICACIÓN FINAL ===");
        $this->info("Torneo: {$torneo->nombre_torneo}");
        $this->info("Tipo: " . ($torneo->es_por_equipos ? 'Por equipos' : 'Individual'));
        $this->info("Rondas generadas: " . $torneo->rondas->count() . " de {$torneo->no_rondas}");
        $this->info("Estado: {$torneo->estado}");

        // Verificar si el torneo está finalizado
        $torneoFinalizado = $torneo->rondas->count() >= $torneo->no_rondas;
        $this->info("¿Torneo finalizado? " . ($torneoFinalizado ? 'Sí' : 'No'));

        if (!$torneoFinalizado) {
            $this->warn("El torneo no está finalizado. Se necesitan {$torneo->no_rondas} rondas.");
            return 0;
        }

        if ($torneo->es_por_equipos) {
            $this->testClasificacionEquipos($torneo);
        } else {
            $this->testClasificacionIndividuales($torneo);
        }

        $this->info("=== FIN DE PRUEBA ===");
        return 0;
    }

    private function testClasificacionEquipos(Torneo $torneo)
    {
        $this->info("\n--- CLASIFICACIÓN POR EQUIPOS ---");
        
        $equipos = $torneo->equipos()->with(['jugadores.miembro'])->get();
        
        foreach ($equipos as $equipo) {
            // Calcular puntos totales
            $puntosTotales = 0;
            $partidasTotales = PartidaIndividual::whereHas('match', function($q) use ($equipo) {
                $q->where('torneo_id', $equipo->torneo_id)
                  ->where(function($q2) use ($equipo) {
                      $q2->where('equipo_a_id', $equipo->id)->orWhere('equipo_b_id', $equipo->id);
                  });
            })->get();
            
            foreach ($partidasTotales as $partida) {
                if ($partida->match->equipo_a_id === $equipo->id) {
                    $puntosTotales += $partida->resultado ?? 0;
                } elseif ($partida->match->equipo_b_id === $equipo->id) {
                    $puntosTotales += $partida->resultado !== null ? 1 - $partida->resultado : 0;
                }
            }
            
            $this->info("Equipo: {$equipo->nombre}");
            $this->info("  - Puntos totales: {$puntosTotales}");
            $this->info("  - Jugadores: " . $equipo->jugadores->count());
            
            // Mostrar partidas del equipo
            $matches = EquipoMatch::where('torneo_id', $torneo->id)
                ->where(function($q) use ($equipo) {
                    $q->where('equipo_a_id', $equipo->id)->orWhere('equipo_b_id', $equipo->id);
                })
                ->orderBy('ronda')
                ->get();
                
            foreach ($matches as $match) {
                $esA = $match->equipo_a_id === $equipo->id;
                $oponente = $esA ? $match->equipoB : $match->equipoA;
                $resultado = $match->resultado_match;
                
                $this->info("  - Ronda {$match->ronda}: vs " . ($oponente ? $oponente->nombre : 'BYE') . 
                           " - Resultado: " . $this->getResultadoTexto($resultado));
            }
            $this->info("");
        }
    }

    private function testClasificacionIndividuales(Torneo $torneo)
    {
        $this->info("\n--- CLASIFICACIÓN INDIVIDUAL ---");
        
        $participantes = $torneo->participantes()->with(['miembro'])->get();
        
        foreach ($participantes as $participante) {
            $this->info("Participante: {$participante->miembro->nombres} {$participante->miembro->apellidos}");
            $this->info("  - Puntos: {$participante->puntos}");
            $this->info("  - Número inicial: {$participante->numero_inicial}");
            
            // Mostrar partidas del participante
            $partidas = PartidaTorneo::where(function($q) use ($participante) {
                    $q->where('jugador_blancas_id', $participante->miembro_id)
                      ->orWhere('jugador_negras_id', $participante->miembro_id);
                })
                ->whereHas('ronda', function($query) use ($torneo) {
                    $query->where('torneo_id', $torneo->id);
                })
                ->orderBy('ronda_id')
                ->get();
                
            foreach ($partidas as $partida) {
                $esBlancas = $partida->jugador_blancas_id === $participante->miembro_id;
                $oponente = $esBlancas ? $partida->jugadorNegras : $partida->jugadorBlancas;
                $resultado = $partida->resultado;
                
                $this->info("  - Ronda {$partida->ronda->numero_ronda}: " . 
                           ($esBlancas ? 'Blancas' : 'Negras') . " vs " . 
                           ($oponente ? $oponente->nombres . ' ' . $oponente->apellidos : 'BYE') . 
                           " - Resultado: " . $this->getResultadoTexto($resultado));
            }
            $this->info("");
        }
    }

    private function getResultadoTexto($resultado)
    {
        if ($resultado === null) return 'Sin resultado';
        if ($resultado === 1) return 'Victoria';
        if ($resultado === 0) return 'Derrota';
        if ($resultado === 0.5) return 'Tablas';
        return 'Desconocido';
    }
} 