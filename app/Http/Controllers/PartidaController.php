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
        return DB::transaction(function () use ($request) {
            $partida = Partida::create($request->validate([
                'participante_id' => 'required|string|exists:miembros,cedula',
                'torneo_id' => 'required|integer|exists:torneos,id_torneo',
                'ronda' => 'required|integer|min:1',
                'mesa' => 'required|integer|min:1',
                'color' => 'required|boolean',
                'tiempo' => 'nullable|date_format:H:i:s',
                'desempate_utilizado_id' => 'nullable|integer|exists:sistemas_desempate,id_desempate',
                'estado_abandono' => 'nullable|boolean',
                'resultado' => 'nullable|numeric|min:0|max:1'
            ]));

            return (new PartidaResource($partida))
                ->response()
                ->setStatusCode(201);
        });
    }

    public function update(Request $request, Partida $partida): JsonResponse
    {
        return DB::transaction(function () use ($request, $partida) {
            $partida->update($request->validate([
                'tiempo' => 'nullable|date_format:H:i:s',
                'desempate_utilizado_id' => 'nullable|integer|exists:sistemas_desempate,id_desempate',
                'estado_abandono' => 'nullable|boolean',
                'resultado' => 'nullable|numeric|min:0|max:1'
            ]));

            // Si se actualizó el resultado, actualizar los puntos del participante
            if ($request->has('resultado')) {
                $this->actualizarPuntosParticipante($partida->torneo_id);
            }

            return new PartidaResource($partida);
        });
    }

    public function destroy(Partida $partida): JsonResponse
    {
        return DB::transaction(function () use ($partida) {
            $torneo_id = $partida->torneo_id;
            $partida->delete();
            $this->actualizarPuntosParticipante($torneo_id);
            return response()->json(['success' => true]);
        });
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

        // Actualizar posiciones
        $posicion = 1;
        $participantes = Participante::where('torneo_id', $torneo_id)
            ->orderBy('puntos', 'desc')
            ->get();

        foreach ($participantes as $participante) {
            $participante->update(['posicion' => $posicion++]);
        }
    }
}