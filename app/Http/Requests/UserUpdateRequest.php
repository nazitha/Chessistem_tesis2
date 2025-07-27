<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contrasena' => 'nullable|min:8|max:80|confirmed',
            'rol_id' => 'sometimes|nullable|exists:roles,id',
            'usuario_estado' => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.max' => 'La contraseña no puede tener más de 80 caracteres.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
            'rol_id.exists' => 'El rol seleccionado no es válido.',
            'usuario_estado.boolean' => 'El estado del usuario debe ser activo o inactivo.'
        ];
    }

    protected function prepareForValidation()
    {
        // Si la contraseña es ********, no actualizar
        if ($this->contrasena === '********' || $this->contrasena_confirmation === '********') {
            $this->merge([
                'contrasena' => null,
                'contrasena_confirmation' => null
            ]);
        }

        // Convertir rol_id a null si viene vacío
        if ($this->rol_id === '') {
            $this->merge(['rol_id' => null]);
        }
    }
}