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
        Log::info('Middleware Authenticate: Verificando autenticación');
        if (!Auth::check()) {
            Log::info('Usuario no autenticado, redirigiendo a login');
            return redirect()->route('login');
        }
        return $next($request);
    }
}