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
        // Ordenar participantes por puntos
        $participantesOrdenados = $this->participantes
            ->sortByDesc('puntos')
            ->values();

        $emparejamientos = [];
        $participantesEmparejados = collect();

        foreach ($participantesOrdenados as $participante) {
            if ($participantesEmparejados->contains($participante->id)) {
                continue;
            }

            // Buscar oponente
            $oponente = $this->buscarOponente(
                $participante,
                $participantesOrdenados,
                $participantesEmparejados,
                $ronda
            );

            if ($oponente) {
                $emparejamientos[] = [
                    'blancas' => $participante,
                    'negras' => $oponente
                ];
                $participantesEmparejados->push($participante->id, $oponente->id);
            } elseif ($this->torneo->permitir_bye) {
                // Asignar bye si está permitido
                $emparejamientos[] = [
                    'blancas' => $participante,
                    'negras' => null
                ];
                $participantesEmparejados->push($participante->id);
            }
        }

        return $emparejamientos;
    }

    private function buscarOponente(
        ParticipanteTorneo $participante,
        Collection $participantesOrdenados,
        Collection $participantesEmparejados,
        int $ronda
    ): ?ParticipanteTorneo {
        $diferenciaPuntosMaxima = 1;
        $intentos = 0;
        $maxIntentos = 3;

        while ($intentos < $maxIntentos) {
            foreach ($participantesOrdenados as $posibleOponente) {
                if ($this->esOponenteValido($participante, $posibleOponente, $participantesEmparejados, $ronda, $diferenciaPuntosMaxima)) {
                    return $posibleOponente;
                }
            }
            $diferenciaPuntosMaxima++;
            $intentos++;
        }

        return null;
    }

    private function esOponenteValido(
        ParticipanteTorneo $participante,
        ParticipanteTorneo $oponente,
        Collection $participantesEmparejados,
        int $ronda,
        int $diferenciaPuntosMaxima
    ): bool {
        // No emparejar consigo mismo
        if ($participante->id === $oponente->id) {
            return false;
        }

        // Verificar si ya está emparejado
        if ($participantesEmparejados->contains($oponente->id)) {
            return false;
        }

        // Verificar diferencia de puntos
        if (abs($participante->puntos - $oponente->puntos) > $diferenciaPuntosMaxima) {
            return false;
        }

        // Verificar emparejamientos anteriores si está configurado
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

        // Verificar colores si está configurado
        if ($this->torneo->alternar_colores) {
            // Obtener la última partida del participante
            $ultimaPartidaParticipante = $this->partidasAnteriores->filter(function ($partida) use ($participante) {
                return $partida->jugador_blancas_id === $participante->miembro_id ||
                       $partida->jugador_negras_id === $participante->miembro_id;
            })->sortByDesc('ronda')->first();

            // Obtener la última partida del oponente
            $ultimaPartidaOponente = $this->partidasAnteriores->filter(function ($partida) use ($oponente) {
                return $partida->jugador_blancas_id === $oponente->miembro_id ||
                       $partida->jugador_negras_id === $oponente->miembro_id;
            })->sortByDesc('ronda')->first();

            if ($ultimaPartidaParticipante && $ultimaPartidaOponente) {
                $participanteJugoBlancas = $ultimaPartidaParticipante->jugador_blancas_id === $participante->miembro_id;
                $oponenteJugoBlancas = $ultimaPartidaOponente->jugador_blancas_id === $oponente->miembro_id;

                // Si ambos jugaron con el mismo color en su última partida
                if ($participanteJugoBlancas === $oponenteJugoBlancas) {
                    return false;
                }
            }
        }

        return true;
    }
} 