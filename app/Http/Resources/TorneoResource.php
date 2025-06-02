<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TorneoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'nombre_torneo' => $this->nombre_torneo,
            'fecha' => $this->fecha_formateada,
            'categoria_torneo' => $this->categoria->categoria_torneo,
            'formato' => $this->controlTiempo->formato_completo,
            'lugar' => $this->lugar,
            'no_rondas' => $this->no_rondas,
            'federacion_id' => $this->federacion->nombre_federacion ?? 'N/A',
            'organizador' => $this->organizador->nombre_completo,
            'arbitro' => $this->arbitro->nombre_completo,
            'arbitro_principal' => $this->arbitroPrincipal->nombre_completo,
            'director_torneo' => $this->director->nombre_completo,
            'arbitro_adjunto' => $this->arbitroAdjunto->nombre_completo,
            'estado' => $this->estado,
            'emparejamiento' => $this->sistemaEmparejamiento->sistema ?? 'N/A'
        ];
    }
}