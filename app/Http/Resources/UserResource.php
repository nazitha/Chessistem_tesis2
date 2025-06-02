<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'correo' => $this->correo,
<<<<<<< HEAD
            'rol' => $this->rol->nombre,
            'estado' => $this->estado,
            'permisos' => $this->rol->permissions->pluck('permiso')
=======
            'rol' => $this->role->nombre,
            'estado' => $this->estado,
            'permisos' => $this->role->permissions->pluck('permiso')
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
        ];
    }
}