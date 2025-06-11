<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartidaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'no_partida' => $this->no_partida,
            'ronda' => $this->ronda,
            'ronda_torneo_id' => $this->ronda_torneo_id,
            'participante_id' => $this->participante_id,
            'torneo_id' => $this->torneo_id,
            'mesa' => $this->mesa,
            'color' => $this->color,
            'tiempo' => $this->tiempo,
            'desempate_utilizado_id' => $this->desempate_utilizado_id,
            'estado_abandono' => $this->estado_abandono,
            'resultado' => $this->resultado,
            'participante' => new MiembroResource($this->whenLoaded('participante')),
            'torneo' => new TorneoResource($this->whenLoaded('torneo')),
            'sistema_desempate' => new SistemaDesempateResource($this->whenLoaded('sistemaDesempate')),
        ];
    }

    private function getResultadoTexto(): string
    {
        if ($this->estado_abandono) {
            return 'Abandono';
        }

        if ($this->resultado === null) {
            return 'Pendiente';
        }

        if ($this->resultado === 1) {
            return 'Victoria';
        }

        if ($this->resultado === 0.5) {
            return 'Tablas';
        }

        return 'Derrota';
    }
}