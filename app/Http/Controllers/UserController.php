<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use App\Services\AuditService;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        try {
            Log::info('UserController@index - Iniciando listado de usuarios');
            
            // Validar que el usuario tenga permiso para ver usuarios
            if (!PermissionService::hasPermission('usuarios.read')) {
                return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esta sección');
            }
            
            $users = User::with(['rol.permissions'])
                ->orderBy('correo')
                ->get();

            Log::info('UserController@index - Usuarios encontrados: ' . $users->count());
            
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('UserController@index - Error al listar usuarios: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Error al obtener la lista de usuarios');
        }
    }

    public function getRoles(): JsonResponse
    {
        $roles = Role::all();
        return RoleResource::collection($roles)->response();
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        if (!PermissionService::hasPermission('usuarios.create')) {
            return response()->json(['error' => 'No tienes permiso para crear usuarios'], 403);
        }

        return DB::transaction(function () use ($request) {
            $user = User::create($request->validated());
            
            AuditService::logUserAction(
                $request->mail_log,
                $user,
                'created',
                $request->validated()
            );

            return response()->json(['success' => true], 201);
        });
    }

    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        if (!PermissionService::hasPermission('usuarios.update')) {
            return response()->json(['error' => 'No tienes permiso para editar usuarios'], 403);
        }

        return DB::transaction(function () use ($request, $user) {
            $originalData = $user->getOriginal();
            $user->update($request->validated());
            
            AuditService::logUserAction(
                $request->mail_log,
                $user,
                'updated',
                $originalData
            );

            return response()->json(['success' => true]);
        });
    }

    public function getRolesWithPermissions(): JsonResponse
    {
        $roles = Role::with('permissions')
            ->orderBy('nombre')
            ->get();

        return RoleResource::collection($roles)->response();
    }

    public function managePermission(PermissionRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $action = $request->action;
            $role = Role::findOrFail($request->rol_id);
            $permission = Permission::findOrFail($request->permiso_id);

            if ($action === 'assign') {
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            } else {
                $role->permissions()->detach($permission->id);
            }

            AuditService::logPermissionAction(
                $request->mail_log,
                $action,
                $role,
                $permission
            );

            return response()->json(['success' => true]);
        });
    }

    public function destroy(User $user): JsonResponse
    {
        if (!PermissionService::hasPermission('usuarios.delete')) {
            return response()->json(['error' => 'No tienes permiso para eliminar usuarios'], 403);
        }

        return DB::transaction(function () use ($user) {
            AuditService::logUserAction(
                Auth::user()->correo,
                $user,
                'deleted'
            );
            
            $user->delete();
            return response()->json(['success' => true]);
        });
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    /**
     * Asignar permisos a un usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function asignarPermisos(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'rol_id' => 'required|in:1,2,3,4',
                'permisos' => 'required|array'
            ]);

            $user = User::findOrFail($request->user_id);
            $user->rol_id = $request->rol_id;
            $user->save();

            // Aquí puedes agregar lógica adicional para guardar los permisos específicos
            // en una tabla de permisos si es necesario

            Log::info('Permisos asignados correctamente', [
                'user_id' => $user->id,
                'rol_id' => $user->rol_id,
                'permisos' => $request->permisos
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permisos asignados correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al asignar permisos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permisos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Devuelve los permisos disponibles y los asignados a un usuario, junto con el rol actual.
     */
    public function apiPermisosUsuario($userId)
    {
        $user = User::with(['rol', 'permissions'])->findOrFail($userId);
        $todos = \DB::table('permisos')->select('id', 'permiso', 'descripcion')->get();
        $asignados = \DB::table('asignaciones_permisos')
            ->where('rol_id', $user->rol_id)
            ->pluck('permiso_id')
            ->toArray();
        return response()->json([
            'rol_id' => $user->rol_id,
            'todos' => $todos,
            'asignados' => $asignados
        ]);
    }
}