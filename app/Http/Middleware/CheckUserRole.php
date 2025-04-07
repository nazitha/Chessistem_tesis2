<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$guards) {
        Log::info('CheckUserRole::handle - Inicio del middleware', [
            'path' => $request->path(),
            'method' => $request->method()
        ]);

        if (!Auth::check()) {
            Log::error('CheckUserRole::handle - Usuario no autenticado');
            return redirect()->route('login');
        }

        $user = Auth::user();
        Log::info('CheckUserRole::handle - Usuario autenticado:', [
            'id' => $user->id_email,
            'correo' => $user->correo,
            'rol' => $user->rol_id
        ]);

        return $next($request);
    }
}