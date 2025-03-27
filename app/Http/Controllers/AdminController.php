<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Role; 

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:1'); // Solo para rol_id = 1 (admin)
    }

    /**
     * Panel principal de administración
     */
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'recentUsers' => User::latest()->take(5)->get()
        ]);
    }

    /**
     * Listado de usuarios
     */
    public function userIndex()
    {
        $users = User::with('rol')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Edición de permisos de usuario
     */
    public function editPermissions(User $user)
    {
        $roles = Role::all(); // Si tienes tabla de roles
        return view('admin.users.permissions', compact('user', 'roles'));
    }

    /**
     * Actualización de permisos
     */
    public function updatePermissions(Request $request, User $user)
    {
        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id' // Ajusta según tu estructura
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Permisos actualizados correctamente');
    }

    /**
     * Gestión de miembros
     */
    public function memberIndex()
    {
        $members = Member::paginate(10); // Ajusta según tu modelo de miembros
        return view('admin.members.index', compact('members'));
    }
    
    /**
     * Historial de cambios
     */
    public function activityLog()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(15);
        return view('admin.logs.index', compact('logs'));
    }
}