<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participante;
use App\Models\Torneo;
use App\Models\Miembro;
use App\Models\Auditoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    public function index()
    {
        $inscripciones = Participante::with(['torneo', 'miembro.fide'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($participante) {
                return [
                    'torneo_id' => $participante->torneo_id,
                    'torneo' => $participante->torneo->nombre_torneo . ' (' . 
                               $participante->torneo->fecha_inicio->translatedFormat('F d, Y') . ')',
                    'participante' => $participante->miembro->nombre_completo,
                    'cedula' => $participante->participante_id,
                    'federacion' => $participante->miembro->fide->fed_id ?? '(Ninguna)',
                    'estado' => $participante->torneo->estado_torneo ? 'Activo' : 'Finalizado'
                ];
            });

        return response()->json($inscripciones);
    }

    public function torneosActivos()
    {
        $torneos = Torneo::where('estado_torneo', true)
            ->orderBy('fecha_inicio', 'desc')
            ->get()
            ->map(function ($torneo) {
                return [
                    'id_torneo' => $torneo->id_torneo,
                    'torneo' => $torneo->nombre_torneo . ' (' . 
                               $torneo->fecha_inicio->translatedFormat('F d, Y') . ')'
                ];
            });

        return response()->json($torneos);
    }

    public function miembrosActivos()
    {
        $miembros = Miembro::where('estado_miembro', true)
            ->orderBy('nombres')
            ->get()
            ->map(function ($miembro) {
                return [
                    'cedula' => $miembro->cedula,
                    'miembro' => $miembro->nombre_completo . ' (' . $miembro->cedula . ')'
                ];
            });

        return response()->json($miembros);
    }

    public function store(Request $request)
    {
        $request->validate([
            'participante_id' => 'required|exists:miembros,cedula',
            'torneo_id' => 'required|exists:torneos,id_torneo'
        ]);

        return DB::transaction(function () use ($request) {
            $participante = Participante::create($request->only(['participante_id', 'torneo_id']));
            
            $this->crearAuditoria(
                $request->mail_log,
                'Inserción',
                "[Participante: {$participante->miembro->nombre_completo} inscrito en el torneo: {$participante->torneo->nombre_torneo}]"
            );

            return response()->json(['success' => true], 201);
        });
    }

    public function update(Request $request, Participante $participante)
    {
        $request->validate([
            'participante_id' => 'required|exists:miembros,cedula',
            'torneo_id' => 'required|exists:torneos,id_torneo'
        ]);

        return DB::transaction(function () use ($request, $participante) {
            $original = $participante->toArray();
            $participante->update($request->only(['participante_id', 'torneo_id']));
            
            $this->crearAuditoria(
                $request->mail_log,
                'Edición',
                "[Participante: {$original['participante_id']} en torneo: {$original['torneo_id']}]",
                "[Participante: {$request->participante_id} en torneo: {$request->torneo_id}]"
            );

            return response()->json(['success' => true]);
        });
    }

    public function destroy(Request $request, Participante $participante)
    {
        return DB::transaction(function () use ($request, $participante) {
            // Guardar datos del participante antes de eliminarlo para auditoría
            $datosParticipante = $participante->toArray();
            $nombreCompleto = $participante->miembro->nombre_completo;
            $nombreTorneo = $participante->torneo->nombre_torneo;
            
            $participante->delete();
            
            $this->crearAuditoria(
                $request->mail_log,
                'Eliminación',
                json_encode($datosParticipante),
                null
            );

            return response()->json(['success' => true]);
        });
    }

    private function crearAuditoria($correo, $accion, $previo, $posterior = null)
    {
        // Usar la zona horaria de Guatemala
        $fechaHora = Carbon::now()->setTimezone('America/Guatemala');
        
        Auditoria::create([
            'correo_id' => $correo,
            'tabla_afectada' => 'Participantes/Inscripciones',
            'accion' => $accion,
            'valor_previo' => $previo,
            'valor_posterior' => $posterior ?? '-',
            'fecha' => $fechaHora->toDateString(),
            'hora' => $fechaHora->toTimeString(),
            'equipo' => request()->ip()
        ]);
    }
}