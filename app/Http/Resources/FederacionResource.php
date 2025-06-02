<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FederacionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'acronimo' => $this->acronimo,
            'federacion' => $this->nombre_federacion . ' (' . $this->acronimo . ')'
        ];
    }
}