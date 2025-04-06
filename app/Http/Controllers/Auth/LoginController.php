<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{

   public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required',
        ]);
    
        if (Auth::attempt(['correo' => $request->correo, 'contrasena' => $request->contrasena])) {
            $request->session()->regenerate();
            return redirect()->route('home'); 
        }

        return back()->withErrors([
            'correo' => 'Credenciales incorrectas.',
        ]);
    }

    protected function authenticated(Request $request, $user) {

       /* switch ($user->rol_id) {
            case 1: // Admin
                return redirect()->route('admin.dashboard');
            case 2: // Evaluador
                return redirect()->route('evaluador.dashboard');
            case 3: // Estudiante
                return redirect()->route('estudiante.dashboard');
            case 4: // Gestor
                return redirect()->route('gestor.dashboard');
            default: // Rol desconocido
                Auth::logout();
                return redirect('/login')->withErrors(['error' => 'Rol no reconocido.']);
        }*/
        return redirect()->route('home'); // Redirige a la ruta de inicio por defecto
    }
   
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();
        return redirect('/login');
    }

    
}