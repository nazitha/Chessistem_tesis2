<?php

namespace App\Traits;

use App\Models\PartidaTorneo;
use App\Models\ParticipanteTorneo;

trait PairingValidations
{
    protected function validarByeRepetido(ParticipanteTorneo $participante): bool
    {
        $byesAnteriores = $this->torneo->partidas()
            ->where(function($query) use ($participante) {
                $query->where('jugador_blancas_id', $participante->miembro_id)
                      ->whereNull('jugador_negras_id');
            })
            ->count();

        return $byesAnteriores < $this->torneo->max_byes_por_jugador ?? 1;
    }

    protected function validarColoresConsecutivos(ParticipanteTorneo $participante, string $color): bool
    {
        $ultimasPartidas = $this->torneo->partidas()
            ->where(function($query) use ($participante) {
                $query->where('jugador_blancas_id', $participante->miembro_id)
                      ->orWhere('jugador_negras_id', $participante->miembro_id);
            })
            ->orderByDesc('ronda')
            ->take(2)
            ->get();

        if ($ultimasPartidas->count() < 2) {
            return true;
        }

        $coloresConsecutivos = $ultimasPartidas->map(function($partida) use ($participante) {
            return $partida->jugador_blancas_id === $participante->miembro_id ? 'B' : 'N';
        });

        return !($coloresConsecutivos->every(fn($c) => $c === $color));
    }

    protected function validarEmparejamientoRepetido(ParticipanteTorneo $jugadorA, ParticipanteTorneo $jugadorB): bool
    {
        $emparejamientosAnteriores = $this->torneo->partidas()
            ->where(function($query) use ($jugadorA, $jugadorB) {
                $query->where(function($q) use ($jugadorA, $jugadorB) {
                    $q->where('jugador_blancas_id', $jugadorA->miembro_id)
                      ->where('jugador_negras_id', $jugadorB->miembro_id);
                })->orWhere(function($q) use ($jugadorA, $jugadorB) {
                    $q->where('jugador_blancas_id', $jugadorB->miembro_id)
                      ->where('jugador_negras_id', $jugadorA->miembro_id);
                });
            })
            ->count();

        return $emparejamientosAnteriores < ($this->torneo->maximo_emparejamientos_repetidos ?? 1);
    }

    protected function validarDiferenciaPuntos(ParticipanteTorneo $jugadorA, ParticipanteTorneo $jugadorB, int $ronda): bool
    {
        $diferenciaMaxima = $this->torneo->diferencia_maxima_puntos ?? 2;
        $diferenciaActual = abs($jugadorA->puntos - $jugadorB->puntos);

        // En las primeras rondas, permitir mayor diferencia
        if ($ronda <= 2) {
            $diferenciaMaxima += 1;
        }

        return $diferenciaActual <= $diferenciaMaxima;
    }
} 