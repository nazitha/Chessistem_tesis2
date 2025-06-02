<?php
namespace App\Http\Controllers;

use App\Models\Fide;
use App\Models\Federacion;
use App\Models\Miembro;
use App\Models\PuntajeElo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportarFidesController extends Controller
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

        if ($request->hasFile('csvFile')) {
            $csvFile = $request->file('csvFile')->getRealPath();

            if (($handle = fopen($csvFile, 'r')) !== false) {
                fgetcsv($handle, 1000, ','); // Omitir encabezado

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $resultado['registrosEncontrados']++;

                    try {
                        $fide_id = (int)trim($data[0]);
                        $cedula = trim($data[1]);
                        $federacion = trim($data[3]);
                        $titulo = !empty(trim($data[4])) ? trim($data[4]) : null;
                        
                        // Validaciones básicas
                        if (empty($fide_id)) {
                            $resultado['registrosIncompletos']++;
                            continue;
                        }

                        DB::transaction(function () use ($fide_id, $cedula, $federacion, $titulo, $data, &$resultado) {
                            // Verificar existencia
                            if (Fide::find($fide_id)) {
                                $resultado['registrosExistentes']++;
                                return;
                            }

                            // Validar federación y miembro
                            if (!Federacion::where('acronimo', $federacion)->exists()) {
                                throw new \Exception("Federación no existe");
                            }

                            if (!Miembro::find($cedula)) {
                                throw new \Exception("Miembro no existe");
                            }

                            // Crear FIDE
                            $fide = Fide::create([
                                'fide_id' => $fide_id,
                                'cedula_ajedrecista_id' => $cedula,
                                'fed_id' => $federacion,
                                'titulo' => $titulo,
                                'fide_estado' => true
                            ]);

                            // Insertar puntajes ELO
                            $eloData = [
                                ['no_categoria_elo' => 1, 'elo' => (int)$data[5] ?? 0],
                                ['no_categoria_elo' => 2, 'elo' => (int)$data[6] ?? 0],
                                ['no_categoria_elo' => 3, 'elo' => (int)$data[7] ?? 0]
                            ];

                            $fide->puntajesElo()->createMany($eloData);

                            $resultado['registrosInsertados']++;
                        });
                    } catch (\Exception $e) {
                        $resultado['errores']++;
                    }
                }

                fclose($handle);
                
                // Calcular registros no insertados
                $resultado['registrosNoInsertados'] = $resultado['registrosEncontrados'] 
                    - $resultado['registrosInsertados'] 
                    - $resultado['registrosExistentes'] 
                    - $resultado['registrosIncompletos'];

                return response()->json($resultado);
            }
            return response()->json(["error" => "Error al leer el archivo"], 400);
        }
        return response()->json(["error" => "No se subió ningún archivo"], 400);
    }
}