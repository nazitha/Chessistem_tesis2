<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Auditoria;
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
    public function index(Request $request)
    {
        try {
            Log::info('UserController@index - Iniciando listado de usuarios');
            
            if (!PermissionService::hasPermission('usuarios.read')) {
                return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esta sección');
            }
            
            // Parámetros de búsqueda y paginación
            $search = $request->get('search');
            $filtroCorreo = $request->get('filtro_correo');
            $filtroRol = $request->get('filtro_rol');
            $filtroEstado = $request->get('filtro_estado');
            $perPage = $request->get('per_page', 10);
            
            // Query base para usuarios
            $usersQuery = User::with(['rol.permissions']);
            
            // Aplicar filtros de búsqueda
            if ($search) {
                $usersQuery->where(function($query) use ($search) {
                    $query->where('correo', 'like', "%{$search}%")
                          ->orWhereHas('rol', function($q) use ($search) {
                              $q->where('nombre', 'like', "%{$search}%");
                          });
                });
            }
            
            if ($filtroCorreo) {
                $usersQuery->where('correo', 'like', "%{$filtroCorreo}%");
            }
            
            if ($filtroRol) {
                $usersQuery->whereHas('rol', function($query) use ($filtroRol) {
                    $query->where('nombre', 'like', "%{$filtroRol}%");
                });
            }
            
            if ($filtroEstado !== null && $filtroEstado !== '') {
                $usersQuery->where('usuario_estado', $filtroEstado);
            }
            
            // Ordenar y paginar
            $users = $usersQuery->orderBy('correo')->paginate($perPage);
            
            // Mantener parámetros de búsqueda en la paginación
            $users->appends($request->all());

            // Obtener datos para la tabla de gestión de roles con búsqueda y paginación
            $rolesDataPaginated = $this->getRolesWithPermissionsDataPaginated($request);

            Log::info('UserController@index - Usuarios encontrados: ' . $users->count());
            
            // Parámetros de búsqueda para permisos
            $searchPermisos = $request->get('search_permisos');
            $filtroRolPermisos = $request->get('filtro_rol_permisos');
            $filtroGrupoPermisos = $request->get('filtro_grupo_permisos');
            $filtroPermisos = $request->get('filtro_permisos');
            
            return view('admin.users.index', compact('users', 'rolesDataPaginated', 'search', 'filtroCorreo', 'filtroRol', 'filtroEstado', 'perPage', 'searchPermisos', 'filtroRolPermisos', 'filtroGrupoPermisos', 'filtroPermisos'));
        } catch (\Exception $e) {
            Log::error('UserController@index - Error al listar usuarios: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Error al obtener la lista de usuarios');
        }
    }

    /**
     * Obtiene los datos paginados para la tabla de gestión de roles
     */
    private function getRolesWithPermissionsDataPaginated($request = null)
    {
        try {
            // Parámetros de búsqueda para permisos
            $searchPermisos = $request ? $request->get('search_permisos') : '';
            $filtroRolPermisos = $request ? $request->get('filtro_rol_permisos') : '';
            $filtroGrupoPermisos = $request ? $request->get('filtro_grupo_permisos') : '';
            $filtroPermisos = $request ? $request->get('filtro_permisos') : '';
            $perPagePermisos = $request ? $request->get('per_page_permisos', 10) : 10;
            
            // Construir la consulta base
            $query = "
                SELECT
                    r.nombre AS rol,
                    g.grupo,
                    COALESCE(
                        (
                            SELECT GROUP_CONCAT(SUBSTRING_INDEX(p.permiso, '.', -1) SEPARATOR ' | ')
                            FROM permisos p
                            INNER JOIN asignaciones_permisos a ON a.permiso_id = p.id AND a.rol_id = r.id
                            WHERE p.grupo = g.grupo
                              AND p.id NOT IN (5, 6, 7, 8, 9, 12, 29)
                        ), '-') AS permisos,
                    COALESCE(
                        (
                            SELECT GROUP_CONCAT(p.descripcion SEPARATOR ' | ')
                            FROM permisos p
                            INNER JOIN asignaciones_permisos a ON a.permiso_id = p.id AND a.rol_id = r.id
                            WHERE p.grupo = g.grupo
                              AND p.id NOT IN (5, 6, 7, 8, 9, 12, 29)
                        ), '-') AS descripciones
                FROM roles r
                CROSS JOIN (
                    SELECT DISTINCT grupo
                    FROM permisos
                    WHERE id NOT IN (5, 6, 7, 8, 9, 12, 29)
                ) g
            ";
            
            // Agregar condiciones WHERE según los filtros
            $conditions = [];
            
            if ($searchPermisos) {
                $conditions[] = "(r.nombre LIKE '%{$searchPermisos}%' OR g.grupo LIKE '%{$searchPermisos}%')";
            }
            
            if ($filtroRolPermisos) {
                $conditions[] = "r.nombre LIKE '%{$filtroRolPermisos}%'";
            }
            
            if ($filtroGrupoPermisos) {
                $conditions[] = "g.grupo LIKE '%{$filtroGrupoPermisos}%'";
            }
            
            if ($filtroPermisos) {
                $conditions[] = "EXISTS (
                    SELECT 1 FROM permisos p
                    INNER JOIN asignaciones_permisos a ON a.permiso_id = p.id AND a.rol_id = r.id
                    WHERE p.grupo = g.grupo
                      AND p.id NOT IN (5, 6, 7, 8, 9, 12, 29)
                      AND (p.permiso LIKE '%{$filtroPermisos}%' OR p.descripcion LIKE '%{$filtroPermisos}%')
                )";
            }
            
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(' AND ', $conditions);
            }
            
            $query .= " ORDER BY r.nombre, g.grupo";
            
            // Obtener todos los resultados para crear paginación manual
            $allRolesData = DB::select($query);
            
            // Crear paginación manual
            $currentPage = $request ? $request->get('page_permisos', 1) : 1;
            $offset = ($currentPage - 1) * $perPagePermisos;
            $rolesData = array_slice($allRolesData, $offset, $perPagePermisos);
            
            // Crear objeto de paginación manual
            $total = count($allRolesData);
            $lastPage = ceil($total / $perPagePermisos);
            
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $rolesData,
                $total,
                $perPagePermisos,
                $currentPage,
                [
                    'path' => $request->url(),
                    'pageName' => 'page_permisos',
                ]
            );
            
            // Agregar todos los parámetros de búsqueda a la paginación
            $paginator->appends($request->all());
            
            return $paginator;
        } catch (\Exception $e) {
            Log::error('Error al obtener datos paginados de roles y permisos: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1);
        }
    }

    /**
     * Obtiene los datos para la tabla de gestión de roles (método original sin paginación)
     */
    private function getRolesWithPermissionsData($request = null)
    {
        try {
            // Parámetros de búsqueda para permisos
            $searchPermisos = $request ? $request->get('search_permisos') : '';
            $filtroRolPermisos = $request ? $request->get('filtro_rol_permisos') : '';
            $filtroGrupoPermisos = $request ? $request->get('filtro_grupo_permisos') : '';
            $filtroPermisos = $request ? $request->get('filtro_permisos') : '';
            
            // Construir la consulta base
            $query = "
                SELECT
                    r.nombre AS rol,
                    g.grupo,
                    COALESCE(
                        (
                            SELECT GROUP_CONCAT(SUBSTRING_INDEX(p.permiso, '.', -1) SEPARATOR ' | ')
                            FROM permisos p
                            INNER JOIN asignaciones_permisos a ON a.permiso_id = p.id AND a.rol_id = r.id
                            WHERE p.grupo = g.grupo
                              AND p.id NOT IN (5, 6, 7, 8, 9, 12, 29)
                        ), '-') AS permisos,
                    COALESCE(
                        (
                            SELECT GROUP_CONCAT(p.descripcion SEPARATOR ' | ')
                            FROM permisos p
                            INNER JOIN asignaciones_permisos a ON a.permiso_id = p.id AND a.rol_id = r.id
                            WHERE p.grupo = g.grupo
                              AND p.id NOT IN (5, 6, 7, 8, 9, 12, 29)
                        ), '-') AS descripciones
                FROM roles r
                CROSS JOIN (
                    SELECT DISTINCT grupo
                    FROM permisos
                    WHERE id NOT IN (5, 6, 7, 8, 9, 12, 29)
                ) g
            ";
            
            // Agregar condiciones WHERE según los filtros
            $conditions = [];
            
            if ($searchPermisos) {
                $conditions[] = "(r.nombre LIKE '%{$searchPermisos}%' OR g.grupo LIKE '%{$searchPermisos}%')";
            }
            
            if ($filtroRolPermisos) {
                $conditions[] = "r.nombre LIKE '%{$filtroRolPermisos}%'";
            }
            
            if ($filtroGrupoPermisos) {
                $conditions[] = "g.grupo LIKE '%{$filtroGrupoPermisos}%'";
            }
            
            if ($filtroPermisos) {
                $conditions[] = "EXISTS (
                    SELECT 1 FROM permisos p
                    INNER JOIN asignaciones_permisos a ON a.permiso_id = p.id AND a.rol_id = r.id
                    WHERE p.grupo = g.grupo
                      AND p.id NOT IN (5, 6, 7, 8, 9, 12, 29)
                      AND (p.permiso LIKE '%{$filtroPermisos}%' OR p.descripcion LIKE '%{$filtroPermisos}%')
                )";
            }
            
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(' AND ', $conditions);
            }
            
            $query .= " ORDER BY r.nombre, g.grupo";
            
            $rolesData = DB::select($query);

            return $rolesData;
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de roles y permisos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica si un permiso específico existe en la base de datos
     */
    private function permissionExists(string $permission): bool
    {
        try {
            return DB::table('permisos')->where('permiso', $permission)->exists();
        } catch (\Exception $e) {
            Log::error('Error al verificar permiso: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los roles disponibles
     */
    public function getRoles()
    {
        try {
            $roles = DB::table('roles')
                ->select('id', 'nombre')
                ->orderBy('nombre')
                ->get();
            
            return response()->json(['success' => true, 'data' => $roles]);
        } catch (\Exception $e) {
            Log::error('Error al obtener roles: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los permisos de un rol específico para el formulario de gestión
     */
    public function getRolePermissions($rolId, $grupo = null)
    {
        try {
            // Obtener permisos disponibles (excluyendo IDs 5-9, 12 y 29)
            $query = DB::table('permisos')
                ->whereNotBetween('id', [5, 9])
                ->where('id', '!=', 12)
                ->where('id', '!=', 29);
            
            // Si se especifica un grupo, filtrar por ese grupo
            if ($grupo) {
                $query->where('grupo', $grupo);
            }
            
            $permisos = $query->get();
            
            // Obtener permisos asignados al rol
            $permisosAsignados = DB::table('asignaciones_permisos')
                ->where('rol_id', $rolId)
                ->pluck('permiso_id')
                ->toArray();
            
            // Construir el resultado
            $resultado = [];
            foreach ($permisos as $permiso) {
                $resultado[] = [
                    'rol' => 'Administrador', // Valor fijo para simplificar
                    'permiso' => $permiso->permiso,
                    'descripcion' => $permiso->descripcion,
                    'grupo' => $permiso->grupo,
                    'asignado' => in_array($permiso->id, $permisosAsignados) ? 1 : 0,
                    'permiso_id' => $permiso->id,
                    'rol_id' => $rolId
                ];
            }
            
            return response()->json(['success' => true, 'data' => $resultado]);
            
        } catch (\Exception $e) {
            Log::error('getRolePermissions: Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza los permisos de un rol
     */
    public function updateRolePermissions(Request $request)
    {
        try {
            Log::info('updateRolePermissions: Iniciando actualización de permisos');
            Log::info('updateRolePermissions: Datos recibidos', $request->all());
            
            $request->validate([
                'rol_id' => 'required|exists:roles,id',
                'permisos' => 'array',
                'permisos.*' => 'exists:permisos,id',
                'grupo' => 'nullable|string'
            ]);

            $rolId = $request->rol_id;
            $permisosSeleccionados = $request->permisos ?? [];
            $grupo = $request->grupo;
            
            Log::info('updateRolePermissions: Rol ID', ['rol_id' => $rolId]);
            Log::info('updateRolePermissions: Permisos seleccionados', ['permisos' => $permisosSeleccionados]);
            Log::info('updateRolePermissions: Grupo', ['grupo' => $grupo]);

            // Si se especifica un grupo, solo trabajar con permisos de ese grupo
            if ($grupo) {
                // Obtener todos los permisos del grupo específico
                $permisosDelGrupo = DB::table('permisos')
                    ->where('grupo', $grupo)
                    ->whereNotBetween('id', [5, 9])
                    ->where('id', '!=', 12)
                    ->where('id', '!=', 29)
                    ->pluck('id')
                    ->toArray();
                
                Log::info('updateRolePermissions: Permisos del grupo', ['permisos_grupo' => $permisosDelGrupo]);
                
                // Obtener permisos actualmente asignados del grupo específico
                $permisosActuales = DB::table('asignaciones_permisos')
                    ->where('rol_id', $rolId)
                    ->whereIn('permiso_id', $permisosDelGrupo)
                    ->pluck('permiso_id')
                    ->toArray();
                
                Log::info('updateRolePermissions: Permisos actuales del grupo', ['permisos_actuales' => $permisosActuales]);

                // Permisos a agregar (nuevos)
                $permisosAAgregar = array_diff($permisosSeleccionados, $permisosActuales);
                
                // Permisos a eliminar (desmarcados) - solo del grupo específico
                $permisosAEliminar = array_diff($permisosActuales, $permisosSeleccionados);
                
                Log::info('updateRolePermissions: Permisos a agregar', ['permisos_agregar' => $permisosAAgregar]);
                Log::info('updateRolePermissions: Permisos a eliminar', ['permisos_eliminar' => $permisosAEliminar]);

                // Insertar nuevos permisos
                if (!empty($permisosAAgregar)) {
                    $datosInsertar = [];
                    foreach ($permisosAAgregar as $permisoId) {
                        $datosInsertar[] = [
                            'rol_id' => $rolId,
                            'permiso_id' => $permisoId
                        ];
                    }
                    Log::info('updateRolePermissions: Insertando permisos', ['datos_insertar' => $datosInsertar]);
                    DB::table('asignaciones_permisos')->insert($datosInsertar);
                }

                // Eliminar permisos desmarcados (solo del grupo específico)
                if (!empty($permisosAEliminar)) {
                    Log::info('updateRolePermissions: Eliminando permisos', ['permisos_eliminar' => $permisosAEliminar]);
                    DB::table('asignaciones_permisos')
                        ->where('rol_id', $rolId)
                        ->whereIn('permiso_id', $permisosAEliminar)
                        ->delete();
                }
            } else {
                // Comportamiento original para cuando no se especifica grupo
                $permisosActuales = DB::table('asignaciones_permisos')
                    ->where('rol_id', $rolId)
                    ->pluck('permiso_id')
                    ->toArray();
                
                Log::info('updateRolePermissions: Permisos actuales (sin grupo)', ['permisos_actuales' => $permisosActuales]);

                $permisosAAgregar = array_diff($permisosSeleccionados, $permisosActuales);
                $permisosAEliminar = array_diff($permisosActuales, $permisosSeleccionados);
                
                Log::info('updateRolePermissions: Permisos a agregar (sin grupo)', ['permisos_agregar' => $permisosAAgregar]);
                Log::info('updateRolePermissions: Permisos a eliminar (sin grupo)', ['permisos_eliminar' => $permisosAEliminar]);

                if (!empty($permisosAAgregar)) {
                    $datosInsertar = [];
                    foreach ($permisosAAgregar as $permisoId) {
                        $datosInsertar[] = [
                            'rol_id' => $rolId,
                            'permiso_id' => $permisoId
                        ];
                    }
                    DB::table('asignaciones_permisos')->insert($datosInsertar);
                }

                if (!empty($permisosAEliminar)) {
                    DB::table('asignaciones_permisos')
                        ->where('rol_id', $rolId)
                        ->whereIn('permiso_id', $permisosAEliminar)
                        ->delete();
                }
            }

            // Log de auditoría
            try {
                if (Auth::check()) {
                    // Formatear datos de permisos para auditoría
                    $datosPermisos = $this->formatearDatosPermisos($grupo, $permisosAAgregar ?? [], $permisosAEliminar ?? []);
                    
                    $this->crearAuditoria(
                        Auth::user()->correo,
                        'Edición de Permisos',
                        json_encode($datosPermisos, JSON_UNESCAPED_UNICODE),
                        null
                    );
                } else {
                    Log::info('updateRolePermissions: Usuario no autenticado para auditoría');
                }
            } catch (\Exception $auditError) {
                Log::warning('updateRolePermissions: Error en auditoría', ['error' => $auditError->getMessage()]);
            }

            Log::info('updateRolePermissions: Actualización completada exitosamente');
            return response()->json([
                'success' => true,
                'message' => 'Permisos del rol actualizados correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('updateRolePermissions: Error completo', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar los permisos del rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRolesByName(Request $request): JsonResponse
    {
        try {
            $nombre = $request->get('nombre');
            
            if ($nombre) {
                $roles = Role::where('nombre', 'LIKE', "%{$nombre}%")->get();
            } else {
        $roles = Role::all();
            }
            
            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener roles por nombre: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener roles'
            ], 500);
        }
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
                
                // Si el correo no cambió, eliminarlo para no actualizar innecesariamente
                if (isset($data['correo']) && $data['correo'] === $user->correo) {
                    unset($data['correo']);
                }
                
                // Si no se proporciona contraseña, no actualizar
                if (empty($data['contrasena'])) {
                    unset($data['contrasena']);
                }
                
                $user->update($data);
                
                // Formatear datos para auditoría (solo lo que se ve en la tabla)
                $datosAnteriores = $this->formatearDatosUsuario($user->getOriginal());
                $datosNuevos = $this->formatearDatosUsuario($user->toArray());
                
                $this->crearAuditoria(
                    Auth::user()->correo,
                    'Edición',
                    json_encode($datosAnteriores, JSON_UNESCAPED_UNICODE),
                    json_encode($datosNuevos, JSON_UNESCAPED_UNICODE)
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
            
            // Guardar datos del usuario antes de eliminarlo para auditoría
            $datosUsuario = $this->formatearDatosUsuario($user->toArray());
            
            $user->delete();
            
            // Registrar auditoría para eliminación de usuario
            $this->crearAuditoria(
                Auth::user()->correo,
                'Eliminación',
                json_encode($datosUsuario, JSON_UNESCAPED_UNICODE),
                null
            );
            
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

    public function exportUsers()
    {
        if (!PermissionService::hasPermission('usuarios.read')) {
            return redirect()->route('home')->with('error', 'No tienes permiso para exportar usuarios');
        }

        try {
            $users = User::with('rol')->get();
            
            $filename = 'usuarios_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                
                // Encabezados
                fputcsv($file, ['ID', 'Correo', 'Rol', 'Fecha Creación']);
                
                // Datos
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id_email,
                        $user->correo,
                        $user->rol ? $user->rol->nombre : 'Sin rol',
                        $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };

            // Registrar auditoría para exportación
            $this->crearAuditoria(
                Auth::user()->correo,
                'Exportación',
                null,
                'Registros exportados en documento .csv'
            );

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error al exportar usuarios: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar usuarios');
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

    public function exportPermissions()
    {
        if (!PermissionService::hasPermission('permisos.read')) {
            return redirect()->route('home')->with('error', 'No tienes permiso para exportar permisos');
        }

        try {
            $roles = Role::with('permissions')->get();
            
            $filename = 'permisos_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($roles) {
                $file = fopen('php://output', 'w');
                
                // Encabezados
                fputcsv($file, ['Rol', 'Permiso', 'Descripción']);
                
                // Datos
                foreach ($roles as $role) {
                    if ($role->permissions->count() > 0) {
                        foreach ($role->permissions as $permission) {
                            fputcsv($file, [
                                $role->nombre,
                                $permission->permiso,
                                $permission->descripcion ?? 'Sin descripción'
                            ]);
                        }
                    } else {
                        fputcsv($file, [
                            $role->nombre,
                            'Sin permisos asignados',
                            'N/A'
                        ]);
                    }
                }
                
                fclose($file);
            };

            // Registrar auditoría para exportación
            $this->crearAuditoria(
                Auth::user()->correo,
                'Exportación',
                null,
                'Registros exportados en documento .csv'
            );

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error al exportar permisos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar permisos');
        }
    }

    public function show($id)
    {
        $user = User::with(['miembro', 'rol'])->findOrFail($id);
        return response()->json($user);
    }

    private function formatearDatosUsuario($datos)
    {
        // Obtener el nombre del rol si existe
        $rolNombre = '';
        if (isset($datos['rol_id']) && $datos['rol_id']) {
            $rol = Role::find($datos['rol_id']);
            $rolNombre = $rol ? $rol->nombre : 'Sin rol';
        }
        
        // Solo los campos que se muestran en la tabla de usuarios
        return [
            'correo' => $datos['correo'] ?? '',
            'rol' => $rolNombre,
            'estado' => isset($datos['usuario_estado']) ? ($datos['usuario_estado'] ? 'Activo' : 'Inactivo') : ''
        ];
    }

    private function formatearDatosPermisos($grupo, $permisosAgregados, $permisosEliminados)
    {
        // Obtener el rol desde la request
        $rolNombre = '';
        $rolId = request()->input('rol_id');
        if ($rolId) {
            $rol = Role::find($rolId);
            $rolNombre = $rol ? $rol->nombre : 'Sin rol';
        }
        
        // Obtener nombres de permisos agregados
        $permisosAgregadosNombres = [];
        if (!empty($permisosAgregados)) {
            $permisos = Permission::whereIn('id', $permisosAgregados)->get();
            $permisosAgregadosNombres = $permisos->pluck('permiso')->toArray();
        }
        
        // Obtener nombres de permisos eliminados
        $permisosEliminadosNombres = [];
        if (!empty($permisosEliminados)) {
            $permisos = Permission::whereIn('id', $permisosEliminados)->get();
            $permisosEliminadosNombres = $permisos->pluck('permiso')->toArray();
        }
        
        return [
            'rol' => $rolNombre,
            'grupo' => $grupo ?? 'General',
            'permisos_agregados' => $permisosAgregadosNombres,
            'permisos_eliminados' => $permisosEliminadosNombres
        ];
    }

    private function crearAuditoria($correo, $accion, $previo, $posterior = null)
    {
        // Usar la zona horaria de Nicaragua
        $fechaHora = now()->setTimezone('America/Managua');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => 'Usuarios',
            'accion' => $accion,
            'valor_previo' => $previo,
            'valor_posterior' => $posterior ?? '-',
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }
}