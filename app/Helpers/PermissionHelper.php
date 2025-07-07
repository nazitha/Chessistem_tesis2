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
        // Para el módulo de usuarios, verificar permisos específicos
        if ($module === 'usuarios') {
            $permissions = [
                "usuarios.update",
                "usuarios.delete",
                "usuario.updatepermisos"
            ];
        } else {
            // Para otros módulos, usar permisos genéricos
        $permissions = [
            "{$module}.create",
            "{$module}.update",
            "{$module}.delete"
        ];
        }
        
        $result = PermissionService::hasAnyPermission($permissions);
        Log::info('PermissionHelper: Verificando permisos de acción', [
            'module' => $module,
            'permissions' => $permissions,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    public static function canUpdatePermisosUsuario(): bool
    {
        $permission = "usuario.updatepermisos";
        $result = PermissionService::hasPermission($permission);
        Log::info('PermissionHelper: Verificando permiso de actualizar permisos de usuario', [
            'permission' => $permission,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene algún permiso de acción en el módulo de usuarios
     * Incluye los permisos básicos (update, delete) y el permiso específico de actualizar permisos
     *
     * @return bool
     */
    public static function hasAnyUsuarioActionPermission(): bool
    {
        $permissions = [
            "usuarios.update", 
            "usuarios.delete",
            "usuario.updatepermisos"
        ];
        
        // Verificar cada permiso individualmente para debug
        $results = [];
        foreach ($permissions as $permission) {
            $hasPermission = PermissionService::hasPermission($permission);
            $results[$permission] = $hasPermission;
            Log::info("PermissionHelper: Verificando permiso individual", [
                'permission' => $permission,
                'result' => $hasPermission
            ]);
        }
        
        $result = PermissionService::hasAnyPermission($permissions);
        Log::info('PermissionHelper: Verificando permisos de acción de usuarios', [
            'permissions' => $permissions,
            'individual_results' => $results,
            'final_result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene algún permiso de acción en el módulo de miembros
     * Incluye los permisos: update, delete y details
     *
     * @return bool
     */
    public static function hasAnyMiembroActionPermission(): bool
    {
        $permissions = [
            "miembros.update", 
            "miembros.delete",
            "miembros.details"
        ];
        
        $result = PermissionService::hasAnyPermission($permissions);
        Log::info('PermissionHelper: Verificando permisos de acción de miembros', [
            'permissions' => $permissions,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene algún permiso de acción en el módulo de academias
     * Incluye los permisos: update, delete y details
     *
     * @return bool
     */
    public static function hasAnyAcademiaActionPermission(): bool
    {
        $permissions = [
            "academias.update", 
            "academias.delete",
            "academias.details"
        ];
        
        $result = PermissionService::hasAnyPermission($permissions);
        Log::info('PermissionHelper: Verificando permisos de acción de academias', [
            'permissions' => $permissions,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene algún permiso de acción en el módulo de torneos
     * Incluye los permisos: update, delete y details
     *
     * @return bool
     */
    public static function hasAnyTorneoActionPermission(): bool
    {
        $permissions = [
            "torneos.update", 
            "torneos.delete",
            "torneos.details"
        ];
        
        $result = PermissionService::hasAnyPermission($permissions);
        Log::info('PermissionHelper: Verificando permisos de acción de torneos', [
            'permissions' => $permissions,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene algún permiso de acción en el módulo de participantes
     * Incluye los permisos: read, create y delete
     *
     * @return bool
     */
    public static function hasAnyParticipanteActionPermission(): bool
    {
        $permissions = [
            "participantes.read", 
            "participantes.create",
            "participantes.delete"
        ];
        
        $result = PermissionService::hasAnyPermission($permissions);
        Log::info('PermissionHelper: Verificando permisos de acción de participantes', [
            'permissions' => $permissions,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene permiso para ver estadísticas personales
     *
     * @return bool
     */
    public static function canViewMisEstadisticas(): bool
    {
        $permission = "misEstadisticas.read";
        $result = PermissionService::hasPermission($permission);
        Log::info('PermissionHelper: Verificando permiso de estadísticas personales', [
            'permission' => $permission,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene permiso para ver estadísticas administrativas
     *
     * @return bool
     */
    public static function canViewEstadisticasAdmin(): bool
    {
        $permission = "estadisticasAdmin.read";
        $result = PermissionService::hasPermission($permission);
        Log::info('PermissionHelper: Verificando permiso de estadísticas administrativas', [
            'permission' => $permission,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }

    /**
     * Verifica si el usuario tiene permiso para ver auditorías
     *
     * @return bool
     */
    public static function canViewAuditoria(): bool
    {
        $permission = "auditorias.read";
        $result = PermissionService::hasPermission($permission);
        Log::info('PermissionHelper: Verificando permiso de auditoría', [
            'permission' => $permission,
            'result' => $result,
            'user_id' => Auth::id(),
            'rol_id' => Auth::user()->rol_id ?? null
        ]);
        return $result;
    }
} 