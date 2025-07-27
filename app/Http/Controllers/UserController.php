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
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            Log::info('UserController@index - Iniciando listado de usuarios');
            
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

        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                
                if (empty($data['contrasena'])) {
                    $data['contrasena'] = 'password123';
                }
                
                $data['created_at'] = now();
                
                $user = User::create($data);
                
                AuditService::logUserAction(
                    Auth::user()->correo,
                    $user,
                    'created',
                    $request->validated()
                );

                return response()->json(['success' => true]);
            });
        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al crear el usuario',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UserUpdateRequest $request, $id): JsonResponse
    {
        if (!PermissionService::hasPermission('usuarios.update')) {
            return response()->json(['error' => 'No tienes permiso para editar usuarios'], 403);
        }
    
        try {
            return DB::transaction(function () use ($request, $id) {
                $user = User::find($id);
                
                if (!$user) {
                    return response()->json(['error' => 'Usuario no encontrado'], 404);
                }
                
                $data = $request->validated();
                
                // Validar correo manualmente si está presente
                if (isset($data['correo'])) {
                    // Si el correo no cambió, eliminarlo
                    if ($data['correo'] === $user->correo) {
                        unset($data['correo']);
                    } else {
                        // Validar que el nuevo correo sea único
                        $exists = User::where('correo', $data['correo'])
                            ->where('id_email', '!=', $id)
                            ->exists();
                        
                        if ($exists) {
                            return response()->json(['error' => 'El correo electrónico ya está en uso.'], 422);
                        }
                    }
                }
                
                // Si no se proporciona contraseña, no actualizar
                if (empty($data['contrasena'])) {
                    unset($data['contrasena']);
                }
                
                $user->update($data);
                
                AuditService::logUserAction(
                    Auth::user()->correo,
                    $user,
                    'updated',
                    $user->getOriginal()
                );
    
                return response()->json(['success' => true]);
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el usuario'], 500);
        }
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

    public function destroy($id): JsonResponse
    {
        if (!PermissionService::hasPermission('usuarios.delete')) {
            return response()->json(['error' => 'No tienes permiso para eliminar usuarios'], 403);
        }

        return DB::transaction(function () use ($id) {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }
            
            $correo = $user->correo;
            
            AuditService::logUserAction(
                Auth::user()->correo,
                $user,
                'deleted'
            );
            
            $user->delete();
            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        });
    }

    public function profile()
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    public function asignarPermisos(Request $request)
    {
        try {
            if ($request->isJson()) {
                $data = $request->json()->all();
            } else {
                $data = $request->all();
            }
            Log::info('Datos recibidos para asignar permisos:', $data);

            $validator = Validator::make($data, [
                'user_id' => 'required|exists:users,id',
                'rol_id' => 'required|in:1,2,3,4',
                'permisos' => 'required|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al asignar permisos: ' . $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($data['user_id']);
            $user->rol_id = $data['rol_id'];
            $user->save();

            Log::info('Permisos asignados correctamente', [
                'user_id' => $user->id,
                'rol_id' => $user->rol_id,
                'permisos' => $data['permisos']
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

    public function show($id)
    {
        $user = User::with(['miembro', 'rol'])->findOrFail($id);
        return response()->json($user);
    }
}