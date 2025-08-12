<?php

namespace App\Http\Controllers;

use App\Models\AnalisisPartida;
use App\Models\Partida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AnalisisPartidaController extends Controller
{
    // Listar todos los análisis
    public function index()
    {
        $analisis = AnalisisPartida::with(['partida', 'jugadorBlancas', 'jugadorNegras'])->latest()->paginate(10);
        return view('analisis_partidas.index', compact('analisis'));
    }

    // Mostrar análisis de una partida
    public function show($id)
    {
        $analisis = AnalisisPartida::with(['partida', 'jugadorBlancas', 'jugadorNegras'])->findOrFail($id);
        return view('analisis_partidas.show', compact('analisis'));
    }

    // Guardar o actualizar análisis de una partida
    public function store(Request $request)
    {

        // Validar que venga al menos uno de los campos requeridos
        if (!$request->has('partida_id') && !$request->has('pgn_manual')) {
            return response()->json(['error' => 'Debe proporcionar una partida existente o un PGN manual.'], 400);
        }

        if ($request->has('partida_id')) {
            // Análisis de partida existente
            $validator = Validator::make($request->all(), [
                'partida_id' => 'required|exists:partidas,no_partida',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $partida = Partida::where('no_partida', $request->partida_id)->first();
            if (!$partida || empty($partida->movimientos)) {
                return response()->json(['error' => 'La partida no tiene movimientos registrados.'], 400);
            }

            // Usar updateOrCreate para evitar duplicados
            $analisis = AnalisisPartida::updateOrCreate(
                ['partida_id' => $partida->no_partida],
                array_merge([
                    'movimientos' => $partida->movimientos,
                    'jugador_blancas_id' => $partida->jugador_blancas_id,
                    'jugador_negras_id' => $partida->jugador_negras_id,
                ], $this->analizarMovimientos($partida->movimientos))
            );

        } else {
            // Análisis de PGN manual
            Log::info('AnalisisPartidaController@store - PGN manual detectado', [
                'has_jugador_blancas' => $request->has('jugador_blancas'),
                'has_jugador_negras' => $request->has('jugador_negras'),
                'has_fecha_partida' => $request->has('fecha_partida'),
                'pgn_length' => strlen($request->pgn_manual ?? '')
            ]);
            
            if ($request->has('jugador_blancas') && $request->has('jugador_negras') && $request->has('fecha_partida')) {
                // PGN manual con información de jugadores (desde tab "Agregar Movimientos")
                Log::info('AnalisisPartidaController@store - Validando PGN con jugadores');
                
                $validator = Validator::make($request->all(), [
                    'pgn_manual' => 'required|string|min:10',
                    'jugador_blancas' => 'required|string|max:255',
                    'jugador_negras' => 'required|string|max:255',
                    'fecha_partida' => 'required|date',
                ]);
                
                if ($validator->fails()) {
                    Log::error('AnalisisPartidaController@store - Validación falló', ['errors' => $validator->errors()]);
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                // Crear un análisis con PGN manual y información de jugadores
                try {
                    $analisis = AnalisisPartida::create(array_merge([
                        'partida_id' => null,
                        'movimientos' => $request->pgn_manual,
                        'jugador_blancas_id' => $request->jugador_blancas,
                        'jugador_negras_id' => $request->jugador_negras,
                    ], $this->analizarMovimientos($request->pgn_manual)));
                    
                    Log::info('AnalisisPartidaController@store - Análisis creado con jugadores', ['analisis_id' => $analisis->id]);
                } catch (\Exception $e) {
                    Log::error('AnalisisPartidaController@store - Error al crear análisis con jugadores', ['error' => $e->getMessage()]);
                    return response()->json(['error' => 'Error al crear el análisis: ' . $e->getMessage()], 500);
                }
            } else {
                // PGN manual simple (desde tab "Pegar/Cargar PGN")
                Log::info('AnalisisPartidaController@store - Validando PGN simple');
                
                $validator = Validator::make($request->all(), [
                    'pgn_manual' => 'required|string|min:10',
                ]);
                
                if ($validator->fails()) {
                    Log::error('AnalisisPartidaController@store - Validación PGN simple falló', ['errors' => $validator->errors()]);
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                // Crear un análisis con PGN manual simple
                try {
                    $analisis = AnalisisPartida::create(array_merge([
                        'partida_id' => null,
                        'movimientos' => $request->pgn_manual,
                        'jugador_blancas_id' => 'PGN_MANUAL',
                        'jugador_negras_id' => 'PGN_MANUAL',
                    ], $this->analizarMovimientos($request->pgn_manual)));
                    
                    Log::info('AnalisisPartidaController@store - Análisis PGN simple creado', ['analisis_id' => $analisis->id]);
                } catch (\Exception $e) {
                    Log::error('AnalisisPartidaController@store - Error al crear análisis PGN simple', ['error' => $e->getMessage()]);
                    return response()->json(['error' => 'Error al crear el análisis: ' . $e->getMessage()], 500);
                }
            }
        }

        return response()->json(['success' => true, 'analisis_id' => $analisis->id]);
    }

    // Análisis mejorado más realista (simulado)
    private function analizarMovimientos($movimientos)
    {
        // Analizar el PGN para obtener información básica
        $pgnLength = strlen($movimientos);
        $moveCount = substr_count($movimientos, '.');
        $hasCheckmate = strpos($movimientos, '#') !== false;
        $hasCheck = strpos($movimientos, '+') !== false;
        $hasCapture = strpos($movimientos, 'x') !== false;
        $hasCastle = strpos($movimientos, 'O-O') !== false;
        
        // Generar métricas más realistas basadas en el contenido
        $baseQuality = $this->calcularCalidadBase($pgnLength, $moveCount, $hasCheckmate, $hasCheck, $hasCapture, $hasCastle);
        
        // Generar estadísticas variadas para cada jugador
        $statsBlancas = $this->generarEstadisticasJugador($baseQuality, 'blancas');
        $statsNegras = $this->generarEstadisticasJugador($baseQuality, 'negras');
        
        // Generar evaluación basada en el resultado y calidad
        $evaluacion = $this->generarEvaluacion($movimientos, $baseQuality, $statsBlancas, $statsNegras);
        
        return [
            'evaluacion_general' => $evaluacion,
            'errores_blancas' => $statsBlancas['errores'],
            'errores_negras' => $statsNegras['errores'],
            'brillantes_blancas' => $statsBlancas['brillantes'],
            'brillantes_negras' => $statsNegras['brillantes'],
            'blunders_blancas' => $statsBlancas['blunders'],
            'blunders_negras' => $statsNegras['blunders']
        ];
    }
    
    // Calcular calidad base de la partida
    private function calcularCalidadBase($pgnLength, $moveCount, $hasCheckmate, $hasCheck, $hasCapture, $hasCastle)
    {
        $quality = 50; // Base neutral
        
        // Ajustar por longitud
        if ($pgnLength < 100) $quality -= 20; // Partida muy corta
        elseif ($pgnLength > 500) $quality += 10; // Partida larga
        
        // Ajustar por complejidad
        if ($hasCheckmate) $quality += 15; // Final decisivo
        if ($hasCheck) $quality += 5; // Posiciones tácticas
        if ($hasCapture) $quality += 3; // Capturas
        if ($hasCastle) $quality += 2; // Enroque
        
        // Ajustar por número de movimientos
        if ($moveCount > 30) $quality += 10;
        elseif ($moveCount < 10) $quality -= 15;
        
        return max(20, min(95, $quality)); // Mantener entre 20-95
    }
    
    // Generar estadísticas para un jugador
    private function generarEstadisticasJugador($baseQuality, $color)
    {
        // Usar el color como semilla para variabilidad
        $seed = crc32($color . $baseQuality);
        srand($seed);
        
        // Calcular métricas basadas en la calidad
        $precision = $baseQuality + rand(-15, 15);
        $precision = max(30, min(95, $precision));
        
        // Generar estadísticas más realistas
        $totalMoves = rand(15, 40);
        $errores = round($totalMoves * (100 - $precision) / 100 * 0.3);
        $blunders = round($totalMoves * (100 - $precision) / 100 * 0.1);
        $brillantes = round($totalMoves * $precision / 100 * 0.05);
        
        // Asegurar valores mínimos y máximos realistas
        $errores = max(0, min(8, $errores));
        $blunders = max(0, min(3, $blunders));
        $brillantes = max(0, min(4, $brillantes));
        
        return [
            'errores' => $errores,
            'blunders' => $blunders,
            'brillantes' => $brillantes,
            'precision' => $precision
        ];
    }
    
    // Generar evaluación textual
    private function generarEvaluacion($movimientos, $baseQuality, $statsBlancas, $statsNegras)
    {
        $evaluacion = '';
        
        // Evaluar la calidad general
        if ($baseQuality >= 80) {
            $evaluacion = 'Partida de alta calidad con excelente nivel técnico. ';
        } elseif ($baseQuality >= 60) {
            $evaluacion = 'Partida interesante con buenas oportunidades tácticas. ';
        } elseif ($baseQuality >= 40) {
            $evaluacion = 'Partida con algunos errores pero momentos interesantes. ';
        } else {
            $evaluacion = 'Partida con múltiples errores y oportunidades perdidas. ';
        }
        
        // Agregar detalles específicos
        if (strpos($movimientos, '1-0') !== false) {
            $evaluacion .= 'Victoria de las blancas tras una lucha intensa.';
        } elseif (strpos($movimientos, '0-1') !== false) {
            $evaluacion .= 'Victoria de las negras tras una lucha intensa.';
        } elseif (strpos($movimientos, '1/2-1/2') !== false) {
            $evaluacion .= 'Tablas tras una partida equilibrada.';
        } else {
            $evaluacion .= 'Partida con resultado incierto.';
        }
        
        // Agregar comentarios sobre la precisión
        $avgPrecision = ($statsBlancas['precision'] + $statsNegras['precision']) / 2;
        if ($avgPrecision >= 85) {
            $evaluacion .= ' Ambos jugadores mostraron alta precisión.';
        } elseif ($avgPrecision >= 70) {
            $evaluacion .= ' Nivel de precisión aceptable.';
        } else {
            $evaluacion .= ' Se cometieron varios errores importantes.';
        }
        
        return $evaluacion;
    }

    // API: Obtener análisis recientes
    public function analisisRecientes()
    {
        $analisis = AnalisisPartida::with(['jugadorBlancas', 'jugadorNegras'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($analisis) {
                return [
                    'id' => $analisis->id,
                    'jugador_blancas_nombre' => $analisis->jugador_blancas_nombre,
                    'jugador_negras_nombre' => $analisis->jugador_negras_nombre,
                    'errores_blancas' => $analisis->errores_blancas,
                    'errores_negras' => $analisis->errores_negras,
                    'brillantes_blancas' => $analisis->brillantes_blancas,
                    'brillantes_negras' => $analisis->brillantes_negras,
                    'evaluacion_general' => $analisis->evaluacion_general,
                    'created_at' => $analisis->created_at->format('Y-m-d H:i')
                ];
            });

        return response()->json($analisis);
    }

        $validator = Validator::make($request->all(), [
            'partida_id' => 'required|exists:partidas,no_partida',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $partida = Partida::where('no_partida', $request->partida_id)->first();
        if (!$partida || empty($partida->movimientos)) {
            return response()->json(['error' => 'La partida no tiene movimientos registrados.'], 400);
        }
        // Evitar duplicados
        $analisis = AnalisisPartida::where('partida_id', $request->partida_id)->first();
        if ($analisis) {
            // Si ya existe, actualizar
            $analisis->update($this->analizarMovimientos($partida));
        } else {
            $analisis = AnalisisPartida::create(array_merge(
                ['partida_id' => $partida->no_partida,
                 'movimientos' => $partida->movimientos,
                 'jugador_blancas_id' => $partida->jugador_blancas_id,
                 'jugador_negras_id' => $partida->jugador_negras_id],
                $this->analizarMovimientos($partida)
            ));
        }
        return response()->json(['success' => true, 'analisis_id' => $analisis->id]);
    }

    // Simulación de análisis (puedes mejorarla luego)
    private function analizarMovimientos($partida)
    {
        // Aquí podrías usar $partida->movimientos (PGN/FEN)
        return [
            'evaluacion_general' => 'Jugada sólida de blancas con mejor posición en el medio juego.',
            'errores_blancas' => 2,
            'errores_negras' => 1,
            'brillantes_blancas' => 1,
            'brillantes_negras' => 0,
            'blunders_blancas' => 0,
            'blunders_negras' => 1
        ];
    }

} 