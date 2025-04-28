<?php

namespace App\Services;

use App\Models\Torneo;
use App\Models\ParticipanteTorneo;
use App\Models\PartidaTorneo;
use Illuminate\Support\Collection;

class SwissPairingService
{
    private Torneo $torneo;
    private Collection $participantes;
    private Collection $partidasAnteriores;

    public function __construct(Torneo $torneo)
    {
        $this->torneo = $torneo;
        $this->participantes = $torneo->participantes()->with('miembro')->get();
        $this->partidasAnteriores = collect();
        
        foreach ($torneo->rondas as $ronda) {
            $this->partidasAnteriores = $this->partidasAnteriores->merge($ronda->partidas);
        }
    }

    public function generarEmparejamientos(int $ronda): array
    {
        // 1. Ordenar jugadores por puntos y elo
        $participantesOrdenados = $this->participantes
            ->sortByDesc('puntos')
            ->sortByDesc(function($participante) {
                return $participante->miembro->elo ?? 0;
            })
            ->values();

        $emparejamientos = [];
        $participantesEmparejados = collect();
        $grupo = $participantesOrdenados;

        // 2. Manejar número impar de participantes
        if ($grupo->count() % 2 !== 0 && $this->torneo->permitir_bye) {
            // Asignar bye al jugador con menor puntuación
            $jugadorBye = $grupo->last();
            $emparejamientos[] = [
                'blancas' => $jugadorBye,
                'negras' => null
            ];
            $participantesEmparejados->push($jugadorBye->id);
            $grupo = $grupo->filter(function($p) use ($jugadorBye) {
                return $p->id !== $jugadorBye->id;
            });
        }

        // 3. Realizar emparejamientos
        while ($grupo->isNotEmpty()) {
            $jugadorA = $grupo->first();
            $oponenteEncontrado = false;

            // Buscar oponente que no haya jugado antes y balancee colores
            foreach ($grupo->skip(1) as $jugadorB) {
                if ($this->esOponenteValido($jugadorA, $jugadorB, $participantesEmparejados, $ronda)) {
                    $emparejamientos[] = [
                        'blancas' => $jugadorA,
                        'negras' => $jugadorB
                    ];
                    $participantesEmparejados->push($jugadorA->id, $jugadorB->id);
                    $grupo = $grupo->filter(function($p) use ($jugadorA, $jugadorB) {
                        return $p->id !== $jugadorA->id && $p->id !== $jugadorB->id;
                    });
                    $oponenteEncontrado = true;
                    break;
                }
            }

            // Si no se encontró oponente, intentar bajar al siguiente grupo
            if (!$oponenteEncontrado) {
                $diferenciaPuntosMaxima = 1;
                $intentos = 0;
                $maxIntentos = 3;

                while ($intentos < $maxIntentos && !$oponenteEncontrado) {
                    foreach ($grupo->skip(1) as $jugadorB) {
                        if (abs($jugadorA->puntos - $jugadorB->puntos) <= $diferenciaPuntosMaxima &&
                            $this->esOponenteValido($jugadorA, $jugadorB, $participantesEmparejados, $ronda)) {
                            $emparejamientos[] = [
                                'blancas' => $jugadorA,
                                'negras' => $jugadorB
                            ];
                            $participantesEmparejados->push($jugadorA->id, $jugadorB->id);
                            $grupo = $grupo->filter(function($p) use ($jugadorA, $jugadorB) {
                                return $p->id !== $jugadorA->id && $p->id !== $jugadorB->id;
                            });
                            $oponenteEncontrado = true;
                            break;
                        }
                    }
                    $diferenciaPuntosMaxima++;
                    $intentos++;
                }

                // Si aún no se encontró oponente, asignar bye si está permitido
                if (!$oponenteEncontrado && $this->torneo->permitir_bye) {
                    $emparejamientos[] = [
                        'blancas' => $jugadorA,
                        'negras' => null
                    ];
                    $participantesEmparejados->push($jugadorA->id);
                    $grupo = $grupo->filter(function($p) use ($jugadorA) {
                        return $p->id !== $jugadorA->id;
                    });
                }
            }
        }

        return $emparejamientos;
    }

    private function esOponenteValido(
        ParticipanteTorneo $participante,
        ParticipanteTorneo $oponente,
        Collection $participantesEmparejados,
        int $ronda
    ): bool {
        // No emparejar consigo mismo
        if ($participante->id === $oponente->id) {
            return false;
        }

        // Verificar si ya está emparejado
        if ($participantesEmparejados->contains($oponente->id)) {
            return false;
        }

        // Verificar emparejamientos anteriores
        if ($this->torneo->evitar_emparejamientos_repetidos) {
            $emparejamientosAnteriores = $this->partidasAnteriores->filter(function ($partida) use ($participante, $oponente) {
                return ($partida->jugador_blancas_id === $participante->miembro_id && 
                        $partida->jugador_negras_id === $oponente->miembro_id) ||
                       ($partida->jugador_blancas_id === $oponente->miembro_id && 
                        $partida->jugador_negras_id === $participante->miembro_id);
            });

            if ($emparejamientosAnteriores->count() >= $this->torneo->maximo_emparejamientos_repetidos) {
                return false;
            }
        }

        // Verificar colores
        if ($this->torneo->alternar_colores) {
            $ultimaPartidaParticipante = $this->partidasAnteriores->filter(function ($partida) use ($participante) {
                return $partida->jugador_blancas_id === $participante->miembro_id ||
                       $partida->jugador_negras_id === $participante->miembro_id;
            })->sortByDesc('ronda')->first();

            $ultimaPartidaOponente = $this->partidasAnteriores->filter(function ($partida) use ($oponente) {
                return $partida->jugador_blancas_id === $oponente->miembro_id ||
                       $partida->jugador_negras_id === $oponente->miembro_id;
            })->sortByDesc('ronda')->first();

            if ($ultimaPartidaParticipante && $ultimaPartidaOponente) {
                $participanteJugoBlancas = $ultimaPartidaParticipante->jugador_blancas_id === $participante->miembro_id;
                $oponenteJugoBlancas = $ultimaPartidaOponente->jugador_blancas_id === $oponente->miembro_id;

                if ($participanteJugoBlancas === $oponenteJugoBlancas) {
                    return false;
                }
            }
        }

        return true;
    }
} 