<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Federacion;
use App\Models\Miembro;
use App\Models\CategoriaTorneo;
use App\Models\Emparejamiento;
use App\Models\Partida;
use App\Http\Requests\TorneoRequest;
use App\Http\Resources\TorneoResource;
use App\Http\Resources\FederacionResource;
use App\Http\Resources\MiembroRolResource;
use App\Http\Resources\FormatoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditService;
use App\Services\SwissPairingService;
use App\Models\ControlTiempo;
use Illuminate\Http\Request;

class TorneoController extends Controller
{
    public function index()
    {
        $torneos = Torneo::withRelations()->paginate(10);
        return view('torneos.index', compact('torneos'));
    }

    public function create()
    {
        $categorias = CategoriaTorneo::all();
        $miembros = Miembro::all();
        $controlesTiempo = ControlTiempo::all();
        $emparejamientos = Emparejamiento::all();

        return view('torneos.create', compact(
            'categorias',
            'miembros',
            'controlesTiempo',
            'emparejamientos'
        ));
    }

    public function getFederaciones(): JsonResponse
    {
        $federaciones = Federacion::select('acronimo', 'nombre_federacion')
            ->orderBy('nombre_federacion')
            ->get();

        return FederacionResource::collection($federaciones)->response();
    }

    public function getMiembrosRoles(): JsonResponse
    {
        $miembros = Miembro::withUsuarioRol()
            ->excluirRol(3)
            ->get();

        return MiembroRolResource::collection($miembros)->response();
    }

    public function getCategorias(): JsonResponse
    {
        $categorias = CategoriaTorneo::orderBy('categoria_torneo')
            ->get(['id_torneo_categoria', 'categoria_torneo']);

        return response()->json($categorias);
    }

    public function getFormatos(CategoriaTorneo $categoria): JsonResponse
    {
        $formatos = $categoria->controlesTiempo()
            ->with('control')
            ->get();

        return FormatoResource::collection($formatos)->response();
    }

    public function getSistemasEmparejamiento(): JsonResponse
    {
        $sistemas = Emparejamiento::all(['id_emparejamiento', 'sistema']);
        return response()->json($sistemas);
    }

    public function generarEmparejamientos(Torneo $torneo, int $ronda): JsonResponse
    {
        try {
            $service = new SwissPairingService($torneo);
            $emparejamientos = $service->generarEmparejamientos($ronda);

            // Guardar los emparejamientos en la base de datos
            foreach ($emparejamientos as $emparejamiento) {
                if (isset($emparejamiento['bye'])) {
                    // Crear partida con bye
                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante1']->miembro_id,
                        'ronda' => $ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'resultado' => 1, // Victoria por bye
                        'color' => true // Color por defecto
                    ]);
                } else {
                    // Crear partidas para ambos jugadores
                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante1']->miembro_id,
                        'ronda' => $ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'color' => true
                    ]);

                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante2']->miembro_id,
                        'ronda' => $ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'color' => false
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'emparejamientos' => $emparejamientos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al generar emparejamientos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(TorneoRequest $request)
    {
        try {
            DB::beginTransaction();

            // Preparar los datos del torneo
            $datos = $request->validated();
            
            // Si viene como borrador, marcar el estado_torneo como false
            if ($request->has('borrador')) {
                $datos['estado_torneo'] = false;
            }
            // Asegurar que los campos booleanos estén presentes
            $datos = array_merge([
                'estado_torneo' => true,
                'usar_buchholz' => false,
                'usar_sonneborn_berger' => false,
                'usar_desempate_progresivo' => false,
                'permitir_bye' => true,
                'alternar_colores' => true,
                'evitar_emparejamientos_repetidos' => true,
                'maximo_emparejamientos_repetidos' => 1,
                'numero_minimo_participantes' => 4
            ], $datos);

            // Convertir checkboxes a booleanos
            $campos_booleanos = [
                'usar_buchholz',
                'usar_sonneborn_berger',
                'usar_desempate_progresivo',
                'permitir_bye',
                'alternar_colores',
                'evitar_emparejamientos_repetidos'
            ];

            foreach ($campos_booleanos as $campo) {
                $datos[$campo] = isset($datos[$campo]) && $datos[$campo] == '1';
            }

            // Asegurar formato correcto de hora
            if (isset($datos['hora_inicio'])) {
                $datos['hora_inicio'] = date('H:i:s', strtotime($datos['hora_inicio']));
            }

            $torneo = Torneo::create($datos);

            DB::commit();

            $mensaje = $request->has('borrador') ? 'Torneo guardado como borrador.' : 'Torneo creado exitosamente.';
            return redirect()
                ->route('torneos.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear torneo: ' . $e->getMessage());
            Log::error('Datos del torneo: ' . json_encode($datos));
            
            return back()
                ->withInput()
                ->with('error', 'Error al crear el torneo. Por favor, verifica los datos e intenta nuevamente.');
        }
    }

