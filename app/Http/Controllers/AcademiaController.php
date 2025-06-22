<?php

namespace App\Http\Controllers;

use App\Models\Academia;
use App\Models\Ciudad;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AcademiaController extends Controller
{
    public function index()
    {
        $academias = Academia::with(['ciudad.departamento.pais'])
            ->orderBy('nombre_academia')
            ->get();

        return view('academias.index', compact('academias'));
    }

    public function create()
    {
        $ciudades = Ciudad::with(['departamento.pais'])
            ->whereHas('departamento.pais', function ($query) {
                $query->where(DB::raw('LOWER(nombre_pais)'), 'nicaragua');
            })
            ->orderBy('nombre_ciudad')
            ->get();
        return view('academias.create', compact('ciudades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_academia' => 'required|string|max:255|unique:academias',
            'correo_academia' => 'required|email|max:255',
            'telefono_academia' => 'required|string|max:20',
            'representante_academia' => 'required|string|max:255',
            'direccion_academia' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id_ciudad',
            'estado_academia' => 'required|boolean'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $academia = Academia::create($request->all());
                
                $this->logAuditoria(
                    Auth::user()->correo,
                    'Academias',
                    'Inserción',
                    null,
                    $academia->toArray()
                );

                return redirect()->route('academias.show', $academia)
                    ->with('success', '¡Academia creada exitosamente!');
            });
        } catch (\Exception $e) {
            Log::error('Error al crear academia: ' . $e->getMessage());
            return redirect()->route('academias.create')
                ->withInput()
                ->with('error', 'Error al crear la academia: ' . $e->getMessage());
        }
    }

    public function show(Academia $academia)
    {
        $academia->load(['ciudad.departamento.pais']);
        return view('academias.show', ['academia' => $academia]);
    }

    public function edit(Academia $academia)
    {
        $ciudades = Ciudad::with(['departamento.pais'])
            ->whereHas('departamento.pais', function ($query) {
                $query->where(DB::raw('LOWER(nombre_pais)'), 'nicaragua');
            })
            ->orderBy('nombre_ciudad')
            ->get();
        return view('academias.edit', ['academia' => $academia, 'ciudades' => $ciudades]);
    }

    public function update(Request $request, Academia $academia)
    {
        $request->validate([
            'nombre_academia' => 'required|string|max:255|unique:academias,nombre_academia,' . $academia->id_academia . ',id_academia',
            'correo_academia' => 'required|email|max:255',
            'telefono_academia' => 'required|string|max:20',
            'representante_academia' => 'required|string|max:255',
            'direccion_academia' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id_ciudad',
            'estado_academia' => 'required|boolean'
        ]);

        try {
            return DB::transaction(function () use ($request, $academia) {
                $originalData = $academia->toArray();
                $academia->update($request->all());
                
                $this->logAuditoria(
                    Auth::user()->correo,
                    'Academias',
                    'Modificación',
                    $originalData,
                    $academia->toArray()
                );

                return redirect()->route('academias.show', $academia)
                    ->with('success', '¡Academia actualizada exitosamente!');
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar academia: ' . $e->getMessage());
            return redirect()->route('academias.edit', $academia)
                ->withInput()
                ->with('error', 'Error al actualizar la academia: ' . $e->getMessage());
        }
    }

    public function destroy(Academia $academia)
    {
        try {
            return DB::transaction(function () use ($academia) {
                $originalData = $academia->toArray();
                $academia->delete();
                
                $this->logAuditoria(
                    Auth::user()->correo,
                    'Academias',
                    'Eliminación',
                    $originalData,
                    null
                );

                return redirect()->route('academias.index')
                    ->with('success', '¡Academia eliminada exitosamente!');
            });
        } catch (\Exception $e) {
            Log::error('Error al eliminar academia: ' . $e->getMessage());
            return redirect()->route('academias.index')
                ->with('error', 'Error al eliminar la academia: ' . $e->getMessage());
        }
    }

    private function logAuditoria(
        string $correo,
        string $tabla,
        string $accion,
        ?array $previo,
        ?array $posterior
    ): void {
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => $tabla,
            'accion' => $accion,
            'valor_previo' => $previo ? json_encode($previo) : '[-]',
            'valor_posterior' => $posterior ? json_encode($posterior) : '[-]',
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }
}