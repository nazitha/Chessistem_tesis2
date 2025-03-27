<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        \Log::info('Intentando autenticar con:', $credentials);
    
        if (Auth::attempt(['correo' => $request->correo, 'password' => $request->contrasena])) {
            \Log::info('Usuario autenticado: ', ['user' => Auth::user()]);
            $request->session()->regenerate();
            return $this->authenticated($request, Auth::user());
        }

        \Log::info('Credenciales incorrectas.');
        return back()->withErrors([
            'correo' => 'Credenciales incorrectas.',
        ]);

    }

    protected function authenticated(Request $request, $user) {
        \Log::info('Usuario autenticado con rol: ' . $user->rol_id);

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