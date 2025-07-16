<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EquipoMatch;
use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\PartidaIndividual;
use Illuminate\Support\Facades\Log;

class TestGenerateMatches extends Command
{
    protected $signature = 'test:generate-matches {torneo_id}';
    protected $description = 'Probar generación de matches para un torneo';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        
        $this->info("Probando generación de matches para el torneo ID: {$torneoId}");
        
        // Verificar torneo
        $torneo = Torneo::find($torneoId);
        if (!$torneo) {
            $this->error("Torneo no encontrado");
            return;
        }
        
        $this->info("Torneo: {$torneo->nombre_torneo}");
        $this->info("Tipo: " . ($torneo->es_por_equipos ? 'Por equipos' : 'Individual'));
        
        if (!$torneo->es_por_equipos) {
            $this->error("Este torneo no es por equipos");
            return;
        }
        
        // Obtener equipos
        $equipos = $torneo->equipos()->orderBy('id')->get();
        $equiposIds = $equipos->pluck('id')->toArray();
        
        $this->info("Equipos encontrados: " . implode(', ', $equiposIds));
        $this->info("Total de equipos: " . count($equiposIds));
        
        // Si el número de equipos es impar, agregar un BYE (null)
        if (count($equiposIds) % 2 !== 0) {
            $equiposIds[] = null;
            $this->info("Agregado BYE - Total equipos: " . count($equiposIds));
        }
        
        // Obtener o crear la ronda
        $rondaTorneo = $torneo->rondas()->orderBy('numero_ronda', 'desc')->first();
        if (!$rondaTorneo) {
            $rondaTorneo = RondaTorneo::create([
                'torneo_id' => $torneo->id,
                'numero_ronda' => 1,
                'fecha_hora' => now(),
                'completada' => false
            ]);
            $this->info("Ronda creada: {$rondaTorneo->numero_ronda}");
        } else {
            $this->info("Usando ronda existente: {$rondaTorneo->numero_ronda}");
        }
        
        // Verificar si ya existen matches para esta ronda
        $matchesExistentes = EquipoMatch::where('torneo_id', $torneo->id)
            ->where('ronda', $rondaTorneo->numero_ronda)
            ->count();
        
        $this->info("Matches existentes para la ronda: {$matchesExistentes}");
        
        if ($matchesExistentes > 0) {
            $this->warn("Ya existen matches para esta ronda");
            return;
        }
        
        // Emparejar y crear matches
        for ($i = 0; $i < count($equiposIds); $i += 2) {
            $equipo1 = $equiposIds[$i];
            $equipo2 = $equiposIds[$i + 1] ?? null;
            
            $this->info("Procesando emparejamiento " . ($i/2 + 1) . ": Equipo1={$equipo1}, Equipo2={$equipo2}");
            
            if ($equipo1 && $equipo2) {
                // Crear match normal entre dos equipos
                $match = EquipoMatch::create([
                    'torneo_id' => $torneo->id,
                    'ronda' => $rondaTorneo->numero_ronda,
                    'equipo_a_id' => $equipo1,
                    'equipo_b_id' => $equipo2,
                    'mesa' => ($i / 2) + 1
                ]);
                
                $this->info("Match creado ID: {$match->id} - Mesa: " . ($i / 2) + 1);
                
                // Obtener los equipos reales para crear las partidas individuales
                $equipoA = $equipos->find($equipo1);
                $equipoB = $equipos->find($equipo2);
                
                // Obtener jugadores ordenados por tablero
                $jugadoresA = $equipoA->jugadores()->orderBy('tablero')->get();
                $jugadoresB = $equipoB->jugadores()->orderBy('tablero')->get();
                
                $this->info("Jugadores equipo A ({$equipoA->nombre}): " . $jugadoresA->count() . ", Jugadores equipo B ({$equipoB->nombre}): " . $jugadoresB->count());
                
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
                    
                    $this->info("Partida creada ID: {$partida->id} - Tablero: {$tableroNum} - Blancas: {$blancas->miembro_id}, Negras: {$negras->miembro_id}");
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
                
                $this->info("BYE creado para equipo {$equipo1} - Match ID: {$match->id}");
                
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
                    
                    $this->info("Partida BYE creada ID: {$partida->id} - Jugador: {$jugador->miembro_id}");
                }
            }
        }
        
        $matchesCreados = EquipoMatch::where('torneo_id', $torneo->id)->where('ronda', $rondaTorneo->numero_ronda)->count();
        $partidasCreadas = PartidaIndividual::whereHas('match', function($q) use ($torneo, $rondaTorneo) {
            $q->where('torneo_id', $torneo->id)->where('ronda', $rondaTorneo->numero_ronda);
        })->count();
        
        $this->info("FIN generación de matches");
        $this->info("Ronda ID: {$rondaTorneo->id}");
        $this->info("Matches creados: {$matchesCreados}");
        $this->info("Partidas creadas: {$partidasCreadas}");
    }
} 