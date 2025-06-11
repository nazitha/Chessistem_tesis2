<?php

namespace App\Http\Controllers;
use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\PartidaTorneo;
use App\Services\SwissPairingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EquipoMatch;
use App\Models\PartidaIndividual;


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
            $emparejamientos = $service->generarEmparejamientos($ronda);

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

    private function generarEmparejamientosIndividuales(Torneo $torneo, RondaTorneo $ronda)
    {
        $service = new SwissPairingService($torneo);
        $emparejamientos = $service->generarEmparejamientos($ronda);

        foreach ($emparejamientos as $index => $emparejamiento) {
            if (is_null($emparejamiento['negras'])) {
                // BYE: jugador blancas descansa
                PartidaTorneo::create([
                    'ronda_id' => $ronda->id,
                    'jugador_blancas_id' => $emparejamiento['blancas']->miembro_id,
                    'jugador_negras_id' => null,
                    'resultado' => 1, // Victoria por bye
                    'mesa' => 0 // Mesa especial para bye
                ]);
            } else {
                PartidaTorneo::create([
                    'ronda_id' => $ronda->id,
                    'jugador_blancas_id' => $emparejamiento['blancas']->miembro_id,
                    'jugador_negras_id' => $emparejamiento['negras']->miembro_id,
                    'mesa' => $index + 1
                ]);
            }
        }
    }

    private function generarEmparejamientosEquipos(Torneo $torneo, RondaTorneo $ronda)
    {

        // Validación para evitar duplicados
        if (\App\Models\EquipoMatch::where('torneo_id', $torneo->id)->where('ronda', $ronda->numero_ronda)->exists()) {
            // Ya existen matches para esta ronda, no crear de nuevo
            return;
        }
        $service = new \App\Services\TeamPairingService($torneo);
        $emparejamientos = $service->generarEmparejamientos($ronda->numero_ronda);
        // Si el servicio devuelve advertencias, pásalas a la sesión
        if (property_exists($service, 'warnings') && !empty($service->warnings)) {
            session()->flash('warnings', $service->warnings);
        }

        foreach ($emparejamientos as $index => $emparejamiento) {
            $match = EquipoMatch::create([
                'torneo_id' => $torneo->id,
                'ronda' => $ronda->numero_ronda,
                'equipo_a_id' => $emparejamiento['equipo_a']->id,
                'equipo_b_id' => $emparejamiento['equipo_b']->id,
                'mesa' => $index + 1
            ]);

            foreach ($emparejamiento['tableros'] as $tablero) {
                PartidaIndividual::create([
                    'equipo_match_id' => $match->id,
                    'jugador_a_id' => $tablero['blancas']->miembro_id,
                    'jugador_b_id' => $tablero['negras']->miembro_id,
                    'tablero' => $tablero['tablero']
                ]);
            }

            $puntajeA = 0;
            $puntajeB = 0;
            foreach ($match->partidas as $partida) {
                if ($partida->match->equipo_a_id === $match->equipo_a_id) {
                    $puntajeA += $partida->resultado ?? 0;
                } elseif ($partida->match->equipo_b_id === $match->equipo_a_id) {
                    $puntajeA += $partida->resultado !== null ? 1 - $partida->resultado : 0;
                }
                if ($partida->match->equipo_b_id === $match->equipo_b_id) {
                    $puntajeB += $partida->resultado ?? 0;
                } elseif ($partida->match->equipo_a_id === $match->equipo_b_id) {
                    $puntajeB += $partida->resultado !== null ? 1 - $partida->resultado : 0;
                }
            }

            $match->puntos_equipo_a = $puntajeA;
            $match->puntos_equipo_b = $puntajeB;
            // Asignar resultado_match
            \Log::info("Calculando resultado_match para match {$match->id}: puntos A = {$puntajeA}, puntos B = {$puntajeB}");
            if ($puntajeA > $puntajeB) {
                $match->resultado_match = 1; // Gana equipo A
            } elseif ($puntajeB > $puntajeA) {
                $match->resultado_match = 2; // Gana equipo B
            } elseif ($puntajeA == $puntajeB && $match->equipo_b_id !== null) {
                $match->resultado_match = 0; // Empate (si no es BYE)
            } else {
                $match->resultado_match = null; // BYE o sin resultado
            }
            \Log::info("Match {$match->id} resultado_match: " . var_export($match->resultado_match, true));
            $match->save();
        }
    }

    public function registrarResultado(Request $request, PartidaTorneo $partida)
    {
        try {
            DB::beginTransaction();

            $partida->update([
                'resultado' => $request->resultado
            ]);

            // Actualizar puntajes de los participantes
            $this->actualizarPuntajes($partida);

            DB::commit();

            return back()->with('success', 'Resultado registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar resultado: ' . $e->getMessage());
            return back()->with('error', 'Error al registrar el resultado.');
        }
    }

    private function actualizarPuntajes(PartidaTorneo $partida)
    {
        $blancas = $partida->jugadorBlancas;
        $negras = $partida->jugadorNegras;

        if ($partida->resultado === 1) { // Victoria blancas
            $blancas->increment('puntos', 1);
        } elseif ($partida->resultado === 2) { // Victoria negras
            $negras->increment('puntos', 1);
        } else { // Empate
            $blancas->increment('puntos', 0.5);
            $negras->increment('puntos', 0.5);
        }
    }

    public function guardarResultadosRonda(Request $request, RondaTorneo $ronda)
    {
        \Log::info('INICIO guardarResultadosRonda', ['ronda_id' => $ronda->id, 'request' => $request->all()]);
        // Forzar decodificación si es string
        if (isset($request->resultados) && is_string($request->resultados)) {
            $array = json_decode($request->resultados, true);
            \Log::info('Intentando decodificar resultados', ['original' => $request->resultados, 'decodificado' => $array]);
            if (is_array($array)) {
                $request->merge(['resultados' => $array]);
            } else {
                return redirect()->back()->with('error', 'No se pudieron decodificar los resultados enviados.');
            }
        }
        \Log::info('Tipo de resultados después de merge', ['tipo' => gettype($request->resultados), 'contenido' => $request->resultados]);
        try {
            $request->validate([
                'resultados' => 'required|array',
                'resultados.*' => 'present|string'
            ]);

            DB::beginTransaction();

            $torneo = $ronda->torneo;
            if ($torneo->es_por_equipos) {
                // Guardar resultados en PartidaIndividual y actualizar puntos de equipos
                $matches = \App\Models\EquipoMatch::with('partidas')->where('torneo_id', $torneo->id)->where('ronda', $ronda->numero_ronda)->get();
                $idsEsperados = [];
                foreach ($matches as $match) {
                    foreach ($match->partidas as $partida) {
                        $idsEsperados[] = $partida->id;
                    }
                }
                \Log::info('IDs esperados por el backend', $idsEsperados);
                \Log::info('IDs recibidos en resultados', array_keys($request->resultados));
                foreach ($matches as $match) {
                    $puntajeA = 0;
                    $puntajeB = 0;
                    foreach ($match->partidas as $partida) {
                        if (!isset($request->resultados[$partida->id])) continue;
                        $texto = trim($request->resultados[$partida->id]);
                        // Normalizar empate
                        $texto_normalizado = strtolower(str_replace([',', ' '], ['.', ''], $texto));
                        if (preg_match('/^(0\.5-0\.5|0\.5|1\/2-1\/2|1\/2|½-½|½)$/u', $texto_normalizado)) {
                            $texto_normalizado = '0.5';
                        }
                        \Log::info('Procesando resultado para partida ID ' . $partida->id . ': input="' . $texto . '", normalizado="' . $texto_normalizado . '"');
                        // Procesar resultado
                        switch ($texto_normalizado) {
                            case '1-0':
                            case '1':
                                $partida->resultado = 1;
                                $puntajeA += 1;
                                break;
                            case '0-1':
                            case '0':
                                $partida->resultado = 0;
                                $puntajeB += 1;
                                break;
                            case '0.5':
                                $partida->resultado = 0.5;
                                $puntajeA += 0.5;
                                $puntajeB += 0.5;
                                break;
                            default:
                                $partida->resultado = null;
                        }
                        \Log::info('Resultado guardado en partida ID ' . $partida->id . ': resultado=' . var_export($partida->resultado, true));
                        $partida->save();
                    }
                }
                // Recalcular puntos y resultado_match para todos los matches de la ronda
                foreach ($matches as $match) {
                    $puntajeA = 0;
                    $puntajeB = 0;
                    foreach ($match->partidas as $partida) {
                        if ($partida->jugador_a_id && $partida->jugador_b_id) {
                            if ($partida->resultado === 1) {
                                $puntajeA += 1;
                            } elseif ($partida->resultado === 0) {
                                $puntajeB += 1;
                            } elseif ($partida->resultado === 0.5) {
                                $puntajeA += 0.5;
                                $puntajeB += 0.5;
                            }
                        }
                    }
                    $match->puntos_equipo_a = $puntajeA;
                    $match->puntos_equipo_b = $puntajeB;
                    // Asignar resultado_match
                    if ($puntajeA > $puntajeB) {
                        $match->resultado_match = 1; // Gana equipo A
                    } elseif ($puntajeB > $puntajeA) {
                        $match->resultado_match = 2; // Gana equipo B
                    } elseif ($puntajeA == $puntajeB && $match->equipo_b_id !== null) {
                        $match->resultado_match = 0; // Empate (si no es BYE)
                    } else {
                        $match->resultado_match = null; // BYE o sin resultado
                    }
                    \Log::info("[RECALCULO] Match {$match->id} resultado_match: " . var_export($match->resultado_match, true));
                    $match->save();
                }
                $ronda->completada = true;
                $ronda->save();
                DB::commit();
                return redirect()->route('torneos.rondas.show', [$torneo, $ronda])->with('success', 'Resultados guardados exitosamente.');
            }

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

                    // Verificar si es la última ronda
                    if ($ronda->numero_ronda === $torneo->no_rondas) {
                        DB::commit();
                        return redirect()
                            ->route('torneos.show', $torneo)
                            ->with('success', '¡Torneo completado! Se muestra la clasificación final.');
                    }

                    // Generar la siguiente ronda
                    $siguienteRonda = RondaTorneo::create([
                        'torneo_id' => $torneo->id,
                        'numero_ronda' => $ronda->numero_ronda + 1,
                        'fecha_hora' => now()
                    ]);

                    if ($torneo->es_por_equipos) {
                        $this->generarEmparejamientosEquipos($torneo, $siguienteRonda);
                    } else {
                        $this->generarEmparejamientosIndividuales($torneo, $siguienteRonda);
                    }
                }

                DB::commit();
                Log::info('=== Transacción completada exitosamente ===');

                // Redirigir a la siguiente ronda si existe
                if (isset($siguienteRonda)) {
                    return redirect()
                        ->route('torneos.rondas.show', [$torneo, $siguienteRonda])
                        ->with('success', 'Resultados guardados y siguiente ronda generada exitosamente.');
                }


                // Recalcular criterios de desempate de equipos si aplica
                if ($torneo->es_por_equipos && ($torneo->usar_buchholz || $torneo->usar_sonneborn_berger || $torneo->usar_desempate_progresivo)) {
                    // Forzar recálculo de la tabla de clasificación (show) al recargar
                }
              return redirect ()
                    ->route('torneos.rondas.show', [$torneo, $ronda])
                    ->with('success', 'Resultados guardados exitosamente.');

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
            $partidas = PartidaTorneo::where('jugador_blancas_id', $jugadorId)
                ->orWhere('jugador_negras_id', $jugadorId)
                ->whereHas('ronda', function($query) use ($torneoId) {
                    $query->where('torneo_id', $torneoId);
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

    /**
     * Muestra una ronda individual con el detalle del torneo y navegación entre rondas.
     */
    public function show(Torneo $torneo, RondaTorneo $ronda)
    {
        // Obtener todas las rondas para navegación
        $rondas = $torneo->rondas()->orderBy('numero_ronda')->get();
        // Participantes y partidas de la ronda
        $partidas = $ronda->partidas()->with(['jugadorBlancas.elo', 'jugadorNegras.elo'])->get();
        // Para la tabla de clasificación
        $participantes = $torneo->participantes()->with(['miembro.elo', 'miembro.fide'])->orderBy('numero_inicial')->get();
        // Si es por equipos, cargar equipos con sus puntos acumulados
        if ($torneo->es_por_equipos) {
            $equipos = $torneo->equipos()->with(['jugadores.miembro.elo'])->get();
            $puntosTotalesEquipos = [];
            // PRIMER BUCLE: Calcular puntos de ronda y totales
            foreach ($equipos as $equipo) {
                $puntos_ronda = 0;
                $puntos_totales = 0;
                // Sumar puntos de tableros de la ronda actual
                $partidas_ronda = \App\Models\PartidaIndividual::whereHas('match', function($q) use ($equipo, $ronda) {
                    $q->where(function($q2) use ($equipo) {
                        $q2->where('equipo_a_id', $equipo->id)->orWhere('equipo_b_id', $equipo->id);
                    })->where('ronda', $ronda->numero_ronda);
                })->get();
                foreach ($partidas_ronda as $partida) {
                    if ($partida->match->equipo_a_id === $equipo->id) {
                        $puntos_ronda += $partida->resultado ?? 0;
                    } elseif ($partida->match->equipo_b_id === $equipo->id) {
                        $puntos_ronda += $partida->resultado !== null ? 1 - $partida->resultado : 0;
                    }
                }
                // Sumar puntos de tableros de todas las rondas
                $partidas_totales = \App\Models\PartidaIndividual::whereHas('match', function($q) use ($equipo) {
                    $q->where('equipo_a_id', $equipo->id)->orWhere('equipo_b_id', $equipo->id);
                })->get();
                foreach ($partidas_totales as $partida) {
                    if ($partida->match->equipo_a_id === $equipo->id) {
                        $puntos_totales += $partida->resultado ?? 0;
                    } elseif ($partida->match->equipo_b_id === $equipo->id) {
                        $puntos_totales += $partida->resultado !== null ? 1 - $partida->resultado : 0;
                    }
                }
                $equipo->puntos_ronda = $puntos_ronda;
                $equipo->puntos_totales = $puntos_totales;
                $puntosTotalesEquipos[$equipo->id] = $puntos_totales;
            }
            // SEGUNDO BUCLE: Calcular desempates
            foreach ($equipos as $equipo) {
                $buchholz = 0;
                $sonneborn = 0;
                $progresivo = 0;
                $acumulado = 0;
                $matches_jugados = \App\Models\EquipoMatch::where('torneo_id', $torneo->id)
                    ->where(function($q) use ($equipo) {
                        $q->where('equipo_a_id', $equipo->id)->orWhere('equipo_b_id', $equipo->id);
                    })
                    ->where('ronda', '<=', $ronda->numero_ronda)
                    ->orderBy('ronda')
                    ->get();
                foreach ($matches_jugados as $match) {
                    $esA = $match->equipo_a_id === $equipo->id;
                    $oponente = $esA ? $match->equipoB : $match->equipoA;
                    $puntos_equipo = $esA ? $match->puntos_equipo_a : $match->puntos_equipo_b;
                    $puntos_oponente = $oponente ? ($puntosTotalesEquipos[$oponente->id] ?? 0) : 0;
                    // Buchholz: suma los puntos totales de los rivales enfrentados
                    if ($oponente) {
                        $buchholz += $puntos_oponente;
                    }
                    // Sonneborn-Berger: victoria suma puntos del rival, empate suma la mitad
                    \Log::info("Equipo {$equipo->nombre} - Match {$match->id} resultado_match: " . var_export($match->resultado_match, true) . ", puntos_oponente: {$puntos_oponente}");
                    if ($match->resultado_match !== null && $oponente) {
                        if (($esA && $match->resultado_match == 1) || (!$esA && $match->resultado_match == 2)) {
                            $sonneborn += $puntos_oponente;
                            \Log::info("SB: Victoria. Suma {$puntos_oponente} a {$equipo->nombre}");
                        } elseif ($match->resultado_match == 0) {
                            $sonneborn += $puntos_oponente / 2;
                            \Log::info("SB: Empate. Suma " . ($puntos_oponente/2) . " a {$equipo->nombre}");
                        }
                    }
                    // Progresivo: suma acumulativa de puntos por ronda
                    $acumulado += $puntos_equipo ?? 0;
                    $progresivo += $acumulado;
                }
                \Log::info("Equipo {$equipo->nombre}: Buchholz={$buchholz}, Sonneborn-Berger={$sonneborn}, Progresivo={$progresivo}");
                $equipo->buchholz = $buchholz;
                $equipo->sonneborn = $sonneborn;
                $equipo->progresivo = $progresivo;
            }
            $equipos = $equipos->sortByDesc('puntos_totales');
        } else {
            $equipos = collect(); // O un array vacío para individuales
        }
        return view('torneos.ronda', compact('torneo', 'ronda', 'rondas', 'partidas', 'participantes', 'equipos'));
    }
} 