<?php

namespace App\Http\Controllers;

use App\Models\Academia;
use App\Models\Ciudad;
use App\Models\Auditoria;
use App\Models\ParticipanteTorneo;
use App\Helpers\PermissionHelper;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AcademiaController extends Controller
{
    public function __construct()
    {
        // Verificar permiso de lectura para todas las acciones excepto export
        $this->middleware(function ($request, $next) {
            if ($request->routeIs('academias.export')) {
                return $next($request);
            }
            
            if (!PermissionHelper::canViewModule('academias')) {
                return redirect()->route('home')->with('error', 'No tienes permisos para acceder a este módulo.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Parámetros de búsqueda y paginación
        $search = $request->get('search');
        $filtroNombre = $request->get('filtro_nombre');
        $filtroCorreo = $request->get('filtro_correo');
        $filtroRepresentante = $request->get('filtro_representante');
        $filtroCiudad = $request->get('filtro_ciudad');
        $filtroEstado = $request->get('filtro_estado');
        $filtroParticipantes = $request->get('filtro_participantes');
        $filtroTorneos = $request->get('filtro_torneos');
        $perPage = $request->get('per_page', 10);
        
        // Query base para academias con relaciones necesarias para las cards
        $academiasQuery = Academia::with(['ciudad.departamento.pais', 'miembros']);
        
        // Aplicar filtros de búsqueda
        if ($search) {
            $academiasQuery->where(function($query) use ($search) {
                $query->where('nombre_academia', 'like', "%{$search}%")
                      ->orWhere('correo_academia', 'like', "%{$search}%")
                      ->orWhere('representante_academia', 'like', "%{$search}%")
                      ->orWhere('direccion_academia', 'like', "%{$search}%")
                      ->orWhereHas('ciudad', function($q) use ($search) {
                          $q->where('nombre_ciudad', 'like', "%{$search}%");
                      });
            });
        }
        
        if ($filtroNombre) {
            $academiasQuery->where('nombre_academia', 'like', "%{$filtroNombre}%");
        }
        
        if ($filtroCorreo) {
            $academiasQuery->where('correo_academia', 'like', "%{$filtroCorreo}%");
        }
        
        if ($filtroRepresentante) {
            $academiasQuery->where('representante_academia', 'like', "%{$filtroRepresentante}%");
        }
        
        if ($filtroCiudad) {
            $academiasQuery->whereHas('ciudad', function($query) use ($filtroCiudad) {
                $query->where('nombre_ciudad', 'like', "%{$filtroCiudad}%");
            });
        }
        
        if ($filtroEstado !== null && $filtroEstado !== '') {
            $academiasQuery->where('estado_academia', $filtroEstado);
        }
        
        // Filtro por mínimo de participantes usando whereExists
        if ($filtroParticipantes !== null && $filtroParticipantes !== '') {
            $academiasQuery->whereExists(function($query) use ($filtroParticipantes) {
                $query->select(DB::raw(1))
                      ->from('miembros')
                      ->whereRaw('miembros.academia_id = academias.id_academia')
                      ->groupBy('miembros.academia_id')
                      ->havingRaw('COUNT(*) >= ?', [$filtroParticipantes]);
            });
        }
        
        // Filtro por mínimo de torneos participados
        if ($filtroTorneos !== null && $filtroTorneos !== '') {
            $academiasQuery->whereHas('miembros', function($query) use ($filtroTorneos) {
                $query->whereHas('participacionesTorneo');
            });
        }
        
        // Agregar el conteo de miembros después de todos los filtros
        $academiasQuery->withCount('miembros');
        
        // Ordenar y paginar
        $academias = $academiasQuery->orderBy('nombre_academia')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $academias->appends($request->all());

        return view('academias.index', compact('academias', 'search', 'filtroNombre', 'filtroCorreo', 'filtroRepresentante', 'filtroCiudad', 'filtroEstado', 'filtroParticipantes', 'filtroTorneos', 'perPage'));
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
            'correo_academia' => 'nullable|email|max:255',
            'telefono_academia' => 'required|string|max:14',
            'representante_academia' => 'required|string|max:255',
            'direccion_academia' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id_ciudad',
            'estado_academia' => 'boolean'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                Log::info('Datos recibidos para crear academia:', $request->all());
                $academia = Academia::create($request->all());
                
                // Formatear datos para auditoría
                $datosNuevos = $this->formatearDatosAcademia($academia->toArray());
                
                $this->crearAuditoria(
                    Auth::user()->correo,
                    'Inserción',
                    null,
                    json_encode($datosNuevos, JSON_UNESCAPED_UNICODE)
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
            'correo_academia' => 'nullable|email|max:255',
            'telefono_academia' => 'required|string|max:14',
            'representante_academia' => 'required|string|max:255',
            'direccion_academia' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id_ciudad',
            'estado_academia' => 'boolean'
        ]);

        try {
            return DB::transaction(function () use ($request, $academia) {
                Log::info('Datos recibidos para actualizar academia:', $request->all());
                $originalData = $academia->toArray();
                $academia->update($request->all());
                
                $datosAnteriores = $this->formatearDatosAcademia($originalData);
                $datosNuevos = $this->formatearDatosAcademia($academia->toArray());
                
                $this->crearAuditoria(
                    Auth::user()->correo,
                    'Edición',
                    json_encode($datosAnteriores, JSON_UNESCAPED_UNICODE),
                    json_encode($datosNuevos, JSON_UNESCAPED_UNICODE)
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
                // Guardar datos de la academia antes de eliminarla para auditoría
                $datosAcademia = $this->formatearDatosAcademia($academia->toArray());
                
                $academia->delete();
                
                // Registrar auditoría para eliminación de academia
                $this->crearAuditoria(
                    Auth::user()->correo,
                    'Eliminación',
                    json_encode($datosAcademia, JSON_UNESCAPED_UNICODE),
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

    public function exportAcademias()
    {
        // Usar exactamente la misma consulta que se usa para llenar los cards
        $academias = Academia::with(['ciudad.departamento.pais', 'miembros'])->get();
        
        $filename = 'academias_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $callback = function() use ($academias) {
            $file = fopen('php://output', 'w');
            
            // Agregar BOM UTF-8 para reconocer acentos y ñ
            fputs($file, "\xEF\xBB\xBF");
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Nombre de la Academia',
                'Correo',
                'Teléfono',
                'Representante',
                'Dirección',
                'Ciudad',
                'Departamento',
                'País',
                'Estado',
                'Participantes Registrados',
                'Torneos Participados'
            ]);
            
            // Datos
            foreach ($academias as $academia) {
                // Calcular torneos participados
                $miembrosIds = $academia->miembros()->pluck('cedula');
                $torneosParticipados = ParticipanteTorneo::whereIn('miembro_id', $miembrosIds)
                    ->distinct('torneo_id')
                    ->count();
                
                fputcsv($file, [
                    $academia->id_academia,
                    $academia->nombre_academia,
                    $academia->correo_academia ?? '',
                    $academia->telefono_academia,
                    $academia->representante_academia,
                    $academia->direccion_academia,
                    $academia->ciudad ? $academia->ciudad->nombre_ciudad : 'Sin ciudad',
                    $academia->ciudad && $academia->ciudad->departamento ? $academia->ciudad->departamento->nombre_depto : '',
                    $academia->ciudad && $academia->ciudad->departamento && $academia->ciudad->departamento->pais ? $academia->ciudad->departamento->pais->nombre_pais : '',
                    $academia->estado_academia ? 'Activo' : 'Inactivo',
                    $academia->miembros()->count(),
                    $torneosParticipados
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
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
        // Usar la zona horaria de Nicaragua
        $fechaHora = now()->setTimezone('America/Managua');
        
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