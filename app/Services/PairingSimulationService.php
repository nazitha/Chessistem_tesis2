<?php

namespace App\Services;

use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Models\ParticipanteTorneo;
use App\Models\EquipoTorneo;
use App\Services\SwissPairingService;
use App\Services\TeamPairingService;
use Illuminate\Support\Collection;

class PairingSimulationService
{
    private Torneo $torneo;
    private Collection $advertencias;

    public function __construct(Torneo $torneo)
    {
        $this->torneo = $torneo;
        $this->advertencias = collect();
    }

    public function simularRonda(): array
    {
        $ronda = new RondaTorneo([
            'torneo_id' => $this->torneo->id,
            'numero_ronda' => $this->torneo->rondas->count() + 1
        ]);

        if ($this->torneo->es_por_equipos) {
            $service = new TeamPairingService($this->torneo);
        } else {
            $service = new SwissPairingService($this->torneo);
        }

        $emparejamientos = $service->generarEmparejamientos($ronda);
        $this->analizarEmparejamientos($emparejamientos);

        return [
            'emparejamientos' => $emparejamientos,
            'advertencias' => $this->advertencias->toArray()
        ];
    }

    private function analizarEmparejamientos(array $emparejamientos): void
    {
        foreach ($emparejamientos as $emparejamiento) {
            if ($this->torneo->es_por_equipos) {
                $this->analizarEmparejamientoEquipos($emparejamiento);
            } else {
                $this->analizarEmparejamientoIndividual($emparejamiento);
            }
        }
    }

    private function analizarEmparejamientoIndividual(array $emparejamiento): void
    {
        $blancas = $emparejamiento['blancas'];
        $negras = $emparejamiento['negras'];

        if (!$negras) {
            $this->advertencias->push([
                'tipo' => 'bye',
                'mensaje' => "El jugador {$blancas->miembro->nombre} recibirá un bye",
                'detalles' => [
                    'jugador' => $blancas->id,
                    'byes_anteriores' => $this->conteoByes($blancas)
                ]
            ]);
            return;
        }

        if ($this->yaSeEnfrentaron($blancas, $negras)) {
            $this->advertencias->push([
                'tipo' => 'enfrentamiento_repetido',
                'mensaje' => "Los jugadores {$blancas->miembro->nombre} y {$negras->miembro->nombre} ya se han enfrentado",
                'detalles' => [
                    'jugador_a' => $blancas->id,
                    'jugador_b' => $negras->id
                ]
            ]);
        }

        $diferenciaPuntos = abs($blancas->puntos - $negras->puntos);
        if ($diferenciaPuntos > $this->torneo->diferencia_maxima_puntos) {
            $this->advertencias->push([
                'tipo' => 'diferencia_puntos',
                'mensaje' => "Gran diferencia de puntos entre {$blancas->miembro->nombre} ({$blancas->puntos}) y {$negras->miembro->nombre} ({$negras->puntos})",
                'detalles' => [
                    'jugador_a' => $blancas->id,
                    'jugador_b' => $negras->id,
                    'diferencia' => $diferenciaPuntos
                ]
            ]);
        }

        if ($this->coloresDesequilibrados($blancas)) {
            $this->advertencias->push([
                'tipo' => 'colores_desequilibrados',
                'mensaje' => "El jugador {$blancas->miembro->nombre} tiene un desbalance significativo de colores",
                'detalles' => [
                    'jugador' => $blancas->id,
                    'blancas' => $this->conteoBlancas($blancas),
                    'negras' => $this->conteoNegras($blancas)
                ]
            ]);
        }
    }

    private function analizarEmparejamientoEquipos(array $emparejamiento): void
    {
        $equipoA = $emparejamiento['equipo_a'];
        $equipoB = $emparejamiento['equipo_b'];

        if (!$equipoB) {
            $this->advertencias->push([
                'tipo' => 'bye_equipo',
                'mensaje' => "El equipo {$equipoA->nombre} recibirá un bye",
                'detalles' => [
                    'equipo' => $equipoA->id,
                    'byes_anteriores' => $this->conteoByesEquipo($equipoA)
                ]
            ]);
            return;
        }

        if ($this->yaSeEnfrentaronEquipos($equipoA, $equipoB)) {
            $this->advertencias->push([
                'tipo' => 'enfrentamiento_equipos_repetido',
                'mensaje' => "Los equipos {$equipoA->nombre} y {$equipoB->nombre} ya se han enfrentado",
                'detalles' => [
                    'equipo_a' => $equipoA->id,
                    'equipo_b' => $equipoB->id
                ]
            ]);
        }

        $diferenciaPuntos = abs($equipoA->puntos - $equipoB->puntos);
        if ($diferenciaPuntos > $this->torneo->diferencia_maxima_puntos) {
            $this->advertencias->push([
                'tipo' => 'diferencia_puntos_equipos',
                'mensaje' => "Gran diferencia de puntos entre {$equipoA->nombre} ({$equipoA->puntos}) y {$equipoB->nombre} ({$equipoB->puntos})",
                'detalles' => [
                    'equipo_a' => $equipoA->id,
                    'equipo_b' => $equipoB->id,
                    'diferencia' => $diferenciaPuntos
                ]
            ]);
        }
    }

    private function conteoByes(ParticipanteTorneo $participante): int
    {
        return $this->torneo->partidas()
            ->where('jugador_blancas_id', $participante->id)
            ->whereNull('jugador_negras_id')
            ->count();
    }

    private function conteoByesEquipo(EquipoTorneo $equipo): int
    {
        return $this->torneo->equipoMatches()
            ->where('equipo_a_id', $equipo->id)
            ->whereNull('equipo_b_id')
            ->count();
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

    private function yaSeEnfrentaronEquipos($equipoA, $equipoB): bool
    {
        return $this->torneo->equipoMatches()
            ->where(function($query) use ($equipoA, $equipoB) {
                $query->where('equipo_a_id', $equipoA->id)
                      ->where('equipo_b_id', $equipoB->id);
            })
            ->orWhere(function($query) use ($equipoA, $equipoB) {
                $query->where('equipo_a_id', $equipoB->id)
                      ->where('equipo_b_id', $equipoA->id);
            })
            ->exists();
    }

    private function coloresDesequilibrados($jugador): bool
    {
        $blancas = $this->torneo->partidas()
            ->where('jugador_blancas_id', $jugador->id)
            ->count();

        $negras = $this->torneo->partidas()
            ->where('jugador_negras_id', $jugador->id)
            ->count();

        return abs($blancas - $negras) > 2;
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