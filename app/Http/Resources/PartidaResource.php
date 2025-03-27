<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartidaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'no_partida' => $this->no_partida,
            'participante_id' => $this->participante_id,
            'torneo_id' => $this->torneo_id,
            'ronda' => $this->ronda,
            'mesa' => $this->mesa,
            'color' => $this->color_descripcion,
            'tiempo' => $this->tiempo,
            'desempate_utilizado_id' => $this->desempate_utilizado_id,
            'estado_abandono' => $this->estado_abandono,
            'resultado' => $this->resultado
        ];
    }
}