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
            'mesa' => $this->mesa,
            'participante' => [
                'cedula' => $this->participante->cedula,
                'nombres' => $this->participante->nombres,
                'apellidos' => $this->participante->apellidos
            ],
            'torneo' => [
                'id' => $this->torneo->id_torneo,
                'nombre' => $this->torneo->nombre_torneo
            ],
            'color' => $this->color ? 'Blancas' : 'Negras',
            'tiempo' => $this->tiempo,
            'sistema_desempate' => $this->whenLoaded('sistemaDesempate', function () {
                return [
                    'id' => $this->sistemaDesempate->id_desempate,
                    'nombre' => $this->sistemaDesempate->sistema_desempate
                ];
            }),
            'estado_abandono' => $this->estado_abandono,
            'resultado' => $this->resultado,
            'resultado_texto' => $this->getResultadoTexto()
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