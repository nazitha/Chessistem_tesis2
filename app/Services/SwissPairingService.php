<?php

namespace App\Services;

use App\Models\Torneo;
use App\Models\Participante;
use App\Models\Partida;
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
        $this->partidasAnteriores = $torneo->partidas()->get();
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
                    'participante1' => $participante,
                    'participante2' => $oponente,
                    'mesa' => count($emparejamientos) + 1,
                    'ronda' => $ronda
                ];
                $participantesEmparejados->push($participante->id, $oponente->id);
            } elseif ($this->torneo->permitir_bye) {
                // Asignar bye si está permitido
                $emparejamientos[] = [
                    'participante1' => $participante,
                    'participante2' => null,
                    'mesa' => count($emparejamientos) + 1,
                    'ronda' => $ronda,
                    'bye' => true
                ];
                $participantesEmparejados->push($participante->id);
            }
        }

        return $emparejamientos;
    }

    private function buscarOponente(
        Participante $participante,
        Collection $participantesOrdenados,
        Collection $participantesEmparejados,
        int $ronda
    ): ?Participante {
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
        Participante $participante,
        Participante $oponente,
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
            $emparejamientosAnteriores = $this->partidasAnteriores
                ->where('ronda', '<', $ronda)
                ->filter(function ($partida) use ($participante, $oponente) {
                    return ($partida->participante_id === $participante->miembro_id && 
                            $partida->oponente_id === $oponente->miembro_id) ||
                           ($partida->participante_id === $oponente->miembro_id && 
                            $partida->oponente_id === $participante->miembro_id);
                });

            if ($emparejamientosAnteriores->count() >= $this->torneo->maximo_emparejamientos_repetidos) {
                return false;
            }
        }

        // Verificar colores si está configurado
        if ($this->torneo->alternar_colores) {
            $ultimaPartida = $this->partidasAnteriores
                ->where('participante_id', $participante->miembro_id)
                ->sortByDesc('ronda')
                ->first();

            if ($ultimaPartida && $ultimaPartida->color === true) {
                // El último color fue blancas, buscar oponente que jugó negras
                $ultimaPartidaOponente = $this->partidasAnteriores
                    ->where('participante_id', $oponente->miembro_id)
                    ->sortByDesc('ronda')
                    ->first();

                if ($ultimaPartidaOponente && $ultimaPartidaOponente->color === true) {
                    return false;
                }
            }
        }

        return true;
    }
} 