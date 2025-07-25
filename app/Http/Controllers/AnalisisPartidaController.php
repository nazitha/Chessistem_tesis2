<?php

namespace App\Http\Controllers;

use App\Models\AnalisisPartida;
use App\Models\Partida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnalisisPartidaController extends Controller
{
    // Listar todos los análisis
    public function index()
    {
        $analisis = AnalisisPartida::with(['partida', 'jugadorBlancas', 'jugadorNegras'])->latest()->paginate(10);
        return view('analisis_partidas.index', compact('analisis'));
    }

    // Mostrar análisis de una partida
    public function show($id)
    {
        $analisis = AnalisisPartida::with(['partida', 'jugadorBlancas', 'jugadorNegras'])->findOrFail($id);
        return view('analisis_partidas.show', compact('analisis'));
    }

    // Guardar o actualizar análisis de una partida
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partida_id' => 'required|exists:partidas,no_partida',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $partida = Partida::where('no_partida', $request->partida_id)->first();
        if (!$partida || empty($partida->movimientos)) {
            return response()->json(['error' => 'La partida no tiene movimientos registrados.'], 400);
        }
        // Evitar duplicados
        $analisis = AnalisisPartida::where('partida_id', $request->partida_id)->first();
        if ($analisis) {
            // Si ya existe, actualizar
            $analisis->update($this->analizarMovimientos($partida));
        } else {
            $analisis = AnalisisPartida::create(array_merge(
                ['partida_id' => $partida->no_partida,
                 'movimientos' => $partida->movimientos,
                 'jugador_blancas_id' => $partida->jugador_blancas_id,
                 'jugador_negras_id' => $partida->jugador_negras_id],
                $this->analizarMovimientos($partida)
            ));
        }
        return response()->json(['success' => true, 'analisis_id' => $analisis->id]);
    }

    // Simulación de análisis (puedes mejorarla luego)
    private function analizarMovimientos($partida)
    {
        // Aquí podrías usar $partida->movimientos (PGN/FEN)
        return [
            'evaluacion_general' => 'Jugada sólida de blancas con mejor posición en el medio juego.',
            'errores_blancas' => 2,
            'errores_negras' => 1,
            'brillantes_blancas' => 1,
            'brillantes_negras' => 0,
            'blunders_blancas' => 0,
            'blunders_negras' => 1
        ];
    }
} 