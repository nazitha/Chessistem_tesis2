<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MiembroRolResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'cedula' => $this->cedula,
            'miembro' => $this->nombres . ' ' . $this->apellidos . ' (' . $this->cedula . ')',
            'rol' => $this->usuario->rol->nombre ?? null,
            'rol_id' => $this->usuario->rol_id ?? null
        ];
    }
}