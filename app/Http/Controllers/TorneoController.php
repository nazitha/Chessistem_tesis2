<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Federacion;
use App\Models\Miembro;
use App\Models\CategoriaTorneo;
use App\Models\Emparejamiento;
use App\Models\Partida;
use App\Models\RondaTorneo;
use App\Models\Auditoria;
use App\Http\Requests\TorneoRequest;
use App\Http\Resources\TorneoResource;
use App\Http\Resources\FederacionResource;
use App\Http\Resources\MiembroRolResource;
use App\Http\Resources\FormatoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;
use App\Services\SwissPairingService;
use App\Models\ControlTiempo;
use Illuminate\Http\Request;

class TorneoController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $filtroNombre = $request->get('filtro_nombre');
        $filtroLugar = $request->get('filtro_lugar');
        $filtroEstado = $request->get('filtro_estado');
        $filtroParticipantes = $request->get('filtro_participantes');
        $filtroRondas = $request->get('filtro_rondas');
        $filtroRondasTotal = $request->get('filtro_rondas_total');
        $filtroRondasDisputar = $request->get('filtro_rondas_disputar');
        $filtroCategoria = $request->get('filtro_categoria');
        $filtroFederacion = $request->get('filtro_federacion');
        $filtroOrganizador = $request->get('filtro_organizador');
        $filtroDirector = $request->get('filtro_director');
        $filtroArbitro = $request->get('filtro_arbitro');
        $filtroArbitroPrincipal = $request->get('filtro_arbitro_principal');
        $filtroArbitroAdjunto = $request->get('filtro_arbitro_adjunto');
        
        $query = Torneo::withRelations();
        
        // Aplicar filtros de búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_torneo', 'like', "%{$search}%")
                  ->orWhere('lugar', 'like', "%{$search}%")
                  ->orWhereHas('categoria', function($catQuery) use ($search) {
                      $catQuery->where('categoria_torneo', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filtros avanzados
        if ($filtroNombre) {
            $query->where('nombre_torneo', 'like', "%{$filtroNombre}%");
        }
        
        if ($filtroLugar) {
            $query->where('lugar', 'like', "%{$filtroLugar}%");
        }
        
        if ($filtroEstado) {
            switch ($filtroEstado) {
                case 'Activo':
                    $query->where('estado_torneo', true)
                          ->where('torneo_cancelado', false)
                          ->where('fecha_inicio', '>', now()->startOfDay());
                    break;
                case 'Borrador':
                    $query->where('estado_torneo', false);
                    break;
                case 'Finalizado':
                    $query->where('fecha_inicio', '<=', now()->startOfDay())
                          ->where('torneo_cancelado', false);
                    break;
                case 'Cancelado':
                    $query->where('torneo_cancelado', true);
                    break;
            }
        }
        
        // Filtro por participantes mínimos
        if ($filtroParticipantes !== null && $filtroParticipantes !== '') {
            $query->whereHas('participantes', function($q) use ($filtroParticipantes) {
                // No necesitamos hacer nada aquí, solo verificar que tenga participantes
            })->withCount('participantes')->having('participantes_count', '>=', $filtroParticipantes);
        }
        
        // Filtro por rondas disputadas mínimas
        if ($filtroRondas !== null && $filtroRondas !== '') {
            $query->whereHas('rondas', function($q) use ($filtroRondas) {
                // No necesitamos hacer nada aquí, solo verificar que tenga rondas
            })->withCount('rondas')->having('rondas_count', '>=', $filtroRondas);
        }
        
        // Filtro por total de rondas
        if ($filtroRondasTotal !== null && $filtroRondasTotal !== '') {
            $query->where('no_rondas', '>=', $filtroRondasTotal);
        }
        
        // Filtro por rondas a disputar (rondas restantes)
        if ($filtroRondasDisputar !== null && $filtroRondasDisputar !== '') {
            $query->whereRaw('no_rondas - (SELECT COUNT(*) FROM rondas_torneo WHERE torneo_id = torneos.id) >= ?', [$filtroRondasDisputar]);
        }
        
        // Filtro por categoría
        if ($filtroCategoria !== null && $filtroCategoria !== '') {
            $query->where('categoria_id', $filtroCategoria);
        }
        
        // Filtro por federación
        if ($filtroFederacion !== null && $filtroFederacion !== '') {
            $query->where('federacion_id', $filtroFederacion);
        }
        
        // Filtros por organizadores
        if ($filtroOrganizador !== null && $filtroOrganizador !== '') {
            $query->whereHas('organizador', function($q) use ($filtroOrganizador) {
                $q->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$filtroOrganizador}%"]);
            });
        }
        
        if ($filtroDirector !== null && $filtroDirector !== '') {
            $query->whereHas('directorTorneo', function($q) use ($filtroDirector) {
                $q->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$filtroDirector}%"]);
            });
        }
        
        if ($filtroArbitro !== null && $filtroArbitro !== '') {
            $query->whereHas('arbitro', function($q) use ($filtroArbitro) {
                $q->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$filtroArbitro}%"]);
            });
        }
        
        if ($filtroArbitroPrincipal !== null && $filtroArbitroPrincipal !== '') {
            $query->whereHas('arbitroPrincipal', function($q) use ($filtroArbitroPrincipal) {
                $q->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$filtroArbitroPrincipal}%"]);
            });
        }
        
        if ($filtroArbitroAdjunto !== null && $filtroArbitroAdjunto !== '') {
            $query->whereHas('arbitroAdjunto', function($q) use ($filtroArbitroAdjunto) {
                $q->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$filtroArbitroAdjunto}%"]);
            });
        }
        
        $torneos = $query->orderBy('fecha_inicio', 'desc')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $torneos->appends($request->all());
        
        // Obtener datos para los selectores
        $categoriasTorneo = \App\Models\CategoriaTorneo::orderBy('categoria_torneo')->get();
        $federaciones = \App\Models\Federacion::orderBy('nombre_federacion')->get();
        
        return view('torneos.index', compact('torneos', 'search', 'filtroNombre', 'filtroLugar', 'filtroEstado', 'filtroParticipantes', 'filtroRondas', 'filtroRondasTotal', 'filtroRondasDisputar', 'filtroCategoria', 'filtroFederacion', 'filtroOrganizador', 'filtroDirector', 'filtroArbitro', 'filtroArbitroPrincipal', 'filtroArbitroAdjunto', 'categoriasTorneo', 'federaciones', 'perPage'));
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

    public function generarEmparejamientos(Torneo $torneo, RondaTorneo $ronda): JsonResponse
    {
        try {
            $service = new SwissPairingService($torneo);
            $emparejamientos = $service->generarEmparejamientos($ronda);

            // Preparar datos para auditoría
            $datosEmparejamiento = [
                'torneo' => $torneo->nombre_torneo,
                'ronda' => $ronda->numero_ronda,
                'emparejamientos' => []
            ];

            // Guardar los emparejamientos en la base de datos
            foreach ($emparejamientos as $emparejamiento) {
                if (isset($emparejamiento['bye'])) {
                    // Crear partida con bye
                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante1']->miembro_id,
                        'ronda' => $ronda->numero_ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'resultado' => 1, // Victoria por bye
                        'color' => true // Color por defecto
                    ]);

                    // Agregar a datos de auditoría
                    $datosEmparejamiento['emparejamientos'][] = [
                        'mesa' => $emparejamiento['mesa'],
                        'participante' => $emparejamiento['participante1']->miembro->nombres . ' ' . $emparejamiento['participante1']->miembro->apellidos,
                        'tipo' => 'bye'
                    ];
                } else {
                    // Crear partidas para ambos jugadores
                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante1']->miembro_id,
                        'ronda' => $ronda->numero_ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'color' => true
                    ]);

                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante2']->miembro_id,
                        'ronda' => $ronda->numero_ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'color' => false
                    ]);

                    // Agregar a datos de auditoría
                    $datosEmparejamiento['emparejamientos'][] = [
                        'mesa' => $emparejamiento['mesa'],
                        'participante1' => $emparejamiento['participante1']->miembro->nombres . ' ' . $emparejamiento['participante1']->miembro->apellidos,
                        'participante2' => $emparejamiento['participante2']->miembro->nombres . ' ' . $emparejamiento['participante2']->miembro->apellidos,
                        'tipo' => 'partida'
                    ];
                }
            }

            // Registrar auditoría para emparejamiento (como acción de Participantes)
            Log::info('TorneoController: Registrando auditoría de emparejamiento');
            $mensajeAuditoria = "Emparejamiento realizado - Torneo: {$torneo->nombre_torneo} - Ronda: {$ronda->numero_ronda}";
            
            // Crear auditoría directamente con tabla_afectada = 'Participantes'
            $fechaHora = now()->setTimezone('America/Managua');
            Auditoria::create([
                'correo_id' => Auth::user()->correo,
                'tabla_afectada' => 'Participantes',
                'accion' => 'Emparejamiento',
                'valor_previo' => null,
                'valor_posterior' => $mensajeAuditoria,
                'fecha' => $fechaHora->toDateString(),
                'hora' => $fechaHora->toTimeString(),
                'equipo' => request()->ip()
            ]);
            
            Log::info('TorneoController: Auditoría de emparejamiento registrada exitosamente');

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

            // Registrar auditoría para creación de torneo
            $this->crearAuditoria(
                Auth::user()->correo,
                'Inserción',
                null,
                "[Torneo creado: {$torneo->nombre_torneo} - Fecha: {$torneo->fecha_inicio} - Lugar: {$torneo->lugar}]"
            );

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

        // Obtener miembros disponibles para inscripción según el tipo de torneo
        if ($torneo->es_por_equipos) {
            // Excluir miembros ya asignados a cualquier equipo del torneo
            $equipoIds = \App\Models\EquipoTorneo::where('torneo_id', $torneo->id)->pluck('id');
            $ocupadosIds = \App\Models\EquipoJugador::whereIn('equipo_id', $equipoIds)->pluck('miembro_id');
            $miembrosDisponibles = Miembro::whereNotIn('cedula', $ocupadosIds)
                ->orderBy('nombres')
                ->get();
        } else {
            // Torneo individual: excluir participantes ya inscritos
            $participantesIds = $torneo->participantes()->pluck('miembro_id');
            $miembrosDisponibles = Miembro::whereNotIn('cedula', $participantesIds)
                ->orderBy('nombres')
                ->get();
        }

        // Verificar si el torneo está finalizado (todas las rondas completadas)
        $torneoFinalizado = $torneo->rondas->count() >= $torneo->no_rondas;
        
        // Inicializar equipos como colección vacía
        $equipos = collect();
        
        // Si el torneo está finalizado y es por equipos, calcular clasificación
        if ($torneoFinalizado && $torneo->es_por_equipos) {
            $equipos = $torneo->equipos()->with(['jugadores.miembro'])->get();
            
            foreach ($equipos as $equipo) {
                // Calcular puntos totales
                $puntosTotales = 0;
                $partidasTotales = \App\Models\PartidaIndividual::whereHas('match', function($q) use ($equipo) {
                    $q->where('torneo_id', $equipo->torneo_id)
                      ->where(function($q2) use ($equipo) {
                          $q2->where('equipo_a_id', $equipo->id)->orWhere('equipo_b_id', $equipo->id);
                      });
                })->get();
                
                foreach ($partidasTotales as $partida) {
                    if ($partida->match->equipo_a_id === $equipo->id) {
                        $puntosTotales += $partida->resultado ?? 0;
                    } elseif ($partida->match->equipo_b_id === $equipo->id) {
                        $puntosTotales += $partida->resultado !== null ? 1 - $partida->resultado : 0;
                    }
                }
                
                $equipo->puntos_totales = $puntosTotales;
                
                // Calcular criterios de desempate si están habilitados
                if ($torneo->usar_buchholz || $torneo->usar_sonneborn_berger || $torneo->usar_desempate_progresivo) {
                    $this->calcularCriteriosDesempateEquipos($torneo, $equipo);
                }
            }
            
            // Ordenar equipos por puntos y criterios de desempate
            $equipos = $equipos->sortByDesc('puntos_totales');
            if ($torneo->usar_buchholz) {
                $equipos = $equipos->sortByDesc('buchholz');
            }
            if ($torneo->usar_sonneborn_berger) {
                $equipos = $equipos->sortByDesc('sonneborn');
            }
            if ($torneo->usar_desempate_progresivo) {
                $equipos = $equipos->sortByDesc('progresivo');
            }
        } elseif ($torneoFinalizado && !$torneo->es_por_equipos) {
            // Calcular criterios de desempate para participantes individuales
            if ($torneo->usar_buchholz || $torneo->usar_sonneborn_berger || $torneo->usar_desempate_progresivo) {
                $this->calcularCriteriosDesempateIndividuales($torneo);
            }
        }
        
        return view('torneos.show', compact('torneo', 'miembrosDisponibles', 'torneoFinalizado', 'equipos'));
    }

    private function calcularCriteriosDesempateIndividuales(Torneo $torneo)
    {
        $participantes = $torneo->participantes;
        
        foreach ($participantes as $participante) {
            $buchholz = 0;
            $sonnebornBerger = 0;
            $progresivo = 0;
            $puntosAcumulados = 0;
            
            foreach ($torneo->rondas as $ronda) {
                foreach ($ronda->partidas as $partida) {
                    if ($partida->jugador_blancas_id === $participante->miembro_id) {
                        if ($partida->jugador_negras_id) { // No contar bye
                            $oponente = $participantes->where('miembro_id', $partida->jugador_negras_id)->first();
                            if ($oponente) {
                                $buchholz += $oponente->puntos;
                                if ($partida->resultado === 1) { // Victoria con blancas
                                    $sonnebornBerger += $oponente->puntos;
                                } elseif ($partida->resultado === 0.5) { // Tablas
                                    $sonnebornBerger += $oponente->puntos / 2;
                                }
                            }
                        }
                        if ($partida->resultado === 1) {
                            $puntosAcumulados += 1;
                        } elseif ($partida->resultado === 0.5) {
                            $puntosAcumulados += 0.5;
                        }
                    } elseif ($partida->jugador_negras_id === $participante->miembro_id) {
                        $oponente = $participantes->where('miembro_id', $partida->jugador_blancas_id)->first();
                        if ($oponente) {
                            $buchholz += $oponente->puntos;
                            if ($partida->resultado === 0) { // Victoria con negras
                                $sonnebornBerger += $oponente->puntos;
                            } elseif ($partida->resultado === 0.5) { // Tablas
                                $sonnebornBerger += $oponente->puntos / 2;
                            }
                        }
                        if ($partida->resultado === 0) {
                            $puntosAcumulados += 1;
                        } elseif ($partida->resultado === 0.5) {
                            $puntosAcumulados += 0.5;
                        }
                    }
                }
                $progresivo += $puntosAcumulados;
            }
            
            // Guardar los valores calculados en la base de datos
            $participante->update([
                'buchholz' => $buchholz,
                'sonneborn_berger' => $sonnebornBerger,
                'progresivo' => $progresivo
            ]);
            
            // También asignar a la instancia en memoria para la vista
            $participante->buchholz = $buchholz;
            $participante->sonneborn_berger = $sonnebornBerger;
            $participante->progresivo = $progresivo;
        }
    }

    private function calcularCriteriosDesempateEquipos(Torneo $torneo, $equipo)
    {
        $equipos = $torneo->equipos;
        $buchholz = 0;
        $sonneborn = 0;
        $progresivo = 0;
        $acumulado = 0;
        
        $matchesJugados = \App\Models\EquipoMatch::where('torneo_id', $torneo->id)
            ->where(function($q) use ($equipo) {
                $q->where('equipo_a_id', $equipo->id)->orWhere('equipo_b_id', $equipo->id);
            })
            ->orderBy('ronda')
            ->get();
            
        foreach ($matchesJugados as $match) {
            $esA = $match->equipo_a_id === $equipo->id;
            $oponente = $esA ? $match->equipoB : $match->equipoA;
            $puntosEquipo = $esA ? $match->puntos_equipo_a : $match->puntos_equipo_b;
            
            if ($oponente) {
                // Calcular puntos totales del oponente
                $puntosOponente = 0;
                $partidasOponente = \App\Models\PartidaIndividual::whereHas('match', function($q) use ($oponente) {
                    $q->where('torneo_id', $oponente->torneo_id)
                      ->where(function($q2) use ($oponente) {
                          $q2->where('equipo_a_id', $oponente->id)->orWhere('equipo_b_id', $oponente->id);
                      });
                })->get();
                
                foreach ($partidasOponente as $partida) {
                    if ($partida->match->equipo_a_id === $oponente->id) {
                        $puntosOponente += $partida->resultado ?? 0;
                    } elseif ($partida->match->equipo_b_id === $oponente->id) {
                        $puntosOponente += $partida->resultado !== null ? 1 - $partida->resultado : 0;
                    }
                }
                
                // Buchholz: suma los puntos totales de los rivales enfrentados
                $buchholz += $puntosOponente;
                
                // Sonneborn-Berger: victoria suma puntos del rival, empate suma la mitad
                if ($match->resultado_match !== null) {
                    if (($esA && $match->resultado_match == 1) || (!$esA && $match->resultado_match == 2)) {
                        $sonneborn += $puntosOponente;
                    } elseif ($match->resultado_match == 0) {
                        $sonneborn += $puntosOponente / 2;
                    }
                }
            }
            
            // Progresivo: suma acumulativa de puntos por ronda
            $acumulado += $puntosEquipo ?? 0;
            $progresivo += $acumulado;
        }
        
        $equipo->buchholz = $buchholz;
        $equipo->sonneborn = $sonneborn;
        $equipo->progresivo = $progresivo;
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
            // Guardar datos previos para auditoría
            $datosPrevios = $this->formatearDatosTorneo($torneo->toArray());
            
            $datos = $request->validated();
            // Siempre activar el torneo al actualizar
            $datos['estado_torneo'] = true;
            $torneo->update($datos);

            // Formatear datos nuevos para auditoría
            $datosNuevos = $this->formatearDatosTorneo($torneo->fresh()->toArray());

            // Registrar auditoría para actualización de torneo
            $this->crearAuditoria(
                Auth::user()->correo,
                'Edición',
                json_encode($datosPrevios),
                json_encode($datosNuevos)
            );

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
            // Guardar datos del torneo antes de eliminarlo para auditoría
            $datosTorneo = $this->formatearDatosTorneo($torneo->toArray());
            
            $torneo->delete();

            // Registrar auditoría para eliminación de torneo
            $this->crearAuditoria(
                Auth::user()->correo,
                'Eliminación',
                json_encode($datosTorneo),
                null
            );

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

            // Registrar auditoría para cancelación de torneo
            $this->crearAuditoria(
                Auth::user()->correo,
                'Cancelación',
                "[Torneo activo: {$torneo->nombre_torneo} - Fecha: {$torneo->fecha_inicio} - Lugar: {$torneo->lugar}]",
                "[Torneo cancelado: {$torneo->nombre_torneo} - Motivo: {$request->motivo_cancelacion}]"
            );

            return redirect()
                ->route('torneos.index')
                ->with('success', 'Torneo cancelado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al cancelar torneo: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Error al cancelar el torneo. Por favor, intente nuevamente.');
        }
    }

    private function formatearDatosTorneo($datos)
    {
        // Obtener nombres de las relaciones
        $categoriaNombre = '';
        if (isset($datos['categoriaTorneo_id']) && $datos['categoriaTorneo_id']) {
            $categoria = CategoriaTorneo::find($datos['categoriaTorneo_id']);
            $categoriaNombre = $categoria ? $categoria->categoria_torneo : 'Sin categoría';
        }
        
        $emparejamientoNombre = '';
        if (isset($datos['sistema_emparejamiento_id']) && $datos['sistema_emparejamiento_id']) {
            $emparejamiento = Emparejamiento::find($datos['sistema_emparejamiento_id']);
            $emparejamientoNombre = $emparejamiento ? $emparejamiento->sistema : 'Sin sistema';
        }
        
        $federacionNombre = '';
        if (isset($datos['federacion_id']) && $datos['federacion_id']) {
            $federacion = Federacion::find($datos['federacion_id']);
            $federacionNombre = $federacion ? $federacion->nombre_federacion : 'Sin federación';
        }
        
        // Solo los campos que se muestran en la tabla de torneos
        return [
            'nombre' => $datos['nombre_torneo'] ?? '',
            'fecha' => $datos['fecha_inicio'] ?? '',
            'categoria' => $categoriaNombre,
            'formato' => isset($datos['es_por_equipos']) ? ($datos['es_por_equipos'] ? 'Por equipos' : 'Individual') : '',
            'emparejamiento' => $emparejamientoNombre,
            'lugar' => $datos['lugar'] ?? '',
            'rondas' => $datos['no_rondas'] ?? '',
            'federacion' => $federacionNombre,
            'organizador' => $datos['organizador_id'] ?? '',
            'director' => $datos['director_torneo_id'] ?? '',
            'arbitro' => $datos['arbitro_id'] ?? '',
            'arbitro_principal' => $datos['arbitro_principal_id'] ?? '',
            'arbitro_adjunto' => $datos['arbitro_adjunto_id'] ?? '',
            'estado' => isset($datos['estado_torneo']) ? ($datos['estado_torneo'] ? 'Activo' : 'Inactivo') : ''
        ];
    }

    private function crearAuditoria($correo, $accion, $previo, $posterior = null)
    {
        // Usar la zona horaria de Nicaragua
        $fechaHora = now()->setTimezone('America/Managua');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => 'Torneos',
            'accion' => $accion,
            'valor_previo' => $previo,
            'valor_posterior' => $posterior ?? '-',
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }

    public function exportTorneos()
    {
        // Usar exactamente la misma consulta que se usa para llenar los cards
        $torneos = Torneo::with([
            'categoria',
            'organizador',
            'directorTorneo',
            'arbitro',
            'arbitroPrincipal',
            'arbitroAdjunto',
            'federacion',
            'controlTiempo',
            'emparejamiento',
            'participantes',
            'rondas'
        ])->get();
        
        $filename = 'torneos_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $callback = function() use ($torneos) {
            $file = fopen('php://output', 'w');
            
            // Agregar BOM UTF-8 para reconocer acentos y ñ
            fputs($file, "\xEF\xBB\xBF");
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Nombre del Torneo',
                'Fecha de Inicio',
                'Hora de Inicio',
                'Estado',
                'Categoría',
                'Lugar',
                'Total de Rondas',
                'Participantes Inscritos',
                'Rondas Disputadas',
                'Rondas a Disputar',
                'Organizador',
                'Director',
                'Árbitro',
                'Árbitro Principal',
                'Árbitro Adjunto',
                'Federación',
                'Formato',
                'Sistema',
                'Motivo Cancelación'
            ]);
            
            // Datos
            foreach ($torneos as $torneo) {
                // Calcular rondas a disputar
                $rondasDisputadas = $torneo->rondas()->count();
                $rondasADisputar = max(0, $torneo->no_rondas - $rondasDisputadas);
                
                // Determinar estado
                $estado = 'Activo';
                if ($torneo->torneo_cancelado) {
                    $estado = 'Cancelado';
                } elseif ($torneo->fecha_inicio && $torneo->fecha_inicio->isPast()) {
                    $estado = 'Finalizado';
                } elseif (!$torneo->estado_torneo) {
                    $estado = 'Borrador';
                }
                
                fputcsv($file, [
                    $torneo->id,
                    $torneo->nombre_torneo,
                    $torneo->fecha_inicio ? $torneo->fecha_inicio->format('d/m/Y') : '',
                    $torneo->hora_inicio ? $torneo->hora_inicio->format('h:i A') : '',
                    $estado,
                    $torneo->categoria->categoria_torneo ?? 'Sin categoría',
                    $torneo->lugar,
                    $torneo->no_rondas,
                    $torneo->participantes()->count(),
                    $rondasDisputadas,
                    $rondasADisputar,
                    $torneo->organizador ? $torneo->organizador->nombres . ' ' . $torneo->organizador->apellidos : 'Sin asignar',
                    $torneo->directorTorneo ? $torneo->directorTorneo->nombres . ' ' . $torneo->directorTorneo->apellidos : 'Sin asignar',
                    $torneo->arbitro ? $torneo->arbitro->nombres . ' ' . $torneo->arbitro->apellidos : 'Sin asignar',
                    $torneo->arbitroPrincipal ? $torneo->arbitroPrincipal->nombres . ' ' . $torneo->arbitroPrincipal->apellidos : 'Sin asignar',
                    $torneo->arbitroAdjunto ? $torneo->arbitroAdjunto->nombres . ' ' . $torneo->arbitroAdjunto->apellidos : 'Sin asignar',
                    $torneo->federacion->nombre_federacion ?? 'Sin federación',
                    $torneo->controlTiempo->formato ?? 'Sin formato',
                    $torneo->emparejamiento->sistema ?? 'Sin sistema',
                    $torneo->motivo_cancelacion ?? ''
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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