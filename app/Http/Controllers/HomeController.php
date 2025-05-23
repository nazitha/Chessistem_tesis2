<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @method void middleware(string $middleware, array $options = [])
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        Log::info('HomeController@__construct - Middleware auth configurado');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        Log::info('HomeController@index - Inicio del método');
        
        if (!Auth::check()) {
            Log::error('HomeController@index - Usuario no autenticado');
            return redirect()->route('login');
        }

        $user = Auth::user();
        Log::info('HomeController@index - Usuario autenticado:', [
            'id' => $user->id_email,
            'correo' => $user->correo,
            'rol' => $user->rol_id
        ]);

        try {
            // Preparar las tarjetas del dashboard según el rol del usuario
            $dashboardCards = $this->getDashboardCards($user->rol_id);
            
            Log::info('HomeController@index - Intentando cargar vista home.index');
            return view('home.index', compact('dashboardCards'));
        } catch (\Exception $e) {
            Log::error('HomeController@index - Error al cargar la vista:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get dashboard cards based on user role.
     *
     * @param int $rolId
     * @return array
     */
    private function getDashboardCards($rolId)
    {
        $cards = [];

        // Tarjetas comunes para todos los roles
        $cards[] = [
            'title' => 'Mi Perfil',
            'description' => 'Gestiona tu información personal',
            'icon' => 'user',
            'route' => 'profile'
        ];

        // Tarjetas específicas según el rol
        switch ($rolId) {
            case 1: // Administrador
                $cards = array_merge($cards, [
                    [
                        'title' => 'Usuarios',
                        'description' => 'Gestión de usuarios del sistema',
                        'icon' => 'users',
                        'route' => 'usuarios.index'
                    ],
                    [
                        'title' => 'Torneos',
                        'description' => 'Administración de torneos',
                        'icon' => 'trophy',
                        'route' => 'torneos.index'
                    ],
                    [
                        'title' => 'Academias',
                        'description' => 'Gestión de academias',
                        'icon' => 'school',
                        'route' => 'academias.index'
                    ]
                ]);
                break;
            case 2: // Evaluador
                $cards = array_merge($cards, [
                    [
                        'title' => 'Evaluaciones',
                        'description' => 'Gestión de evaluaciones',
                        'icon' => 'clipboard-check',
                        'route' => 'evaluaciones.index'
                    ]
                ]);
                break;
            case 3: // Estudiante
                $cards = array_merge($cards, [
                    [
                        'title' => 'Mis Torneos',
                        'description' => 'Ver mis torneos',
                        'icon' => 'chess',
                        'route' => 'torneos.estudiante'
                    ]
                ]);
                break;
            case 4: // Gestor
                $cards = array_merge($cards, [
                    [
                        'title' => 'Gestión de Torneos',
                        'description' => 'Administrar torneos asignados',
                        'icon' => 'cog',
                        'route' => 'torneos.gestor'
                    ]
                ]);
                break;
        }

        return $cards;
    }
}