<?php

namespace App\Traits;

use App\Models\PairingLog;
use App\Models\ParticipanteTorneo;
use App\Models\EquipoTorneo;

trait PairingLogs
{
    protected function logEmparejamiento($torneo, $ronda, $participante, $motivo, $detalles = null)
    {
        PairingLog::create([
            'torneo_id' => $torneo->id,
            'ronda_id' => $ronda->id,
            'participante_id' => $participante instanceof ParticipanteTorneo ? $participante->id : null,
            'equipo_id' => $participante instanceof EquipoTorneo ? $participante->id : null,
            'tipo_decision' => 'emparejamiento',
            'motivo' => $motivo,
            'detalles' => $detalles
        ]);
    }

    protected function logFlotamiento($torneo, $ronda, $participante, $motivo, $detalles = null)
    {
        PairingLog::create([
            'torneo_id' => $torneo->id,
            'ronda_id' => $ronda->id,
            'participante_id' => $participante instanceof ParticipanteTorneo ? $participante->id : null,
            'equipo_id' => $participante instanceof EquipoTorneo ? $participante->id : null,
            'tipo_decision' => 'flotamiento',
            'motivo' => $motivo,
            'detalles' => $detalles
        ]);
    }

    protected function logBye($torneo, $ronda, $participante, $motivo, $detalles = null)
    {
        PairingLog::create([
            'torneo_id' => $torneo->id,
            'ronda_id' => $ronda->id,
            'participante_id' => $participante instanceof ParticipanteTorneo ? $participante->id : null,
            'equipo_id' => $participante instanceof EquipoTorneo ? $participante->id : null,
            'tipo_decision' => 'bye',
            'motivo' => $motivo,
            'detalles' => $detalles
        ]);
    }

    protected function logColor($torneo, $ronda, $participante, $color, $motivo, $detalles = null)
    {
        PairingLog::create([
            'torneo_id' => $torneo->id,
            'ronda_id' => $ronda->id,
            'participante_id' => $participante instanceof ParticipanteTorneo ? $participante->id : null,
            'equipo_id' => $participante instanceof EquipoTorneo ? $participante->id : null,
            'tipo_decision' => 'color',
            'motivo' => $motivo,
            'detalles' => array_merge($detalles ?? [], ['color' => $color])
        ]);
    }
} 