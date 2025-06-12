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
            'nombre_torneo' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'lugar' => 'required|string|max:100',
            'categoriaTorneo_id' => 'required|exists:categorias_torneo,id_torneo_categoria',
            'no_rondas' => 'required|integer|min:1',
            'sistema_emparejamiento_id' => 'required|exists:sistemas_de_emparejamiento,id_emparejamiento',
            'control_tiempo_id' => 'required|exists:controles_tiempo,id_control_tiempo',
            'federacion_id' => 'nullable|exists:federaciones,acronimo',
            'organizador_id' => 'required|exists:miembros,cedula',
            'director_torneo_id' => 'required|exists:miembros,cedula',
            'arbitro_principal_id' => 'required|exists:miembros,cedula',
            'arbitro_id' => 'required|exists:miembros,cedula',
            'arbitro_adjunto_id' => 'required|exists:miembros,cedula',
            'estado_torneo' => 'sometimes|boolean',
            'usar_buchholz' => 'sometimes|boolean',
            'usar_sonneborn_berger' => 'sometimes|boolean',
            'usar_desempate_progresivo' => 'sometimes|boolean',
            'numero_minimo_participantes' => 'sometimes|integer|min:4',
            'permitir_bye' => 'sometimes|boolean',
            'alternar_colores' => 'sometimes|boolean',
            'evitar_emparejamientos_repetidos' => 'sometimes|boolean',
            'maximo_emparejamientos_repetidos' => 'sometimes|integer|min:1|max:3',
            'es_por_equipos' => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_torneo.required' => 'El nombre del torneo es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora debe estar en formato HH:MM',
            'lugar.required' => 'El lugar del torneo es obligatorio.',
            'categoriaTorneo_id.required' => 'La categoría es obligatoria.',
            'no_rondas.required' => 'El número de rondas es obligatorio.',
            'no_rondas.min' => 'El número de rondas debe ser al menos 1',
            'sistema_emparejamiento_id.required' => 'El sistema de emparejamiento es obligatorio.',
            'control_tiempo_id.required' => 'El control de tiempo es obligatorio.',
            'organizador_id.required' => 'El organizador es obligatorio.',
            'director_torneo_id.required' => 'El director del torneo es obligatorio.',
            'arbitro_principal_id.required' => 'El árbitro principal es obligatorio.',
            'arbitro_id.required' => 'El árbitro es obligatorio.',
            'arbitro_adjunto_id.required' => 'El árbitro adjunto es obligatorio.',
        ];
    }
}