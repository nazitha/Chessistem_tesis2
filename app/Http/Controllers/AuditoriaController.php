<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditoria;
use App\Helpers\PermissionHelper;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        // Verificar permiso de lectura
        if (!PermissionHelper::canViewModule('auditorias')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a auditorías.');
        }

        // Parámetros de búsqueda y paginación
        $search = $request->get('search');
        $filtroUsuario = $request->get('filtro_usuario');
        $filtroAccion = $request->get('filtro_accion');
        $filtroTabla = $request->get('filtro_tabla');
        $filtroFecha = $request->get('filtro_fecha');
        $filtroEquipo = $request->get('filtro_equipo');
        $perPage = $request->get('per_page', 20);

        $query = Auditoria::query();

        // Búsqueda general
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('correo_id', 'like', "%{$search}%")
                  ->orWhere('accion', 'like', "%{$search}%")
                  ->orWhere('tabla_afectada', 'like', "%{$search}%")
                  ->orWhere('valor_previo', 'like', "%{$search}%")
                  ->orWhere('valor_posterior', 'like', "%{$search}%")
                  ->orWhere('equipo', 'like', "%{$search}%")
                  ->orWhere('fecha', 'like', "%{$search}%")
                  ->orWhere('hora', 'like', "%{$search}%");
            });
        }

        // Filtros específicos
        if ($filtroUsuario) {
            $query->where('correo_id', 'like', "%{$filtroUsuario}%");
        }
        if ($filtroAccion) {
            $query->where('accion', $filtroAccion);
        }
        if ($filtroTabla) {
            $query->where('tabla_afectada', $filtroTabla);
        }
        if ($filtroFecha) {
            $query->where('fecha', $filtroFecha);
        }
        if ($filtroEquipo) {
            $query->where('equipo', 'like', "%{$filtroEquipo}%");
        }

        // Ordenar y paginar
        $auditorias = $query->orderByDesc('fecha')->orderByDesc('hora')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $auditorias->appends($request->all());

        // Para los selects de filtro (solo si no hay filtros activos para optimizar)
        $usuarios = Auditoria::select('correo_id')->distinct()->orderBy('correo_id')->pluck('correo_id');
        $acciones = Auditoria::select('accion')->distinct()->orderBy('accion')->pluck('accion');
        $tablas = Auditoria::select('tabla_afectada')->distinct()->orderBy('tabla_afectada')->pluck('tabla_afectada');

        return view('auditoria.index', compact('auditorias', 'usuarios', 'acciones', 'tablas', 'search', 'filtroUsuario', 'filtroAccion', 'filtroTabla', 'filtroFecha', 'filtroEquipo', 'perPage'));
    }
} 