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
                
                $datosFormateados = $this->formatearDatosMiembro($data);
                $this->crearAuditoria(
                    Auth::user()->correo,
                    'Creación',
                    null,
                    json_encode($datosFormateados)
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
            $datosAnteriores = $this->formatearDatosMiembro($originalData);
            $datosNuevos = $this->formatearDatosMiembro($miembro->toArray());
            
            $this->crearAuditoria(
                $request->mail_log ?? (auth()->user() ? auth()->user()->correo : 'sistema'),
                'Edición',
                json_encode($datosAnteriores),
                json_encode($datosNuevos)
            );
            return redirect()->route('miembros.show', $miembro)
                ->with('success', '¡Miembro actualizado exitosamente!');
        });
    }

    public function destroy(Miembro $miembro)
    {
        try {
            DB::transaction(function () use ($miembro) {
                $originalData = $miembro->toArray();
                
                if ($miembro->fide) {
                    DB::table('puntajes_elo')->where('fide_id_miembro', $miembro->fide->fide_id)->delete();
                    $miembro->fide->delete();
                }
                
                $miembro->delete();
                
                $datosFormateados = $this->formatearDatosMiembro($originalData);
                $this->crearAuditoria(
                    Auth::user()->correo,
                    'Eliminación',
                    json_encode($datosFormateados),
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
        // Usar la zona horaria de Guatemala
        $fechaHora = now()->setTimezone('America/Guatemala');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => $tabla,
            'accion' => $accion,
            'valor_previo' => $this->formatAuditData($previo),
            'valor_posterior' => $this->formatAuditData($posterior),
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }

    private function formatAuditData($data): string
    {
        if (!$data) return '[-]';
        
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

    private function formatearDatosMiembro($datos)
    {
        // Obtener nombre de la academia si existe
        $academiaNombre = '';
        if (isset($datos['academia_id']) && $datos['academia_id']) {
            $academia = \App\Models\Academia::find($datos['academia_id']);
            $academiaNombre = $academia ? $academia->nombre_academia : 'Sin academia';
        }
        
        // Solo los campos que se muestran en la tabla de miembros
        return [
            'cedula' => $datos['cedula'] ?? '',
            'nombres' => $datos['nombres'] ?? '',
            'apellidos' => $datos['apellidos'] ?? '',
            'sexo' => $datos['sexo'] ?? '',
            'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? '',
            'fecha_inscripcion' => $datos['fecha_inscripcion'] ?? '',
            'estado' => isset($datos['estado_miembro']) ? ($datos['estado_miembro'] ? 'Activo' : 'Inactivo') : '',
            'academia' => $academiaNombre,
            'elo' => $datos['elo'] ?? '',
            'correo' => $datos['correo'] ?? ''
        ];
    }

    private function crearAuditoria($correo, $accion, $previo, $posterior = null)
    {
        // Usar la zona horaria de Guatemala
        $fechaHora = now()->setTimezone('America/Guatemala');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => 'Miembros',
            'accion' => $accion,
            'valor_previo' => $previo,
            'valor_posterior' => $posterior ?? '-',
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }

    public function exportMiembros()
    {
        // Solo registrar auditoría
        Auditoria::create([
            'correo_id' => Auth::user()->correo,
            'tabla_afectada' => 'Miembros',
            'accion' => 'Exportación',
            'valor_previo' => null,
            'valor_posterior' => 'Registros exportados en documento .csv',
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'equipo' => request()->ip()
        ]);

        return response()->json(['success' => true]);
    }
}
