<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\PartidaTorneo;
use App\Services\SwissPairingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ParticipanteTorneo;

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
            $emparejamientos = $service->generarEmparejamientos($ronda->numero_ronda);

            // Guardar partidas
            foreach ($emparejamientos as $index => $emparejamiento) {
                PartidaTorneo::create([
                    'ronda_id' => $ronda->id,
                    'jugador_blancas_id' => $emparejamiento['blancas']->miembro_id,
                    'jugador_negras_id' => isset($emparejamiento['negras']) ? $emparejamiento['negras']->miembro_id : null,
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
                'resultado_texto' => 'required|string'
            ]);

            // Convertir el texto del resultado a número
            $resultadoTexto = trim($request->resultado_texto);
            $resultado = null;

            switch ($resultadoTexto) {
                case '1-0':
                    $resultado = 1;
                    break;
                case '0-1':
                    $resultado = 2;
                    break;
                case '½-½':
                case '1/2-1/2':
                case '0.5-0.5':
                    $resultado = 3;
                    break;
                default:
                    return redirect()->back()->with('error', 'Formato de resultado inválido. Use 1-0, 0-1 o ½-½');
            }

            DB::beginTransaction();

            try {
                // 1. Actualizar el resultado de esta partida específica
                $partida = PartidaTorneo::lockForUpdate()->find($partida->id);
                if (!$partida) {
                    throw new \Exception('Partida no encontrada');
                }
                
                $partida->resultado = $resultado;
                $partida->save();

                // 2. Actualizar puntos de los jugadores involucrados
                if ($partida->jugador_blancas_id) {
                    $this->actualizarPuntosJugador($partida->jugador_blancas_id, $partida->ronda->torneo_id);
                }
                if ($partida->jugador_negras_id) {
                    $this->actualizarPuntosJugador($partida->jugador_negras_id, $partida->ronda->torneo_id);
                }

                // 3. Verificar si la ronda está completa
                $ronda = $partida->ronda;
                $partidasSinResultado = PartidaTorneo::where('ronda_id', $ronda->id)
                    ->whereNull('resultado')
                    ->where(function($query) {
                        $query->whereNotNull('jugador_negras_id')
                              ->orWhereNull('jugador_negras_id');
                    })
                    ->count();

                if ($partidasSinResultado === 0) {
                    $ronda->completada = true;
                    $ronda->save();

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
                return redirect()->back()->with('success', 'Resultado registrado exitosamente.');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error al registrar resultado: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Error al registrar el resultado: ' . $e->getMessage());
        }
    }

    private function actualizarPuntosJugador($jugadorId, $torneoId)
    {
        $participante = ParticipanteTorneo::where('torneo_id', $torneoId)
            ->where('miembro_id', $jugadorId)
            ->lockForUpdate()
            ->first();

        if (!$participante) {
            return;
        }

        $puntosTotales = 0;
        
        // Obtener todas las partidas del jugador en este torneo
        $partidas = PartidaTorneo::whereHas('ronda', function($query) use ($torneoId) {
            $query->where('torneo_id', $torneoId);
        })->where(function($query) use ($jugadorId) {
            $query->where('jugador_blancas_id', $jugadorId)
                  ->orWhere('jugador_negras_id', $jugadorId);
        })->get();

        foreach ($partidas as $p) {
            if ($p->resultado === null) continue;

            // Partida de BYE
            if (!$p->jugador_negras_id && $p->jugador_blancas_id === $jugadorId) {
                $puntosTotales += 1;
                continue;
            }

            // Partidas normales
            if ($p->jugador_blancas_id === $jugadorId) {
                if ($p->resultado === 1) $puntosTotales += 1;      // Victoria con blancas
                elseif ($p->resultado === 3) $puntosTotales += 0.5; // Tablas
            } elseif ($p->jugador_negras_id === $jugadorId) {
                if ($p->resultado === 2) $puntosTotales += 1;      // Victoria con negras
                elseif ($p->resultado === 3) $puntosTotales += 0.5; // Tablas
            }
        }

        $participante->puntos = $puntosTotales;
        $participante->save();
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