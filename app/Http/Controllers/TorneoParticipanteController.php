<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Miembro;
use App\Models\ParticipanteTorneo;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TorneoParticipanteController extends Controller
{
    public function store(Request $request, Torneo $torneo)
    {
        if ($torneo->estado === 'Finalizado') {
            return back()->with('error', 'No se pueden agregar participantes a un torneo finalizado.');
        }

        try {
            $request->validate([
                'participantes' => 'required|array|min:1',
                'participantes.*' => 'required|exists:miembros,cedula'
            ]);

            DB::beginTransaction();

            $participantesAgregados = [];
            foreach ($request->participantes as $miembroId) {
                $participante = ParticipanteTorneo::create([
                    'torneo_id' => $torneo->id,
                    'miembro_id' => $miembroId,
                    'activo' => true
                ]);
                
                // Recargar con relaciones para auditoría
                $participante->load(['torneo', 'miembro']);
                $participantesAgregados[] = [
                    'torneo' => $participante->torneo ? $participante->torneo->nombre_torneo : 'Sin torneo',
                    'miembro' => $participante->miembro ? $participante->miembro->nombres . ' ' . $participante->miembro->apellidos : 'Sin miembro',
                    'activo' => $participante->activo
                ];
            }

            DB::commit();

            // Registrar auditoría para inserción
            $this->crearAuditoria(
                Auth::user()->correo,
                'Inserción',
                null,
                json_encode($participantesAgregados, JSON_UNESCAPED_UNICODE)
            );

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

            // Preparar datos para auditoría antes de eliminar
            $participante->load(['torneo', 'miembro']);
            $datosParticipante = [
                'torneo' => $participante->torneo ? $participante->torneo->nombre_torneo : 'Sin torneo',
                'miembro' => $participante->miembro ? $participante->miembro->nombres . ' ' . $participante->miembro->apellidos : 'Sin miembro',
                'numero_inicial' => $participante->numero_inicial,
                'puntos' => $participante->puntos,
                'posicion' => $participante->posicion,
                'activo' => $participante->activo
            ];

            $participante->delete();

            // Registrar auditoría para retiro
            $this->crearAuditoria(
                Auth::user()->correo,
                'Retiro',
                json_encode($datosParticipante, JSON_UNESCAPED_UNICODE),
                null
            );

            return redirect()
                ->route('torneos.show', $torneo)
                ->with('success', 'Participante retirado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al retirar participante: ' . $e->getMessage());
            
            return back()->with('error', 'Error al retirar participante. Por favor, intente nuevamente.');
        }
    }

    private function crearAuditoria($correo, $accion, $previo, $posterior = null)
    {
        // Usar la zona horaria de Nicaragua
        $fechaHora = now()->setTimezone('America/Managua');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => 'Participantes',
            'accion' => $accion,
            'valor_previo' => $previo,
            'valor_posterior' => $posterior ?? '-',
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }
} 