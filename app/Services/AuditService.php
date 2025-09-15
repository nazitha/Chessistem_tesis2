<?php
namespace App\Services;

use App\Models\Auditoria;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log an audit entry
     *
     * @param string $user
     * @param mixed $modelo
     * @param string $accion
     * @param mixed|null $previo
     * @return void
     */
    public static function log($user, $modelo, $accion, $previo = null)
    {
        Auditoria::create([
            'usuario' => $user,
            'modelo' => get_class($modelo),
            'modelo_id' => $modelo->id ?? null,
            'accion' => $accion,
            'datos_previos' => $previo ? json_encode($previo) : null,
            'datos_nuevos' => json_encode($modelo->getAttributes()),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public static function logUserAction($email, User $user, $action, $prevData = null): void
    {
        // Mapear acciones en inglés a español
        $accionesEspanol = [
            'deleted' => 'Eliminación',
            'updated' => 'Edición',
            'created' => 'Creación',
            'permissions_updated' => 'Edición de Permisos'
        ];
        
        $logData = [
            'correo_id' => $email,
            'tabla_afectada' => 'Usuarios',
            'accion' => $accionesEspanol[$action] ?? $action,
            'valor_previo' => $prevData ? json_encode($prevData) : null,
            'valor_posterior' => $action !== 'deleted' ? json_encode($user) : null,
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'equipo' => request()->ip()
        ];

        Auditoria::create($logData);
    }

    public static function logPermissionAction($email, $action, Role $role, Permission $permission): void
    {
        Auditoria::create([
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