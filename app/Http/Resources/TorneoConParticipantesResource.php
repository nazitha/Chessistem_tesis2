<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TorneoConParticipantesResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'cantidad_participantes' => $this->participantes_count,
            'torneo_id' => $this->id_torneo,
            'torneo' => $this->nombre_torneo . ', ' . $this->fecha_inicio->format('d/m/Y')
        ];
    }
}