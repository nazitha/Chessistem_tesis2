<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParticipanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'miembro_id' => 'required|string|exists:miembros,cedula',
            'torneo_id' => 'required|integer|exists:torneos,id_torneo',
            'puntos' => 'nullable|numeric|min:0',
            'posicion' => 'nullable|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'miembro_id.required' => 'El miembro es requerido',
            'miembro_id.exists' => 'El miembro seleccionado no existe',
            'torneo_id.required' => 'El torneo es requerido',
            'torneo_id.exists' => 'El torneo seleccionado no existe',
            'puntos.numeric' => 'Los puntos deben ser un número',
            'puntos.min' => 'Los puntos no pueden ser negativos',
            'posicion.integer' => 'La posición debe ser un número entero',
            'posicion.min' => 'La posición debe ser mayor a 0'
        ];
    }
} 