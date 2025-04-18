<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:assign,remove',
            'rol_id' => 'required|exists:roles,id',
            'permiso_id' => 'required|exists:permisos,id'
        ];
    }
}