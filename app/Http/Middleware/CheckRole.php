<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|int  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $userRoleId = $request->user()->rol_id;
        
        foreach ($roles as $role) {
            if ($userRoleId == $role) {
                return $next($request);
            }
        }

        return redirect('/home')->with('error', 'No tienes permiso para acceder a esta sección');
    }
} 