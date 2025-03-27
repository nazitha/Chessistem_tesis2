<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Federacion;
use App\Models\Miembro;
use App\Models\CategoriaTorneo;
use App\Models\Emparejamiento;
use App\Http\Requests\TorneoRequest;
use App\Http\Resources\TorneoResource;
use App\Http\Resources\FederacionResource;
use App\Http\Resources\MiembroRolResource;
use App\Http\Resources\FormatoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TorneoController extends Controller
{
    public function index(): JsonResponse
    {
        $torneos = Torneo::withRelations()
            ->orderBy('nombre_torneo')
            ->get();

        return TorneoResource::collection($torneos)->response();
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

    public function store(TorneoRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $torneo = Torneo::create($request->validated());
            $this->logAuditoria($request, $torneo, 'creado');
            return response()->json(['success' => true], 201);
        });
    }

    public function update(TorneoRequest $request, Torneo $torneo): JsonResponse
    {
        return DB::transaction(function () use ($request, $torneo) {
            $originalData = $torneo->getOriginal();
            $torneo->update($request->validated());
            $this->logAuditoria($request, $torneo, 'actualizado', $originalData);
            return response()->json(['success' => true]);
        });
    }

    public function destroy(Torneo $torneo): JsonResponse
    {
        return DB::transaction(function () use ($torneo) {
            $this->logAuditoria(auth()->user()->email, $torneo, 'eliminado');
            $torneo->delete();
            return response()->json(['success' => true]);
        });
    }

    private function logAuditoria($request, $torneo, $accion, $previo = null)
    {
        AuditoriaService::log(
            user: $request->mail_log,
            modelo: $torneo,
            accion: $accion,
            previo: $previo
        );
    }
}