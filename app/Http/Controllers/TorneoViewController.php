<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Participante;
use App\Models\Partida;
use App\Services\SwissPairingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TorneoViewController extends Controller
{
    public function show(Torneo $torneo)
    {
        $participantes = $torneo->participantes()
            ->with('miembro')
            ->orderBy('puntos', 'desc')
            ->orderBy('posicion')
            ->get();

        $rondas = $torneo->partidas()
            ->select('ronda')
            ->distinct()
            ->orderBy('ronda')
            ->pluck('ronda');

        $ultimaRonda = $rondas->last() ?? 0;
        $siguienteRonda = $ultimaRonda + 1;

        return view('torneos.show', compact('torneo', 'participantes', 'rondas', 'ultimaRonda', 'siguienteRonda'));
    }

    public function generarEmparejamientos(Torneo $torneo, Request $request)
    {
        // Verificar roles permitidos (1: Admin, 4: Gestor)
        $this->requireRole([1, 4]);

        $ronda = $request->input('ronda');
        
        if ($ronda <= 0 || $ronda > $torneo->no_rondas) {
            return back()->with('error', 'Ronda inválida');
        }

        if ($torneo->partidas()->where('ronda', $ronda)->exists()) {
            return back()->with('error', 'Esta ronda ya ha sido generada');
        }

        try {
            $service = new SwissPairingService($torneo);
            $emparejamientos = $service->generarEmparejamientos($ronda);

            DB::beginTransaction();

            foreach ($emparejamientos as $emparejamiento) {
                if (isset($emparejamiento['bye'])) {
                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante1']->miembro_id,
                        'ronda' => $ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'resultado' => 1,
                        'color' => true
                    ]);
                } else {
                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante1']->miembro_id,
                        'ronda' => $ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'color' => true
                    ]);

                    Partida::create([
                        'torneo_id' => $torneo->id_torneo,
                        'participante_id' => $emparejamiento['participante2']->miembro_id,
                        'ronda' => $ronda,
                        'mesa' => $emparejamiento['mesa'],
                        'color' => false
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Emparejamientos generados correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar emparejamientos: ' . $e->getMessage());
        }
    }

    public function actualizarResultado(Partida $partida, Request $request)
    {
        // Verificar roles permitidos (1: Admin, 4: Gestor)
        $this->requireRole([1, 4]);

        $resultado = $request->input('resultado');
        
        if (!in_array($resultado, ['', '0', '0.5', '1'])) {
            return back()->with('error', 'Resultado inválido');
        }

        try {
            DB::beginTransaction();

            // Actualizar resultado de la partida
            $partida->update(['resultado' => $resultado]);

            // Actualizar puntos del participante
            $participante = Participante::where('torneo_id', $partida->torneo_id)
                ->where('miembro_id', $partida->participante_id)
                ->first();

            if ($participante) {
                $puntos = Partida::where('torneo_id', $partida->torneo_id)
                    ->where('participante_id', $partida->participante_id)
                    ->sum('resultado');
                
                $participante->update(['puntos' => $puntos]);
            }

            DB::commit();
            return back()->with('success', 'Resultado actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar resultado: ' . $e->getMessage());
        }
    }
} 