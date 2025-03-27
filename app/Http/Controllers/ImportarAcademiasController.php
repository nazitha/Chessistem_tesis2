<?php
namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\CategoriaTorneo;
use App\Models\Emparejamiento;
use App\Models\Miembro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ImportarAcademiasController extends Controller
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

        try {
            if ($request->file('file')->isValid()) {
                $file = $request->file('file')->getRealPath();

                DB::transaction(function () use ($file, &$resultado) {
                    if (($handle = fopen($file, "r")) !== FALSE) {
                        fgetcsv($handle, 1000, ","); // Omitir encabezado

                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $resultado['registrosEncontrados']++;

                            // Validar campos vacíos
                            if (in_array(null, $data, true) || in_array('', $data, true)) {
                                $resultado['registrosIncompletos']++;
                                continue;
                            }

                            // Procesar datos
                            try {
                                // Validar y formatear fechas
                                $fechaInicio = Carbon::createFromFormat('d/m/Y', $data[1]);
                                $horaInicio = Carbon::createFromFormat('g:i A', str_replace(
                                    [' a. m.', ' p. m.'],
                                    [' AM', ' PM'],
                                    $data[2]
                                ));

                                // Buscar categoría
                                $categoria = CategoriaTorneo::where('categoria_torneo', $data[3])->first();
                                if (!$categoria) {
                                    throw new \Exception("Categoría no encontrada: {$data[3]}");
                                }

                                // Buscar sistema de emparejamiento
                                $sistema = Emparejamiento::where('sistema', $data[4])->first();
                                if (!$sistema) {
                                    throw new \Exception("Sistema no encontrado: {$data[4]}");
                                }

                                // Validar miembros
                                $miembrosIds = array_slice($data, 7, 5);
                                foreach ($miembrosIds as $cedula) {
                                    if (!Miembro::find($cedula)) {
                                        throw new \Exception("Cédula no existe: {$cedula}");
                                    }
                                }

                                // Crear torneo
                                Torneo::create([
                                    'nombre_torneo' => $data[0],
                                    'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                                    'hora_inicio' => $horaInicio->format('H:i:s'),
                                    'categoriaTorneo_id' => $categoria->id_torneo_categoria,
                                    'sistema_emparejamiento_id' => $sistema->id_emparejamiento,
                                    'lugar' => $data[5],
                                    'no_rondas' => (int)$data[6],
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

                $resultado['registrosNoInsertados'] = $resultado['registrosEncontrados'] - $resultado['registrosInsertados'];
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'detalles' => $resultado
            ], 500);
        }

        return response()->json($resultado);
    }
}