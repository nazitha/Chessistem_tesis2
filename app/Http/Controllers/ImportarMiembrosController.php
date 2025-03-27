<?php
namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Miembro;
use App\Models\CategoriaTorneo;
use App\Models\SistemaEmparejamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportarMiembrosController extends Controller
{
    public function importar(Request $request)
    {
        $resultado = [
            "registrosEncontrados" => 0,
            "registrosInsertados" => 0,
            "registrosExistentes" => 0,
            "registrosIncompletos" => 0,
            "errores" => 0,
            "registrosNoInsertados" => 0,
            "detallesErrores" => []
        ];

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file')->getRealPath();

            DB::transaction(function () use ($file, &$resultado) {
                if (($handle = fopen($file, "r")) !== false) {
                    fgetcsv($handle, 1000, ","); // Omitir encabezado

                    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                        $resultado['registrosEncontrados']++;

                        try {
                            // Convertir a UTF-8 y trim
                            $data = array_map(fn($value) => trim(utf8_encode($value)), $data);

                            // Validar campos obligatorios
                            if (empty($data[0]) || empty($data[1]) || empty($data[2])) {
                                $resultado['registrosIncompletos']++;
                                continue;
                            }

                            // Procesar fechas con Carbon
                            $fechaInicio = Carbon::createFromFormat('d/m/Y', $data[1]);
                            $horaInicio = Carbon::createFromFormat('g:i A', 
                                str_replace([' p. m.', ' a. m.'], [' PM', ' AM'], $data[2])
                            );

                            // Buscar o crear categoría
                            $categoria = CategoriaTorneo::firstOrCreate(
                                ['categoria_torneo' => $data[3]],
                                ['categoria_torneo' => $data[3]]
                            );

                            // Buscar sistema de emparejamiento
                            $sistema = SistemaEmparejamiento::firstOrCreate(
                                ['sistema' => $data[4]],
                                ['sistema' => $data[4]]
                            );

                            // Validar miembros
                            $miembrosIds = array_slice($data, 7, 5);
                            foreach ($miembrosIds as $cedula) {
                                if (!Miembro::where('cedula', $cedula)->exists()) {
                                    throw new \Exception("Miembro con cédula $cedula no existe");
                                }
                            }

                            // Verificar si el torneo existe
                            if (Torneo::where('nombre_torneo', $data[0])
                                ->whereDate('fecha_inicio', $fechaInicio)
                                ->exists()) {
                                $resultado['registrosExistentes']++;
                                continue;
                            }

                            // Crear torneo
                            Torneo::create([
                                'nombre_torneo' => $data[0],
                                'fecha_inicio' => $fechaInicio,
                                'hora_inicio' => $horaInicio,
                                'categoria_torneo_id' => $categoria->id,
                                'sistema_emparejamiento_id' => $sistema->id,
                                'lugar' => $data[5],
                                'no_rondas' => $data[6],
                                'organizador_id' => $data[7],
                                'director_torneo_id' => $data[8],
                                'arbitro_id' => $data[9],
                                'arbitro_principal_id' => $data[10],
                                'arbitro_adjunto_id' => $data[11],
                                'estado_torneo' => true
                            ]);

                            $resultado['registrosInsertados']++;

                        } catch (\Exception $e) {
                            $resultado['errores']++;
                            $resultado['detallesErrores'][] = "Línea {$resultado['registrosEncontrados']}: " . $e->getMessage();
                        }
                    }
                    fclose($handle);
                }
            });

            // Calcular registros no insertados
            $resultado['registrosNoInsertados'] = $resultado['registrosEncontrados'] 
                - $resultado['registrosInsertados'] 
                - $resultado['registrosExistentes'] 
                - $resultado['registrosIncompletos'];

            return response()->json($resultado);
        }

        return response()->json(["error" => "Archivo no válido"], 400);
    }
}