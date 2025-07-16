<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;
use App\Models\PartidaTorneo;
use App\Models\ParticipanteTorneo;
use Illuminate\Support\Facades\Log;

class TestDesempateIndividual extends Command
{
    protected $signature = 'test:desempate-individual {torneo_id}';
    protected $description = 'Probar criterios de desempate en torneos individuales';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        $torneo = Torneo::find($torneoId);

        if (!$torneo) {
            $this->error("Torneo con ID {$torneoId} no encontrado.");
            return 1;
        }

        if ($torneo->es_por_equipos) {
            $this->error("Este comando es solo para torneos individuales.");
            return 1;
        }

        $this->info("=== PRUEBA DE CRITERIOS DE DESEMPATE INDIVIDUAL ===");
        $this->info("Torneo: {$torneo->nombre_torneo}");
        $this->info("Rondas: " . $torneo->rondas->count() . " de {$torneo->no_rondas}");
        $this->info("Buchholz: " . ($torneo->usar_buchholz ? 'Sí' : 'No'));
        $this->info("Sonneborn-Berger: " . ($torneo->usar_sonneborn_berger ? 'Sí' : 'No'));
        $this->info("Progresivo: " . ($torneo->usar_desempate_progresivo ? 'Sí' : 'No'));

        $participantes = $torneo->participantes()->with(['miembro'])->get();
        
        $this->info("\n--- PARTICIPANTES Y SUS PUNTOS ---");
        foreach ($participantes as $participante) {
            $this->info("{$participante->miembro->nombres} {$participante->miembro->apellidos}: {$participante->puntos} puntos");
        }

        $this->info("\n--- PARTIDAS DEL TORNEO ---");
        foreach ($torneo->rondas as $ronda) {
            $this->info("Ronda {$ronda->numero_ronda}:");
            foreach ($ronda->partidas as $partida) {
                $blancas = $participantes->where('miembro_id', $partida->jugador_blancas_id)->first();
                $negras = $partida->jugador_negras_id ? $participantes->where('miembro_id', $partida->jugador_negras_id)->first() : null;
                
                $resultado = $this->getResultadoTexto($partida->resultado);
                $this->info("  Mesa {$partida->mesa}: " . 
                           ($blancas ? $blancas->miembro->nombres . ' ' . $blancas->miembro->apellidos : 'BYE') . 
                           " vs " . 
                           ($negras ? $negras->miembro->nombres . ' ' . $negras->miembro->apellidos : 'BYE') . 
                           " - Resultado: {$resultado}");
            }
        }

        // Calcular criterios de desempate manualmente
        $this->info("\n--- CÁLCULO MANUAL DE CRITERIOS DE DESEMPATE ---");
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
            
            $this->info("{$participante->miembro->nombres} {$participante->miembro->apellidos}:");
            $this->info("  - Puntos: {$participante->puntos}");
            $this->info("  - Buchholz: {$buchholz}");
            $this->info("  - Sonneborn-Berger: {$sonnebornBerger}");
            $this->info("  - Progresivo: {$progresivo}");
            $this->info("");
        }

        return 0;
    }

    private function getResultadoTexto($resultado)
    {
        if ($resultado === null) return 'Sin resultado';
        if ($resultado === 1) return '1-0 (Victoria blancas)';
        if ($resultado === 0) return '0-1 (Victoria negras)';
        if ($resultado === 0.5) return '½-½ (Tablas)';
        return 'Desconocido';
    }
} 