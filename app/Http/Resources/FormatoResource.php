<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FormatoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'control_tiempo_id' => $this->control->id_control_tiempo,
            'formato' => $this->control->control_tiempo . ' (' . $this->control->formato . ')'
        ];
    }
}