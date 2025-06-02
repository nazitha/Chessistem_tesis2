<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'correo' => $this->correo,
            'rol' => $this->rol->nombre,
            'estado' => $this->estado,
            'permisos' => $this->rol->permissions->pluck('permiso')
        ];
    }
}