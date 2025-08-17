<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si existen los permisos de usuarios, si no, crearlos
        $permisosUsuarios = [
            'usuarios.read',
            'usuarios.create', 
            'usuarios.update',
            'usuarios.delete'
        ];

        foreach ($permisosUsuarios as $permiso) {
            $permisoId = DB::table('permisos')->where('permiso', $permiso)->value('id');
            if (!$permisoId) {
                $permisoId = DB::table('permisos')->insertGetId([
                    'permiso' => $permiso,
                    'descripcion' => 'Permiso para ' . str_replace('.', ' ', $permiso)
                ]);
            }
            
            // Asignar al rol admin
            DB::table('asignaciones_permisos')->updateOrInsert([
                'rol_id' => 1,
                'permiso_id' => $permisoId
            ]);
        }

        echo "Permisos de usuarios asignados al rol admin correctamente.\n";

        $permisosAcademias = [
            'academias.read',
            'academias.create',
            'academias.update',
            'academias.delete'
        ];

        foreach ($permisosAcademias as $permiso) {
            $permisoId = DB::table('permisos')->where('permiso', $permiso)->value('id');
            if (!$permisoId) {
                $permisoId = DB::table('permisos')->insertGetId([
                    'permiso' => $permiso,
                    'descripcion' => 'Permiso para ' . str_replace('.', ' ', $permiso)
                ]);
            }
            DB::table('asignaciones_permisos')->updateOrInsert([
                'rol_id' => 1,
                'permiso_id' => $permisoId
            ]);
        }

        echo "Permisos de academias asignados al rol admin correctamente.\n";

        $permisosMiembros = [
            'miembros.read',
            'miembros.create',
            'miembros.update',
            'miembros.delete',
            'miembros.details'
        ];

        foreach ($permisosMiembros as $permiso) {
            $permisoId = DB::table('permisos')->where('permiso', $permiso)->value('id');
            if (!$permisoId) {
                $permisoId = DB::table('permisos')->insertGetId([
                    'permiso' => $permiso,
                    'descripcion' => 'Permiso para ' . str_replace('.', ' ', $permiso)
                ]);
            }
            DB::table('asignaciones_permisos')->updateOrInsert([
                'rol_id' => 1,
                'permiso_id' => $permisoId
            ]);
        }

        echo "Permisos de miembros asignados al rol admin correctamente.\n";

        // Agregar permisos de asignaciones
        $permisosAsignaciones = [
            'asignaciones.read',
            'asignaciones.update'
        ];

        foreach ($permisosAsignaciones as $permiso) {
            $permisoId = DB::table('permisos')->where('permiso', $permiso)->value('id');
            if (!$permisoId) {
                $permisoId = DB::table('permisos')->insertGetId([
                    'permiso' => $permiso,
                    'descripcion' => 'Permiso para ' . str_replace('.', ' ', $permiso),
                    'grupo' => 'asignaciones'
                ]);
            }
            DB::table('asignaciones_permisos')->updateOrInsert([
                'rol_id' => 1,
                'permiso_id' => $permisoId
            ]);
        }

        echo "Permisos de asignaciones asignados al rol admin correctamente.\n";
    }
} 