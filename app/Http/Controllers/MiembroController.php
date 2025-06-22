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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MiembroController extends Controller
{
    public function index()
    {
        $miembros = Miembro::with(['usuario.rol', 'ciudad.departamento.pais', 'academia'])
            ->orderBy('cedula')
            ->get();

        return view('miembros.index', compact('miembros'));
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

    public function store(MiembroRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $this->prepareData($request);
                $miembro = Miembro::create($data);
                
                $this->logAuditoria(
                    Auth::user()->correo,
                    'Miembros',
                    'Inserción',
                    null,
                    $data // Passing the array data instead of the model
                );

                return redirect()->route('miembros.show', $miembro)
                    ->with('success', '¡Miembro creado exitosamente!');
            });
        } catch (\Exception $e) {
            Log::error('Error al crear miembro: ' . $e->getMessage());
            return redirect()->route('miembros.create')
                ->withInput()
                ->with('error', 'Error al crear el miembro: ' . $e->getMessage());
        }
    }

    public function update(MiembroRequest $request, Miembro $miembro)
    {
        return \DB::transaction(function () use ($request, $miembro) {
            $originalData = $miembro->getOriginal();
            $miembro->update($this->prepareData($request));
            $this->logAuditoria(
                $request->mail_log ?? (auth()->user() ? auth()->user()->correo : 'sistema'),
                'Miembros',
                'Modificación',
                $originalData,
                $miembro->getChanges()
            );
            // Redirigir a la vista de detalle con mensaje de éxito
            return redirect()->route('miembros.show', $miembro)
                ->with('success', '¡Miembro actualizado exitosamente!');
        });
    }

    public function destroy(Miembro $miembro)
    {
        try {
            DB::transaction(function () use ($miembro) {
                $originalData = $miembro->toArray();
                
                // First delete related puntajes_elo records
                if ($miembro->fide) {
                    DB::table('puntajes_elo')->where('fide_id_miembro', $miembro->fide->fide_id)->delete();
                    // Then delete the fide record
                    $miembro->fide->delete();
                }
                
                // Now we can safely delete the member
                $miembro->delete();
                
                $this->logAuditoria(
                    Auth::user()->correo,
                    'Miembros',
                    'Eliminación',
                    $originalData,
                    null
                );
            });

            return redirect()->route('miembros.index')
                ->with('success', '¡Miembro eliminado exitosamente!');

        } catch (\Exception $e) {
            \Log::error('Error al eliminar miembro: ' . $e->getMessage());
            return redirect()->route('miembros.index')
                ->with('error', 'Error al eliminar el miembro: ' . $e->getMessage());
        }
    }

    public function show(Miembro $miembro)
    {
        if (request()->ajax()) {
            return view('miembros.partials.detalle', compact('miembro'))->render();
        }
        return view('miembros.show', compact('miembro'));
    }

    public function edit(Miembro $miembro)
    {
        $academias = \App\Models\Academia::all();
        return view('miembros.edit', compact('miembro', 'academias'));
    }

    public function create()
    {
        $academias = Academia::orderBy('nombre_academia')->get();
        $usuarios = User::active()->whereDoesntHave('miembro')->get();
        return view('miembros.create', compact('academias', 'usuarios'));
    }

    private function prepareData($request): array
    {
        return $request->validated();
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

    private function formatAuditData($data): string
    {
        if (!$data) return '[-]';
        
        // Convert Model to array if necessary
        if ($data instanceof \Illuminate\Database\Eloquent\Model) {
            $data = $data->toArray();
        }
        
        return collect($data)->map(function ($value, $key) {
            return match ($key) {
                'fecha_nacimiento', 'fecha_inscripcion' => $value ? Carbon::parse($value)->format('d-m-Y') : null,
                'estado_miembro' => $value ? 'Activo' : 'Inactivo',
                'sexo' => $value == 'M' ? 'Masculino' : 'Femenino',
                'academia_id' => $value ? Academia::find($value)?->nombre_academia ?? '-' : '-',
                default => $value
            };
        })->toJson();
    }
}