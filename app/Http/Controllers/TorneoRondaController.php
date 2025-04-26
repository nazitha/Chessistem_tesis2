<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\PartidaTorneo;
use App\Services\SwissPairingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TorneoRondaController extends Controller
{
    public function store(Request $request, Torneo $torneo)
    {
        try {
            if ($torneo->rondas()->count() >= $torneo->no_rondas) {
                return back()->with('error', 'Ya se han generado todas las rondas del torneo.');
            }

            if ($torneo->participantes()->count() < 2) {
                return back()->with('error', 'Se necesitan al menos 2 participantes para generar emparejamientos.');
            }

            DB::beginTransaction();

            // Crear nueva ronda
            $ronda = RondaTorneo::create([
                'torneo_id' => $torneo->id,
                'numero_ronda' => $torneo->rondas()->count() + 1,
                'fecha_hora' => now()
            ]);

            // Generar emparejamientos
            $service = new SwissPairingService($torneo);
            $emparejamientos = $service->generarEmparejamientos();

            // Guardar partidas
            foreach ($emparejamientos as $index => $emparejamiento) {
                PartidaTorneo::create([
                    'ronda_id' => $ronda->id,
                    'jugador_blancas_id' => $emparejamiento['blancas']->miembro_id,
                    'jugador_negras_id' => $emparejamiento['negras']->miembro_id ?? null,
                    'mesa' => $index + 1
                ]);
            }

            DB::commit();

            return redirect()
                ->route('torneos.show', $torneo)
                ->with('success', 'Ronda generada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al generar ronda: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Error al generar la ronda. Por favor, intente nuevamente.');
        }
    }

    public function registrarResultado(Request $request, PartidaTorneo $partida)
    {
        try {
            $request->validate([
                'resultado' => 'required|in:1,2,3' // 1=victoria blancas, 2=victoria negras, 3=tablas
            ]);

            DB::beginTransaction();

            $partida->update(['resultado' => $request->resultado]);

            // Actualizar puntuaciones
            switch ($request->resultado) {
                case 1: // Victoria blancas
                    $partida->jugadorBlancas->increment('puntos');
                    break;
                case 2: // Victoria negras
                    $partida->jugadorNegras->increment('puntos');
                    break;
                case 3: // Tablas
                    $partida->jugadorBlancas->increment('puntos', 0.5);
                    $partida->jugadorNegras->increment('puntos', 0.5);
                    break;
            }

            // Verificar si la ronda está completa
            $ronda = $partida->ronda;
            if ($ronda->partidas()->whereNull('resultado')->count() === 0) {
                $ronda->update(['completada' => true]);
                
                // Actualizar criterios de desempate
                $torneo = $ronda->torneo;
                if ($torneo->usar_buchholz) {
                    $this->actualizarBuchholz($torneo);
                }
                if ($torneo->usar_sonneborn_berger) {
                    $this->actualizarSonnebornBerger($torneo);
                }
                if ($torneo->usar_desempate_progresivo) {
                    $this->actualizarProgresivo($torneo);
                }
            }

            DB::commit();

            return redirect()
                ->route('torneos.show', $partida->ronda->torneo)
                ->with('success', 'Resultado registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar resultado: ' . $e->getMessage());
            
            return back()->with('error', 'Error al registrar el resultado. Por favor, intente nuevamente.');
        }
    }

    private function actualizarBuchholz(Torneo $torneo)
    {
        foreach ($torneo->participantes as $participante) {
            $buchholz = 0;
            foreach ($torneo->rondas as $ronda) {
                foreach ($ronda->partidas as $partida) {
                    if ($partida->jugador_blancas_id === $participante->miembro_id) {
                        if ($partida->jugador_negras_id) { // No contar bye
                            $buchholz += $partida->jugadorNegras->puntos;
                        }
                    } elseif ($partida->jugador_negras_id === $participante->miembro_id) {
                        $buchholz += $partida->jugadorBlancas->puntos;
                    }
                }
            }
            $participante->update(['buchholz' => $buchholz]);
        }
    }

    private function actualizarSonnebornBerger(Torneo $torneo)
    {
        foreach ($torneo->participantes as $participante) {
            $sonnebornBerger = 0;
            foreach ($torneo->rondas as $ronda) {
                foreach ($ronda->partidas as $partida) {
                    if ($partida->jugador_blancas_id === $participante->miembro_id) {
                        if ($partida->jugador_negras_id) { // No contar bye
                            if ($partida->resultado === 1) { // Victoria
                                $sonnebornBerger += $partida->jugadorNegras->puntos;
                            } elseif ($partida->resultado === 3) { // Tablas
                                $sonnebornBerger += $partida->jugadorNegras->puntos / 2;
                            }
                        }
                    } elseif ($partida->jugador_negras_id === $participante->miembro_id) {
                        if ($partida->resultado === 2) { // Victoria
                            $sonnebornBerger += $partida->jugadorBlancas->puntos;
                        } elseif ($partida->resultado === 3) { // Tablas
                            $sonnebornBerger += $partida->jugadorBlancas->puntos / 2;
                        }
                    }
                }
            }
            $participante->update(['sonneborn_berger' => $sonnebornBerger]);
        }
    }

    private function actualizarProgresivo(Torneo $torneo)
    {
        foreach ($torneo->participantes as $participante) {
            $progresivo = 0;
            $puntos_acumulados = 0;
            foreach ($torneo->rondas as $ronda) {
                foreach ($ronda->partidas as $partida) {
                    if ($partida->jugador_blancas_id === $participante->miembro_id) {
                        if ($partida->resultado === 1) {
                            $puntos_acumulados += 1;
                        } elseif ($partida->resultado === 3) {
                            $puntos_acumulados += 0.5;
                        }
                    } elseif ($partida->jugador_negras_id === $participante->miembro_id) {
                        if ($partida->resultado === 2) {
                            $puntos_acumulados += 1;
                        } elseif ($partida->resultado === 3) {
                            $puntos_acumulados += 0.5;
                        }
                    }
                }
                $progresivo += $puntos_acumulados;
            }
            $participante->update(['progresivo' => $progresivo]);
        }
    }
} 