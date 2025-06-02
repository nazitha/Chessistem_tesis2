<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Miembro;
use App\Models\ParticipanteTorneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TorneoParticipanteController extends Controller
{
    public function store(Request $request, Torneo $torneo)
    {
        try {
            $request->validate([
                'participantes' => 'required|array|min:1',
                'participantes.*' => 'required|exists:miembros,cedula'
            ]);

            DB::beginTransaction();

            foreach ($request->participantes as $miembroId) {
                ParticipanteTorneo::create([
                    'torneo_id' => $torneo->id,
                    'miembro_id' => $miembroId
                ]);
            }

            DB::commit();

            return redirect()
                ->route('torneos.show', $torneo)
                ->with('success', 'Participantes agregados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar participantes: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error al agregar participantes. Por favor, intente nuevamente.');
        }
    }

    public function destroy(Torneo $torneo, ParticipanteTorneo $participante)
    {
        try {
            if ($torneo->rondas()->count() > 0) {
                return back()->with('error', 'No se puede retirar participantes una vez iniciado el torneo.');
            }

            $participante->delete();

            return redirect()
                ->route('torneos.show', $torneo)
                ->with('success', 'Participante retirado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al retirar participante: ' . $e->getMessage());
            
            return back()->with('error', 'Error al retirar participante. Por favor, intente nuevamente.');
        }
    }
} 