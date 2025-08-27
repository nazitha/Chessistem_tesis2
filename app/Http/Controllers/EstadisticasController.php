<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Torneo;
use App\Models\ParticipanteTorneo;
use Illuminate\Support\Facades\DB;

class EstadisticasController extends Controller
{
    /**
     * Obtiene las estadísticas mensuales de torneos para el gráfico
     */
    public function estadisticasMensuales(): JsonResponse
    {
        try {
            $estadisticas = DB::select("
                SELECT 
                    YEAR(t.fecha_inicio) AS anio,
                    MONTH(t.fecha_inicio) AS mes,
                    CASE MONTH(t.fecha_inicio)
                        WHEN 1 THEN 'Ene'
                        WHEN 2 THEN 'Feb'
                        WHEN 3 THEN 'Mar'
                        WHEN 4 THEN 'Abr'
                        WHEN 5 THEN 'May'
                        WHEN 6 THEN 'Jun'
                        WHEN 7 THEN 'Jul'
                        WHEN 8 THEN 'Ago'
                        WHEN 9 THEN 'Sep'
                        WHEN 10 THEN 'Oct'
                        WHEN 11 THEN 'Nov'
                        WHEN 12 THEN 'Dic'
                    END AS mes_corto,
                    COUNT(t.id) AS total_torneos,
                    SUM(CASE 
                        WHEN t.fecha_fin IS NOT NULL AND t.fecha_fin < CURDATE() THEN 1 
                        ELSE 0 
                    END) AS torneos_completados,
                    SUM(CASE 
                        WHEN t.estado_torneo = 1 AND (t.fecha_fin IS NULL OR t.fecha_fin >= CURDATE()) THEN 1 
                        ELSE 0 
                    END) AS torneos_en_curso,
                    SUM(CASE 
                        WHEN t.estado_torneo = 0 AND (t.fecha_fin IS NULL OR t.fecha_fin >= CURDATE()) THEN 1 
                        ELSE 0 
                    END) AS torneos_pendientes,
                    SUM(COALESCE(pt_count.participantes_count, 0)) AS total_participantes,
                    ROUND(AVG(COALESCE(pt_count.participantes_count, 0)), 1) AS promedio_participantes
                FROM torneos t
                LEFT JOIN (
                    SELECT 
                        torneo_id,
                        COUNT(*) AS participantes_count
                    FROM participantes_torneo
                    GROUP BY torneo_id
                ) pt_count ON t.id = pt_count.torneo_id
                WHERE t.fecha_inicio IS NOT NULL
                AND t.fecha_inicio >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY YEAR(t.fecha_inicio), MONTH(t.fecha_inicio), CASE MONTH(t.fecha_inicio)
                        WHEN 1 THEN 'Ene'
                        WHEN 2 THEN 'Feb'
                        WHEN 3 THEN 'Mar'
                        WHEN 4 THEN 'Abr'
                        WHEN 5 THEN 'May'
                        WHEN 6 THEN 'Jun'
                        WHEN 7 THEN 'Jul'
                        WHEN 8 THEN 'Ago'
                        WHEN 9 THEN 'Sep'
                        WHEN 10 THEN 'Oct'
                        WHEN 11 THEN 'Nov'
                        WHEN 12 THEN 'Dic'
                    END
                ORDER BY YEAR(t.fecha_inicio) ASC, MONTH(t.fecha_inicio) ASC
            ");

            // Crear array con todos los meses del rango (últimos 12 meses)
            $meses = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            
            $datosFormateados = [];
            $fechaActual = now();
            // Calcular fecha de inicio: mes actual del año anterior
            $fechaInicio = $fechaActual->copy()->subYear()->startOfMonth();
            
            // Calcular fecha de fin: mes actual del año actual
            $fechaFin = $fechaActual->copy()->startOfMonth();
            
            // Calcular cuántos meses hay entre las fechas
            $mesesDiferencia = $fechaInicio->diffInMonths($fechaFin) + 1;
            
            // Crear datos para cada mes del rango (desde fecha de inicio hasta fecha de fin)
            for ($i = 0; $i < $mesesDiferencia; $i++) {
                $fecha = $fechaInicio->copy()->addMonths($i);
                $mesIndex = $fecha->month - 1;
                $mesNombre = $meses[$mesIndex];
                
                // Buscar datos para este mes
                $datosMes = null;
                foreach ($estadisticas as $estadistica) {
                    if ($estadistica->anio == $fecha->year && $estadistica->mes == $fecha->month) {
                        $datosMes = $estadistica;
                        break;
                    }
                }
                
                // Si no hay datos para este mes, crear entrada con ceros
                if ($datosMes) {
                    $datosFormateados[] = [
                        'mes' => $mesNombre,
                        'torneos' => (int)$datosMes->total_torneos,
                        'participantes' => (int)$datosMes->total_participantes,
                        'completados' => (int)$datosMes->torneos_completados,
                        'en_curso' => (int)$datosMes->torneos_en_curso,
                        'pendientes' => (int)$datosMes->torneos_pendientes,
                        'promedio_participantes' => (float)$datosMes->promedio_participantes
                    ];
                } else {
                    $datosFormateados[] = [
                        'mes' => $mesNombre,
                        'torneos' => 0,
                        'participantes' => 0,
                        'completados' => 0,
                        'en_curso' => 0,
                        'pendientes' => 0,
                        'promedio_participantes' => 0.0
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $datosFormateados
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene estadísticas generales del dashboard
     */
    public function estadisticasGenerales(): JsonResponse
    {
        try {
            $estadisticas = [
                'total_torneos' => Torneo::count(),
                'torneos_completados' => Torneo::whereNotNull('fecha_fin')
                    ->where('fecha_fin', '<', now())->count(),
                'torneos_en_curso' => Torneo::where('estado_torneo', 1)
                    ->where(function($q) {
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>=', now());
                    })->count(),
                'torneos_pendientes' => Torneo::where('estado_torneo', 0)
                    ->where(function($q) {
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>=', now());
                    })->count(),
                'total_participantes' => ParticipanteTorneo::count(),
                'promedio_participantes' => round(ParticipanteTorneo::count() / max(Torneo::count(), 1), 1)
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas generales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene las estadísticas personales de partidas del usuario logueado
     */
    public function estadisticasPersonales(): JsonResponse
    {
        try {
            // Obtener el usuario logueado
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Obtener la cédula del miembro asociado al usuario
            $miembro = DB::table('miembros')
                ->where('correo_sistema_id', $user->correo)
                ->first();

            if (!$miembro) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del miembro asociado al usuario'
                ], 404);
            }

            $cedulaParticipante = $miembro->cedula;

            // Consulta para obtener estadísticas mensuales del participante
            $estadisticas = DB::select("
                SELECT 
                    YEAR(t.fecha_inicio) AS anio,
                    MONTH(t.fecha_inicio) AS mes,
                    CASE MONTH(t.fecha_inicio)
                        WHEN 1 THEN 'Ene'
                        WHEN 2 THEN 'Feb'
                        WHEN 3 THEN 'Mar'
                        WHEN 4 THEN 'Abr'
                        WHEN 5 THEN 'May'
                        WHEN 6 THEN 'Jun'
                        WHEN 7 THEN 'Jul'
                        WHEN 8 THEN 'Ago'
                        WHEN 9 THEN 'Sep'
                        WHEN 10 THEN 'Oct'
                        WHEN 11 THEN 'Nov'
                        WHEN 12 THEN 'Dic'
                    END AS mes_corto,
                    
                    -- Estadísticas de torneos del participante
                    COUNT(DISTINCT t.id) AS total_torneos_participados,
                    COUNT(DISTINCT CASE WHEN p.no_partida IS NOT NULL THEN t.id END) AS torneos_jugados_en_mes,
                    
                    -- Estadísticas de partidas del participante
                    COUNT(p.no_partida) AS total_partidas,
                    SUM(CASE 
                        WHEN p.resultado = 1.0 THEN 1 
                        ELSE 0 
                    END) AS victorias,
                    SUM(CASE 
                        WHEN p.resultado = 0.0 THEN 1 
                        ELSE 0 
                    END) AS derrotas,
                    SUM(CASE 
                        WHEN p.resultado = 0.5 THEN 1 
                        ELSE 0 
                    END) AS empates
                
                FROM partidas p
                JOIN torneos t ON p.torneo_id = t.id
                JOIN miembros m ON p.participante_id = m.cedula
                
                WHERE t.fecha_inicio IS NOT NULL
                AND t.fecha_inicio >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                AND p.participante_id = ?
                
                GROUP BY 
                    YEAR(t.fecha_inicio), 
                    MONTH(t.fecha_inicio), 
                    CASE MONTH(t.fecha_inicio)
                        WHEN 1 THEN 'Ene'
                        WHEN 2 THEN 'Feb'
                        WHEN 3 THEN 'Mar'
                        WHEN 4 THEN 'Abr'
                        WHEN 5 THEN 'May'
                        WHEN 6 THEN 'Jun'
                        WHEN 7 THEN 'Jul'
                        WHEN 8 THEN 'Ago'
                        WHEN 9 THEN 'Sep'
                        WHEN 10 THEN 'Oct'
                        WHEN 11 THEN 'Nov'
                        WHEN 12 THEN 'Dic'
                    END
                
                ORDER BY YEAR(t.fecha_inicio) ASC, MONTH(t.fecha_inicio) ASC
            ", [$cedulaParticipante]);

            // Crear array con todos los meses del rango (últimos 12 meses)
            $meses = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            
            $datosFormateados = [];
            $fechaActual = now();
            // Calcular fecha de inicio: mes actual del año anterior
            $fechaInicio = $fechaActual->copy()->subYear()->startOfMonth();
            
            // Calcular fecha de fin: mes actual del año actual
            $fechaFin = $fechaActual->copy()->startOfMonth();
            
            // Calcular cuántos meses hay entre las fechas
            $mesesDiferencia = $fechaInicio->diffInMonths($fechaFin) + 1;
            
            // Crear datos para cada mes del rango
            for ($i = 0; $i < $mesesDiferencia; $i++) {
                $fecha = $fechaInicio->copy()->addMonths($i);
                $mesIndex = $fecha->month - 1;
                $mesNombre = $meses[$mesIndex];
                
                // Buscar datos para este mes
                $datosMes = null;
                foreach ($estadisticas as $estadistica) {
                    if ($estadistica->anio == $fecha->year && $estadistica->mes == $fecha->month) {
                        $datosMes = $estadistica;
                        break;
                    }
                }
                
                // Si no hay datos para este mes, crear entrada con ceros
                if ($datosMes) {
                    $datosFormateados[] = [
                        'mes' => $mesNombre,
                        'victorias' => (int)$datosMes->victorias,
                        'derrotas' => (int)$datosMes->derrotas,
                        'empates' => (int)$datosMes->empates,
                        'total_partidas' => (int)$datosMes->total_partidas,
                        'torneos_participados' => (int)$datosMes->total_torneos_participados,
                        'torneos_jugados' => (int)$datosMes->torneos_jugados_en_mes
                    ];
                } else {
                    $datosFormateados[] = [
                        'mes' => $mesNombre,
                        'victorias' => 0,
                        'derrotas' => 0,
                        'empates' => 0,
                        'total_partidas' => 0,
                        'torneos_participados' => 0,
                        'torneos_jugados' => 0
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $datosFormateados,
                'usuario' => $user->correo,
                'participante' => $miembro->nombres . ' ' . $miembro->apellidos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas personales: ' . $e->getMessage()
            ], 500);
        }
    }
}
