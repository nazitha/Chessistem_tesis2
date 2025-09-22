<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParticipanteTorneo;
use App\Models\Torneo;
use App\Models\Miembro;
use App\Models\Auditoria;
use App\Helpers\PermissionHelper;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParticipantesController extends Controller
{
    public function __construct()
    {
        // Verificar permiso de lectura para todas las acciones
        $this->middleware(function ($request, $next) {
            Log::info('=== MIDDLEWARE PARTICIPANTES EJECUTADO ===');
            Log::info('Ruta: ' . $request->getPathInfo());
            Log::info('Usuario: ' . (Auth::check() ? Auth::user()->correo : 'No autenticado'));
            
            $canView = PermissionHelper::canViewModule('participantes');
            Log::info('canViewModule(participantes): ' . ($canView ? 'SÍ' : 'NO'));
            
            if (!$canView) {
                Log::error('Usuario bloqueado por middleware - sin permisos para participantes');
                return redirect()->route('home')->with('error', 'No tienes permisos para acceder a este módulo.');
            }
            
            Log::info('Middleware: Continuando a la acción');
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Parámetros de búsqueda y paginación
        $search = $request->get('search');
        $filtroMiembro = $request->get('filtro_miembro');
        $filtroTorneo = $request->get('filtro_torneo');
        $filtroEstado = $request->get('filtro_estado');
        $filtroPuntos = $request->get('filtro_puntos');
        $filtroPosicion = $request->get('filtro_posicion');
        $perPage = $request->get('per_page', 20);
        
        // Query base para participantes
        $participantesQuery = ParticipanteTorneo::with(['torneo', 'miembro.usuario']);
        
        // Aplicar filtros de búsqueda
        if ($search) {
            $participantesQuery->where(function($query) use ($search) {
                $query->whereHas('miembro', function($q) use ($search) {
                    $q->where('nombres', 'like', "%{$search}%")
                      ->orWhere('apellidos', 'like', "%{$search}%")
                      ->orWhere('cedula', 'like', "%{$search}%");
                })
                ->orWhereHas('torneo', function($q) use ($search) {
                    $q->where('nombre_torneo', 'like', "%{$search}%");
                })
                ->orWhere('numero_inicial', 'like', "%{$search}%")
                ->orWhere('puntos', 'like', "%{$search}%")
                ->orWhere('posicion', 'like', "%{$search}%");
            });
        }
        
        if ($filtroMiembro) {
            $participantesQuery->whereHas('miembro', function($query) use ($filtroMiembro) {
                $query->where('nombres', 'like', "%{$filtroMiembro}%")
                      ->orWhere('apellidos', 'like', "%{$filtroMiembro}%");
            });
        }
        
        if ($filtroTorneo) {
            $participantesQuery->whereHas('torneo', function($query) use ($filtroTorneo) {
                $query->where('nombre_torneo', 'like', "%{$filtroTorneo}%");
            });
        }
        
        if ($filtroEstado !== null && $filtroEstado !== '') {
            $participantesQuery->where('activo', $filtroEstado);
        }
        
        if ($filtroPuntos !== null && $filtroPuntos !== '') {
            $participantesQuery->where('puntos', '>=', $filtroPuntos);
        }
        
        if ($filtroPosicion !== null && $filtroPosicion !== '') {
            $participantesQuery->where('posicion', '<=', $filtroPosicion);
        }
        
        // Ordenar y paginar
        $participantes = $participantesQuery->orderByDesc('created_at')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $participantes->appends($request->all());

        // Para los selects de filtro
        $torneos = Torneo::select('id', 'nombre_torneo')->orderBy('nombre_torneo')->get();
        $miembros = Miembro::select('cedula', 'nombres', 'apellidos')->orderBy('nombres')->get();

        return view('participantes.index', compact('participantes', 'torneos', 'miembros', 'search', 'filtroMiembro', 'filtroTorneo', 'filtroEstado', 'filtroPuntos', 'filtroPosicion', 'perPage'));
    }

    public function show(ParticipanteTorneo $participante)
    {
        if (!PermissionHelper::canViewModule('participantes')) {
            return redirect()->route('participantes.index')->with('error', 'No tienes permisos para ver detalles de participantes.');
        }

        $participante->load(['torneo', 'miembro.usuario']);
        return view('participantes.show', ['participante' => $participante]);
    }

    public function destroy(ParticipanteTorneo $participante)
    {
        if (!PermissionHelper::canDelete('participantes')) {
            return redirect()->route('participantes.index')->with('error', 'No tienes permisos para eliminar participantes.');
        }

        try {
            // Verificar si el torneo tiene rondas jugadas
            if ($participante->torneo->rondas()->count() > 0) {
                return redirect()->route('participantes.index')->with('error', 'No se puede eliminar un participante de un torneo que ya tiene rondas jugadas.');
            }

            // Preparar datos para auditoría antes de eliminar
            $datosParticipante = [
                'torneo' => $participante->torneo ? $participante->torneo->nombre_torneo : 'Sin torneo',
                'miembro' => $participante->miembro ? $participante->miembro->nombres . ' ' . $participante->miembro->apellidos : 'Sin miembro',
                'numero_inicial' => $participante->numero_inicial,
                'puntos' => $participante->puntos,
                'posicion' => $participante->posicion,
                'activo' => $participante->activo
            ];

            $participante->delete();
            
            // Registrar auditoría para retiro
            $this->crearAuditoria(
                Auth::user()->correo,
                'Retiro',
                json_encode($datosParticipante, JSON_UNESCAPED_UNICODE),
                null
            );
            
            return redirect()->route('participantes.index')->with('success', 'Participante eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar participante: ' . $e->getMessage());
            return redirect()->route('participantes.index')->with('error', 'Error al eliminar el participante: ' . $e->getMessage());
        }
    }

    public function exportParticipantes()
    {
        // Log para verificar si el método se ejecuta desde el navegador
        Log::info('=== EXPORT PARTICIPANTES LLAMADO DESDE NAVEGADOR ===');
        Log::info('Usuario: ' . (Auth::check() ? Auth::user()->correo : 'No autenticado'));
        Log::info('IP: ' . request()->ip());
        Log::info('User Agent: ' . request()->userAgent());
        
        if (!PermissionService::hasPermission('participantes.read')) {
            Log::error('Usuario sin permisos para exportar participantes');
            return redirect()->route('home')->with('error', 'No tienes permiso para exportar participantes');
        }

        try {
            // Registrar auditoría para exportación PRIMERO
            Log::info('ParticipantesController: Iniciando exportación y auditoría');
            $this->crearAuditoria(
                Auth::user()->correo,
                'Exportación',
                null,
                'Registros exportados en documento .csv'
            );
            Log::info('ParticipantesController: Auditoría registrada exitosamente');

            $participantes = ParticipanteTorneo::with(['torneo', 'miembro.usuario'])->get();
            
            $filename = 'participantes_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            $callback = function() use ($participantes) {
                $file = fopen('php://output', 'w');
                
                // Agregar BOM UTF-8 para compatibilidad con Excel
                fputs($file, "\xEF\xBB\xBF");
                
                // Encabezados
                fputcsv($file, ['ID', 'Torneo', 'Miembro', 'Número Inicial', 'Puntos', 'Posición', 'Buchholz', 'Sonneborn-Berger', 'Progresivo', 'Estado']);
                
                // Datos
                foreach ($participantes as $participante) {
                    fputcsv($file, [
                        $participante->id,
                        $participante->torneo ? $participante->torneo->nombre_torneo : 'Sin torneo',
                        $participante->miembro ? $participante->miembro->nombres . ' ' . $participante->miembro->apellidos : 'Sin miembro',
                        $participante->numero_inicial ?? '',
                        $participante->puntos ?? 0,
                        $participante->posicion ?? '',
                        $participante->buchholz ?? 0,
                        $participante->sonneborn_berger ?? 0,
                        $participante->progresivo ?? 0,
                        $participante->activo ? 'Activo' : 'Inactivo'
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error al exportar participantes: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar participantes');
        }
    }

    private function crearAuditoria($correo, $accion, $previo, $posterior = null)
    {
        // Usar la zona horaria de Nicaragua
        $fechaHora = now()->setTimezone('America/Managua');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => 'Participantes',
            'accion' => $accion,
            'valor_previo' => $previo,
            'valor_posterior' => $posterior ?? '-',
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }
}
