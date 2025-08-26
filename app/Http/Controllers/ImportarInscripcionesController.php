<?php
namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Miembro;
use App\Models\Participante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportarInscripcionesController extends Controller
{
    public function importar(Request $request)
    {
        $resultado = [
            "registrosEncontrados" => 0,
            "registrosInsertados" => 0,
            "registrosExistentes" => 0,
            "registrosIncompletos" => 0,
            "errores" => 0,
            "registrosNoInsertados" => 0
        ];

        if ($request->hasFile('csvFile') && $request->file('csvFile')->isValid()) {
            $file = $request->file('csvFile')->getRealPath();

            if (($handle = fopen($file, 'r')) !== false) {
                fgetcsv($handle); 

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $resultado['registrosEncontrados']++;

                    try {
                        DB::transaction(function () use ($data, &$resultado) {
                            if (empty($data[0]) || empty($data[1]) || empty($data[2])) {
                                $resultado['registrosIncompletos']++;
                                return;
                            }

                            $nombreTorneo = trim($data[0]);
                            $fechaInicio = Carbon::createFromFormat('d/m/Y', trim($data[1]));
                            $cedula = trim($data[2]);

                            $torneo = Torneo::where('nombre_torneo', $nombreTorneo)
                                ->whereDate('fecha_inicio', $fechaInicio->format('Y-m-d'))
                                ->first();

                            if (!$torneo) {
                                $resultado['errores']++;
                                return;
                            }

                            if (!Miembro::find($cedula)) {
                                $resultado['errores']++;
                                return;
                            }

                            if (Participante::where('participante_id', $cedula)
                                ->where('torneo_id', $torneo->id_torneo)
                                ->exists()) {
                                $resultado['registrosExistentes']++;
                                return;
                            }

                            Participante::create([
                                'participante_id' => $cedula,
                                'torneo_id' => $torneo->id_torneo
                            ]);

                            $resultado['registrosInsertados']++;
                        });
                    } catch (\Exception $e) {
                        $resultado['errores']++;
                    }
                }

                fclose($handle);

                $resultado['registrosNoInsertados'] = $resultado['registrosEncontrados'] 
                    - $resultado['registrosInsertados'] 
                    - $resultado['registrosExistentes'] 
                    - $resultado['registrosIncompletos'];

                return response()->json($resultado);
            }
            return response()->json(["error" => "Error al leer el archivo"], 400);
        }
        return response()->json(["error" => "Archivo no válido"], 400);
    }
}
