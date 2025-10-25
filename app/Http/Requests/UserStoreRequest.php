<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correo' => 'required|email|unique:usuarios,correo,NULL,id_email',
            // La contraseña es obligatoria al crear un usuario
            'contrasena' => 'required|min:8|confirmed',
            'rol_id' => 'required|exists:roles,id',
            'usuario_estado' => 'required|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe tener un formato válido.',
            'correo.unique' => 'El correo electrónico ya está en uso.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
            'rol_id.required' => 'El rol es obligatorio.',
            'rol_id.exists' => 'El rol seleccionado no es válido.',
            'usuario_estado.required' => 'El estado del usuario es obligatorio.',
            'usuario_estado.boolean' => 'El estado del usuario debe ser activo o inactivo.'
        ];
    }
}