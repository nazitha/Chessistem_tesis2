<?php

namespace App\Services;

use App\Models\Torneo;
use App\Models\EquipoTorneo;
use App\Models\EquipoMatch;
use App\Models\PartidaIndividual;
use Illuminate\Support\Collection;
use App\Traits\PairingValidations;

class TeamPairingService
{
    use PairingValidations;

    private Torneo $torneo;
    private Collection $equipos;
    private Collection $matchesAnteriores;

    public function __construct(Torneo $torneo)
    {
        $this->torneo = $torneo;
        $this->equipos = $torneo->equipos()
            ->with('jugadores.miembro')
            ->where('activo', true)
            ->get();
        $this->matchesAnteriores = $torneo->equipoMatches()
            ->with(['equipoA', 'equipoB'])
            ->get();
    }

    public function generarEmparejamientos(int $ronda): array
    {
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
                if ($this->esOponenteValido($equipoA, $equipoB, $ronda)) {
                    $emparejamientos[] = [
                        'equipo_a' => $equipoA,
                        'equipo_b' => $equipoB,
                        'tableros' => $this->asignarColoresTableros($equipoA, $equipoB, $ronda)
                    ];

                    $equiposDisponibles = $equiposDisponibles->filter(function($e) use ($equipoA, $equipoB) {
                        return $e->id !== $equipoA->id && $e->id !== $equipoB->id;
                    });

                    $oponenteEncontrado = true;
                    break;
                }
            }

            if (!$oponenteEncontrado) {
                $this->moverEquipoFlotante($equipoA, $equiposDisponibles);
            }
        }

        // 3. Manejar bye
        if ($equiposDisponibles->count() === 1 && !$byeAsignado) {
            $equipoBye = $equiposDisponibles->first();
            if ($this->validarByeRepetido($equipoBye)) {
                $this->asignarByeEquipo($equipoBye, $ronda);
                $byeAsignado = true;
            }
        }

        return $emparejamientos;
    }

    private function esOponenteValido($equipoA, $equipoB, $ronda): bool
    {
        return !$this->yaSeEnfrentaronEquipos($equipoA, $equipoB) &&
               $this->validarDiferenciaPuntos($equipoA, $equipoB, $ronda);
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
        $numTableros = $equipoA->jugadores->count();
        $esRondaPar = $ronda % 2 === 0;

        for ($i = 1; $i <= $numTableros; $i++) {
            $esTableroImpar = $i % 2 === 1;
            
            if ($esRondaPar) {
                $tableros[] = [
                    'tablero' => $i,
                    'blancas' => $esTableroImpar ? $equipoA : $equipoB,
                    'negras' => $esTableroImpar ? $equipoB : $equipoA
                ];
            } else {
                $tableros[] = [
                    'tablero' => $i,
                    'blancas' => $esTableroImpar ? $equipoB : $equipoA,
                    'negras' => $esTableroImpar ? $equipoA : $equipoB
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

    private function asignarByeEquipo($equipo, $ronda)
    {
        $match = EquipoMatch::create([
            'torneo_id' => $this->torneo->id,
            'ronda' => $ronda,
            'equipo_a_id' => $equipo->id,
            'equipo_b_id' => null,
            'resultado_match' => 1, // Victoria por bye
            'mesa' => 0 // Mesa especial para bye
        ]);

        // Asignar victorias a todos los jugadores del equipo
        foreach ($equipo->jugadores as $jugador) {
            PartidaIndividual::create([
                'equipo_match_id' => $match->id,
                'jugador_blancas_id' => $jugador->miembro_id,
                'jugador_negras_id' => null,
                'resultado' => 1, // Victoria por bye
                'tablero' => $jugador->tablero
            ]);
        }
    }
} 