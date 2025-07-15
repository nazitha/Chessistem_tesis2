<?php

namespace App\Http\Controllers;

use App\Models\EquipoTorneo;
use App\Models\EquipoJugador;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class EquipoTorneoController extends Controller
{
    // Listar equipos de un torneo
    public function index($torneoId)
    {
        $torneo = Torneo::findOrFail($torneoId);
        $equipos = $torneo->equipos()->with(['jugadores.miembro', 'capitan'])->get();
        return view('equipos.index', compact('torneo', 'equipos'));
    }

    // Registrar un nuevo equipo
    public function store(Request $request, Torneo $torneo)
    {
        $request->validate([
            'nombre' => [
                'required', 'string', 'max:255',
                Rule::unique('equipos_torneo')->where(function ($query) use ($torneo) {
                    return $query->where('torneo_id', $torneo->id);
                })
            ],
            'capitan_id' => ['nullable', 'exists:miembros,cedula'],
            'federacion' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'jugadores' => ['required', 'array', 'min:2', 'max:10'],
            'jugadores.*.miembro_id' => ['required', 'exists:miembros,cedula'],
            'jugadores.*.tablero' => ['required', 'integer', 'min:1'],
        ]);

        // Validar tableros únicos
        $tableros = array_column($request->jugadores, 'tablero');
        if (count($tableros) !== count(array_unique($tableros))) {
            return back()->withErrors(['mensaje' => 'No puede haber tableros repetidos en el mismo equipo.'])->withInput();
        }

        // Validar jugadores únicos
        $miembros = array_column($request->jugadores, 'miembro_id');
        if (count($miembros) !== count(array_unique($miembros))) {
            return back()->withErrors(['mensaje' => 'No puede haber jugadores repetidos en el mismo equipo.'])->withInput();
        }

        // Validar que un jugador no esté en dos equipos del mismo torneo
        $yaAsignados = EquipoJugador::whereIn('miembro_id', $miembros)
            ->whereHas('equipo', function($q) use ($torneo) {
                $q->where('torneo_id', $torneo->id);
            })->pluck('miembro_id')->toArray();
        if ($yaAsignados) {
            return back()->withErrors(['mensaje' => 'Uno o más jugadores ya están asignados a otro equipo en este torneo.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $equipo = EquipoTorneo::create([
                'torneo_id' => $torneo->id,
                'nombre' => $request->nombre,
                'capitan_id' => $request->capitan_id,
                'federacion' => $request->federacion,
                'logo' => $request->logo, // Manejar upload si aplica
                'notas' => $request->notas,
                'elo_medio' => null // Calcular después si quieres
            ]);
            foreach ($request->jugadores as $jugador) {
                EquipoJugador::create([
                    'equipo_id' => $equipo->id,
                    'miembro_id' => $jugador['miembro_id'],
                    'tablero' => $jugador['tablero']
                ]);
            }
            DB::commit();
            return redirect()->route('torneos.show', $torneo->id)->with('success', 'Equipo registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['mensaje' => 'Error al registrar el equipo: ' . $e->getMessage()])->withInput();
        }
    }

    // Ver composición de un equipo
    public function show($torneoId, $equipoId)
    {
        $equipo = EquipoTorneo::with('jugadores.miembro')->findOrFail($equipoId);
        return view('equipos.show', compact('equipo'));
    }

    // Agregar jugador a equipo
    public function addJugador(Request $request, $equipoId)
    {
        $equipo = EquipoTorneo::findOrFail($equipoId);
        $torneoId = $equipo->torneo_id;
        $request->validate([
            'miembro_id' => [
                'required',
                'exists:miembros,cedula',
                Rule::unique('equipo_jugadores')->where(function ($query) use ($equipoId) {
                    return $query->where('equipo_id', $equipoId);
                }),
            ],
            'tablero' => [
                'required', 'integer', 'min:1',
                Rule::unique('equipo_jugadores')->where(function ($query) use ($equipoId) {
                    return $query->where('equipo_id', $equipoId);
                }),
            ],
        ]);

        // Validar que el jugador no esté en otro equipo del torneo
        $yaAsignado = EquipoJugador::where('miembro_id', $request->miembro_id)
            ->whereHas('equipo', function($q) use ($torneoId, $equipoId) {
                $q->where('torneo_id', $torneoId)->where('id', '!=', $equipoId);
            })->exists();
        if ($yaAsignado) {
            return back()->withErrors(['mensaje' => 'El jugador ya está asignado a otro equipo en este torneo.']);
        }

        // Validar máximo de jugadores
        if ($equipo->jugadores()->count() >= 10) {
            return back()->withErrors(['mensaje' => 'El equipo ya tiene el máximo permitido de jugadores.']);
        }

        // (Opcional) Validar si el torneo ya empezó
        // if ($equipo->torneo->rondas()->count() > 0) {
        //     return back()->withErrors(['mensaje' => 'No se pueden agregar jugadores después de iniciado el torneo.']);
        // }

        EquipoJugador::create([
            'equipo_id' => $equipoId,
            'miembro_id' => $request->miembro_id,
            'tablero' => $request->tablero
        ]);
        return back()->with('success', 'Jugador agregado correctamente.');
    }

    // Quitar jugador de equipo
    public function removeJugador($equipoId, $jugadorId)
    {
        $jugador = EquipoJugador::where('equipo_id', $equipoId)->where('id', $jugadorId)->firstOrFail();
        $jugador->delete();
        return back()->with('success', 'Jugador eliminado correctamente.');
    }

    public function edit($torneoId, $equipoId)
    {
        // Vista temporal de edición
        return response('Vista de edición de equipo (en construcción)', 200);
    }

    public function destroy($torneoId, $equipoId)
    {
        // Lógica temporal de eliminación
        return back()->with('success', 'Eliminación de equipo (en construcción)');
    }

    public function update(Request $request, $torneoId, $equipoId)
    {
        $equipo = EquipoTorneo::findOrFail($equipoId);
        $request->validate([
            'nombre' => 'required|string|max:255|unique:equipos_torneo,nombre,' . $equipoId . ',id,torneo_id,' . $torneoId,
            'capitan_id' => 'nullable|exists:miembros,cedula',
            'federacion' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'notas' => 'nullable|string|max:1000',
        ]);
        $equipo->nombre = $request->nombre;
        $equipo->capitan_id = $request->capitan_id;
        $equipo->federacion = $request->federacion;
        $equipo->notas = $request->notas;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $equipo->logo = $logoPath;
        }
        $equipo->save();
        return redirect()->route('torneos.show', $torneoId)->with('success', 'Equipo actualizado correctamente.');
    }
} 