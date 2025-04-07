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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with(['role.permissions'])
            ->orderBy('correo')
            ->get();

        return UserResource::collection($users)->response();
    }

    public function getRoles(): JsonResponse
    {
        $roles = Role::all();
        return RoleResource::collection($roles)->response();
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
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
}