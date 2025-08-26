<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\Torneo;
use App\Models\Participante;
use App\Http\Resources\PartidaResource;
use App\Http\Resources\TorneoConParticipantesResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PartidaController extends Controller
{
    // Case 1: Obtener todas las partidas
    public function index(): JsonResponse
    {
        $partidas = Partida::with(['participante', 'torneo', 'sistemaDesempate'])
            ->orderBy('no_partida')
            ->get();

        return PartidaResource::collection($partidas)->response();
    }

    public function partidasPorTorneo(Torneo $torneo): JsonResponse
    {
        $partidas = $torneo->partidas()
            ->with(['participante', 'sistemaDesempate'])
            ->orderBy('ronda')
            ->orderBy('mesa')
            ->get();

        return PartidaResource::collection($partidas)->response();
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ronda' => 'required|integer',
            'ronda_torneo_id' => 'required|exists:torneos,id_torneo',
            'participante_id' => 'required|exists:miembros,cedula',
            'torneo_id' => 'required|exists:torneos,id_torneo',
            'mesa' => 'required|integer',
            'color' => 'required|boolean',
            'tiempo' => 'nullable|date_format:H:i:s',
            'desempate_utilizado_id' => 'nullable|exists:sistemas_desempate,id_sistema_desempate',
            'estado_abandono' => 'nullable|boolean',
            'resultado' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $partida = Partida::create($request->all());

            DB::commit();
            return response()->json($partida, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear la partida: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Partida $partida): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ronda' => 'sometimes|required|integer',
            'ronda_torneo_id' => 'sometimes|required|exists:torneos,id_torneo',
            'participante_id' => 'sometimes|required|exists:miembros,cedula',
            'torneo_id' => 'sometimes|required|exists:torneos,id_torneo',
            'mesa' => 'sometimes|required|integer',
            'color' => 'sometimes|required|boolean',
            'tiempo' => 'nullable|date_format:H:i:s',
            'desempate_utilizado_id' => 'nullable|exists:sistemas_desempate,id_sistema_desempate',
            'estado_abandono' => 'nullable|boolean',
            'resultado' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $partida->update($request->all());

            if ($request->has('resultado')) {
                $this->actualizarPuntosParticipante($partida->torneo_id);

                $torneo = $partida->torneo;
                if ($torneo && $torneo->tipo_torneo === 'Eliminación Directa') {
                    if ($partida->resultado !== null && in_array($partida->resultado, [0, 1])) {
                        $ganadorId = null;
                        if ($partida->color && $partida->resultado == 1) {
                            $ganadorId = $partida->participante_id; // Blancas ganan
                        } elseif (!$partida->color && $partida->resultado == 0) {
                            $ganadorId = $partida->participante_id; // Negras ganan
                        }
                        if ($ganadorId) {
                            $siguientePartida = Partida::where('torneo_id', $partida->torneo_id)
                                ->where('ronda', $partida->ronda + 1)
                                ->where('mesa', $partida->mesa)
                                ->whereNull('participante_id')
                                ->first();
                            if ($siguientePartida) {
                                $siguientePartida->participante_id = $ganadorId;
                                $siguientePartida->save();
                            }
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(new PartidaResource($partida));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar la partida: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Partida $partida): JsonResponse
    {
        try {
            DB::beginTransaction();

            $torneo_id = $partida->torneo_id;
            $partida->delete();
            $this->actualizarPuntosParticipante($torneo_id);

            DB::commit();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la partida: ' . $e->getMessage()], 500);
        }
    }

    // Case 2: Obtener torneos con conteo de participantes
    public function torneosConParticipantes(): JsonResponse
    {
        $torneos = Torneo::activo()
            ->withCount('participantes')
            ->select('id_torneo', 'nombre_torneo', 'fecha_inicio')
            ->get();

        return TorneoConParticipantesResource::collection($torneos)->response();
    }

    private function actualizarPuntosParticipante(int $torneo_id): void
    {
        $participantes = Participante::where('torneo_id', $torneo_id)->get();
        foreach ($participantes as $participante) {
            $puntos = Partida::where('torneo_id', $torneo_id)
                ->where('participante_id', $participante->miembro_id)
                ->sum('resultado');
            $participante->update(['puntos' => $puntos]);
        }

        $posicion = 1;
        $participantes = Participante::where('torneo_id', $torneo_id)
            ->orderBy('puntos', 'desc')
            ->get();

        foreach ($participantes as $participante) {
            $participante->update(['posicion' => $posicion++]);
        }
    }

    public function getPartidasByRonda($torneoId, $ronda)
    {
        $partidas = Partida::with(['participante', 'sistemaDesempate'])
            ->where('torneo_id', $torneoId)
            ->where('ronda', $ronda)
            ->orderBy('mesa')
            ->get();

        return response()->json($partidas);
    }

    public function generarPartidasRoundRobin(Torneo $torneo): JsonResponse
    {
        try {
            DB::beginTransaction();

            if ($torneo->tipo_torneo !== 'Round Robin') {
                return response()->json(['error' => 'Este método solo es válido para torneos tipo Round Robin'], 400);
            }

            $participantes = $torneo->participantes()->get();
            $numParticipantes = $participantes->count();
            
            if ($numParticipantes < 2) {
                return response()->json(['error' => 'Se necesitan al menos 2 participantes'], 400);
            }

            if ($torneo->partidas()->exists()) {
                return response()->json(['error' => 'Ya existen partidas generadas para este torneo'], 400);
            }

            $rondas = $this->generarRondasRoundRobin($participantes, $torneo->tipo_participante);
            
            foreach ($rondas as $numRonda => $emparejamientos) {
                foreach ($emparejamientos as $mesa => $emparejamiento) {
                    Partida::create([
                        'ronda' => $numRonda + 1,
                        'ronda_torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['blancas'],
                        'torneo_id' => $torneo->id_torneo,
                        'mesa' => $mesa + 1,
                        'color' => true, 
                        'resultado' => null
                    ]);

                    if ($emparejamiento['negras']) {
                        Partida::create([
                            'ronda' => $numRonda + 1,
                            'ronda_torneo_id' => $torneo->id_torneo,
                            'participante_id' => $emparejamiento['negras'],
                            'torneo_id' => $torneo->id_torneo,
                            'mesa' => $mesa + 1,
                            'color' => false, 
                            'resultado' => null
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Partidas Round Robin generadas exitosamente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al generar partidas: ' . $e->getMessage()], 500);
        }
    }

    public function generarPartidasEliminacionDirecta(Torneo $torneo): JsonResponse
    {
        try {
            DB::beginTransaction();

            if ($torneo->tipo_torneo !== 'Eliminación Directa') {
                return response()->json(['error' => 'Este método solo es válido para torneos tipo Eliminación Directa'], 400);
            }

            $participantes = $torneo->participantes()->get();
            $numParticipantes = $participantes->count();
            
            if ($numParticipantes < 2) {
                return response()->json(['error' => 'Se necesitan al menos 2 participantes'], 400);
            }

            if ($torneo->partidas()->exists()) {
                return response()->json(['error' => 'Ya existen partidas generadas para este torneo'], 400);
            }

            if ($torneo->tipo_participante === 'Equipo') {
                $this->generarEliminacionDirectaEquipos($torneo, $participantes);
            } else {
                $this->generarEliminacionDirectaIndividual($torneo, $participantes);
            }

            DB::commit();
            return response()->json(['message' => 'Estructura de eliminación directa generada exitosamente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al generar estructura: ' . $e->getMessage()], 500);
        }
    }

    private function generarEliminacionDirectaIndividual($torneo, $participantes)
    {
        $jugadores = $participantes->pluck('participante_id')->toArray();
        shuffle($jugadores); // Mezclar aleatoriamente

        $numParticipantes = count($jugadores);
        $potenciaDe2 = pow(2, ceil(log($numParticipantes, 2)));
        
        while (count($jugadores) < $potenciaDe2) {
            $jugadores[] = null; // null representa "BYE"
        }

        $ronda = 1;
        $jugadoresActuales = $jugadores;

        while (count($jugadoresActuales) > 1) {
            $nuevaLista = [];
            
            for ($i = 0; $i < count($jugadoresActuales) - 1; $i += 2) {
                $jugador1 = $jugadoresActuales[$i];
                $jugador2 = $jugadoresActuales[$i + 1];

                if ($jugador1 === null) { // BYE
                    $nuevaLista[] = $jugador2;
                } elseif ($jugador2 === null) { // BYE
                    $nuevaLista[] = $jugador1;
                } else {
                    Partida::create([
                        'ronda' => $ronda,
                        'ronda_torneo_id' => $torneo->id_torneo,
                        'participante_id' => $jugador1,
                        'torneo_id' => $torneo->id_torneo,
                        'mesa' => ($i / 2) + 1,
                        'color' => true,
                        'resultado' => null
                    ]);

                    Partida::create([
                        'ronda' => $ronda,
                        'ronda_torneo_id' => $torneo->id_torneo,
                        'participante_id' => $jugador2,
                        'torneo_id' => $torneo->id_torneo,
                        'mesa' => ($i / 2) + 1,
                        'color' => false,
                        'resultado' => null
                    ]);

                    Partida::create([
                        'ronda' => $ronda + 1,
                        'ronda_torneo_id' => $torneo->id_torneo,
                        'participante_id' => null, 
                        'torneo_id' => $torneo->id_torneo,
                        'mesa' => ($i / 2) + 1,
                        'color' => true,
                        'resultado' => null
                    ]);

                    $nuevaLista[] = null; 
                }
            }

            $jugadoresActuales = $nuevaLista;
            $ronda++;
        }
    }

    private function generarEliminacionDirectaEquipos($torneo, $participantes)
    {
        $equipos = $participantes->pluck('participante_id')->toArray();
        shuffle($equipos); 

        $numEquipos = count($equipos);
        $potenciaDe2 = pow(2, ceil(log($numEquipos, 2)));
        
        while (count($equipos) < $potenciaDe2) {
            $equipos[] = null; // null representa "BYE"
        }

        $ronda = 1;
        $equiposActuales = $equipos;
        $tablerosPorEquipo = $torneo->tableros_por_equipo ?? 4; 

        while (count($equiposActuales) > 1) {
            $nuevosEquipos = [];
            
            for ($i = 0; $i < count($equiposActuales) - 1; $i += 2) {
                $equipo1 = $equiposActuales[$i];
                $equipo2 = $equiposActuales[$i + 1];

                if ($equipo1 === null) { // BYE
                    $nuevosEquipos[] = $equipo2;
                } elseif ($equipo2 === null) { // BYE
                    $nuevosEquipos[] = $equipo1;
                } else {
                    $this->asignarTablerosConColoresAlternos(
                        $torneo,
                        $ronda,
                        $equipo1,
                        $equipo2,
                        $tablerosPorEquipo,
                        ($i / 2) + 1
                    );

                    for ($t = 1; $t <= $tablerosPorEquipo; $t++) {
                        Partida::create([
                            'ronda' => $ronda + 1,
                            'ronda_torneo_id' => $torneo->id_torneo,
                            'participante_id' => null, 
                            'torneo_id' => $torneo->id_torneo,
                            'mesa' => (($i / 2) * $tablerosPorEquipo) + $t,
                            'color' => true,
                            'resultado' => null
                        ]);
                    }

                    $nuevosEquipos[] = null; 
                }
            }

            $equiposActuales = $nuevosEquipos;
            $ronda++;
        }
    }

    private function asignarTablerosConColoresAlternos($torneo, $ronda, $equipo1, $equipo2, $totalTableros, $mesaBase)
    {
        $jugadoresEquipo1 = $this->obtenerJugadoresEquipo($equipo1);
        $jugadoresEquipo2 = $this->obtenerJugadoresEquipo($equipo2);

        for ($t = 1; $t <= $totalTableros; $t++) {
            if ($t % 2 == 1) {
                $jugadorBlancas = $jugadoresEquipo1[$t - 1] ?? null;
                $jugadorNegras = $jugadoresEquipo2[$t - 1] ?? null;
            } else {
                $jugadorBlancas = $jugadoresEquipo2[$t - 1] ?? null;
                $jugadorNegras = $jugadoresEquipo1[$t - 1] ?? null;
            }

            if ($jugadorBlancas && $jugadorNegras) {
                Partida::create([
                    'ronda' => $ronda,
                    'ronda_torneo_id' => $torneo->id_torneo,
                    'participante_id' => $jugadorBlancas,
                    'torneo_id' => $torneo->id_torneo,
                    'mesa' => ($mesaBase - 1) * $totalTableros + $t,
                    'color' => true,
                    'resultado' => null
                ]);

                Partida::create([
                    'ronda' => $ronda,
                    'ronda_torneo_id' => $torneo->id_torneo,
                    'participante_id' => $jugadorNegras,
                    'torneo_id' => $torneo->id_torneo,
                    'mesa' => ($mesaBase - 1) * $totalTableros + $t,
                    'color' => false,
                    'resultado' => null
                ]);
            }
        }
    }

    private function obtenerJugadoresEquipo($equipoId)
    {
        $equipo = Participante::with('miembros')
            ->where('participante_id', $equipoId)
            ->first();
            
        return $equipo ? $equipo->miembros->pluck('cedula')->toArray() : [];
    }

    public function generarPartidasSuizo(Torneo $torneo): JsonResponse
    {
        try {
            DB::beginTransaction();

            if ($torneo->tipo_torneo !== 'Suizo') {
                return response()->json(['error' => 'Este método solo es válido para torneos tipo Suizo'], 400);
            }

            $participantes = $torneo->participantes()->get();
            $numParticipantes = $participantes->count();
            
            if ($numParticipantes < 2) {
                return response()->json(['error' => 'Se necesitan al menos 2 participantes'], 400);
            }

            // Verificar si ya existen partidas generadas
            if ($torneo->partidas()->exists()) {
                return response()->json(['error' => 'Ya existen partidas generadas para este torneo'], 400);
            }

            
            $this->generarPrimeraRondaSuizo($torneo, $participantes);

            DB::commit();
            return response()->json(['message' => 'Primera ronda del sistema suizo generada exitosamente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al generar partidas: ' . $e->getMessage()], 500);
        }
    }

    private function generarRondasRoundRobin($participantes, $tipoParticipante)
    {
        $numParticipantes = $participantes->count();
        $participantes = $participantes->pluck('participante_id')->toArray();
        
        if ($numParticipantes % 2 != 0) {
            $participantes[] = null;
            $numParticipantes++;
        }

        $rondas = [];
        $numRondas = $numParticipantes - 1;
        $mitad = $numParticipantes / 2;

        for ($ronda = 0; $ronda < $numRondas; $ronda++) {
            $emparejamientos = [];
            
            for ($i = 0; $i < $mitad; $i++) {
                $blancas = $participantes[$i];
                $negras = $participantes[$numParticipantes - 1 - $i];
                
                if ($blancas && $negras) {
                    if ($tipoParticipante === 'Equipo') {
                        $clubBlancas = $this->obtenerClubParticipante($blancas);
                        $clubNegras = $this->obtenerClubParticipante($negras);
                        
                        if ($clubBlancas === $clubNegras) {
                            $nuevoEmparejamiento = $this->buscarEmparejamientoAlternativo(
                                $participantes,
                                $i,
                                $numParticipantes - 1 - $i,
                                $clubBlancas
                            );
                            
                            if ($nuevoEmparejamiento) {
                                $blancas = $nuevoEmparejamiento['blancas'];
                                $negras = $nuevoEmparejamiento['negras'];
                            }
                        }
                    }
                    
                    $emparejamientos[] = [
                        'blancas' => $blancas,
                        'negras' => $negras
                    ];
                }
            }
            
            $rondas[] = $emparejamientos;
            
            $ultimo = array_pop($participantes);
            array_unshift($participantes, $ultimo);
        }

        return $rondas;
    }

    private function obtenerClubParticipante($participanteId)
    {
        $participante = Participante::with('miembro.club')
            ->where('participante_id', $participanteId)
            ->first();
            
        return $participante ? $participante->miembro->club->id_club : null;
    }

    private function buscarEmparejamientoAlternativo($participantes, $indiceBlancas, $indiceNegras, $clubEvitar)
    {
        for ($i = 0; $i < count($participantes); $i++) {
            if ($i !== $indiceBlancas && $i !== $indiceNegras) {
                $clubCandidato = $this->obtenerClubParticipante($participantes[$i]);
                if ($clubCandidato !== $clubEvitar) {
                    return [
                        'blancas' => $participantes[$indiceBlancas],
                        'negras' => $participantes[$i]
                    ];
                }
            }
        }
        return null;
    }

    private function generarPrimeraRondaSuizo($torneo, $participantes)
    {
        $participantes = $participantes->pluck('participante_id')->toArray();
        shuffle($participantes); 
        
        $numParticipantes = count($participantes);
        $numPartidas = floor($numParticipantes / 2);
        
        for ($i = 0; $i < $numPartidas; $i++) {
            if ($torneo->tipo_participante === 'Equipo') {
                $clubBlancas = $this->obtenerClubParticipante($participantes[$i]);
                $clubNegras = $this->obtenerClubParticipante($participantes[$numParticipantes - 1 - $i]);
                
                if ($clubBlancas === $clubNegras) {
                    for ($j = 0; $j < $numParticipantes; $j++) {
                        if ($j !== $i && $j !== ($numParticipantes - 1 - $i)) {
                            $clubCandidato = $this->obtenerClubParticipante($participantes[$j]);
                            if ($clubCandidato !== $clubBlancas) {
                                $temp = $participantes[$j];
                                $participantes[$j] = $participantes[$numParticipantes - 1 - $i];
                                $participantes[$numParticipantes - 1 - $i] = $temp;
                                break;
                            }
                        }
                    }
                }
            }
            
            Partida::create([
                'ronda' => 1,
                'ronda_torneo_id' => $torneo->id_torneo,
                'participante_id' => $participantes[$i],
                'torneo_id' => $torneo->id_torneo,
                'mesa' => $i + 1,
                'color' => true,
                'resultado' => null
            ]);

            Partida::create([
                'ronda' => 1,
                'ronda_torneo_id' => $torneo->id_torneo,
                'participante_id' => $participantes[$numParticipantes - 1 - $i],
                'torneo_id' => $torneo->id_torneo,
                'mesa' => $i + 1,
                'color' => false,
                'resultado' => null
            ]);
        }
    }

  
    public function partidasConMovimientos()
    {
        $partidas = Partida::whereNotNull('movimientos')
            ->where('movimientos', '!=', '')
            ->orderByDesc('no_partida')
            ->get(['no_partida', 'torneo_id', 'participante_id', 'movimientos']);
        return response()->json($partidas);
    }

  
    public function partidasSinMovimientos()
    {
        $partidas = Partida::whereNull('movimientos')
            ->orWhere('movimientos', '=', '')
            ->orderByDesc('no_partida')
            ->get(['no_partida', 'torneo_id', 'participante_id']);
        return response()->json($partidas);
    }

   
    public function agregarMovimientos(Request $request, $id)
    {
        $request->validate([
            'movimientos' => 'required|string|min:10'
        ]);

        $partida = Partida::findOrFail($id);
        $partida->update([
            'movimientos' => $request->movimientos
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Movimientos agregados correctamente',
            'partida_id' => $partida->no_partida
        ]);
    }

}
