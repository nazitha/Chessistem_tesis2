<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MiembroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date',
            'fecha_inscripcion' => 'required|date',
            'estado_miembro' => 'boolean',
            'telefono' => 'nullable|string|max:20',
            'club' => 'nullable|string|max:255',
            'correo_sistema_id' => 'nullable|exists:usuarios,correo',
            'ciudad_id' => 'nullable|exists:ciudades,id',
            'academia_id' => 'required|exists:academias,id_academia',
            'elo' => 'nullable|integer|min:0|max:3000'
        ];

        if ($this->isMethod('post')) {
            $rules['cedula'] = 'required|unique:miembros';
        } else {
            $rules['cedula'] = 'required|unique:miembros,cedula,' . $this->miembro->cedula . ',cedula';
        }

        return $rules;
    }
}