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
            'correo' => 'required|email|unique:usuarios',
            'contrasena' => 'required|min:8|confirmed',
            'rol_id' => 'required|exists:roles,id',
            'usuario_estado' => 'required|boolean'
        ];
    }
}