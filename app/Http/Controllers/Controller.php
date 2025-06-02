<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function checkRole($roles)
    {
        if (!Auth::check()) {
            return false;
        }

        $userRoleId = Auth::user()->rol_id;
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        return in_array($userRoleId, $roles);
    }

    protected function requireRole($roles)
    {
        if (!$this->checkRole($roles)) {
            if (request()->expectsJson()) {
                abort(403, 'No autorizado');
            }
            return redirect('/home')->with('error', 'No tienes permiso para acceder a esta sección');
        }
    }
}
