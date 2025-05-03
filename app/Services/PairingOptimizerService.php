<?php

namespace App\Services;

use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\ParticipanteTorneo;
use App\Models\EquipoTorneo;
use Illuminate\Support\Collection;

class PairingOptimizerService
{
    private Torneo $torneo;
    private Collection $participantes;
    private array $mejorEmparejamiento;
    private float $mejorPuntuacion = PHP_FLOAT_MAX;

    public function __construct(Torneo $torneo)
    {
        $this->torneo = $torneo;
        $this->participantes = $torneo->participantes()
            ->where('activo', true)
            ->get();
    }

    public function optimizarEmparejamientos(RondaTorneo $ronda): array
    {
        $this->generarCombinaciones([], $this->participantes->all());
        return $this->mejorEmparejamiento;
    }

    private function generarCombinaciones(array $emparejamientoActual, array $participantesRestantes): void
    {
        if (empty($participantesRestantes)) {
            $puntuacion = $this->calcularPuntuacion($emparejamientoActual);
            if ($puntuacion < $this->mejorPuntuacion) {
                $this->mejorPuntuacion = $puntuacion;
                $this->mejorEmparejamiento = $emparejamientoActual;
            }
            return;
        }

        $jugadorA = array_shift($participantesRestantes);
        foreach ($participantesRestantes as $i => $jugadorB) {
            if ($this->esOponenteValido($jugadorA, $jugadorB)) {
                $nuevoEmparejamiento = $emparejamientoActual;
                $nuevoEmparejamiento[] = [
                    'blancas' => $jugadorA,
                    'negras' => $jugadorB
                ];

                $restantes = $participantesRestantes;
                unset($restantes[$i]);
                $restantes = array_values($restantes);

                $this->generarCombinaciones($nuevoEmparejamiento, $restantes);
            }
        }

        // Considerar bye si es necesario
        if ($this->torneo->permitir_bye && $this->validarByeRepetido($jugadorA)) {
            $nuevoEmparejamiento = $emparejamientoActual;
            $nuevoEmparejamiento[] = [
                'blancas' => $jugadorA,
                'negras' => null
            ];

            $this->generarCombinaciones($nuevoEmparejamiento, $participantesRestantes);
        }
    }

    private function calcularPuntuacion(array $emparejamiento): float
    {
        $puntuacion = 0;

        foreach ($emparejamiento as $match) {
            if ($match['negras'] === null) {
                // Penalizar byes
                $puntuacion += 10;
                continue;
            }

            // Penalizar diferencia de puntos
            $diferenciaPuntos = abs($match['blancas']->puntos - $match['negras']->puntos);
            $puntuacion += $diferenciaPuntos * 2;

            // Penalizar desbalance de colores
            $blancasA = $this->conteoBlancas($match['blancas']);
            $negrasA = $this->conteoNegras($match['blancas']);
            $blancasB = $this->conteoBlancas($match['negras']);
            $negrasB = $this->conteoNegras($match['negras']);

            $puntuacion += abs(($blancasA - $negrasA) + ($blancasB - $negrasB));

            // Penalizar enfrentamientos repetidos
            if ($this->yaSeEnfrentaron($match['blancas'], $match['negras'])) {
                $puntuacion += 5;
            }
        }

        return $puntuacion;
    }

    private function esOponenteValido($jugadorA, $jugadorB): bool
    {
        return !$this->yaSeEnfrentaron($jugadorA, $jugadorB) &&
               abs($jugadorA->puntos - $jugadorB->puntos) <= $this->torneo->diferencia_maxima_puntos;
    }

    private function validarByeRepetido($participante): bool
    {
        return $this->torneo->partidas()
            ->where('jugador_blancas_id', $participante->id)
            ->whereNull('jugador_negras_id')
            ->count() < $this->torneo->max_byes_por_jugador;
    }

    private function yaSeEnfrentaron($jugadorA, $jugadorB): bool
    {
        return $this->torneo->partidas()
            ->where(function($query) use ($jugadorA, $jugadorB) {
                $query->where('jugador_blancas_id', $jugadorA->id)
                      ->where('jugador_negras_id', $jugadorB->id);
            })
            ->orWhere(function($query) use ($jugadorA, $jugadorB) {
                $query->where('jugador_blancas_id', $jugadorB->id)
                      ->where('jugador_negras_id', $jugadorA->id);
            })
            ->exists();
    }

    private function conteoBlancas($jugador): int
    {
        return $this->torneo->partidas()
            ->where('jugador_blancas_id', $jugador->id)
            ->count();
    }

    private function conteoNegras($jugador): int
    {
        return $this->torneo->partidas()
            ->where('jugador_negras_id', $jugador->id)
            ->count();
    }
} 