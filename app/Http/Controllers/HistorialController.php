<?php
namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function cargarDatos()
    {
        $auditorias = Auditoria::with('miembro')
            ->orderBy('fecha', 'DESC')
            ->orderBy('hora', 'DESC')
            ->get()
            ->map(function ($auditoria) {
                return [
                    'usuario' => $auditoria->miembro ? 
                        $auditoria->miembro->nombres . ' ' . $auditoria->miembro->apellidos : 
                        'Usuario desconocido',
                    'correo_id' => $auditoria->correo_id,
                    'tabla_afectada' => $auditoria->tabla_afectada,
                    'accion' => $auditoria->accion,
                    'valor_previo' => $auditoria->valor_previo,
                    'valor_posterior' => $auditoria->valor_posterior,
                    'tiempo' => $auditoria->tiempo,
                    'equipo' => $auditoria->equipo
                ];
            });

        return response()->json($auditorias);
    }
}