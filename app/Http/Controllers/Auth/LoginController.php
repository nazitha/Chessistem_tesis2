<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

   public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('LoginController@login - Iniciando proceso de login');
        
        // Validar los datos
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required',
        ]);
    
        // Buscar el usuario por correo
        $user = \App\Models\User::where('correo', $request->correo)->first();
        
        Log::info('LoginController@login - Usuario encontrado:', [
            'existe' => $user ? 'sí' : 'no',
            'estado' => $user ? ($user->usuario_estado ? 'activo' : 'inactivo') : 'N/A'
        ]);

        // Verificar si el usuario existe y está activo
        if (!$user || !$user->usuario_estado) {
            Log::info('LoginController@login - Autenticación fallida: usuario no existe o inactivo');
            return back()->withErrors([
                'correo' => 'Credenciales incorrectas o usuario inactivo.',
            ]);
        }
        
        // Verificar la contraseña usando Hash::check
        if (Hash::check($request->contrasena, $user->contrasena)) {
            // Iniciar sesión manualmente
            Auth::login($user);
            $request->session()->regenerate();
            Log::info('LoginController@login - Usuario autenticado: ' . $user->id_email);
            return $this->authenticated($request, $user);
        }

        Log::info('LoginController@login - Autenticación fallida: contraseña incorrecta');
        return back()->withErrors([
            'correo' => 'Credenciales incorrectas.',
        ]);
    }

    protected function authenticated(Request $request, $user) {
        \Illuminate\Support\Facades\Log::info('LoginController@authenticated - Usuario: ' . $user->id . ', Rol: ' . $user->rol_id);
        
        switch ($user->rol_id) {
            case 1: // Admin
                \Illuminate\Support\Facades\Log::info('LoginController@authenticated - Redirigiendo a home (Admin)');
                return redirect()->route('home');
            case 2: // Evaluador
                \Illuminate\Support\Facades\Log::info('LoginController@authenticated - Redirigiendo a home (Evaluador)');
                return redirect()->route('home');
            case 3: // Estudiante
                \Illuminate\Support\Facades\Log::info('LoginController@authenticated - Redirigiendo a home (Estudiante)');
                return redirect()->route('home');
            case 4: // Gestor
                \Illuminate\Support\Facades\Log::info('LoginController@authenticated - Redirigiendo a home (Gestor)');
                return redirect()->route('home');
            default: // Rol desconocido
                \Illuminate\Support\Facades\Log::warning('LoginController@authenticated - Rol no reconocido: ' . $user->rol_id);
                Auth::logout();
                return redirect('/login')->withErrors(['error' => 'Rol no reconocido.']);
        }
    }
   
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();
        return redirect('/login');
    }

    
}