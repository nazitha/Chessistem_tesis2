<?php
namespace App\Services;

use App\Models\Audit;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class AuditService
{
    public static function logUserAction($email, User $user, $action, $prevData = null): void
    {
        $logData = [
            'correo_id' => $email,
            'tabla_afectada' => 'Usuarios',
            'accion' => $action,
            'valor_previo' => $prevData ? json_encode($prevData) : null,
            'valor_posterior' => $action !== 'deleted' ? json_encode($user) : null,
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'equipo' => request()->ip()
        ];

        Audit::create($logData);
    }

    public static function logPermissionAction($email, $action, Role $role, Permission $permission): void
    {
        Audit::create([
            'correo_id' => $email,
            'tabla_afectada' => 'Permisos',
            'accion' => $action,
            'valor_previo' => "Rol: {$role->nombre}, Permiso: {$permission->permiso}",
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }
}