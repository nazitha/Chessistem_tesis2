<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SistemaDesempateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_sistema_desempate' => $this->id_sistema_desempate,
            'nombre_sistema_desempate' => $this->nombre_sistema_desempate,
            'descrip_sistema_desempate' => $this->descrip_sistema_desempate,
        ];
    }
} 