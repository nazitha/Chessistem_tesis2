<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'correo' => $this->correo,
            'rol' => $this->role->nombre,
            'estado' => $this->estado,
            'permisos' => $this->role->permissions->pluck('permiso')
        ];
    }
}