    public function show(Torneo $torneo)
    {
        // Cargar las relaciones necesarias
        $torneo->load([
            'categoria',
            'organizador',
            'controlTiempo',
            'directorTorneo',
            'arbitroPrincipal',
            'arbitro',
            'arbitroAdjunto',
            'federacion',
            'emparejamiento',
            'participantes.miembro',
            'rondas.partidas.jugadorBlancas',
            'rondas.partidas.jugadorNegras'
        ]);

        // Obtener miembros que no están participando en este torneo
        $participantesIds = $torneo->participantes()->pluck('miembro_id')->toArray();
        $miembrosDisponibles = Miembro::whereNotIn('cedula', $participantesIds)
            ->orderBy('nombres')
            ->get();

        return view('torneos.show', compact('torneo', 'miembrosDisponibles'));
    }

    public function edit(Torneo $torneo)
    {
        $categorias = CategoriaTorneo::all();
        $miembros = Miembro::all();
        $controlesTiempo = ControlTiempo::all();
        $emparejamientos = Emparejamiento::all();

        return view('torneos.edit', compact(
            'torneo',
            'categorias',
            'miembros',
            'controlesTiempo',
            'emparejamientos'
        ));
    }

    public function update(TorneoRequest $request, Torneo $torneo)
    {
        try {
            $datos = $request->validated();
            // Siempre activar el torneo al actualizar
            $datos['estado_torneo'] = true;
            $torneo->update($datos);
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('success', 'Torneo actualizado y activado exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el torneo: ' . $e->getMessage());
        }
    }

    public function destroy(Torneo $torneo)
    {
        try {
            $torneo->delete();
            return redirect()
                ->route('torneos.index')
                ->with('success', 'Torneo eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar torneo: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Error al eliminar el torneo. Por favor, intente nuevamente.');
        }
    }

    public function cancelar(Request $request, Torneo $torneo)
    {
        try {
            if ($torneo->fecha_inicio->isPast()) {
                return back()->with('error', 'No se puede cancelar un torneo que ya ha finalizado.');
            }

            if ($torneo->torneo_cancelado) {
                return back()->with('error', 'El torneo ya está cancelado.');
            }

            $request->validate([
                'motivo_cancelacion' => 'required|string|max:255'
            ]);

            $torneo->update([
                'torneo_cancelado' => true,
                'estado_torneo' => false,
                'motivo_cancelacion' => $request->motivo_cancelacion
            ]);

            return redirect()
                ->route('torneos.index')
                ->with('success', 'Torneo cancelado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al cancelar torneo: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Error al cancelar el torneo. Por favor, intente nuevamente.');
        }
    }

    private function logAuditoria($request, $torneo, $accion, $previo = null)
    {
        AuditService::log(
            user: $request->mail_log,
            modelo: $torneo,
            accion: $accion,
            previo: $previo
        );
    }

    public function listaParaDuplicar()
    {
        $torneos = \App\Models\Torneo::orderBy('fecha_inicio', 'desc')
            ->take(10)
            ->get(['id_torneo', 'nombre_torneo', 'fecha_inicio', 'categoriaTorneo_id']);
        foreach ($torneos as $torneo) {
            $torneo->categoria = $torneo->categoria ? $torneo->categoria->categoria_torneo : '';
        }
        return response()->json($torneos);
    }

    public function datosParaDuplicar($id)
    {
        $torneo = \App\Models\Torneo::findOrFail($id);
        return response()->json([
            'nombre_torneo' => $torneo->nombre_torneo,
            'fecha_inicio' => $torneo->fecha_inicio ? $torneo->fecha_inicio->format('Y-m-d') : '',
            'hora_inicio' => $torneo->hora_inicio,
            'lugar' => $torneo->lugar,
            'organizador_id' => $torneo->organizador_id,
            'director_torneo_id' => $torneo->director_torneo_id,
            'arbitro_principal_id' => $torneo->arbitro_principal_id,
            'arbitro_id' => $torneo->arbitro_id,
            'arbitro_adjunto_id' => $torneo->arbitro_adjunto_id,
            'categoriaTorneo_id' => $torneo->categoriaTorneo_id,
            'no_rondas' => $torneo->no_rondas,
            'sistema_emparejamiento_id' => $torneo->sistema_emparejamiento_id,
            'control_tiempo_id' => $torneo->control_tiempo_id,
            'usar_buchholz' => $torneo->usar_buchholz,
            'usar_sonneborn_berger' => $torneo->usar_sonneborn_berger,
            'usar_desempate_progresivo' => $torneo->usar_desempate_progresivo
        ]);
    }
}