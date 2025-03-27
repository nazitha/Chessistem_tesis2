<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emparejamiento;

class EmparejamientoController extends Controller
{
    public function cargarDatos()
    {
        try {
            $emparejamientos = Emparejamiento::with('torneo')
                ->orderBy('torneo_text')
                ->get();

            return response()->json($emparejamientos);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function insertarEmparejamiento(Request $request)
    {
        $validated = $request->validate([
            'torneo_val' => 'required|integer|exists:torneos,id',
            'torneo_text' => 'required|string|max:255'
        ]);

        try {
            $emparejamiento = Emparejamiento::create($validated);
            return response()->json([
                'success' => true,
                'data' => $emparejamiento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear emparejamiento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actualizarEmparejamiento(Request $request, $id)
    {
        $validated = $request->validate([
            'torneo_val' => 'required|integer|exists:torneos,id',
            'torneo_text' => 'required|string|max:255'
        ]);

        try {
            $emparejamiento = Emparejamiento::findOrFail($id);
            $emparejamiento->update($validated);
            
            return response()->json([
                'success' => true,
                'data' => $emparejamiento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar emparejamiento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function eliminarEmparejamiento($id)
    {
        try {
            Emparejamiento::destroy($id);
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar emparejamiento: ' . $e->getMessage()
            ], 500);
        }
    }
}