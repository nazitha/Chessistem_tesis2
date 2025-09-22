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
use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;


class TorneoRondaController extends Controller
{
    public function store(Request $request, Torneo $torneo)
    {
        Log::info("[GenerarRonda] Iniciando generación de ronda", [
            'torneo_id' => $torneo->id,
            'torneo_nombre' => $torneo->nombre_torneo,
            'tipo_torneo' => $torneo->tipo_torneo,
            'es_por_equipos' => $torneo->es_por_equipos,
            'estado' => $torneo->estado,
            'rondas_existentes' => $torneo->rondas()->count(),
            'no_rondas' => $torneo->no_rondas,
            'participantes_count' => $torneo->participantes()->count(),
            'equipos_count' => $torneo->es_por_equipos ? $torneo->equipos()->count() : 0
        ]);

        if ($torneo->estado === 'Finalizado') {
            Log::warning("[GenerarRonda] Torneo finalizado, no se puede generar ronda");
            return back()->with('error', 'No se pueden generar emparejamientos para un torneo finalizado.');
        }
        try {
            if ($torneo->rondas()->count() >= $torneo->no_rondas) {
                Log::warning("[GenerarRonda] Ya se han generado todas las rondas del torneo");
                return back()->with('error', 'Ya se han generado todas las rondas del torneo.');
            }

            if ($torneo->es_por_equipos) {
                if ($torneo->equipos()->count() < 2) {
                    Log::warning("[GenerarRonda] No hay suficientes equipos: " . $torneo->equipos()->count());
                    return back()->with('error', 'Se necesitan al menos 2 equipos para generar emparejamientos.');
                }
            } else {
            if ($torneo->participantes()->count() < 2) {
                    Log::warning("[GenerarRonda] No hay suficientes participantes: " . $torneo->participantes()->count());
                return back()->with('error', 'Se necesitan al menos 2 participantes para generar emparejamientos.');
                }
            }

            Log::info("[GenerarRonda] Validaciones pasadas, iniciando generación");

            DB::beginTransaction();

            // Detectar sistema de emparejamiento
            $sistema = strtolower(trim($torneo->emparejamiento->sistema ?? 'suizo'));
            $esEquipos = $torneo->es_por_equipos;
            $rondaActual = $torneo->rondas()->count() + 1;

            if (str_contains($sistema, 'suizo')) {
                Log::info("[GenerarRonda] Procesando sistema suizo", [
                    'es_por_equipos' => $esEquipos,
                    'ronda_actual' => $rondaActual
                ]);
                
                if ($esEquipos) {
                    Log::info("[GenerarRonda] Creando ronda para suizo por equipos");
                    // Crear la ronda antes de generar los emparejamientos
                $ronda = RondaTorneo::create([
                    'torneo_id' => $torneo->id,
                    'numero_ronda' => $rondaActual,
                        'fecha_hora' => now(),
                        'completada' => false
                    ]);
                    Log::info("[GenerarRonda] Ronda creada ID: {$ronda->id}");
                    
                    Log::info("[GenerarRonda] Llamando a generarEmparejamientosEquipos");
                    $this->generarEmparejamientosEquipos($torneo, $ronda);
                    Log::info("[GenerarRonda] generarEmparejamientosEquipos completado");
                } else {
                    Log::info("[GenerarRonda] Procesando suizo individual");
                    // Lógica actual suizo individual
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
                    
                    // Generar emparejamientos Round Robin para equipos
                    $this->generarRoundRobinEquiposRonda($torneo, $rondaActual);
                } else {
                    $this->generarRoundRobinIndividual($torneo, $rondaActual);
                }
            } elseif ($torneo->tipo_torneo === 'Eliminación Directa') {
                Log::info("[GenerarRonda] Procesando eliminación directa", [
                    'es_por_equipos' => $esEquipos,
                    'ronda_actual' => $rondaActual
                ]);
                
                if ($esEquipos) {
                    Log::info("[GenerarRonda] Creando ronda para eliminación directa por equipos");
                    // Crear la ronda antes de generar los emparejamientos
                    $ronda = RondaTorneo::create([
                        'torneo_id' => $torneo->id,
                        'numero_ronda' => $rondaActual,
                        'fecha_hora' => now(),
                        'completada' => false
                    ]);
                    Log::info("[GenerarRonda] Ronda creada ID: {$ronda->id}");
                    
                    Log::info("[GenerarRonda] Llamando a generarEliminacionDirectaEquipos");
                    $this->generarEliminacionDirectaEquipos($torneo);
                    Log::info("[GenerarRonda] generarEliminacionDirectaEquipos completado");
                } else {
                    Log::info("[GenerarRonda] Procesando eliminación directa individual");
                    $participantes = $torneo->participantes()->orderBy('numero_inicial')->get();
                    $ids = $participantes->pluck('miembro_id')->toArray(); // CORREGIDO: usar miembro_id
                    $this->generarEliminacionDirectaIndividual($torneo, $ids, 1);
                }
            } else {
                DB::rollBack();
                return back()->with('error', 'Sistema de emparejamiento no soportado.');
            }

            DB::commit();

            // Registrar auditoría para emparejamiento (como acción de Participantes)
            $mensajeAuditoria = "Emparejamiento realizado - Torneo: {$torneo->nombre_torneo} - Ronda: {$rondaActual}";
            
            $fechaHora = now()->setTimezone('America/Managua');
            Auditoria::create([
                'correo_id' => Auth::user()->correo,
                'tabla_afectada' => 'Participantes',
                'accion' => 'Emparejamiento',
                'valor_previo' => null,
                'valor_posterior' => $mensajeAuditoria,
                'fecha' => $fechaHora->toDateString(),
                'hora' => $fechaHora->toTimeString(),
                'equipo' => request()->ip()
            ]);

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
    private function generarRoundRobinIndividual(Torneo $torneo, $numeroRonda)
    {
        $participantes = $torneo->participantes()->orderBy('numero_inicial')->get();
        $jugadores = $participantes->pluck('miembro_id')->toArray();
        $n = count($jugadores);
        if ($n % 2 !== 0) {
            $jugadores[] = 'BYE';
            $n++;
        }
        $rondas = $n - 1;
        $mitad = $n / 2;

        // Rotar para llegar a la ronda deseada
        for ($r = 1; $r < $numeroRonda; $r++) {
            $fijo = array_shift($jugadores);
            $ultimo = array_pop($jugadores);
            array_unshift($jugadores, $fijo);
            array_splice($jugadores, 1, 0, $ultimo);
        }

        // Revisar si la ronda ya existe
        $ronda = RondaTorneo::where('torneo_id', $torneo->id)
            ->where('numero_ronda', $numeroRonda)
            ->first();
        if (!$ronda) {
            $ronda = RondaTorneo::create([
                'torneo_id' => $torneo->id,
                'numero_ronda' => $numeroRonda,
                'fecha_hora' => now(),
                'completada' => false
            ]);
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
        }
    }

    private function generarRoundRobinEquiposRonda(Torneo $torneo, $numeroRonda)
    {
        Log::info("[RoundRobinEquipos] Generando ronda {$numeroRonda} para torneo {$torneo->id}");
        
        // Obtener equipos ordenados por id
        $equipos = $torneo->equipos()->orderBy('id')->get();
        $equiposArray = $equipos->pluck('id')->toArray();
        $n = count($equiposArray);
        
        if ($n % 2 !== 0) {
            $equiposArray[] = 'BYE';
            $n++;
        }
        
        $mitad = $n / 2;
        
        // Rotar para llegar a la ronda deseada
        for ($r = 1; $r < $numeroRonda; $r++) {
            $fijo = array_shift($equiposArray);
            $ultimo = array_pop($equiposArray);
            array_unshift($equiposArray, $fijo);
            array_splice($equiposArray, 1, 0, $ultimo);
        }
        
        // Verificar si ya existen matches para esta ronda
        $matchesExistentes = EquipoMatch::where('torneo_id', $torneo->id)
            ->where('ronda', $numeroRonda)
            ->count();
        
        if ($matchesExistentes > 0) {
            Log::warning("[RoundRobinEquipos] Ya existen matches para la ronda {$numeroRonda}");
            return;
        }
        
        // Generar enfrentamientos para la ronda
        for ($i = 0; $i < $mitad; $i++) {
            $equipo1 = $equiposArray[$i];
            $equipo2 = $equiposArray[$n - 1 - $i];
            
            if ($equipo1 !== 'BYE' && $equipo2 !== 'BYE') {
                Log::info("[RoundRobinEquipos] Creando match: Equipo1={$equipo1}, Equipo2={$equipo2}");
                
                // Crear el enfrentamiento entre equipos
                $match = EquipoMatch::create([
                    'torneo_id' => $torneo->id,
                    'ronda' => $numeroRonda,
                    'equipo_a_id' => $equipo1,
                    'equipo_b_id' => $equipo2,
                        'mesa' => $i + 1
                    ]);
                
                Log::info("[RoundRobinEquipos] Match creado ID: {$match->id}");
                
                // Obtener los equipos reales para acceder a sus jugadores
                $equipoA = $equipos->find($equipo1);
                $equipoB = $equipos->find($equipo2);
                
                // Obtener jugadores ordenados por tablero
                $jugadoresA = $equipoA->jugadores()->orderBy('tablero')->get();
                $jugadoresB = $equipoB->jugadores()->orderBy('tablero')->get();
                
                Log::info("[RoundRobinEquipos] Jugadores equipo A ({$equipoA->nombre}): " . $jugadoresA->count() . ", Jugadores equipo B ({$equipoB->nombre}): " . $jugadoresB->count());
                
                // Crear partidas individuales con colores alternos por tablero
                $numTableros = min($jugadoresA->count(), $jugadoresB->count());
                
                for ($t = 0; $t < $numTableros; $t++) {
                    $tableroNum = $t + 1;
                    $esTableroImpar = $tableroNum % 2 === 1;
                    $esRondaPar = $numeroRonda % 2 === 0;
                    
                    // Determinar colores según tablero y ronda
                    if ($esRondaPar) {
                        $blancas = $esTableroImpar ? $jugadoresA[$t] : $jugadoresB[$t];
                        $negras = $esTableroImpar ? $jugadoresB[$t] : $jugadoresA[$t];
                    } else {
                        $blancas = $esTableroImpar ? $jugadoresB[$t] : $jugadoresA[$t];
                        $negras = $esTableroImpar ? $jugadoresA[$t] : $jugadoresB[$t];
                    }
                    
                    $partida = PartidaIndividual::create([
                        'equipo_match_id' => $match->id,
                        'jugador_a_id' => $blancas->miembro_id,
                        'jugador_b_id' => $negras->miembro_id,
                        'tablero' => $tableroNum
                    ]);
                    
                    Log::info("[RoundRobinEquipos] Partida creada ID: {$partida->id}", [
                        'tablero' => $tableroNum,
                        'blancas' => $blancas->miembro_id,
                        'negras' => $negras->miembro_id
                    ]);
                }
            } elseif ($equipo1 === 'BYE' && $equipo2 !== 'BYE') {
                // BYE para equipo2
                Log::info("[RoundRobinEquipos] BYE para equipo {$equipo2}");
                
                $match = EquipoMatch::create([
                    'torneo_id' => $torneo->id,
                    'ronda' => $numeroRonda,
                    'equipo_a_id' => null,
                    'equipo_b_id' => $equipo2,
                    'resultado_match' => 2, // Victoria por bye
                    'mesa' => $i + 1
                ]);
                
                // Asignar victorias a todos los jugadores del equipo
                $equipoB = $equipos->find($equipo2);
                $jugadoresB = $equipoB->jugadores()->orderBy('tablero')->get();
                
                foreach ($jugadoresB as $jugador) {
                    PartidaIndividual::create([
                        'equipo_match_id' => $match->id,
                        'jugador_a_id' => null,
                        'jugador_b_id' => $jugador->miembro_id,
                        'resultado' => 0, // Victoria para el jugador del equipo
                        'tablero' => $jugador->tablero
                    ]);
                }
            } elseif ($equipo1 !== 'BYE' && $equipo2 === 'BYE') {
                // BYE para equipo1
                Log::info("[RoundRobinEquipos] BYE para equipo {$equipo1}");
                
                $match = EquipoMatch::create([
                    'torneo_id' => $torneo->id,
                    'ronda' => $numeroRonda,
                    'equipo_a_id' => $equipo1,
                    'equipo_b_id' => null,
                    'resultado_match' => 1, // Victoria por bye
                    'mesa' => $i + 1
                ]);
                
                // Asignar victorias a todos los jugadores del equipo
                $equipoA = $equipos->find($equipo1);
                $jugadoresA = $equipoA->jugadores()->orderBy('tablero')->get();
                
                foreach ($jugadoresA as $jugador) {
                    PartidaIndividual::create([
                        'equipo_match_id' => $match->id,
                        'jugador_a_id' => $jugador->miembro_id,
                        'jugador_b_id' => null,
                        'resultado' => 1, // Victoria para el jugador del equipo
                        'tablero' => $jugador->tablero
                    ]);
                }
            }
        }
        
        Log::info("[RoundRobinEquipos] Ronda {$numeroRonda} completada");
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
            Log::info("[EliminacionDirecta] Iniciando chequeo de ronda", [
                'torneo_id' => $torneo->id,
                'numero_ronda' => $numeroRonda
            ]);
            $rondaExistente = RondaTorneo::where('torneo_id', $torneo->id)
                ->where('numero_ronda', $numeroRonda)
                ->lockForUpdate()
                ->first();
            if ($rondaExistente) {
                Log::warning("[EliminacionDirecta] Ya existe la ronda $numeroRonda para el torneo {$torneo->id}, no se crea de nuevo.", [
                    'ronda_id' => $rondaExistente->id
                ]);
                return $rondaExistente;
            }
            Log::info("[EliminacionDirecta] Creando nueva ronda", [
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
            Log::info('[EliminacionDirecta] FIN generarEliminacionDirectaIndividual', [
                'ronda_id' => $rondaTorneo->id,
                'partidas_creadas' => PartidaTorneo::where('ronda_id', $rondaTorneo->id)->count()
            ]);
            return $rondaTorneo;
        });
    }

    private function generarEliminacionDirectaEquipos(Torneo $torneo)
    {
        return DB::transaction(function () use ($torneo) {
            Log::info("[EliminacionDirectaEquipos] Iniciando generación de eliminación directa por equipos", [
                'torneo_id' => $torneo->id,
                'torneo_nombre' => $torneo->nombre_torneo
            ]);

            // Obtener todos los equipos del torneo
            $equipos = $torneo->equipos()->orderBy('id')->get();
            $equiposIds = $equipos->pluck('id')->toArray();

            Log::info("[EliminacionDirectaEquipos] Equipos encontrados: " . implode(', ', $equiposIds));
            Log::info("[EliminacionDirectaEquipos] Total de equipos: " . count($equiposIds));

            // Si el número de equipos es impar, agregar un BYE (null)
            if (count($equiposIds) % 2 !== 0) {
                $equiposIds[] = null;
                Log::info("[EliminacionDirectaEquipos] Agregado BYE - Total equipos: " . count($equiposIds));
            }

            // Obtener la ronda que ya fue creada
            $rondaTorneo = $torneo->rondas()->orderBy('numero_ronda', 'desc')->first();
            if (!$rondaTorneo) {
                Log::error("[EliminacionDirectaEquipos] No se encontró la ronda para el torneo");
                throw new \Exception('No se encontró la ronda para el torneo');
            }

            Log::info("[EliminacionDirectaEquipos] Usando ronda existente: {$rondaTorneo->numero_ronda}");

            // Verificar si ya existen matches para esta ronda
            $matchesExistentes = EquipoMatch::where('torneo_id', $torneo->id)
                ->where('ronda', $rondaTorneo->numero_ronda)
                ->count();
            
            Log::info("[EliminacionDirectaEquipos] Matches existentes para la ronda: {$matchesExistentes}");
            
            if ($matchesExistentes > 0) {
                Log::warning("[EliminacionDirectaEquipos] Ya existen matches para esta ronda, no se crean de nuevo");
                return $rondaTorneo;
            }

            // Emparejar y crear matches
            for ($i = 0; $i < count($equiposIds); $i += 2) {
                $equipo1 = $equiposIds[$i];
                $equipo2 = $equiposIds[$i + 1] ?? null;

                Log::info("[EliminacionDirectaEquipos] Procesando emparejamiento " . ($i/2 + 1) . ": Equipo1={$equipo1}, Equipo2={$equipo2}");

                if ($equipo1 && $equipo2) {
                    // Crear match normal entre dos equipos
                    $match = EquipoMatch::create([
                        'torneo_id' => $torneo->id,
                        'ronda' => $rondaTorneo->numero_ronda,
                        'equipo_a_id' => $equipo1,
                        'equipo_b_id' => $equipo2,
                        'mesa' => ($i / 2) + 1
                    ]);

                    Log::info("[EliminacionDirectaEquipos] Match creado ID: {$match->id} - Mesa: " . ($i / 2) + 1);

                    // Obtener los equipos reales para crear las partidas individuales
                    $equipoA = $equipos->find($equipo1);
                    $equipoB = $equipos->find($equipo2);

                    // Obtener jugadores ordenados por tablero
                    $jugadoresA = $equipoA->jugadores()->orderBy('tablero')->get();
                    $jugadoresB = $equipoB->jugadores()->orderBy('tablero')->get();

                    Log::info("[EliminacionDirectaEquipos] Jugadores equipo A ({$equipoA->nombre}): " . $jugadoresA->count() . ", Jugadores equipo B ({$equipoB->nombre}): " . $jugadoresB->count());

                    // Crear partidas individuales con colores alternos por tablero
                    $numTableros = min($jugadoresA->count(), $jugadoresB->count());

                    for ($t = 0; $t < $numTableros; $t++) {
                        $tableroNum = $t + 1;
                        $esTableroImpar = $tableroNum % 2 === 1;

                        // Determinar colores según tablero (impar: A blancas, par: B blancas)
                        if ($esTableroImpar) {
                            $blancas = $jugadoresA[$t];
                            $negras = $jugadoresB[$t];
                        } else {
                            $blancas = $jugadoresB[$t];
                            $negras = $jugadoresA[$t];
                        }

                        $partida = PartidaIndividual::create([
                            'equipo_match_id' => $match->id,
                            'jugador_a_id' => $blancas->miembro_id,
                            'jugador_b_id' => $negras->miembro_id,
                            'tablero' => $tableroNum
                        ]);

                        Log::info("[EliminacionDirectaEquipos] Partida creada ID: {$partida->id} - Tablero: {$tableroNum} - Blancas: {$blancas->miembro_id}, Negras: {$negras->miembro_id}");
                    }
                } elseif ($equipo1 && !$equipo2) {
                    // BYE para equipo1
                    $match = EquipoMatch::create([
                        'torneo_id' => $torneo->id,
                        'ronda' => $rondaTorneo->numero_ronda,
                        'equipo_a_id' => $equipo1,
                        'equipo_b_id' => null,
                        'resultado_match' => 1, // Victoria por bye
                        'mesa' => ($i / 2) + 1
                    ]);

                    Log::info("[EliminacionDirectaEquipos] BYE creado para equipo {$equipo1} - Match ID: {$match->id}");

                    // Asignar victorias a todos los jugadores del equipo
                    $equipoA = $equipos->find($equipo1);
                    foreach ($equipoA->jugadores as $jugador) {
                        $partida = PartidaIndividual::create([
                            'equipo_match_id' => $match->id,
                            'jugador_a_id' => $jugador->miembro_id,
                            'jugador_b_id' => null,
                            'resultado' => 1, // Victoria por bye
                            'tablero' => $jugador->tablero
                        ]);

                        Log::info("[EliminacionDirectaEquipos] Partida BYE creada ID: {$partida->id} - Jugador: {$jugador->miembro_id}");
                    }
                }
            }

            $matchesCreados = EquipoMatch::where('torneo_id', $torneo->id)->where('ronda', $rondaTorneo->numero_ronda)->count();
            $partidasCreadas = PartidaIndividual::whereHas('match', function($q) use ($torneo, $rondaTorneo) {
                $q->where('torneo_id', $torneo->id)->where('ronda', $rondaTorneo->numero_ronda);
            })->count();

            Log::info('[EliminacionDirectaEquipos] FIN generarEliminacionDirectaEquipos', [
                'ronda_id' => $rondaTorneo->id,
                'matches_creados' => $matchesCreados,
                'partidas_creadas' => $partidasCreadas
            ]);

            return $rondaTorneo;
        });
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
        Log::info("[GenerarEmparejamientosEquipos] Iniciando generación de emparejamientos por equipos", [
            'torneo_id' => $torneo->id,
            'ronda_id' => $ronda->id,
            'ronda_numero' => $ronda->numero_ronda
        ]);

        // Validación para evitar duplicados
        $matchesExistentes = \App\Models\EquipoMatch::where('torneo_id', $torneo->id)->where('ronda', $ronda->numero_ronda)->count();
        Log::info("[GenerarEmparejamientosEquipos] Matches existentes para la ronda: {$matchesExistentes}");
        
        if ($matchesExistentes > 0) {
            Log::warning("[GenerarEmparejamientosEquipos] Ya existen matches para esta ronda, no crear de nuevo");
            return;
        }
        
        try {
            Log::info("[GenerarEmparejamientosEquipos] Creando TeamPairingService");
        $service = new \App\Services\TeamPairingService($torneo);
            
            Log::info("[GenerarEmparejamientosEquipos] Llamando a generarEmparejamientos");
        $emparejamientos = $service->generarEmparejamientos($ronda->numero_ronda);
            
            Log::info("[GenerarEmparejamientosEquipos] Emparejamientos generados: " . count($emparejamientos));
            
        // Si el servicio devuelve advertencias, pásalas a la sesión
        if (property_exists($service, 'warnings') && !empty($service->warnings)) {
                Log::warning("[GenerarEmparejamientosEquipos] Advertencias del servicio: " . implode(', ', $service->warnings));
            session()->flash('warnings', $service->warnings);
        }

        foreach ($emparejamientos as $index => $emparejamiento) {
                Log::info("[GenerarEmparejamientosEquipos] Procesando emparejamiento {$index}", [
                    'equipo_a_id' => $emparejamiento['equipo_a']->id,
                    'equipo_b_id' => $emparejamiento['equipo_b']->id,
                    'tableros_count' => count($emparejamiento['tableros'])
                ]);
                
            $match = EquipoMatch::create([
                'torneo_id' => $torneo->id,
                'ronda' => $ronda->numero_ronda,
                'equipo_a_id' => $emparejamiento['equipo_a']->id,
                'equipo_b_id' => $emparejamiento['equipo_b']->id,
                'mesa' => $index + 1
            ]);
                
                Log::info("[GenerarEmparejamientosEquipos] Match creado ID: {$match->id}");

            foreach ($emparejamiento['tableros'] as $tablero) {
                    $partida = PartidaIndividual::create([
                    'equipo_match_id' => $match->id,
                    'jugador_a_id' => $tablero['blancas']->miembro_id,
                    'jugador_b_id' => $tablero['negras']->miembro_id,
                    'tablero' => $tablero['tablero']
                ]);
                    
                    Log::info("[GenerarEmparejamientosEquipos] Partida creada ID: {$partida->id}", [
                        'tablero' => $tablero['tablero'],
                        'blancas' => $tablero['blancas']->miembro_id,
                        'negras' => $tablero['negras']->miembro_id
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
                Log::info("Calculando resultado_match para match {$match->id}: puntos A = {$puntajeA}, puntos B = {$puntajeB}");
            if ($puntajeA > $puntajeB) {
                $match->resultado_match = 1; // Gana equipo A
            } elseif ($puntajeB > $puntajeA) {
                $match->resultado_match = 2; // Gana equipo B
            } elseif ($puntajeA == $puntajeB && $match->equipo_b_id !== null) {
                $match->resultado_match = 0; // Empate (si no es BYE)
            } else {
                $match->resultado_match = null; // BYE o sin resultado
            }
                Log::info("Match {$match->id} resultado_match: " . var_export($match->resultado_match, true));
            $match->save();
            }
            
            $matchesCreados = EquipoMatch::where('torneo_id', $torneo->id)->where('ronda', $ronda->numero_ronda)->count();
            $partidasCreadas = PartidaIndividual::whereHas('match', function($q) use ($torneo, $ronda) {
                $q->where('torneo_id', $torneo->id)->where('ronda', $ronda->numero_ronda);
            })->count();
            
            Log::info("[GenerarEmparejamientosEquipos] FIN generación", [
                'matches_creados' => $matchesCreados,
                'partidas_creadas' => $partidasCreadas
            ]);
            
        } catch (\Exception $e) {
            Log::error("[GenerarEmparejamientosEquipos] Error: " . $e->getMessage());
            Log::error("[GenerarEmparejamientosEquipos] Stack trace: " . $e->getTraceAsString());
            throw $e;
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
        Log::info('=== INICIO guardarResultadosRonda ===', ['ronda_id' => $ronda->id, 'request' => $request->all()]);
        // Forzar decodificación si es string
        if (isset($request->resultados) && is_string($request->resultados)) {
            $array = json_decode($request->resultados, true);
            Log::info('Intentando decodificar resultados', ['original' => $request->resultados, 'decodificado' => $array]);
            if (is_array($array)) {
                $request->merge(['resultados' => $array]);
            } else {
                return redirect()->back()->with('error', 'Por favor, rellene todos los resultados de la ronda');
            }
        }
        Log::info('Tipo de resultados después de merge', ['tipo' => gettype($request->resultados), 'contenido' => $request->resultados]);
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
                Log::info('IDs esperados por el backend', $idsEsperados);
                Log::info('IDs recibidos en resultados', array_keys($request->resultados));
                
                // Validar que no haya empates en eliminación directa por equipos
                if ($torneo->tipo_torneo === 'Eliminación Directa') {
                    $empatesEncontrados = [];
                foreach ($matches as $match) {
                    $puntajeA = 0;
                    $puntajeB = 0;
                        $partidasConResultado = 0;
                        
                    foreach ($match->partidas as $partida) {
                        if (!isset($request->resultados[$partida->id])) continue;
                        $texto = trim($request->resultados[$partida->id]);
                            if (empty($texto)) continue;
                            
                            $partidasConResultado++;
                        // Normalizar empate
                        $texto_normalizado = strtolower(str_replace([',', ' '], ['.', ''], $texto));
                        if (preg_match('/^(0\.5-0\.5|0\.5|1\/2-1\/2|1\/2|½-½|½)$/u', $texto_normalizado)) {
                            $texto_normalizado = '0.5';
                        }
                            
                            // Procesar resultado para validación
                            switch ($texto_normalizado) {
                                case '1-0':
                                case '1':
                                    $puntajeA += 1;
                                    break;
                                case '0-1':
                                case '0':
                                    $puntajeB += 1;
                                    break;
                                case '0.5':
                                    $puntajeA += 0.5;
                                    $puntajeB += 0.5;
                                    break;
                            }
                        }
                        
                        // Si todas las partidas tienen resultado y hay empate
                        if ($partidasConResultado > 0 && $puntajeA == $puntajeB) {
                            $equipoA = $torneo->equipos()->find($match->equipo_a_id);
                            $equipoB = $torneo->equipos()->find($match->equipo_b_id);
                            $empatesEncontrados[] = [
                                'equipo_a' => $equipoA ? $equipoA->nombre : 'Equipo A',
                                'equipo_b' => $equipoB ? $equipoB->nombre : 'Equipo B',
                                'mesa' => $match->mesa,
                                'puntaje_a' => $puntajeA,
                                'puntaje_b' => $puntajeB
                            ];
                        }
                    }
                    
                    // Si hay empates, no permitir guardar y mostrar error
                    if (!empty($empatesEncontrados)) {
                        $mensajeEmpates = "No se pueden guardar los resultados porque se detectaron empates en eliminación directa:\n";
                        foreach ($empatesEncontrados as $empate) {
                            $mensajeEmpates .= "- Mesa {$empate['mesa']}: {$empate['equipo_a']} vs {$empate['equipo_b']} ({$empate['puntaje_a']}-{$empate['puntaje_b']})\n";
                        }
                        $mensajeEmpates .= "\nEn eliminación directa no se permiten empates. Por favor, registre un ganador para cada match.";
                        
                        return redirect()
                            ->route('torneos.rondas.show', [$torneo, $ronda])
                            ->with('error', $mensajeEmpates);
                    }
                }
                
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
                        Log::info('Procesando resultado para partida ID ' . $partida->id . ': input="' . $texto . '", normalizado="' . $texto_normalizado . '"');
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
                        Log::info('Resultado guardado en partida ID ' . $partida->id . ': resultado=' . var_export($partida->resultado, true));
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
                    Log::info("[RECALCULO] Match {$match->id} resultado_match: " . var_export($match->resultado_match, true));
                    $match->save();
                }
                $ronda->completada = true;
                $ronda->save();
                DB::commit();
                
                // Registrar auditoría para guardar resultados de ronda (fuera de la transacción)
                Log::info('=== LLAMANDO AUDITORÍA DE RESULTADOS ===');
                Log::info('Torneo: ' . $torneo->nombre_torneo);
                Log::info('Ronda: ' . $ronda->numero_ronda);
                $this->crearAuditoriaResultados($torneo, $ronda, $request->resultados);
                
                // return redirect()->route('torneos.rondas.show', [$torneo, $ronda])->with('success', 'Resultados guardados exitosamente.');
                
                // Verificar si es eliminación directa y generar siguiente ronda
                $siguienteRonda = null;
                if ($torneo->tipo_torneo === 'Eliminación Directa' && $ronda->numero_ronda < $torneo->no_rondas) {
                    $nuevaRonda = RondaTorneo::create([
                        'torneo_id' => $torneo->id,
                        'numero_ronda' => $ronda->numero_ronda + 1,
                        'fecha_hora' => now(),
                        'completada' => false
                    ]);
                    try {
                        if ($torneo->es_por_equipos) {
                            $this->generarSiguienteRondaEliminacionDirectaEquipos($torneo, $ronda, $nuevaRonda);
                        } else {
                            $this->generarSiguienteRondaEliminacionDirecta($torneo, $ronda, $nuevaRonda);
                        }
                        $siguienteRonda = $nuevaRonda;
                    } catch (\Exception $e) {
                        // Si la excepción contiene información sobre empates, mostrar warning
                        if (strpos($e->getMessage(), 'empates registrados') !== false) {
                            return redirect()
                                ->route('torneos.rondas.show', [$torneo, $ronda])
                                ->with('warning', $e->getMessage());
                        } elseif ($e->getMessage() === 'TORNEO_FINALIZADO') {
                            // Torneo finalizado, redirigir al detalle del torneo
                            return redirect()
                                ->route('torneos.show', $torneo)
                                ->with('success', '¡Torneo finalizado! Se ha determinado el ganador.');
                        } else {
                            // Para otras excepciones, eliminar la ronda vacía y mostrar error
                            $nuevaRonda->delete();
                            throw $e;
                        }
                    }
                }
                
                if ($siguienteRonda) {
                    return redirect()
                        ->route('torneos.rondas.show', [$torneo, $siguienteRonda])
                        ->with('success', 'Resultados guardados y siguiente ronda generada exitosamente.');
                } else {
                return redirect()->route('torneos.rondas.show', [$torneo, $ronda])->with('success', 'Resultados guardados exitosamente.');
                }

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
                    ->whereNotNull('jugador_blancas_id')
                    ->whereNotNull('jugador_negras_id') // Solo partidas reales, no BYE
                    ->count();

                Log::info('Verificando si la ronda está completa', [
                    'ronda_id' => $ronda->id,
                    'partidasSinResultado' => $partidasSinResultado
                ]);
                if ($partidasSinResultado === 0) {
                    $ronda->completada = true;
                    $ronda->save();
                    
                    // Verificar si es eliminación directa y generar siguiente ronda
                            $siguienteRonda = null;
                    if ($torneo->tipo_torneo === 'Eliminación Directa' && $ronda->numero_ronda < $torneo->no_rondas) {
                        $nuevaRonda = RondaTorneo::create([
                                'torneo_id' => $torneo->id,
                            'numero_ronda' => $ronda->numero_ronda + 1,
                            'fecha_hora' => now(),
                            'completada' => false
                        ]);
                        try {
                            if ($torneo->es_por_equipos) {
                                $this->generarSiguienteRondaEliminacionDirectaEquipos($torneo, $ronda, $nuevaRonda);
                            } else {
                                $this->generarSiguienteRondaEliminacionDirecta($torneo, $ronda, $nuevaRonda);
                            }
                            $siguienteRonda = $nuevaRonda;
                        } catch (\Exception $e) {
                            // Si la excepción contiene información sobre empates, mostrar warning
                            if (strpos($e->getMessage(), 'empates registrados') !== false) {
                                return redirect()
                                    ->route('torneos.rondas.show', [$torneo, $ronda])
                                    ->with('warning', $e->getMessage());
                            } elseif ($e->getMessage() === 'TORNEO_FINALIZADO') {
                                // Torneo finalizado, redirigir al detalle del torneo
                                return redirect()
                                    ->route('torneos.show', $torneo)
                                    ->with('success', '¡Torneo finalizado! Se ha determinado el ganador.');
                            } else {
                                // Para otras excepciones, eliminar la ronda vacía y mostrar error
                                $nuevaRonda->delete();
                                throw $e;
                        }
                    }
                }

                DB::commit();
                
                // Registrar auditoría para guardar resultados de ronda (fuera de la transacción)
                Log::info('=== LLAMANDO AUDITORÍA DE RESULTADOS (COMMIT 2) ===');
                Log::info('Torneo: ' . $torneo->nombre_torneo);
                Log::info('Ronda: ' . $ronda->numero_ronda);
                $this->crearAuditoriaResultados($torneo, $ronda, $request->resultados);
                
                    if ($siguienteRonda) {
                    return redirect()
                        ->route('torneos.rondas.show', [$torneo, $siguienteRonda])
                        ->with('success', 'Resultados guardados y siguiente ronda generada exitosamente.');
                } else {
                        return redirect()->route('torneos.rondas.show', [$torneo, $ronda])->with('success', 'Resultados guardados exitosamente.');
                    }
                }

                // Recalcular criterios de desempate de equipos si aplica
                if ($torneo->es_por_equipos && ($torneo->usar_buchholz || $torneo->usar_sonneborn_berger || $torneo->usar_desempate_progresivo)) {
                    // Forzar recálculo de la tabla de clasificación (show) al recargar
                }
              // Registrar auditoría para guardar resultados de ronda (fuera de la transacción)
              Log::info('=== LLAMANDO AUDITORÍA DE RESULTADOS (COMMIT 3) ===');
              Log::info('Torneo: ' . $torneo->nombre_torneo);
              Log::info('Ronda: ' . $ronda->numero_ronda);
              $this->crearAuditoriaResultados($torneo, $ronda, $request->resultados);
              
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
            
            return redirect()->back()->with('error', 'Por favor, rellene todos los resultados de la ronda');
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
                // Partida de BYE (solo sumar si el jugador es blancas y negras es null)
                if (!$p->jugador_negras_id && $p->jugador_blancas_id === $jugadorId) {
                    $puntosTotales += 1.0;
                    continue;
                }
                // Partidas normales
                $resultado = floatval($p->resultado);
                if ($p->jugador_blancas_id === $jugadorId) {
                    if ($resultado == 1.0) $puntosTotales += 1.0;      // Victoria con blancas
                    elseif ($resultado == 0.5) $puntosTotales += 0.5;  // Tablas
                }
                if ($p->jugador_negras_id === $jugadorId) {
                    if ($resultado == 0.0) $puntosTotales += 1.0;      // Victoria con negras
                    elseif ($resultado == 0.5) $puntosTotales += 0.5;  // Tablas
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

    private function generarSiguienteRondaEliminacionDirecta(Torneo $torneo, RondaTorneo $rondaActual, RondaTorneo $nuevaRonda)
    {
        Log::info("[GenerarSiguienteRondaEliminacionDirecta] Iniciando lógica de eliminación directa", [
            'torneo_id' => $torneo->id,
            'ronda_actual_id' => $rondaActual->id,
            'nueva_ronda_id' => $nuevaRonda->id
        ]);

        // Determinar ganadores SOLO por resultado 1 (blancas) o 0 (negras). No se aceptan tablas (0.5)
        $ganadores = [];
        $partidas = $rondaActual->partidas()->get();
        Log::info("[EliminacionDirecta] Total de partidas encontradas: " . $partidas->count());
        
        foreach ($partidas as $partida) {
            Log::info("[EliminacionDirecta] Procesando partida ID: {$partida->id}", [
                'jugador_blancas_id' => $partida->jugador_blancas_id,
                'jugador_negras_id' => $partida->jugador_negras_id,
                'resultado' => $partida->resultado,
                'tipo_resultado' => gettype($partida->resultado),
                'resultado_igual_1' => $partida->resultado == 1,
                'resultado_igual_0' => $partida->resultado == 0,
                'resultado_igual_0.5' => $partida->resultado == 0.5,
                'resultado_es_null' => $partida->resultado === null
            ]);

            if ($partida->resultado == 1) {
                $ganadores[] = $partida->jugador_blancas_id;
                Log::info("[EliminacionDirecta] Victoria blancas - Ganador: {$partida->jugador_blancas_id}");
            } elseif ($partida->resultado == 0) {
                $ganadores[] = $partida->jugador_negras_id;
                Log::info("[EliminacionDirecta] Victoria negras - Ganador: {$partida->jugador_negras_id}");
            } elseif ($partida->resultado == 0.5) {
                // No permitir tablas en eliminación directa
                Log::warning("[EliminacionDirecta] Se detectó una partida con tablas. Esto no está permitido. Partida ID: {$partida->id}");
            } elseif ($partida->resultado === null) {
                Log::warning("[EliminacionDirecta] Partida sin resultado encontrada. Partida ID: {$partida->id}");
            } else {
                Log::warning("[EliminacionDirecta] Resultado inesperado: " . var_export($partida->resultado, true) . " para partida ID: {$partida->id}");
            }
        }

        Log::info("[EliminacionDirecta] Ganadores encontrados: " . implode(', ', $ganadores));

        // Si no hay ganadores, no crear la ronda
        if (count($ganadores) < 2) {
            Log::warning("[EliminacionDirecta] No hay suficientes ganadores para crear la siguiente ronda. Ganadores: " . implode(', ', $ganadores));
            // Eliminar la ronda vacía creada
            $nuevaRonda->delete();
            return;
        }

        // Si el número de ganadores es impar, agregar un BYE (null)
        if (count($ganadores) % 2 !== 0) {
            $ganadores[] = null;
        }

        // Emparejar y crear partidas
        for ($i = 0; $i < count($ganadores); $i += 2) {
            $jugador1 = $ganadores[$i];
            $jugador2 = $ganadores[$i + 1] ?? null;
            if ($jugador1 && $jugador2) {
                PartidaTorneo::create([
                    'ronda_id' => $nuevaRonda->id,
                    'jugador_blancas_id' => $jugador1,
                    'jugador_negras_id' => $jugador2,
                    'mesa' => ($i / 2) + 1,
                    'resultado' => null
                ]);
            } elseif ($jugador1 && !$jugador2) {
                // BYE para jugador1
                PartidaTorneo::create([
                    'ronda_id' => $nuevaRonda->id,
                    'jugador_blancas_id' => $jugador1,
                    'jugador_negras_id' => null,
                    'resultado' => 1, // Victoria por bye
                    'mesa' => ($i / 2) + 1,
                    'resultado' => null
                ]);
            }
        }
        Log::info('[EliminacionDirecta] FIN generarSiguienteRondaEliminacionDirecta', [
            'ronda_id' => $nuevaRonda->id,
            'partidas_creadas' => PartidaTorneo::where('ronda_id', $nuevaRonda->id)->count()
        ]);
    }

    private function generarSiguienteRondaEliminacionDirectaEquipos(Torneo $torneo, RondaTorneo $rondaActual, RondaTorneo $nuevaRonda)
    {
        Log::info("[GenerarSiguienteRondaEliminacionDirectaEquipos] Iniciando lógica de eliminación directa por equipos", [
            'torneo_id' => $torneo->id,
            'ronda_actual_id' => $rondaActual->id,
            'nueva_ronda_id' => $nuevaRonda->id,
            'ronda_actual_numero' => $rondaActual->numero_ronda
        ]);

        // Determinar ganadores basándose en los matches de la ronda actual
        $ganadores = [];
        $matches = EquipoMatch::where('torneo_id', $torneo->id)
            ->where('ronda', $rondaActual->numero_ronda)
            ->get();

        Log::info("[EliminacionDirectaEquipos] Total de matches encontrados: " . $matches->count());
        Log::info("[EliminacionDirectaEquipos] Buscando matches con ronda: " . $rondaActual->numero_ronda);

        // Verificar si hay empates registrados
        $empatesEncontrados = [];
        foreach ($matches as $match) {
            Log::info("[EliminacionDirectaEquipos] Procesando match ID: {$match->id}", [
                'equipo_a_id' => $match->equipo_a_id,
                'equipo_b_id' => $match->equipo_b_id,
                'resultado_match' => $match->resultado_match,
                'ronda_match' => $match->ronda
            ]);

            if ($match->resultado_match == 1) {
                // Victoria del equipo A
                $ganadores[] = $match->equipo_a_id;
                Log::info("[EliminacionDirectaEquipos] Victoria equipo A - Ganador: {$match->equipo_a_id}");
            } elseif ($match->resultado_match == 2) {
                // Victoria del equipo B
                $ganadores[] = $match->equipo_b_id;
                Log::info("[EliminacionDirectaEquipos] Victoria equipo B - Ganador: {$match->equipo_b_id}");
            } elseif ($match->resultado_match == 0) {
                // Empate - no debería ocurrir en eliminación directa
                Log::warning("[EliminacionDirectaEquipos] Se detectó un match con empate. Esto no está permitido. Match ID: {$match->id}");
                
                // Obtener nombres de los equipos para el warning
                $equipoA = $torneo->equipos()->find($match->equipo_a_id);
                $equipoB = $torneo->equipos()->find($match->equipo_b_id);
                $empatesEncontrados[] = [
                    'match_id' => $match->id,
                    'equipo_a' => $equipoA ? $equipoA->nombre : 'Equipo A',
                    'equipo_b' => $equipoB ? $equipoB->nombre : 'Equipo B',
                    'mesa' => $match->mesa
                ];
            } elseif ($match->resultado_match === null) {
                Log::warning("[EliminacionDirectaEquipos] Match sin resultado encontrado. Match ID: {$match->id}");
            } else {
                Log::warning("[EliminacionDirectaEquipos] Resultado inesperado: " . var_export($match->resultado_match, true) . " para match ID: {$match->id}");
            }
        }

        // Si hay empates registrados, mostrar warning y no generar la siguiente ronda
        if (!empty($empatesEncontrados)) {
            Log::warning("[EliminacionDirectaEquipos] Se encontraron empates registrados. No se puede generar la siguiente ronda.");
            
            // Eliminar la ronda vacía creada
            $nuevaRonda->delete();
            
            // Crear mensaje de warning con detalles de los empates
            $mensajeEmpates = "No se puede generar la siguiente ronda porque se encontraron empates registrados en la ronda actual:\n";
            foreach ($empatesEncontrados as $empate) {
                $mensajeEmpates .= "- Mesa {$empate['mesa']}: {$empate['equipo_a']} vs {$empate['equipo_b']}\n";
            }
            $mensajeEmpates .= "\nEn eliminación directa no se permiten empates. Por favor, registre un ganador para cada match antes de generar la siguiente ronda.";
            
            // Lanzar excepción con el mensaje de warning
            throw new \Exception($mensajeEmpates);
        }

        Log::info("[EliminacionDirectaEquipos] Ganadores encontrados: " . implode(', ', $ganadores));

        // Si no hay ganadores, no crear la ronda
        if (count($ganadores) < 2) {
            Log::warning("[EliminacionDirectaEquipos] No hay suficientes ganadores para crear la siguiente ronda. Ganadores: " . implode(', ', $ganadores));
            // Eliminar la ronda vacía creada
            $nuevaRonda->delete();
            
            // Si solo hay un ganador, el torneo ha terminado
            if (count($ganadores) == 1) {
                Log::info("[EliminacionDirectaEquipos] Torneo finalizado. Ganador: " . $ganadores[0]);
                // Marcar el torneo como finalizado
                $torneo->estado = 'Finalizado';
                $torneo->save();
                
                // Lanzar excepción especial para redirigir al detalle del torneo
                throw new \Exception('TORNEO_FINALIZADO');
            }
            return;
        }

        // Si el número de ganadores es impar, agregar un BYE (null)
        if (count($ganadores) % 2 !== 0) {
            $ganadores[] = null;
        }

        Log::info("[EliminacionDirectaEquipos] Ganadores finales (con BYE si es necesario): " . implode(', ', $ganadores));

        // Obtener todos los equipos para crear las partidas individuales
        $equipos = $torneo->equipos()->orderBy('id')->get();

        // Emparejar y crear matches
        for ($i = 0; $i < count($ganadores); $i += 2) {
            $equipo1 = $ganadores[$i];
            $equipo2 = $ganadores[$i + 1] ?? null;

            Log::info("[EliminacionDirectaEquipos] Creando match: Equipo1={$equipo1}, Equipo2={$equipo2}");

            if ($equipo1 && $equipo2) {
                // Crear match normal entre dos equipos
                $match = EquipoMatch::create([
                    'torneo_id' => $torneo->id,
                    'ronda' => $nuevaRonda->numero_ronda,
                    'equipo_a_id' => $equipo1,
                    'equipo_b_id' => $equipo2,
                    'mesa' => ($i / 2) + 1
                ]);

                Log::info("[EliminacionDirectaEquipos] Match creado ID: {$match->id}");

                // Obtener los equipos reales para crear las partidas individuales
                $equipoA = $equipos->find($equipo1);
                $equipoB = $equipos->find($equipo2);

                // Obtener jugadores ordenados por tablero
                $jugadoresA = $equipoA->jugadores()->orderBy('tablero')->get();
                $jugadoresB = $equipoB->jugadores()->orderBy('tablero')->get();

                Log::info("[EliminacionDirectaEquipos] Jugadores equipo A: " . $jugadoresA->count() . ", Jugadores equipo B: " . $jugadoresB->count());

                // Crear partidas individuales con colores alternos por tablero
                $numTableros = min($jugadoresA->count(), $jugadoresB->count());

                for ($t = 0; $t < $numTableros; $t++) {
                    $tableroNum = $t + 1;
                    $esTableroImpar = $tableroNum % 2 === 1;

                    // Determinar colores según tablero (impar: A blancas, par: B blancas)
                    if ($esTableroImpar) {
                        $blancas = $jugadoresA[$t];
                        $negras = $jugadoresB[$t];
                    } else {
                        $blancas = $jugadoresB[$t];
                        $negras = $jugadoresA[$t];
                    }

                    $partida = PartidaIndividual::create([
                        'equipo_match_id' => $match->id,
                        'jugador_a_id' => $blancas->miembro_id,
                        'jugador_b_id' => $negras->miembro_id,
                        'tablero' => $tableroNum
                    ]);

                    Log::info("[EliminacionDirectaEquipos] Partida creada ID: {$partida->id} - Tablero: {$tableroNum}");
                }
            } elseif ($equipo1 && !$equipo2) {
                // BYE para equipo1
                $match = EquipoMatch::create([
                    'torneo_id' => $torneo->id,
                    'ronda' => $nuevaRonda->numero_ronda,
                    'equipo_a_id' => $equipo1,
                    'equipo_b_id' => null,
                    'resultado_match' => 1, // Victoria por bye
                    'mesa' => ($i / 2) + 1
                ]);

                Log::info("[EliminacionDirectaEquipos] BYE creado para equipo {$equipo1} - Match ID: {$match->id}");

                // Asignar victorias a todos los jugadores del equipo
                $equipoA = $equipos->find($equipo1);
                foreach ($equipoA->jugadores as $jugador) {
                    $partida = PartidaIndividual::create([
                        'equipo_match_id' => $match->id,
                        'jugador_a_id' => $jugador->miembro_id,
                        'jugador_b_id' => null,
                        'resultado' => 1, // Victoria por bye
                        'tablero' => $jugador->tablero
                    ]);

                    Log::info("[EliminacionDirectaEquipos] Partida BYE creada ID: {$partida->id} - Jugador: {$jugador->miembro_id}");
                }
            }
        }
        Log::info('[EliminacionDirectaEquipos] FIN generarSiguienteRondaEliminacionDirectaEquipos', [
            'ronda_id' => $nuevaRonda->id,
            'matches_creados' => EquipoMatch::where('torneo_id', $torneo->id)->where('ronda', $nuevaRonda->numero_ronda)->count()
        ]);
    }

    /**
     * Muestra una ronda individual con el detalle del torneo y navegación entre rondas.
     */
    public function show(Torneo $torneo, RondaTorneo $ronda)
    {
        Log::info("[ShowRonda] Iniciando método show", [
            'torneo_id' => $torneo->id,
            'torneo_nombre' => $torneo->nombre_torneo,
            'tipo_torneo' => $torneo->tipo_torneo,
            'es_por_equipos' => $torneo->es_por_equipos,
            'ronda_id' => $ronda->id,
            'ronda_numero' => $ronda->numero_ronda
        ]);

        // Obtener todas las rondas para navegación
        $rondas = $torneo->rondas()->orderBy('numero_ronda')->get();
        // Participantes y partidas de la ronda
        $partidas = $ronda->partidas()->with(['jugadorBlancas.elo', 'jugadorNegras.elo'])->get();
        // Para la tabla de clasificación
        $participantes = $torneo->participantes()->with(['miembro.elo', 'miembro.fide'])->orderBy('numero_inicial')->get();
        $matches = collect();
        // Si es por equipos, cargar equipos con sus puntos acumulados y los matches de la ronda
        if ($torneo->es_por_equipos) {
            Log::info("[ShowRonda] Es torneo por equipos, cargando matches");
            
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
                    Log::info("Equipo {$equipo->nombre} - Match {$match->id} resultado_match: " . var_export($match->resultado_match, true) . ", puntos_oponente: {$puntos_oponente}");
                    if ($match->resultado_match !== null && $oponente) {
                        if (($esA && $match->resultado_match == 1) || (!$esA && $match->resultado_match == 2)) {
                            $sonneborn += $puntos_oponente;
                            Log::info("SB: Victoria. Suma {$puntos_oponente} a {$equipo->nombre}");
                        } elseif ($match->resultado_match == 0) {
                            $sonneborn += $puntos_oponente / 2;
                            Log::info("SB: Empate. Suma " . ($puntos_oponente/2) . " a {$equipo->nombre}");
                        }
                    }
                    // Progresivo: suma acumulativa de puntos por ronda
                    $acumulado += $puntos_equipo ?? 0;
                    $progresivo += $acumulado;
                }
                Log::info("Equipo {$equipo->nombre}: Buchholz={$buchholz}, Sonneborn-Berger={$sonneborn}, Progresivo={$progresivo}");
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

            Log::info("[ShowRonda] Matches cargados", [
                'matches_count' => $matches->count(),
                'ronda_numero' => $ronda->numero_ronda,
                'torneo_id' => $torneo->id
            ]);

            // Log detallado de cada match encontrado
            foreach ($matches as $match) {
                Log::info("[ShowRonda] Match encontrado", [
                    'match_id' => $match->id,
                    'equipo_a_id' => $match->equipo_a_id,
                    'equipo_b_id' => $match->equipo_b_id,
                    'equipo_a_nombre' => $match->equipoA ? $match->equipoA->nombre : 'null',
                    'equipo_b_nombre' => $match->equipoB ? $match->equipoB->nombre : 'null',
                    'ronda' => $match->ronda,
                    'mesa' => $match->mesa,
                    'partidas_count' => $match->partidas->count()
                ]);
            }

            // Verificar si hay matches en la base de datos para esta ronda
            $matchesDB = \App\Models\EquipoMatch::where('torneo_id', $torneo->id)
                ->where('ronda', $ronda->numero_ronda)
                ->get();
            
            Log::info("[ShowRonda] Verificación directa en DB", [
                'matches_db_count' => $matchesDB->count(),
                'ronda_buscada' => $ronda->numero_ronda
            ]);

            foreach ($matchesDB as $match) {
                Log::info("[ShowRonda] Match en DB", [
                    'match_id' => $match->id,
                    'equipo_a_id' => $match->equipo_a_id,
                    'equipo_b_id' => $match->equipo_b_id,
                    'ronda' => $match->ronda
                ]);
            }

        } else {
            $equipos = collect(); // O un array vacío para individuales
        }
        return view('torneos.ronda', compact('torneo', 'ronda', 'rondas', 'partidas', 'participantes', 'equipos', 'matches'));
    }
    
    /**
     * Crear auditoría para guardar resultados de ronda
     */
    private function crearAuditoriaResultados($torneo, $ronda, $resultados)
    {
        try {
            // Procesar resultados para cambiar null por 'BYE'
            $resultadosProcesados = [];
            foreach ($resultados as $partidaId => $resultado) {
                if ($resultado === null || $resultado === '') {
                    $resultadosProcesados[$partidaId] = 'BYE';
                } else {
                    $resultadosProcesados[$partidaId] = $resultado;
                }
            }
            
            $fechaHora = now()->setTimezone('America/Managua');
            
            $auditoria = Auditoria::create([
                'correo_id' => Auth::user()->correo,
                'tabla_afectada' => 'Rondas',
                'accion' => 'Inserción',
                'valor_previo' => null,
                'valor_posterior' => json_encode([
                    'torneo' => $torneo->nombre_torneo,
                    'ronda' => $ronda->numero_ronda,
                    'resultados' => $resultadosProcesados
                ], JSON_UNESCAPED_UNICODE),
                'fecha' => $fechaHora->toDateString(),
                'hora' => $fechaHora->toTimeString(),
                'equipo' => request()->ip()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al crear auditoría de resultados: ' . $e->getMessage());
        }
    }
} 