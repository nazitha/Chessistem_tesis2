<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipanteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'miembro' => [
                'cedula' => $this->miembro->cedula,
                'nombres' => $this->miembro->nombres,
                'apellidos' => $this->miembro->apellidos
            ],
            'torneo' => [
                'id' => $this->torneo->id_torneo,
                'nombre' => $this->torneo->nombre_torneo
            ],
            'puntos' => $this->puntos,
            'posicion' => $this->posicion
        ];
    }
} 