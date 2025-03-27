<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\Torneo;
use App\Http\Resources\PartidaResource;
use App\Http\Resources\TorneoConParticipantesResource;
use Illuminate\Http\JsonResponse;

class PartidaController extends Controller
{
    // Case 1: Obtener todas las partidas
    public function index(): JsonResponse
    {
        $partidas = Partida::with(['participante', 'torneo'])
            ->orderBy('no_partida')
            ->get();

        return PartidaResource::collection($partidas)->response();
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
}