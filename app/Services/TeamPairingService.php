<?php

namespace App\Services;

use App\Models\Torneo;
use App\Models\EquipoTorneo;
use App\Models\EquipoMatch;
use App\Models\PartidaIndividual;
use Illuminate\Support\Collection;
use App\Traits\PairingValidations;
use Illuminate\Support\Facades\Log;

class TeamPairingService
{
    use PairingValidations;

    private Torneo $torneo;
    private Collection $equipos;
    private Collection $matchesAnteriores;
    public array $warnings = [];

    public function __construct(Torneo $torneo)
    {
        $this->torneo = $torneo;
        $this->equipos = $torneo->equipos()
            ->with('jugadores.miembro')
            ->get();
        $this->matchesAnteriores = $torneo->equipoMatches()
            ->with(['equipoA', 'equipoB'])
            ->get();
    }

    public function generarEmparejamientos(int $ronda): array
    {
        Log::info('== INICIO GENERAR EMPAREJAMIENTOS EQUIPOS ==');
        // 1. Ordenar equipos por puntaje
        $equiposOrdenados = $this->equipos
            ->sortByDesc('puntos')
            ->values();

        $emparejamientos = [];
        $byeAsignado = false;
        $equiposDisponibles = $equiposOrdenados;

        // 2. Procesar emparejamientos
        while ($equiposDisponibles->count() >= 2) {
            $equipoA = $equiposDisponibles->first();
            $oponenteEncontrado = false;

            foreach ($equiposDisponibles->skip(1) as $equipoB) {
                if ($equipoA->id === $equipoB->id) continue;
                if ($this->esOponenteValido($equipoA, $equipoB, $ronda)) {
                    Log::info('Emparejando equipoA: ' . $equipoA->nombre . ' con equipoB: ' . $equipoB->nombre);
                    $tableros = $this->asignarColoresTableros($equipoA, $equipoB, $ronda);
                    $emparejamientos[] = [
                        'equipo_a' => $equipoA,
                        'equipo_b' => $equipoB,
                        'tableros' => $tableros
                    ];

                    // Crear el match y las partidas individuales
                    $match = EquipoMatch::create([
                        'torneo_id' => $this->torneo->id,
                        'ronda' => $ronda,
                        'equipo_a_id' => $equipoA->id,
                        'equipo_b_id' => $equipoB->id,
                        'mesa' => 0
                    ]);
                    foreach ($tableros as $tablero) {
                        if (!$tablero['blancas'] || !$tablero['negras']) continue;
                        PartidaIndividual::create([
                            'equipo_match_id' => $match->id,
                            'jugador_a_id' => $tablero['blancas']->miembro_id,
                            'jugador_b_id' => $tablero['negras']->miembro_id,
                            'tablero' => $tablero['tablero']
                        ]);
                    }

                    $equiposDisponibles = $equiposDisponibles->filter(function($e) use ($equipoA, $equipoB) {
                        return $e->id !== $equipoA->id && $e->id !== $equipoB->id;
                    });

                    $oponenteEncontrado = true;
                    break;
                }
            }

            if (!$oponenteEncontrado) {
                Log::info('No se encontró oponente para equipo: ' . $equipoA->nombre);
                $this->moverEquipoFlotante($equipoA, $equiposDisponibles);
            }
        }

        // 3. Manejar bye
        if ($equiposDisponibles->count() === 1 && !$byeAsignado) {
            $equipoBye = $equiposDisponibles->first();
            if ($this->validarByeRepetido($equipoBye)) {
                Log::info('Asignando BYE a equipo: ' . $equipoBye->nombre);
                $match = EquipoMatch::create([
                    'torneo_id' => $this->torneo->id,
                    'ronda' => $ronda,
                    'equipo_a_id' => $equipoBye->id,
                    'equipo_b_id' => null,
                    'resultado_match' => 1, // Victoria por bye
                    'mesa' => 0 // Mesa especial para bye
                ]);
                // Asignar victorias a todos los jugadores del equipo
                foreach ($equipoBye->jugadores as $jugador) {
                    PartidaIndividual::create([
                        'equipo_match_id' => $match->id,
                        'jugador_a_id' => $jugador->miembro_id,
                        'jugador_b_id' => null,
                        'resultado' => 1, // Victoria por bye
                        'tablero' => $jugador->tablero
                    ]);
                }
                $byeAsignado = true;
            }
        }

        Log::info('Emparejamientos generados: ' . count($emparejamientos));
        return $emparejamientos;
    }

    private function esOponenteValido($equipoA, $equipoB, $ronda): bool
    {
        return !$this->yaSeEnfrentaronEquipos($equipoA, $equipoB);
    }

    private function yaSeEnfrentaronEquipos($equipoA, $equipoB): bool
    {
        return $this->matchesAnteriores->contains(function($match) use ($equipoA, $equipoB) {
            return ($match->equipo_a_id === $equipoA->id && $match->equipo_b_id === $equipoB->id) ||
                   ($match->equipo_a_id === $equipoB->id && $match->equipo_b_id === $equipoA->id);
        });
    }

    private function asignarColoresTableros($equipoA, $equipoB, $ronda): array
    {
        $tableros = [];
        // Filtrar y reindexar: solo un jugador por cada número de tablero (del 1 al N), forzando a entero
        $jugadoresA = $equipoA->jugadores
            ->filter(function($j) { return $j->tablero !== null; })
            ->map(function($j) { $j->tablero = (int)$j->tablero; return $j; })
            ->unique('tablero')
            ->sortBy('tablero', SORT_NUMERIC)
            ->values();
        $jugadoresB = $equipoB->jugadores
            ->filter(function($j) { return $j->tablero !== null; })
            ->map(function($j) { $j->tablero = (int)$j->tablero; return $j; })
            ->unique('tablero')
            ->sortBy('tablero', SORT_NUMERIC)
            ->values();
        $numTableros = min($jugadoresA->count(), $jugadoresB->count());
        $esRondaPar = $ronda % 2 === 0;

        for ($i = 0; $i < $numTableros; $i++) {
            $jugadorA = $jugadoresA[$i];
            $jugadorB = $jugadoresB[$i];
            $tableroNum = $i + 1;
            $esTableroImpar = $tableroNum % 2 === 1;
            if ($esRondaPar) {
                $tableros[] = [
                    'tablero' => $tableroNum,
                    'blancas' => $esTableroImpar ? $jugadorA : $jugadorB,
                    'negras' => $esTableroImpar ? $jugadorB : $jugadorA
                ];
            } else {
                $tableros[] = [
                    'tablero' => $tableroNum,
                    'blancas' => $esTableroImpar ? $jugadorB : $jugadorA,
                    'negras' => $esTableroImpar ? $jugadorA : $jugadorB
                ];
            }
        }
        return $tableros;
    }

    private function moverEquipoFlotante($equipo, &$equiposDisponibles)
    {
        $indiceActual = $equiposDisponibles->search($equipo);
        if ($indiceActual < $equiposDisponibles->count() - 1) {
            $equiposDisponibles = $equiposDisponibles->splice($indiceActual, 1);
            $equiposDisponibles->push($equipo);
        }
    }
} 