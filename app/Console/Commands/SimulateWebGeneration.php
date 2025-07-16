<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EquipoMatch;
use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\PartidaIndividual;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SimulateWebGeneration extends Command
{
    protected $signature = 'simulate:web-generation {torneo_id}';
    protected $description = 'Simular la generación de ronda desde la interfaz web';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        
        $this->info("Simulando generación de ronda desde la interfaz web para el torneo ID: {$torneoId}");
        
        // Verificar torneo
        $torneo = Torneo::find($torneoId);
        if (!$torneo) {
            $this->error("Torneo no encontrado");
            return;
        }
        
        $this->info("Torneo: {$torneo->nombre_torneo}");
        $this->info("Tipo: " . ($torneo->es_por_equipos ? 'Por equipos' : 'Individual'));
        $this->info("Tipo torneo: {$torneo->tipo_torneo}");
        
        if (!$torneo->es_por_equipos) {
            $this->error("Este torneo no es por equipos");
            return;
        }
        
        if ($torneo->tipo_torneo !== 'Eliminación Directa') {
            $this->error("Este torneo no es de eliminación directa");
            return;
        }
        
        try {
            if ($torneo->rondas()->count() >= $torneo->no_rondas) {
                $this->error("Ya se han generado todas las rondas del torneo");
                return;
            }

            if ($torneo->equipos()->count() < 2) {
                $this->error("Se necesitan al menos 2 equipos para generar emparejamientos");
                return;
            }

            $this->info("Validaciones pasadas, iniciando generación");

            DB::beginTransaction();

            // Detectar sistema de emparejamiento
            $sistema = strtolower(trim($torneo->emparejamiento->sistema ?? 'suizo'));
            $esEquipos = $torneo->es_por_equipos;
            $rondaActual = $torneo->rondas()->count() + 1;

            $this->info("Sistema: {$sistema}");
            $this->info("Es equipos: " . ($esEquipos ? 'Sí' : 'No'));
            $this->info("Ronda actual: {$rondaActual}");

            if ($torneo->tipo_torneo === 'Eliminación Directa') {
                $this->info("Procesando eliminación directa");
                
                if ($esEquipos) {
                    $this->info("Creando ronda para eliminación directa por equipos");
                    // Crear la ronda antes de generar los emparejamientos
                    $ronda = RondaTorneo::create([
                        'torneo_id' => $torneo->id,
                        'numero_ronda' => $rondaActual,
                        'fecha_hora' => now(),
                        'completada' => false
                    ]);
                    $this->info("Ronda creada ID: {$ronda->id}");
                    
                    $this->info("Llamando a generarEliminacionDirectaEquipos");
                    $this->generarEliminacionDirectaEquipos($torneo);
                    $this->info("generarEliminacionDirectaEquipos completado");
                }
            }

            DB::commit();
            $this->info("Transacción completada exitosamente");

            // Verificar resultados
            $rondasCreadas = $torneo->rondas()->count();
            $matchesCreados = EquipoMatch::where('torneo_id', $torneo->id)->count();
            $partidasCreadas = PartidaIndividual::whereHas('match', function($q) use ($torneo) {
                $q->where('torneo_id', $torneo->id);
            })->count();

            $this->info("Resultados:");
            $this->info("- Rondas creadas: {$rondasCreadas}");
            $this->info("- Matches creados: {$matchesCreados}");
            $this->info("- Partidas creadas: {$partidasCreadas}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error al generar ronda: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    private function generarEliminacionDirectaEquipos(Torneo $torneo)
    {
        return DB::transaction(function () use ($torneo) {
            $this->info("[EliminacionDirectaEquipos] Iniciando generación de eliminación directa por equipos");

            // Obtener todos los equipos del torneo
            $equipos = $torneo->equipos()->orderBy('id')->get();
            $equiposIds = $equipos->pluck('id')->toArray();

            $this->info("[EliminacionDirectaEquipos] Equipos encontrados: " . implode(', ', $equiposIds));
            $this->info("[EliminacionDirectaEquipos] Total de equipos: " . count($equiposIds));

            // Si el número de equipos es impar, agregar un BYE (null)
            if (count($equiposIds) % 2 !== 0) {
                $equiposIds[] = null;
                $this->info("[EliminacionDirectaEquipos] Agregado BYE - Total equipos: " . count($equiposIds));
            }

            // Obtener la ronda que ya fue creada
            $rondaTorneo = $torneo->rondas()->orderBy('numero_ronda', 'desc')->first();
            if (!$rondaTorneo) {
                $this->error("[EliminacionDirectaEquipos] No se encontró la ronda para el torneo");
                throw new \Exception('No se encontró la ronda para el torneo');
            }

            $this->info("[EliminacionDirectaEquipos] Usando ronda existente: {$rondaTorneo->numero_ronda}");

            // Verificar si ya existen matches para esta ronda
            $matchesExistentes = EquipoMatch::where('torneo_id', $torneo->id)
                ->where('ronda', $rondaTorneo->numero_ronda)
                ->count();
            
            $this->info("[EliminacionDirectaEquipos] Matches existentes para la ronda: {$matchesExistentes}");
            
            if ($matchesExistentes > 0) {
                $this->warn("[EliminacionDirectaEquipos] Ya existen matches para esta ronda, no se crean de nuevo");
                return $rondaTorneo;
            }

            // Emparejar y crear matches
            for ($i = 0; $i < count($equiposIds); $i += 2) {
                $equipo1 = $equiposIds[$i];
                $equipo2 = $equiposIds[$i + 1] ?? null;

                $this->info("[EliminacionDirectaEquipos] Procesando emparejamiento " . ($i/2 + 1) . ": Equipo1={$equipo1}, Equipo2={$equipo2}");

                if ($equipo1 && $equipo2) {
                    // Crear match normal entre dos equipos
                    $match = EquipoMatch::create([
                        'torneo_id' => $torneo->id,
                        'ronda' => $rondaTorneo->numero_ronda,
                        'equipo_a_id' => $equipo1,
                        'equipo_b_id' => $equipo2,
                        'mesa' => ($i / 2) + 1
                    ]);

                    $this->info("[EliminacionDirectaEquipos] Match creado ID: {$match->id} - Mesa: " . ($i / 2) + 1);

                    // Obtener los equipos reales para crear las partidas individuales
                    $equipoA = $equipos->find($equipo1);
                    $equipoB = $equipos->find($equipo2);

                    // Obtener jugadores ordenados por tablero
                    $jugadoresA = $equipoA->jugadores()->orderBy('tablero')->get();
                    $jugadoresB = $equipoB->jugadores()->orderBy('tablero')->get();

                    $this->info("[EliminacionDirectaEquipos] Jugadores equipo A ({$equipoA->nombre}): " . $jugadoresA->count() . ", Jugadores equipo B ({$equipoB->nombre}): " . $jugadoresB->count());

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

                        $this->info("[EliminacionDirectaEquipos] Partida creada ID: {$partida->id} - Tablero: {$tableroNum} - Blancas: {$blancas->miembro_id}, Negras: {$negras->miembro_id}");
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

                    $this->info("[EliminacionDirectaEquipos] BYE creado para equipo {$equipo1} - Match ID: {$match->id}");

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

                        $this->info("[EliminacionDirectaEquipos] Partida BYE creada ID: {$partida->id} - Jugador: {$jugador->miembro_id}");
                    }
                }
            }

            $matchesCreados = EquipoMatch::where('torneo_id', $torneo->id)->where('ronda', $rondaTorneo->numero_ronda)->count();
            $partidasCreadas = PartidaIndividual::whereHas('match', function($q) use ($torneo, $rondaTorneo) {
                $q->where('torneo_id', $torneo->id)->where('ronda', $rondaTorneo->numero_ronda);
            })->count();

            $this->info('[EliminacionDirectaEquipos] FIN generarEliminacionDirectaEquipos');
            $this->info("Ronda ID: {$rondaTorneo->id}");
            $this->info("Matches creados: {$matchesCreados}");
            $this->info("Partidas creadas: {$partidasCreadas}");

            return $rondaTorneo;
        });
    }
} 