<?php

namespace App\Http\Controllers;

use App\Models\Federacion;
use App\Models\Fide;
use App\Models\Miembro;
use App\Models\PuntajeElo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FideController extends Controller
{
    public function cargarDatos()
    {
        $datos = Fide::with(['miembro', 'federacion', 'puntajesElo.categoria'])
            ->get()
            ->map(function ($fide) {
                return [
                    'fide_id' => $fide->fide_id,
                    'cedula' => $fide->miembro->cedula,
                    'nombre' => $fide->miembro->nombres . ' ' . $fide->miembro->apellidos,
                    'fed_id' => $fide->fed_id,
                    'titulo' => $fide->titulo,
                    'elo_blitz' => $fide->elo_blitz,
                    'elo_clasico' => $fide->elo_clasico,
                    'elo_rapido' => $fide->elo_rapido
                ];
            });

        return response()->json($datos);
    }

    public function cargarFederaciones()
    {
        $federaciones = Federacion::all()->map(function ($federacion) {
            return [
                'acronimo' => $federacion->acronimo,
                'federacion' => $federacion->nombre_federacion . ' (' . $federacion->acronimo . ')'
            ];
        });

        return response()->json($federaciones);
    }

    public function cargarAjedrecistas()
    {
        $ajedrecistas = Miembro::where('estado_miembro', 1)
            ->whereDoesntHave('fide')
            ->get()
            ->map(function ($miembro) {
                return [
                    'cedula' => $miembro->cedula,
                    'miembro' => $miembro->nombres . ' ' . $miembro->apellidos . ' (' . $miembro->cedula . ')',
                    'sexo' => $miembro->sexo
                ];
            });

        return response()->json($ajedrecistas);
    }

    public function insertarFide(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $fide = Fide::create([
                    'fide_id' => $request->fide_id,
                    'cedula_ajedrecista_id' => $request->identificacion_val,
                    'fed_id' => $request->federacion_val,
                    'titulo' => $request->titulo_val,
                    'fide_estado' => true
                ]);

                $fide->puntajesElo()->createMany([
                    ['no_categoria_elo' => 1, 'elo' => $request->elo_clasico],
                    ['no_categoria_elo' => 2, 'elo' => $request->elo_rapido],
                    ['no_categoria_elo' => 3, 'elo' => $request->elo_blitz]
                ]);
            });

            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function actualizarFide(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $fide = Fide::findOrFail($request->search);
                
                $fide->update([
                    'fide_id' => $request->fide_id,
                    'cedula_ajedrecista_id' => $request->identificacion_val,
                    'fed_id' => $request->federacion_val,
                    'titulo' => $request->titulo_val
                ]);

                // Actualizar puntajes ELO
                $fide->puntajesElo()->updateOrCreate(
                    ['no_categoria_elo' => 1],
                    ['elo' => $request->elo_clasico]
                );
                
                $fide->puntajesElo()->updateOrCreate(
                    ['no_categoria_elo' => 2],
                    ['elo' => $request->elo_rapido]
                );
                
                $fide->puntajesElo()->updateOrCreate(
                    ['no_categoria_elo' => 3],
                    ['elo' => $request->elo_blitz]
                );
            });

            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function eliminarFide(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $fide = Fide::findOrFail($request->search);
                $fide->puntajesElo()->delete();
                $fide->delete();
            });

            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }
}