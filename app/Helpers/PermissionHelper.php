<?php

namespace App\Helpers;

use App\Services\PermissionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Verifica si el usuario tiene permiso para ver un módulo
     *
     * @param string $module Nombre del módulo
     * @return bool
     */
    public static function canViewModule(string $module): bool
    {
        $permission = "{$module}.read";
        $result = PermissionService::hasPermission($permission);
        Log::info('PermissionHelper: Verificando permiso de lectura', [
            'module' => $module,
            'permission' => $permission,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene permiso para crear en un módulo
     *
     * @param string $module Nombre del módulo
     * @return bool
     */
    public static function canCreate(string $module): bool
    {
        $permission = "{$module}.create";
        $result = PermissionService::hasPermission($permission);
        Log::info('PermissionHelper: Verificando permiso de creación', [
            'module' => $module,
            'permission' => $permission,
            'result' => $result
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene permiso para editar en un módulo
     *
     * @param string $module Nombre del módulo
     * @return bool
     */
    public static function canUpdate(string $module): bool
    {
        $permission = "{$module}.update";
        $result = PermissionService::hasPermission($permission);
        Log::info('PermissionHelper: Verificando permiso de actualización', [
            'module' => $module,
            'permission' => $permission,
            'result' => $result
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene permiso para eliminar en un módulo
     *
     * @param string $module Nombre del módulo
     * @return bool
     */
    public static function canDelete(string $module): bool
    {
        $permission = "{$module}.delete";
        $result = PermissionService::hasPermission($permission);
        Log::info('PermissionHelper: Verificando permiso de eliminación', [
            'module' => $module,
            'permission' => $permission,
            'result' => $result
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene algún permiso de acción en un módulo
     *
     * @param string $module Nombre del módulo
     * @return bool
     */
    public static function hasAnyActionPermission(string $module): bool
    {
        $permissions = [
            "{$module}.create",
            "{$module}.update",
            "{$module}.delete"
        ];
        $result = PermissionService::hasAnyPermission($permissions);
        Log::info('PermissionHelper: Verificando permisos de acción', [
            'module' => $module,
            'permissions' => $permissions,
            'result' => $result
        ]);
        return $result;
    }
} 