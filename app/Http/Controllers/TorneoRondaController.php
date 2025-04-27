<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RondaTorneo;
use App\Models\PartidaTorneo;
use App\Models\ParticipanteTorneo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Torneo;
use App\Models\Participante;
use App\Services\SwissPairingService;

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

            DB::beginTransaction();

            try {
                Log::info('=== Iniciando registro de resultado ===');
                Log::info('Partida ID: ' . $partida->id);
                Log::info('Resultado texto recibido: ' . $request->resultado_texto);
                Log::info('Jugador Blancas ID: ' . $partida->jugador_blancas_id);
                Log::info('Jugador Negras ID: ' . ($partida->jugador_negras_id ?? 'BYE'));

                // 1. Actualizar el resultado de esta partida específica
                $partida = PartidaTorneo::lockForUpdate()->find($partida->id);
                if (!$partida) {
                    throw new \Exception('Partida no encontrada');
                }
                
                try {
                    $resultadoAnterior = $partida->resultado;
                    $partida->setResultadoFromTexto($request->resultado_texto);
                    $partida->save();

                    Log::info('Resultado anterior: ' . $resultadoAnterior);
                    Log::info('Nuevo resultado (código): ' . $partida->resultado);
                    Log::info('Nuevo resultado (texto): ' . $partida->getResultadoTexto());
                } catch (\InvalidArgumentException $e) {
                    Log::error('Error al establecer resultado: ' . $e->getMessage());
                    return redirect()->back()->with('error', $e->getMessage());
                }

                // 2. Actualizar puntos de los jugadores involucrados
                if ($partida->jugador_blancas_id) {
                    Log::info('=== Actualizando puntos jugador blancas ===');
                    Log::info('ID: ' . $partida->jugador_blancas_id);
                    $this->actualizarPuntosJugador($partida->jugador_blancas_id, $partida->ronda->torneo_id);
                }
                if ($partida->jugador_negras_id) {
                    Log::info('=== Actualizando puntos jugador negras ===');
                    Log::info('ID: ' . $partida->jugador_negras_id);
                    $this->actualizarPuntosJugador($partida->jugador_negras_id, $partida->ronda->torneo_id);
                }

                // 3. Verificar si la ronda está completa y actualizar estado
                $ronda = $partida->ronda;
                $partidasSinResultado = PartidaTorneo::where('ronda_id', $ronda->id)
                    ->whereNull('resultado')
                    ->where(function($query) {
                        $query->whereNotNull('jugador_negras_id')
                              ->orWhereNull('jugador_negras_id');
                    })
                    ->count();

                Log::info('Partidas sin resultado en la ronda: ' . $partidasSinResultado);

                if ($partidasSinResultado === 0) {
                    $ronda->completada = true;
                    $ronda->save();
                    Log::info('Ronda marcada como completada');

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
                Log::info('=== Transacción completada exitosamente ===');
                
                // Forzar recarga de relaciones
                $partida->load(['jugadorBlancas.participanteTorneo', 'jugadorNegras.participanteTorneo']);
                
                return redirect()->back()->with('success', 'Resultado registrado exitosamente.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en la transacción: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error al registrar resultado: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Error al registrar el resultado: ' . $e->getMessage());
        }
    }

    public function guardarResultadosRonda(Request $request, RondaTorneo $ronda)
    {
        try {
            $request->validate([
                'resultados' => 'required|array',
                'resultados.*' => 'required|string'
            ]);

            DB::beginTransaction();

            try {
                Log::info('=== Iniciando registro de resultados de ronda ===');
                Log::info('Ronda ID: ' . $ronda->id);
                Log::info('Resultados recibidos: ' . json_encode($request->resultados));

                foreach ($request->resultados as $partidaId => $resultado) {
                    $partida = PartidaTorneo::lockForUpdate()->find($partidaId);
                    
                    if (!$partida || $partida->ronda_id !== $ronda->id) {
                        throw new \Exception('Partida no encontrada o no pertenece a esta ronda');
                    }

                    Log::info("Procesando partida ID: {$partidaId}");
                    Log::info("Resultado recibido: {$resultado}");
                    
                    $resultadoAnterior = $partida->resultado;
                    $partida->setResultadoFromTexto($resultado);
                    $partida->save();

                    Log::info("Resultado anterior: {$resultadoAnterior}");
                    Log::info("Nuevo resultado (código): {$partida->resultado}");
                    Log::info("Nuevo resultado (texto): " . $partida->getResultadoTexto());

                    // Actualizar puntos de los jugadores
                    if ($partida->jugador_blancas_id) {
                        $this->actualizarPuntosJugador($partida->jugador_blancas_id, $ronda->torneo_id);
                    }
                    if ($partida->jugador_negras_id) {
                        $this->actualizarPuntosJugador($partida->jugador_negras_id, $ronda->torneo_id);
                    }
                }

                // Verificar si la ronda está completa
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
                    Log::info('Ronda marcada como completada');

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
                Log::info('=== Transacción completada exitosamente ===');

                return redirect()->back()->with('success', 'Resultados guardados exitosamente.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en la transacción: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error al guardar resultados: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Error al guardar los resultados: ' . $e->getMessage());
        }
    }

    private function actualizarPuntosJugador($jugadorId, $torneoId)
    {
        try {
            DB::beginTransaction();

            $participante = ParticipanteTorneo::where('torneo_id', $torneoId)
                ->where('miembro_id', $jugadorId)
                ->lockForUpdate()
                ->first();

            if (!$participante) {
                DB::rollBack();
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
                    $puntosTotales += 1.0;
                    continue;
                }

                // Partidas normales
                if ($p->jugador_blancas_id === $jugadorId) {
                    if ($p->resultado === 1) $puntosTotales += 1.0;      // Victoria con blancas
                    elseif ($p->resultado === 3) $puntosTotales += 0.5;  // Tablas
                } elseif ($p->jugador_negras_id === $jugadorId) {
                    if ($p->resultado === 2) $puntosTotales += 1.0;      // Victoria con negras
                    elseif ($p->resultado === 3) $puntosTotales += 0.5;  // Tablas
                }
            }

            // Actualizar en participantes_torneo
            $participante->puntos = $puntosTotales;
            $participante->save();
            
            // Actualizar también en la tabla participantes si existe
            $participanteAntiguo = Participante::where('torneo_id', $torneoId)
                ->where('miembro_id', $jugadorId)
                ->first();
            
            if ($participanteAntiguo) {
                $participanteAntiguo->puntos = $puntosTotales;
                $participanteAntiguo->save();
            }

            DB::commit();
            
            // Forzar la recarga del modelo para asegurar que los cambios sean visibles
            $participante->refresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar puntos del jugador: ' . $e->getMessage());
            throw $e;
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
                            $oponente = ParticipanteTorneo::where('torneo_id', $torneo->id)
                                ->where('miembro_id', $partida->jugador_negras_id)
                                ->first();
                            if ($oponente) {
                                $buchholz += $oponente->puntos;
                            }
                        }
                    } elseif ($partida->jugador_negras_id === $participante->miembro_id) {
                        $oponente = ParticipanteTorneo::where('torneo_id', $torneo->id)
                            ->where('miembro_id', $partida->jugador_blancas_id)
                            ->first();
                        if ($oponente) {
                            $buchholz += $oponente->puntos;
                        }
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
                            $oponente = ParticipanteTorneo::where('torneo_id', $torneo->id)
                                ->where('miembro_id', $partida->jugador_negras_id)
                                ->first();
                            if ($oponente) {
                                if ($partida->resultado === 1) { // Victoria
                                    $sonnebornBerger += $oponente->puntos;
                                } elseif ($partida->resultado === 3) { // Tablas
                                    $sonnebornBerger += $oponente->puntos / 2;
                                }
                            }
                        }
                    } elseif ($partida->jugador_negras_id === $participante->miembro_id) {
                        $oponente = ParticipanteTorneo::where('torneo_id', $torneo->id)
                            ->where('miembro_id', $partida->jugador_blancas_id)
                            ->first();
                        if ($oponente) {
                            if ($partida->resultado === 2) { // Victoria
                                $sonnebornBerger += $oponente->puntos;
                            } elseif ($partida->resultado === 3) { // Tablas
                                $sonnebornBerger += $oponente->puntos / 2;
                            }
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