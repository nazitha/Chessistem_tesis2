<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'permisos' => $this->whenLoaded('permissions', function () {
                return $this->permissions->pluck('permiso');
            })
        ];
    }
}