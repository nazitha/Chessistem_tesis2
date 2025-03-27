<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correo' => 'sometimes|email|unique:usuarios,correo,' . $this->user->id,
            'contrasena' => 'sometimes|min:8|confirmed',
            'rol_id' => 'sometimes|exists:roles,id',
            'usuario_estado' => 'sometimes|boolean'
        ];
    }
}