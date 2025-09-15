<?php

namespace App\Http\Controllers;

use App\Models\Academia;
use App\Models\Ciudad;
use App\Models\Auditoria;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AcademiaController extends Controller
{
    public function __construct()
    {
        // Verificar permiso de lectura para todas las acciones
        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::canViewModule('academias')) {
                return redirect()->route('home')->with('error', 'No tienes permisos para acceder a este módulo.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $academias = Academia::with(['ciudad.departamento.pais'])
            ->orderBy('nombre_academia')
            ->get();

        return view('academias.index', compact('academias'));
    }

    public function create()
    {
        if (!PermissionHelper::canCreate('academias')) {
            return redirect()->route('academias.index')->with('error', 'No tienes permisos para crear academias.');
        }

        $ciudades = Ciudad::with(['departamento.pais'])
            ->whereHas('departamento.pais', function ($query) {
                $query->where(DB::raw('LOWER(nombre_pais)'), 'nicaragua');
            })
            ->orderBy('nombre_ciudad')
            ->get();
        return view('academias.create', compact('ciudades'));
    }

    public function store(Request $request)
    {
        if (!PermissionHelper::canCreate('academias')) {
            return redirect()->route('academias.index')->with('error', 'No tienes permisos para crear academias.');
        }

        $request->validate([
            'nombre_academia' => 'required|string|max:255|unique:academias',
            'correo_academia' => 'required|email|max:255',
            'telefono_academia' => 'required|string|max:20',
            'representante_academia' => 'required|string|max:255',
            'direccion_academia' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id_ciudad',
            'estado_academia' => 'required|boolean'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $academia = Academia::create($request->all());
                
                $this->logAuditoria(
                    Auth::user()->correo,
                    'Academias',
                    'Inserción',
                    null,
                    $academia->toArray()
                );

                return redirect()->route('academias.show', $academia)
                    ->with('success', '¡Academia creada exitosamente!');
            });
        } catch (\Exception $e) {
            Log::error('Error al crear academia: ' . $e->getMessage());
            return redirect()->route('academias.create')
                ->withInput()
                ->with('error', 'Error al crear la academia: ' . $e->getMessage());
        }
    }

    public function show(Academia $academia)
    {
        if (!PermissionHelper::canViewModule('academias')) {
            return redirect()->route('academias.index')->with('error', 'No tienes permisos para ver detalles de academias.');
        }

        $academia->load(['ciudad.departamento.pais']);
        return view('academias.show', ['academia' => $academia]);
    }

    public function edit(Academia $academia)
    {
        if (!PermissionHelper::canUpdate('academias')) {
            return redirect()->route('academias.index')->with('error', 'No tienes permisos para editar academias.');
        }

        $ciudades = Ciudad::with(['departamento.pais'])
            ->whereHas('departamento.pais', function ($query) {
                $query->where(DB::raw('LOWER(nombre_pais)'), 'nicaragua');
            })
            ->orderBy('nombre_ciudad')
            ->get();
        return view('academias.edit', ['academia' => $academia, 'ciudades' => $ciudades]);
    }

    public function update(Request $request, Academia $academia)
    {
        if (!PermissionHelper::canUpdate('academias')) {
            return redirect()->route('academias.index')->with('error', 'No tienes permisos para editar academias.');
        }

        $request->validate([
            'nombre_academia' => 'required|string|max:255|unique:academias,nombre_academia,' . $academia->id_academia . ',id_academia',
            'correo_academia' => 'required|email|max:255',
            'telefono_academia' => 'required|string|max:20',
            'representante_academia' => 'required|string|max:255',
            'direccion_academia' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id_ciudad',
            'estado_academia' => 'required|boolean'
        ]);

        try {
            return DB::transaction(function () use ($request, $academia) {
                $originalData = $academia->toArray();
                $academia->update($request->all());
                
                $datosAnteriores = $this->formatearDatosAcademia($originalData);
                $datosNuevos = $this->formatearDatosAcademia($academia->toArray());
                
                $this->crearAuditoria(
                    Auth::user()->correo,
                    'Edición',
                    json_encode($datosAnteriores),
                    json_encode($datosNuevos)
                );

                return redirect()->route('academias.show', $academia)
                    ->with('success', '¡Academia actualizada exitosamente!');
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar academia: ' . $e->getMessage());
            return redirect()->route('academias.edit', $academia)
                ->withInput()
                ->with('error', 'Error al actualizar la academia: ' . $e->getMessage());
        }
    }

    public function destroy(Academia $academia)
    {
        if (!PermissionHelper::canDelete('academias')) {
            return redirect()->route('academias.index')->with('error', 'No tienes permisos para eliminar academias.');
        }

        try {
            return DB::transaction(function () use ($academia) {
                $originalData = $academia->toArray();
                $academia->delete();
                
                $this->logAuditoria(
                    Auth::user()->correo,
                    'Academias',
                    'Eliminación',
                    $originalData,
                    null
                );

                return redirect()->route('academias.index')
                    ->with('success', '¡Academia eliminada exitosamente!');
            });
        } catch (\Exception $e) {
            Log::error('Error al eliminar academia: ' . $e->getMessage());
            return redirect()->route('academias.index')
                ->with('error', 'Error al eliminar la academia: ' . $e->getMessage());
        }
    }

    private function logAuditoria(
        string $correo,
        string $tabla,
        string $accion,
        ?array $previo,
        ?array $posterior
    ): void {
        // Usar la zona horaria de Guatemala
        $fechaHora = now()->setTimezone('America/Guatemala');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => $tabla,
            'accion' => $accion,
            'valor_previo' => $previo ? json_encode($previo) : '[-]',
            'valor_posterior' => $posterior ? json_encode($posterior) : '[-]',
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }

    private function formatearDatosAcademia($datos)
    {
        // Obtener nombre de la ciudad si existe
        $ciudadNombre = '';
        if (isset($datos['ciudad_id']) && $datos['ciudad_id']) {
            $ciudad = \App\Models\Ciudad::find($datos['ciudad_id']);
            $ciudadNombre = $ciudad ? $ciudad->nombre_ciudad : 'Sin ciudad';
        }
        
        // Solo los campos que se muestran en la tabla de academias
        return [
            'academia' => $datos['nombre_academia'] ?? '',
            'correo' => $datos['correo_academia'] ?? '',
            'telefono' => $datos['telefono_academia'] ?? '',
            'director' => $datos['representante_academia'] ?? '',
            'direccion' => $datos['direccion_academia'] ?? '',
            'ciudad' => $ciudadNombre,
            'estado' => isset($datos['estado_academia']) ? ($datos['estado_academia'] ? 'Activo' : 'Inactivo') : ''
        ];
    }

    private function crearAuditoria($correo, $accion, $previo, $posterior = null)
    {
        // Usar la zona horaria de Guatemala
        $fechaHora = now()->setTimezone('America/Guatemala');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => 'Academias',
            'accion' => $accion,
            'valor_previo' => $previo,
            'valor_posterior' => $posterior ?? '-',
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }
}