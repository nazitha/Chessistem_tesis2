<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TorneoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_torneo' => 'required|unique:torneos',
            'fecha_inicio' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'categoriaTorneo_id' => 'required|exists:categorias_torneo,id_torneo_categoria',
            'lugar' => 'required|string|max:255',
            'no_rondas' => 'required|integer|min:1',
            'sistema_emparejamiento_id' => 'nullable|exists:sistemas_emparejamiento,id_emparejamiento',
            'control_tiempo_id' => 'required|exists:controles_tiempo,id_control_tiempo',
            'federacion_id' => 'nullable|exists:federaciones,acronimo',
            'organizador_id' => 'required|exists:miembros,cedula',
            'director_torneo_id' => 'required|exists:miembros,cedula',
            'arbitro_id' => 'required|exists:miembros,cedula',
            'arbitro_principal_id' => 'required|exists:miembros,cedula',
            'arbitro_adjunto_id' => 'required|exists:miembros,cedula',
            'estado_torneo' => 'sometimes|boolean'
        ];
    }
}