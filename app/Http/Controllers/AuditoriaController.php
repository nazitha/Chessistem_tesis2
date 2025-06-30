<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditoria;
use App\Helpers\PermissionHelper;

class AuditoriaController extends Controller
{
    public function __construct()
    {
        // Verificar permiso de lectura para todas las acciones
        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::canViewModule('auditorias')) {
                return redirect()->route('home')->with('error', 'No tienes permisos para acceder a este módulo.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Auditoria::query();

        // Filtros
        if ($request->filled('usuario')) {
            $query->where('correo_id', 'like', '%' . $request->usuario . '%');
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('tabla')) {
            $query->where('tabla_afectada', $request->tabla);
        }
        if ($request->filled('fecha')) {
            $query->where('fecha', $request->fecha);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('correo_id', 'like', "%$search%")
                  ->orWhere('accion', 'like', "%$search%")
                  ->orWhere('tabla_afectada', 'like', "%$search%")
                  ->orWhere('valor_previo', 'like', "%$search%")
                  ->orWhere('valor_posterior', 'like', "%$search%")
                  ->orWhere('equipo', 'like', "%$search%")
                  ->orWhere('fecha', 'like', "%$search%")
                  ->orWhere('hora', 'like', "%$search%") ;
            });
        }

        $auditorias = $query->orderByDesc('fecha')->orderByDesc('hora')->paginate(20)->appends($request->all());

        // Para los selects de filtro
        $usuarios = Auditoria::select('correo_id')->distinct()->pluck('correo_id');
        $acciones = Auditoria::select('accion')->distinct()->pluck('accion');
        $tablas = Auditoria::select('tabla_afectada')->distinct()->pluck('tabla_afectada');

        return view('auditoria.index', compact('auditorias', 'usuarios', 'acciones', 'tablas'));
    }

    /**
     * Obtener datos de auditoría para DataTable
     */
    public function getAuditoriaData()
    {
        $auditorias = Auditoria::orderByDesc('fecha')
            ->orderByDesc('hora')
            ->get();

        return response()->json(['data' => $auditorias]);
    }
} 