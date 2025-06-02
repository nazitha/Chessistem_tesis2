<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use App\Models\Academia;
use App\Models\User;
use App\Models\Auditoria;
use App\Http\Requests\MiembroRequest;
use App\Http\Resources\MiembroResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MiembroController extends Controller
{
    public function index(): JsonResponse
    {
        $miembros = Miembro::with(['usuario.rol', 'ciudad.departamento.pais', 'academia'])
            ->orderBy('cedula')
            ->get();

        return response()->json(MiembroResource::collection($miembros));
    }

    public function getAcademias(): JsonResponse
    {
        $academias = Academia::active()
            ->orderBy('nombre_academia')
            ->pluck('nombre_academia');

        return response()->json($academias);
    }

    public function getAvailableEmails(): JsonResponse
    {
        $correos = User::active()
            ->unlinked()
            ->get(['correo', 'rol_id']);

        return response()->json($correos);
    }

    public function store(MiembroRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $miembro = Miembro::create($this->prepareData($request));
            
            $this->logAuditoria(
                $request->mail_log,
                'Miembros',
                'Inserción',
                null,
                $miembro
            );

            return response()->json(['success' => true], 201);
        });
    }

    public function update(MiembroRequest $request, Miembro $miembro): JsonResponse
    {
        return DB::transaction(function () use ($request, $miembro) {
            $originalData = $miembro->getOriginal();
            
            $miembro->update($this->prepareData($request));
            
            $this->logAuditoria(
                $request->mail_log,
                'Miembros',
                'Modificación',
                $originalData,
                $miembro->getChanges()
            );

            return response()->json(['success' => true]);
        });
    }

    public function destroy(Miembro $miembro): JsonResponse
    {
        return DB::transaction(function () use ($miembro) {
            $originalData = $miembro->toArray();
            
            $miembro->delete();
            
            $this->logAuditoria(
                Auth::user()->correo,
                'Miembros',
                'Eliminación',
                $originalData,
                null
            );

            return response()->json(['success' => true]);
        });
    }

    private function prepareData($request): array
    {
        return array_merge($request->validated(), [
            'academia_id' => Academia::firstOrCreate(
                ['nombre_academia' => $request->academia],
                ['estado_academia' => true]
            )->id
        ]);
    }

    private function logAuditoria(
        string $correo,
        string $tabla,
        string $accion,
        ?array $previo,
        $posterior
    ): void {
        /**
         * @var \Illuminate\Support\Facades\Auth $auth
         * @method \App\Models\User|null user()
         */
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => $tabla,
            'accion' => $accion,
            'valor_previo' => $this->formatAuditData($previo),
            'valor_posterior' => $this->formatAuditData($posterior),
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }

    private function formatAuditData(?array $data): string
    {
        if (!$data) return '[-]';
        
        return collect($data)->map(function ($value, $key) {
            return match ($key) {
                'fecha_nacimiento', 'fecha_inscripcion' => Carbon::parse($value)->format('d-m-Y'),
                'estado_miembro' => $value ? 'Activo' : 'Inactivo',
                'sexo' => $value == 'M' ? 'Masculino' : 'Femenino',
                'academia_id' => Academia::find($value)->nombre_academia ?? '-',
                default => $value
            };
        })->toJson();
    }
}