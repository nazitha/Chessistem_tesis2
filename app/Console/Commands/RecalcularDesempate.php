<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;
use App\Models\ParticipanteTorneo;
use Illuminate\Support\Facades\Log;

class RecalcularDesempate extends Command
{
    protected $signature = 'torneo:recalcular-desempate {torneo_id}';
    protected $description = 'Recalcular criterios de desempate para un torneo';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        $torneo = Torneo::find($torneoId);

        if (!$torneo) {
            $this->error("Torneo con ID {$torneoId} no encontrado.");
            return 1;
        }

        $this->info("=== RECALCULANDO CRITERIOS DE DESEMPATE ===");
        $this->info("Torneo: {$torneo->nombre_torneo}");
        $this->info("Tipo: " . ($torneo->es_por_equipos ? 'Por equipos' : 'Individual'));

        if ($torneo->es_por_equipos) {
            $this->recalcularDesempateEquipos($torneo);
        } else {
            $this->recalcularDesempateIndividuales($torneo);
        }

        $this->info("Recálculo completado.");
        return 0;
    }

    private function recalcularDesempateIndividuales(Torneo $torneo)
    {
        $this->info("Recalculando para torneo individual...");
        
        $participantes = $torneo->participantes()->with(['miembro'])->get();
        
        foreach ($participantes as $participante) {
            $buchholz = 0;
            $sonnebornBerger = 0;
            $progresivo = 0;
            $puntosAcumulados = 0;
            
            foreach ($torneo->rondas as $ronda) {
                foreach ($ronda->partidas as $partida) {
                    if ($partida->jugador_blancas_id === $participante->miembro_id) {
                        if ($partida->jugador_negras_id) { // No contar bye
                            $oponente = $participantes->where('miembro_id', $partida->jugador_negras_id)->first();
                            if ($oponente) {
                                $buchholz += $oponente->puntos;
                                if ($partida->resultado === 1) { // Victoria con blancas
                                    $sonnebornBerger += $oponente->puntos;
                                } elseif ($partida->resultado === 0.5) { // Tablas
                                    $sonnebornBerger += $oponente->puntos / 2;
                                }
                            }
                        }
                        if ($partida->resultado === 1) {
                            $puntosAcumulados += 1;
                        } elseif ($partida->resultado === 0.5) {
                            $puntosAcumulados += 0.5;
                        }
                    } elseif ($partida->jugador_negras_id === $participante->miembro_id) {
                        $oponente = $participantes->where('miembro_id', $partida->jugador_blancas_id)->first();
                        if ($oponente) {
                            $buchholz += $oponente->puntos;
                            if ($partida->resultado === 0) { // Victoria con negras
                                $sonnebornBerger += $oponente->puntos;
                            } elseif ($partida->resultado === 0.5) { // Tablas
                                $sonnebornBerger += $oponente->puntos / 2;
                            }
                        }
                        if ($partida->resultado === 0) {
                            $puntosAcumulados += 1;
                        } elseif ($partida->resultado === 0.5) {
                            $puntosAcumulados += 0.5;
                        }
                    }
                }
                $progresivo += $puntosAcumulados;
            }
            
            // Guardar en la base de datos
            $participante->update([
                'buchholz' => $buchholz,
                'sonneborn_berger' => $sonnebornBerger,
                'progresivo' => $progresivo
            ]);
            
            $this->info("{$participante->miembro->nombres} {$participante->miembro->apellidos}:");
            $this->info("  - Puntos: {$participante->puntos}");
            $this->info("  - Buchholz: {$buchholz}");
            $this->info("  - Sonneborn-Berger: {$sonnebornBerger}");
            $this->info("  - Progresivo: {$progresivo}");
        }
    }

    private function recalcularDesempateEquipos(Torneo $torneo)
    {
        $this->info("Recalculando para torneo por equipos...");
        
        $equipos = $torneo->equipos()->with(['jugadores.miembro'])->get();
        
        foreach ($equipos as $equipo) {
            // Calcular puntos totales
            $puntosTotales = 0;
            $partidasTotales = \App\Models\PartidaIndividual::whereHas('match', function($q) use ($equipo) {
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
            
            // Calcular criterios de desempate
            $buchholz = 0;
            $sonneborn = 0;
            $progresivo = 0;
            $acumulado = 0;
            
            $matchesJugados = \App\Models\EquipoMatch::where('torneo_id', $torneo->id)
                ->where(function($q) use ($equipo) {
                    $q->where('equipo_a_id', $equipo->id)->orWhere('equipo_b_id', $equipo->id);
                })
                ->orderBy('ronda')
                ->get();
                
            foreach ($matchesJugados as $match) {
                $esA = $match->equipo_a_id === $equipo->id;
                $oponente = $esA ? $match->equipoB : $match->equipoA;
                $puntosEquipo = $esA ? $match->puntos_equipo_a : $match->puntos_equipo_b;
                
                if ($oponente) {
                    // Calcular puntos totales del oponente
                    $puntosOponente = 0;
                    $partidasOponente = \App\Models\PartidaIndividual::whereHas('match', function($q) use ($oponente) {
                        $q->where('torneo_id', $oponente->torneo_id)
                          ->where(function($q2) use ($oponente) {
                              $q2->where('equipo_a_id', $oponente->id)->orWhere('equipo_b_id', $oponente->id);
                          });
                    })->get();
                    
                    foreach ($partidasOponente as $partida) {
                        if ($partida->match->equipo_a_id === $oponente->id) {
                            $puntosOponente += $partida->resultado ?? 0;
                        } elseif ($partida->match->equipo_b_id === $oponente->id) {
                            $puntosOponente += $partida->resultado !== null ? 1 - $partida->resultado : 0;
                        }
                    }
                    
                    // Buchholz: suma los puntos totales de los rivales enfrentados
                    $buchholz += $puntosOponente;
                    
                    // Sonneborn-Berger: victoria suma puntos del rival, empate suma la mitad
                    if ($match->resultado_match !== null) {
                        if (($esA && $match->resultado_match == 1) || (!$esA && $match->resultado_match == 2)) {
                            $sonneborn += $puntosOponente;
                        } elseif ($match->resultado_match == 0) {
                            $sonneborn += $puntosOponente / 2;
                        }
                    }
                }
                
                // Progresivo: suma acumulativa de puntos por ronda
                $acumulado += $puntosEquipo ?? 0;
                $progresivo += $acumulado;
            }
            
            // Guardar en la base de datos (asumiendo que los equipos tienen estos campos)
            // Nota: Esto dependerá de la estructura de tu tabla de equipos
            $this->info("Equipo {$equipo->nombre}:");
            $this->info("  - Puntos totales: {$puntosTotales}");
            $this->info("  - Buchholz: {$buchholz}");
            $this->info("  - Sonneborn-Berger: {$sonneborn}");
            $this->info("  - Progresivo: {$progresivo}");
        }
    }
} 