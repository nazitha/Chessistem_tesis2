<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    /**
     * Verifica si el usuario tiene un permiso específico
     *
     * @param string $permission Nombre del permiso (ej: usuarios.create)
     * @return bool
     */
    public static function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) {
            Log::info('PermissionService: No hay usuario autenticado');
            return false;
        }

        // Obtener el ID del permiso
        $permissionId = DB::table('permisos')
            ->where('permiso', $permission)
            ->value('id');

        Log::info('PermissionService: Verificando permiso', [
            'user_id' => $user->id_email,
            'rol_id' => $user->rol_id,
            'permission' => $permission,
            'permission_id' => $permissionId
        ]);

        if (!$permissionId) {
            Log::warning('PermissionService: Permiso no encontrado en la base de datos', [
                'permission' => $permission
            ]);
            return false;
        }

        // Verificar directamente en la tabla de asignaciones
        $hasPermission = DB::table('asignaciones_permisos')
            ->where('rol_id', $user->rol_id)
            ->where('permiso_id', $permissionId)
            ->exists();

        Log::info('PermissionService: Resultado de verificación', [
            'user_id' => $user->id_email,
            'rol_id' => $user->rol_id,
            'permission' => $permission,
            'has_permission' => $hasPermission
        ]);

        return $hasPermission;
    }

    /**
     * Verifica si el usuario tiene todos los permisos especificados
     *
     * @param array $permissions Array de permisos a verificar
     * @return bool
     */
    public static function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!self::hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verifica si el usuario tiene al menos uno de los permisos especificados
     *
     * @param array $permissions Array de permisos a verificar
     * @return bool
     */
    public static function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obtiene todos los permisos de un módulo específico
     *
     * @param string $module Nombre del módulo (ej: usuarios)
     * @return array
     */
    public static function getModulePermissions(string $module): array
    {
        return Permission::where('grupo', $module)
            ->pluck('permiso')
            ->toArray();
    }

    /**
     * Limpia la caché de permisos de un usuario
     *
     * @param int $userId ID del usuario
     * @return void
     */
    public static function clearUserPermissionsCache(int $userId): void
    {
        Cache::forget("user_permissions_{$userId}");
    }
} 