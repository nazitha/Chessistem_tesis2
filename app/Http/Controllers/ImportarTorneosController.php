<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CategoriaTorneo;
use App\Models\Emparejamiento;
use App\Models\Miembro;
use App\Models\Torneo;
use Illuminate\Support\Facades\Validator;

class ImportarTorneosController extends Controller
{
    public function importar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Archivo inválido'], 400);
        }

        $resultado = [
            'registrosEncontrados' => 0,
            'registrosInsertados' => 0,
            'registrosExistentes' => 0,
            'registrosIncompletos' => 0,
            'errores' => 0,
            'registrosNoInsertados' => 0,
            'detallesErrores' => []
        ];

        DB::transaction(function () use ($request, &$resultado) {
            if ($request->file('file')->isValid()) {
                $file = $request->file('file')->path();

                if (($handle = fopen($file, "r")) !== FALSE) {
                    fgetcsv($handle, 1000, ",");

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $resultado['registrosEncontrados']++;

                        try {
                            $processedData = $this->procesarFila($data, $resultado);
                            if (!$processedData) continue;

                            $this->crearTorneo($processedData, $resultado);

                        } catch (\Exception $e) {
                            $this->registrarError($resultado, $e->getMessage());
                        }
                    }
                    fclose($handle);
                }
            }
        });

        $resultado['registrosNoInsertados'] = $resultado['registrosEncontrados'] 
            - $resultado['registrosInsertados']
            - $resultado['registrosExistentes']
            - $resultado['registrosIncompletos'];

        return response()->json($resultado);
    }

    private function procesarFila($data, &$resultado)
    {
        // Validación básica de campos
        if (count($data) < 12 || in_array(null, $data, true)) {
            $resultado['registrosIncompletos']++;
            return null;
        }

        // Procesamiento de fechas
        try {
            $fechaInicio = Carbon::createFromFormat('d/m/Y', trim($data[1]));
            $horaInicio = Carbon::createFromFormat(
                'g:i A', 
                str_replace([' p. m.', ' a. m.'], ['PM', 'AM'], trim($data[2]))
            );
        } catch (\Exception $e) {
            $this->registrarError($resultado, "Formato de fecha/hora inválido");
            return null;
        }

        // Búsqueda de relaciones
        $relaciones = $this->buscarRelaciones($data, $resultado);
        if (!$relaciones) return null;

        return [
            'nombre' => trim($data[0]),
            'fecha_inicio' => $fechaInicio,
            'hora_inicio' => $horaInicio,
            'lugar' => trim($data[5]),
            'no_rondas' => trim($data[6]),
            'relaciones' => $relaciones
        ];
    }

    private function buscarRelaciones($data, &$resultado)
    {
        $requiredRelations = [
            'categoria' => CategoriaTorneo::where('categoria_torneo', trim($data[3]))->first(),
            'sistema' => Emparejamiento::where('sistema', trim($data[4]))->first(),
            'organizador' => Miembro::find(trim($data[7])),
            'director' => Miembro::find(trim($data[8])),
            'arbitro' => Miembro::find(trim($data[9])),
            'arbitro_principal' => Miembro::find(trim($data[10])),
            'arbitro_adjunto' => Miembro::find(trim($data[11]))
        ];

        foreach ($requiredRelations as $key => $relation) {
            if (!$relation) {
                $this->registrarError($resultado, "$key no encontrado");
                return null;
            }
        }

        return $requiredRelations;
    }

    private function crearTorneo($data, &$resultado)
    {
        if (Torneo::where('nombre_torneo', $data['nombre'])
            ->whereDate('fecha_inicio', $data['fecha_inicio'])
            ->exists()) {
            $resultado['registrosExistentes']++;
            return;
        }

        Torneo::create([
            'nombre_torneo' => $data['nombre'],
            'fecha_inicio' => $data['fecha_inicio'],
            'hora_inicio' => $data['hora_inicio']->format('H:i:s'),
            'lugar' => $data['lugar'],
            'no_rondas' => $data['no_rondas'],
            'categoriaTorneo_id' => $data['relaciones']['categoria']->id,
            'sistema_emparejamiento_id' => $data['relaciones']['sistema']->id,
            'organizador_id' => $data['relaciones']['organizador']->cedula,
            'director_torneo_id' => $data['relaciones']['director']->cedula,
            'arbitro_id' => $data['relaciones']['arbitro']->cedula,
            'arbitro_principal_id' => $data['relaciones']['arbitro_principal']->cedula,
            'arbitro_adjunto_id' => $data['relaciones']['arbitro_adjunto']->cedula
        ]);

        $resultado['registrosInsertados']++;
    }

    private function registrarError(&$resultado, $mensaje)
    {
        $resultado['errores']++;
        $resultado['detallesErrores'][] = [
            'linea' => $resultado['registrosEncontrados'],
            'error' => $mensaje
        ];
    }
}