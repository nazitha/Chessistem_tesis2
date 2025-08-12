<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TorneosPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permisos para torneos
        $permisosTorneos = [
            'torneos.read',
            'torneos.create', 
            'torneos.update',
            'torneos.delete',
            'torneos.details',
            'torneos.cancel',
            'torneos.emparejamientos',
            'torneos.participantes',
            'torneos.rondas'
        ];

        foreach ($permisosTorneos as $permiso) {
            // Verificar si el permiso ya existe
            $permisoId = DB::table('permisos')->where('permiso', $permiso)->value('id');
            
            if (!$permisoId) {
                // Crear el permiso si no existe
                $permisoId = DB::table('permisos')->insertGetId([
                    'permiso' => $permiso,
                    'descripcion' => 'Permiso para ' . str_replace('.', ' ', $permiso),
                    'grupo' => 'torneos'
                ]);
            }

            // Asignar al rol admin (rol_id = 1)
            DB::table('asignaciones_permisos')->updateOrInsert(
                [
                    'rol_id' => 1,
                    'permiso_id' => $permisoId
                ],
                [
                    'rol_id' => 1,
                    'permiso_id' => $permisoId
                ]
            );
        }

        echo "Permisos de torneos agregados exitosamente:\n";
        foreach ($permisosTorneos as $permiso) {
            echo "- $permiso\n";
        }
    }
}
