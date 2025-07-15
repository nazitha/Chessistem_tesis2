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
use App\Models\ParticipanteTorneo;
use App\Models\Participante;


class TorneoRondaController extends Controller
{
    public function store(Request $request, Torneo $torneo)
    {
        if ($torneo->estado === 'Finalizado') {
            return back()->with('error', 'No se pueden generar emparejamientos para un torneo finalizado.');
        }
        try {
            if ($torneo->rondas()->count() >= $torneo->no_rondas) {
                return back()->with('error', 'Ya se han generado todas las rondas del torneo.');
            }

            if ($torneo->participantes()->count() < 2) {
                return back()->with('error', 'Se necesitan al menos 2 participantes para generar emparejamientos.');
            }

            DB::beginTransaction();

            // Detectar sistema de emparejamiento
            $sistema = strtolower(trim($torneo->emparejamiento->sistema ?? 'suizo'));
            $esEquipos = $torneo->es_por_equipos;
            $rondaActual = $torneo->rondas()->count() + 1;

            if (str_contains($sistema, 'suizo')) {
                // Lógica actual suizo
                $ronda = RondaTorneo::create([
                    'torneo_id' => $torneo->id,
                    'numero_ronda' => $rondaActual,
                    'fecha_hora' => now(),
                    'completada' => false
                ]);
                $service = new SwissPairingService($torneo);
                $emparejamientos = $service->generarEmparejamientos($ronda);
                foreach ($emparejamientos as $index => $emparejamiento) {
                    PartidaTorneo::create([
                        'ronda_id' => $ronda->id,
                        'jugador_blancas_id' => $emparejamiento['blancas']->miembro_id,
                        'jugador_negras_id' => $emparejamiento['negras']->miembro_id ?? null,
                        'mesa' => $index + 1,
                        'resultado' => null
                    ]);
                }
            } elseif (str_contains($sistema, 'round robin')) {
                if ($esEquipos) {
                    // Crear la ronda antes de generar los matches
                    $ronda = RondaTorneo::create([
                        'torneo_id' => $torneo->id,
                        'numero_ronda' => $rondaActual,
                        'fecha_hora' => now(),
                        'completada' => false
                    ]);
                    $service = new \App\Services\TeamPairingService($torneo);
                    $service->generarEmparejamientos($rondaActual);
                } else {
                    $this->generarRoundRobinIndividual($torneo);
                }
            } elseif ($torneo->tipo_torneo === 'Eliminación Directa') {
                if ($esEquipos) {
                    $this->generarEliminacionDirectaEquipos($torneo);
                } else {
                    $participantes = $torneo->participantes()->orderBy('numero_inicial')->get();
                    $ids = $participantes->pluck('miembro_id')->toArray(); // CORREGIDO: usar miembro_id
                    $this->generarEliminacionDirectaIndividual($torneo, $ids, 1);
                }
            } else {
                DB::rollBack();
                return back()->with('error', 'Sistema de emparejamiento no soportado.');
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

    // --- STUBS PARA SISTEMAS DE EMPAREJAMIENTO ---
    private function generarRoundRobinIndividual(Torneo $torneo)
    {
        // Obtener participantes ordenados por numero_inicial o id
        $participantes = $torneo->participantes()->orderBy('numero_inicial')->get();
        $jugadores = $participantes->pluck('miembro_id')->toArray();
        $n = count($jugadores);
        $tieneBye = false;
        if ($n % 2 !== 0) {
            $jugadores[] = 'BYE';
            $n++;
            $tieneBye = true;
        }
        $rondas = $n - 1;
        $mitad = $n / 2;
        // Generar todas las rondas
        for ($r = 1; $r <= $rondas; $r++) {
            // Crear la ronda en la base de datos
            $ronda = RondaTorneo::create([
                'torneo_id' => $torneo->id,
                'numero_ronda' => $r,
                'fecha_hora' => now(),
                'completada' => false
            ]);
            // Generar partidas para la ronda
            for ($i = 0; $i < $mitad; $i++) {
                $jugador1 = $jugadores[$i];
                $jugador2 = $jugadores[$n - 1 - $i];
                if ($jugador1 !== 'BYE' && $jugador2 !== 'BYE') {
                    PartidaTorneo::create([
                        'ronda_id' => $ronda->id,
                        'jugador_blancas_id' => $jugador1,
                        'jugador_negras_id' => $jugador2,
                        'mesa' => $i + 1,
                        'resultado' => null
                    ]);
                }
            }
            // Rotar jugadores, manteniendo fijo el primero
            $fijo = array_shift($jugadores);
            $ultimo = array_pop($jugadores);
            array_unshift($jugadores, $fijo);
            array_splice($jugadores, 1, 0, $ultimo);
        }
    }

    private function generarRoundRobinEquipos(Torneo $torneo)
    {
        // Obtener equipos ordenados por id
        $equipos = $torneo->equipos()->orderBy('id')->get();
        $equiposArray = $equipos->pluck('id')->toArray();
        $n = count($equiposArray);
        $tieneBye = false;
        
        if ($n % 2 !== 0) {
            $equiposArray[] = 'BYE';
            $n++;
            $tieneBye = true;
        }
        
        $rondas = $n - 1;
        $mitad = $n / 2;
        
        // Generar todas las rondas
        for ($r = 1; $r <= $rondas; $r++) {
            // Crear la ronda en la base de datos
            $ronda = RondaTorneo::create([
                'torneo_id' => $torneo->id,
                'numero_ronda' => $r,
                'fecha_hora' => now(),
                'completada' => false
            ]);
            
            // Generar enfrentamientos para la ronda
            for ($i = 0; $i < $mitad; $i++) {
                $equipo1 = $equiposArray[$i];
                $equipo2 = $equiposArray[$n - 1 - $i];
                
                if ($equipo1 !== 'BYE' && $equipo2 !== 'BYE') {
                    // Crear el enfrentamiento entre equipos
                    $match = EquipoMatch::create([
                        'torneo_id' => $torneo->id,
                        'ronda' => $r,
                        'equipo_a_id' => $equipo1,
                        'equipo_b_id' => $equipo2,
                        'mesa' => $i + 1
                    ]);
                    
                    // Obtener los equipos reales para acceder a sus jugadores
                    $equipoA = $equipos->find($equipo1);
                    $equipoB = $equipos->find($equipo2);
                    
                    // Obtener jugadores ordenados por tablero
                    $jugadoresA = $equipoA->jugadores()->orderBy('tablero')->get();
                    $jugadoresB = $equipoB->jugadores()->orderBy('tablero')->get();
                    
                    // Crear partidas individuales con colores alternos por tablero
                    $numTableros = min($jugadoresA->count(), $jugadoresB->count());
                    
                    for ($t = 0; $t < $numTableros; $t++) {
                        $tableroNum = $t + 1;
                        $esTableroImpar = $tableroNum % 2 === 1;
                        $esRondaPar = $r % 2 === 0;
                        
                        // Determinar colores según tablero y ronda
                        if ($esRondaPar) {
                            $blancas = $esTableroImpar ? $jugadoresA[$t] : $jugadoresB[$t];
                            $negras = $esTableroImpar ? $jugadoresB[$t] : $jugadoresA[$t];
                        } else {
                            $blancas = $esTableroImpar ? $jugadoresB[$t] : $jugadoresA[$t];
                            $negras = $esTableroImpar ? $jugadoresA[$t] : $jugadoresB[$t];
                        }
                        
                        PartidaIndividual::create([
                            'equipo_match_id' => $match->id,
                            'jugador_a_id' => $blancas->miembro_id,
                            'jugador_b_id' => $negras->miembro_id,
                            'tablero' => $tableroNum
                        ]);
                    }
                } elseif ($equipo1 === 'BYE' && $equipo2 !== 'BYE') {
                    // BYE para equipo2
                    $match = EquipoMatch::create([
                        'torneo_id' => $torneo->id,
                        'ronda' => $r,
                        'equipo_a_id' => null,
                        'equipo_b_id' => $equipo2,
                        'resultado_match' => 2, // Victoria por bye
                        'mesa' => $i + 1
                    ]);
                    
                    // Asignar victorias a todos los jugadores del equipo
                    $equipoB = $equipos->find($equipo2);
                    foreach ($equipoB->jugadores as $jugador) {
                        PartidaIndividual::create([
                            'equipo_match_id' => $match->id,
                            'jugador_a_id' => null,
                            'jugador_b_id' => $jugador->miembro_id,
                            'resultado' => 1, // Victoria por bye
                            'tablero' => $jugador->tablero
                        ]);
                    }
                } elseif ($equipo1 !== 'BYE' && $equipo2 === 'BYE') {
                    // BYE para equipo1
                    $match = EquipoMatch::create([
                        'torneo_id' => $torneo->id,
                        'ronda' => $r,
                        'equipo_a_id' => $equipo1,
                        'equipo_b_id' => null,
                        'resultado_match' => 1, // Victoria por bye
                        'mesa' => $i + 1
                    ]);
                    
                    // Asignar victorias a todos los jugadores del equipo
                    $equipoA = $equipos->find($equipo1);
                    foreach ($equipoA->jugadores as $jugador) {
                        PartidaIndividual::create([
                            'equipo_match_id' => $match->id,
                            'jugador_a_id' => $jugador->miembro_id,
                            'jugador_b_id' => null,
                            'resultado' => 1, // Victoria por bye
                            'tablero' => $jugador->tablero
                        ]);
                    }
                }
            }
            
            // Rotar equipos, manteniendo fijo el primero
            $fijo = array_shift($equiposArray);
            $ultimo = array_pop($equiposArray);
            array_unshift($equiposArray, $fijo);
            array_splice($equiposArray, 1, 0, $ultimo);
        }
    }

    private function generarEliminacionDirectaIndividual(Torneo $torneo, array $participantesIds, int $numeroRonda)
    {
        return DB::transaction(function () use ($torneo, $participantesIds, $numeroRonda) {
            \Log::info("[EliminacionDirecta] Iniciando chequeo de ronda", [
                'torneo_id' => $torneo->id,
                'numero_ronda' => $numeroRonda
            ]);
            $rondaExistente = RondaTorneo::where('torneo_id', $torneo->id)
                ->where('numero_ronda', $numeroRonda)
                ->lockForUpdate()
                ->first();
            if ($rondaExistente) {
                \Log::warning("[EliminacionDirecta] Ya existe la ronda $numeroRonda para el torneo {$torneo->id}, no se crea de nuevo.", [
                    'ronda_id' => $rondaExistente->id
                ]);
                return $rondaExistente;
            }
            \Log::info("[EliminacionDirecta] Creando nueva ronda", [
                'torneo_id' => $torneo->id,
                'numero_ronda' => $numeroRonda
            ]);
            $rondaTorneo = RondaTorneo::create([
                'torneo_id' => $torneo->id,
                'numero_ronda' => $numeroRonda,
                'fecha_hora' => now(),
                'completada' => false
            ]);

            // Si el número de participantes es impar, agregar un BYE (null)
            if (count($participantesIds) % 2 !== 0) {
                $participantesIds[] = null;
            }

            // Emparejar y crear partidas
            for ($i = 0; $i < count($participantesIds); $i += 2) {
                $jugador1 = $participantesIds[$i];
                $jugador2 = $participantesIds[$i + 1] ?? null;
                if ($jugador1 && $jugador2) {
                    // Crear partida normal SIN resultado
                    PartidaTorneo::create([
                        'ronda_id' => $rondaTorneo->id,
                        'jugador_blancas_id' => $jugador1,
                        'jugador_negras_id' => $jugador2,
                        'mesa' => ($i / 2) + 1,
                        'resultado' => null
                    ]);
                } elseif ($jugador1 && !$jugador2) {
                    // BYE para jugador1
                    PartidaTorneo::create([
                        'ronda_id' => $rondaTorneo->id,
                        'jugador_blancas_id' => $jugador1,
                        'jugador_negras_id' => null,
                        'resultado' => 1, // Victoria por bye
                        'mesa' => ($i / 2) + 1,
                        'resultado' => null
                    ]);
                } elseif (!$jugador1 && $jugador2) {
                    // BYE para jugador2
                    PartidaTorneo::create([
                        'ronda_id' => $rondaTorneo->id,
                        'jugador_blancas_id' => $jugador2,
                        'jugador_negras_id' => null,
                        'resultado' => 1, // Victoria por bye
                        'mesa' => ($i / 2) + 1,
                        'resultado' => null
                    ]);
                }
            }
            \Log::info('[EliminacionDirecta] FIN generarEliminacionDirectaIndividual', [
                'ronda_id' => $rondaTorneo->id,
                'partidas_creadas' => PartidaTorneo::where('ronda_id', $rondaTorneo->id)->count()
            ]);
            return $rondaTorneo;
        });
    }

    private function generarEliminacionDirectaEquipos(Torneo $torneo)
    {
        // TODO: Implementar lógica eliminación directa equipos
    }

    private function generarEmparejamientosIndividuales(Torneo $torneo, RondaTorneo $ronda)
    {
        $service = new SwissPairingService($torneo);
        $emparejamientos = $service->generarEmparejamientos($ronda);

        foreach ($emparejamientos as $index => $emparejamiento) {
            if (is_null($emparejamiento['negras'])) {
                // Verificar si ya existe una partida BYE para este jugador y ronda
                $existeBye = PartidaTorneo::where('ronda_id', $ronda->id)
                    ->where('jugador_blancas_id', $emparejamiento['blancas']->miembro_id)
                    ->whereNull('jugador_negras_id')
                    ->exists();
                if (!$existeBye) {
                    PartidaTorneo::create([
                        'ronda_id' => $ronda->id,
                        'jugador_blancas_id' => $emparejamiento['blancas']->miembro_id,
                        'jugador_negras_id' => null,
                        'resultado' => 1, // Victoria por bye
                        'mesa' => 0, // Mesa especial para bye
                        'resultado' => null
                    ]);
                }
            } else {
                // Verificar si ya existe la partida entre estos dos jugadores en esta ronda
                $existePartida = PartidaTorneo::where('ronda_id', $ronda->id)
                    ->where('jugador_blancas_id', $emparejamiento['blancas']->miembro_id)
                    ->where('jugador_negras_id', $emparejamiento['negras']->miembro_id)
                    ->exists();
                if (!$existePartida) {
                    PartidaTorneo::create([
                        'ronda_id' => $ronda->id,
                        'jugador_blancas_id' => $emparejamiento['blancas']->miembro_id,
                        'jugador_negras_id' => $emparejamiento['negras']->miembro_id,
                        'mesa' => $index + 1,
                        'resultado' => null
                    ]);
                }
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
                    $esEliminacionDirecta = $torneo->tipo_torneo === 'Eliminación Directa' && !$torneo->es_por_equipos;
                    $partida->setResultadoFromTexto($resultado, $esEliminacionDirecta);
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

                Log::info('Verificando si la ronda está completa', [
                    'ronda_id' => $ronda->id,
                    'partidasSinResultado' => $partidasSinResultado
                ]);
                if ($partidasSinResultado === 0) {
                    Log::info('Entrando a bloque de generación de siguiente ronda', [
                        'tipo_torneo' => $torneo->tipo_torneo,
                        'es_por_equipos' => $torneo->es_por_equipos
                    ]);
                    $ronda->completada = true;
                    $ronda->save();
                    Log::info('Ronda marcada como completada');

                    $torneo = $ronda->torneo;
                    $siguienteRonda = null; // Inicializar para evitar variable indefinida
                    if ($torneo->tipo_torneo === 'Eliminación Directa' && !$torneo->es_por_equipos) {
                        // Limitar el número de rondas generadas
                        if ($ronda->numero_ronda >= $torneo->no_rondas) {
                            // --- FIX: Marcar la ronda como completada y guardar antes de salir ---
                            $ronda->completada = true;
                            $ronda->save();
                            DB::commit();
                            // No generar más rondas, redirigir al detalle del torneo
                            return redirect()->route('torneos.show', $torneo)
                                ->with('success', '¡Torneo finalizado! Se muestra la clasificación final.');
                        }
                        // Obtener ganadores de la ronda actual
                        $ganadores = [];
                        foreach ($ronda->partidas as $partida) {
                            if ($partida->resultado === null) continue;
                            if ($partida->jugador_blancas_id && $partida->jugador_negras_id) {
                                // Victoria blancas
                                if ($partida->resultado == 1) {
                                    $ganadores[] = $partida->jugador_blancas_id;
                                } elseif ($partida->resultado == 0) {
                                    $ganadores[] = $partida->jugador_negras_id;
                                }
                            } elseif ($partida->jugador_blancas_id && !$partida->jugador_negras_id) {
                                // BYE
                                $ganadores[] = $partida->jugador_blancas_id;
                            } elseif ($partida->jugador_negras_id && !$partida->jugador_blancas_id) {
                                // BYE
                                $ganadores[] = $partida->jugador_negras_id;
                            }
                        }
                        Log::info('Intentando generar siguiente ronda de eliminación directa', [
                            'ronda_actual' => $ronda->numero_ronda,
                            'siguiente_numero_ronda' => $ronda->numero_ronda + 1,
                            'ganadores' => $ganadores,
                            'torneo_id' => $torneo->id
                        ]);
                        
                        // Validar que hay ganadores para generar la siguiente ronda
                        if (empty($ganadores)) {
                            Log::warning('No hay ganadores para generar la siguiente ronda de eliminación directa', [
                                'torneo_id' => $torneo->id,
                                'ronda_actual' => $ronda->numero_ronda
                            ]);
                            $siguienteRonda = null;
                        } else {
                            // Llamada a la función de generación SOLO si no se ha superado el límite de rondas
                            $resultadoGeneracion = $this->generarEliminacionDirectaIndividual($torneo, $ganadores, $ronda->numero_ronda + 1);
                            Log::info('Resultado de generarEliminacionDirectaIndividual', [
                                'resultado' => $resultadoGeneracion,
                                'torneo_id' => $torneo->id,
                                'numero_ronda' => $ronda->numero_ronda + 1
                            ]);
                            
                            // Obtener la siguiente ronda (ya sea que se haya creado o ya existiera)
                            $siguienteRonda = RondaTorneo::where('torneo_id', $torneo->id)
                                ->where('numero_ronda', $ronda->numero_ronda + 1)
                                ->first();
                                
                            if (!$siguienteRonda) {
                                Log::warning('No se pudo obtener la siguiente ronda después de generar eliminación directa', [
                                    'torneo_id' => $torneo->id,
                                    'numero_ronda' => $ronda->numero_ronda + 1
                                ]);
                            }
                        }
                    } else {
                        // Para Suizo, Round Robin, etc.
                        $siguienteRonda = RondaTorneo::where('torneo_id', $torneo->id)
                            ->where('numero_ronda', $ronda->numero_ronda + 1)
                            ->first();
                        if (!$siguienteRonda && $ronda->numero_ronda < $torneo->no_rondas) {
                            // Si no existe la siguiente ronda y no es la última, generarla automáticamente
                            if ($torneo->es_por_equipos) {
                                $this->generarEmparejamientosEquipos($torneo, RondaTorneo::create([
                                    'torneo_id' => $torneo->id,
                                    'numero_ronda' => $ronda->numero_ronda + 1,
                                    'fecha_hora' => now(),
                                    'completada' => false
                                ]));
                            } else {
                                $nuevaRonda = RondaTorneo::create([
                                    'torneo_id' => $torneo->id,
                                    'numero_ronda' => $ronda->numero_ronda + 1,
                                    'fecha_hora' => now(),
                                    'completada' => false
                                ]);
                                $this->generarEmparejamientosIndividuales($torneo, $nuevaRonda);
                                $siguienteRonda = $nuevaRonda;
                            }
                        }
                        if (!$siguienteRonda) {
                            $siguienteRonda = RondaTorneo::where('torneo_id', $torneo->id)
                                ->where('numero_ronda', $ronda->numero_ronda + 1)
                                ->first();
                        }
                    }
                }

                DB::commit();
                Log::info('=== Transacción completada exitosamente ===');

                // Redirigir a la siguiente ronda si existe
                if (isset($siguienteRonda) && $siguienteRonda) {
                    return redirect()
                        ->route('torneos.rondas.show', [$torneo, $siguienteRonda])
                        ->with('success', 'Resultados guardados y siguiente ronda generada exitosamente.');
                } else if ($torneo->rondas()->count() < $torneo->no_rondas) {
                    // Si no existe la siguiente ronda pero aún faltan rondas, redirigir a la ronda actual y mostrar botón para generarla
                    return redirect()
                        ->route('torneos.rondas.show', [$torneo, $ronda])
                        ->with('info', 'Resultados guardados. Puedes generar la siguiente ronda.');
                } else {
                    return redirect()
                        ->route('torneos.show', $torneo)
                        ->with('success', '¡Torneo finalizado! Se muestra la clasificación final.');
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

            // Obtener todas las partidas válidas del jugador en este torneo (solo de rondas existentes y jugadas)
            $partidas = PartidaTorneo::where(function($q) use ($jugadorId) {
                    $q->where('jugador_blancas_id', $jugadorId)
                      ->orWhere('jugador_negras_id', $jugadorId);
                })
                ->whereHas('ronda', function($query) use ($torneoId) {
                    $query->where('torneo_id', $torneoId)
                          ->where(function($q2) {
                              $q2->where('completada', true)
                                 ->orWhereColumn('rondas_torneo.id', '=', 'partidas_torneo.ronda_id'); // Permite sumar la ronda actual si está en juego
                          });
                })
                ->get();

            $puntosTotales = 0;
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
                    if ($p->resultado === 2 || $p->resultado === 0) $puntosTotales += 1.0;      // Victoria con negras (ambos sistemas)
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
        $matches = collect();
        // Si es por equipos, cargar equipos con sus puntos acumulados y los matches de la ronda
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
            // Cargar los matches de la ronda actual
            $matches = \App\Models\EquipoMatch::with(['equipoA.jugadores.miembro.elo', 'equipoB.jugadores.miembro.elo', 'partidas.jugadorA.elo', 'partidas.jugadorB.elo'])
                ->where('torneo_id', $torneo->id)
                ->where('ronda', $ronda->numero_ronda)
                ->orderBy('mesa')
                ->get();
        } else {
            $equipos = collect(); // O un array vacío para individuales
        }
        return view('torneos.ronda', compact('torneo', 'ronda', 'rondas', 'partidas', 'participantes', 'equipos', 'matches'));
    }
} 