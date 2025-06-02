<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Torneo;
use App\Http\Requests\ParticipanteRequest;
use App\Http\Resources\ParticipanteResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ParticipanteController extends Controller
{
    public function index(): JsonResponse
    {
        $participantes = Participante::with(['torneo', 'miembro'])
            ->orderBy('torneo_id')
            ->get();

        return ParticipanteResource::collection($participantes)->response();
    }

    public function participantesPorTorneo(Torneo $torneo): JsonResponse
    {
        $participantes = $torneo->participantes()
            ->with('miembro')
            ->orderBy('posicion')
            ->orderBy('puntos', 'desc')
            ->get();

        return ParticipanteResource::collection($participantes)->response();
    }

    public function store(ParticipanteRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $participante = Participante::create($request->validated());
            return (new ParticipanteResource($participante))
                ->response()
                ->setStatusCode(201);
        });
    }

    public function update(ParticipanteRequest $request, Participante $participante): JsonResponse
    {
        return DB::transaction(function () use ($request, $participante) {
            $participante->update($request->validated());
            return new ParticipanteResource($participante);
        });
    }

    public function destroy(Participante $participante): JsonResponse
    {
        return DB::transaction(function () use ($participante) {
            $participante->delete();
            return response()->json(['success' => true]);
        });
    }

    public function actualizarPuntos(Torneo $torneo): JsonResponse
    {
        return DB::transaction(function () use ($torneo) {
            $participantes = $torneo->participantes()
                ->with('partidas')
                ->get();

            foreach ($participantes as $participante) {
                $puntos = $participante->partidas->sum('resultado');
                $participante->update(['puntos' => $puntos]);
            }

            // Actualizar posiciones basado en puntos
            $posicion = 1;
            $participantes = $torneo->participantes()
                ->orderBy('puntos', 'desc')
                ->get();

            foreach ($participantes as $participante) {
                $participante->update(['posicion' => $posicion++]);
            }

            return response()->json(['success' => true]);
        });
    }
} 