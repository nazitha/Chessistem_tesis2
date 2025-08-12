<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Torneo;
use App\Models\Participante;
use App\Models\Partida;
use App\Models\AnalisisPartida;
use App\Models\Miembro;

class AnalisisTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos miembros si no existen
        $miembros = [
            ['cedula' => '1234567890', 'nombres' => 'Carlos Magno', 'apellidos' => 'García'],
            ['cedula' => '0987654321', 'nombres' => 'Ana María', 'apellidos' => 'López'],
            ['cedula' => '1122334455', 'nombres' => 'Roberto', 'apellidos' => 'Martínez'],
            ['cedula' => '5566778899', 'nombres' => 'Sofia', 'apellidos' => 'Rodríguez'],
        ];

        foreach ($miembros as $miembroData) {
            Miembro::firstOrCreate(
                ['cedula' => $miembroData['cedula']],
                $miembroData
            );
        }

        // Crear un torneo de prueba
        $torneo = Torneo::firstOrCreate(
            ['nombre_torneo' => 'Torneo de Prueba para Análisis'],
            [
                'nombre_torneo' => 'Torneo de Prueba para Análisis',
                'fecha_inicio' => '2025-01-15',
                'fecha_fin' => '2025-01-20',
                'es_por_equipos' => false,
                'estado_torneo' => 'Finalizado'
            ]
        );

        // Crear participantes
        $participantes = [];
        foreach ($miembros as $miembroData) {
            $participante = Participante::firstOrCreate(
                ['miembro_id' => $miembroData['cedula'], 'torneo_id' => $torneo->id],
                [
                    'miembro_id' => $miembroData['cedula'],
                    'torneo_id' => $torneo->id,
                    'puntos' => 0,
                    'posicion' => 0
                ]
            );
            $participantes[] = $participante;
        }

        // Crear partidas con movimientos de prueba
        $partidasData = [
            [
                'movimientos' => '1.e4 e5 2.Nf3 Nc6 3.Bb5 a6 4.Ba4 Nf6 5.O-O Be7 6.Re1 b5 7.Bb3 d6 8.c3 O-O 9.h3 Nb8 10.d4 Nbd7 11.Nbd2 Bb7 12.Bc2 Re8 13.Nf1 Bf8 14.Ng3 g6 15.Bg5 h6 16.Bd2 c5 17.d5 c4 18.Nh2 Nc5 19.Ng4 Nfd7 20.f4 exf4 21.Bxf4 Ne5 22.Nxe5 dxe5 23.Bxe5 Bg7 24.Bxg7 Kxg7 25.Qd2+ Kg8 26.e5 Qc7 27.Nf6+ Nxf6 28.exf6 Qd6 29.Qe3 Qxf6 30.Qxe8+ Qf8 31.Qxf8+ Kxf8 32.Re7 Bc8 33.Rc7 Bb7 34.Rc6 Ke8 35.Rxb6 Kd7 36.Rb7+ Kc8 37.Rb8+ Kc7 38.Rb7+ Kc8 39.Rb8+ Kc7 40.Rb7+ Kc8 41.Rb8+ Kc7 42.Rb7+ Kc8 43.Rb8+ Kc7 44.Rb7+ Kc8 45.Rb8+ Kc7 46.Rb7+ Kc8 47.Rb8+ Kc7 48.Rb7+ Kc8 49.Rb8+ Kc7 50.Rb7+ Kc8 51.Rb8+ Kc7 52.Rb7+ Kc8 53.Rb8+ Kc7 54.Rb7+ Kc8 55.Rb8+ Kc7 56.Rb7+ Kc8 57.Rb8+ Kc7 58.Rb7+ Kc8 59.Rb8+ Kc7 60.Rb7+ Kc8 61.Rb8+ Kc7 62.Rb7+ Kc8 63.Rb8+ Kc7 64.Rb7+ Kc8 65.Rb8+ Kc7 66.Rb7+ Kc8 67.Rb8+ Kc7 68.Rb7+ Kc8 69.Rb8+ Kc7 70.Rb7+ Kc8 71.Rb8+ Kc7 72.Rb7+ Kc8 73.Rb8+ Kc7 74.Rb7+ Kc8 75.Rb8+ Kc7 76.Rb7+ Kc8 77.Rb8+ Kc7 78.Rb7+ Kc8 79.Rb8+ Kc7 80.Rb7+ Kc8 81.Rb8+ Kc7 82.Rb7+ Kc8 83.Rb8+ Kc7 84.Rb7+ Kc8 85.Rb8+ Kc7 86.Rb7+ Kc8 87.Rb8+ Kc7 88.Rb7+ Kc8 89.Rb8+ Kc7 90.Rb7+ Kc8 91.Rb8+ Kc7 92.Rb7+ Kc8 93.Rb8+ Kc7 94.Rb7+ Kc8 95.Rb8+ Kc7 96.Rb7+ Kc8 97.Rb8+ Kc7 98.Rb7+ Kc8 99.Rb8+ Kc7 100.Rb7+ Kc8 101.Rb8+ Kc7 102.Rb7+ Kc8 103.Rb8+ Kc7 104.Rb7+ Kc8 105.Rb8+ Kc7 106.Rb7+ Kc8 107.Rb8+ Kc7 108.Rb7+ Kc8 109.Rb8+ Kc7 110.Rb7+ Kc8 111.Rb8+ Kc7 112.Rb7+ Kc8 113.Rb8+ Kc7 114.Rb7+ Kc8 115.Rb8+ Kc7 116.Rb7+ Kc8 117.Rb8+ Kc7 118.Rb7+ Kc8 119.Rb8+ Kc7 120.Rb7+ Kc8 121.Rb8+ Kc7 122.Rb7+ Kc8 123.Rb8+ Kc7 124.Rb7+ Kc8 125.Rb8+ Kc7 126.Rb7+ Kc8 127.Rb8+ Kc7 128.Rb7+ Kc8 129.Rb8+ Kc7 130.Rb7+ Kc8 131.Rb8+ Kc7 132.Rb7+ Kc8 133.Rb8+ Kc7 134.Rb7+ Kc8 135.Rb8+ Kc7 136.Rb7+ Kc8 137.Rb8+ Kc7 138.Rb7+ Kc8 139.Rb8+ Kc7 140.Rb7+ Kc8 141.Rb8+ Kc7 142.Rb7+ Kc8 143.Rb8+ Kc7 144.Rb7+ Kc8 145.Rb8+ Kc7 146.Rb7+ Kc8 147.Rb8+ Kc7 148.Rb7+ Kc8 149.Rb8+ Kc7 150.Rb7+ Kc8 1/2-1/2',
                'jugador_blancas_id' => '1234567890',
                'jugador_negras_id' => '0987654321',
                'resultado' => 3.0 // Tablas
            ],
            [
                'movimientos' => '1.d4 Nf6 2.c4 e6 3.Nc3 Bb4 4.e3 O-O 5.Bd3 d5 6.Nf3 c5 7.O-O dxc4 8.Bxc4 Nbd7 9.Qe2 a6 10.Rd1 Qc7 11.Bd3 b5 12.Ne4 Nxe4 13.Bxe4 Bb7 14.Bd3 cxd4 15.exd4 Bxc3 16.bxc3 Qxc3 17.Bb2 Qc7 18.c4 bxc4 19.Bxc4 Nb6 20.Bb3 Rac8 21.Rac1 Qd6 22.Qe3 Rc7 23.Rc3 Rfc8 24.Rdc1 Qb4 25.Qd2 Qxd2 26.Nxd2 Rxc3 27.Rxc3 Rxc3 28.Bxc3 Nc4 29.Bb4 a5 30.Bc5 Nxd2 31.Bxd2 Bc6 32.Bc3 f6 33.Kf1 Kf7 34.Ke2 Ke7 35.Kd3 Kd6 36.Bb4+ Kc7 37.Bc5 Kd7 38.Bb4 Ke7 39.Bc5 Kf7 40.Bb4 Kg6 41.Bc5 Kh5 42.Bb4 Kg4 43.Bc5 Kf3 44.Bb4 Ke2 45.Bc5 Kd1 46.Bb4 Kc1 47.Bc5 Kb1 48.Bb4 Ka1 49.Bc5 Kb1 50.Bb4 Kc1 51.Bc5 Kd1 52.Bb4 Ke2 53.Bc5 Kf3 54.Bb4 Kg4 55.Bc5 Kh5 56.Bb4 Kg6 57.Bc5 Kf7 58.Bb4 Ke7 59.Bc5 Kd7 60.Bb4 Kc7 61.Bc5 Kd6 62.Bb4 Ke7 63.Bc5 Kf7 64.Bb4 Kg6 65.Bc5 Kh5 66.Bb4 Kg4 67.Bc5 Kf3 68.Bb4 Ke2 69.Bc5 Kd1 70.Bb4 Kc1 71.Bc5 Kb1 72.Bb4 Ka1 73.Bc5 Kb1 74.Bb4 Kc1 75.Bc5 Kd1 76.Bb4 Ke2 77.Bc5 Kf3 78.Bb4 Kg4 79.Bc5 Kh5 80.Bb4 Kg6 81.Bc5 Kf7 82.Bb4 Ke7 83.Bc5 Kd7 84.Bb4 Kc7 85.Bc5 Kd6 86.Bb4 Ke7 87.Bc5 Kf7 88.Bb4 Kg6 89.Bc5 Kh5 90.Bb4 Kg4 91.Bc5 Kf3 92.Bb4 Ke2 93.Bc5 Kd1 94.Bb4 Kc1 95.Bc5 Kb1 96.Bb4 Ka1 97.Bc5 Kb1 98.Bb4 Kc1 99.Bc5 Kd1 100.Bb4 Ke2 1/2-1/2',
                'jugador_blancas_id' => '1122334455',
                'jugador_negras_id' => '5566778899',
                'resultado' => 3.0 // Tablas
            ],
            [
                'movimientos' => '1.e4 c5 2.Nf3 d6 3.d4 cxd4 4.Nxd4 Nf6 5.Nc3 a6 6.Be3 e5 7.Nb3 Be6 8.f3 Be7 9.Qd2 O-O 10.O-O-O Nbd7 11.g4 b5 12.g5 b4 13.Ne2 Ne8 14.f4 a5 15.f5 Bc4 16.Ng3 a4 17.Nc1 a3 18.b3 Bb5 19.Bd3 Bxd3 20.cxd3 Qa5 21.Kb1 Qa7 22.Nce2 Nc7 23.Nc4 Nc5 24.Nxc5 dxc5 25.Qe3 Qb6 26.Qxb6 Nxb6 27.Bxc5 Bxc5 28.Nxc5 Nc8 29.Ne6 fxe6 30.fxe6 Rf7 31.Rhf1 Rf6 32.exf7+ Kxf7 33.Rf5 Ke8 34.Rf8+ Kd7 35.Rf7+ Ke8 36.Rf8+ Kd7 37.Rf7+ Ke8 38.Rf8+ Kd7 39.Rf7+ Ke8 40.Rf8+ Kd7 41.Rf7+ Ke8 42.Rf8+ Kd7 43.Rf7+ Ke8 44.Rf8+ Kd7 45.Rf7+ Ke8 46.Rf8+ Kd7 47.Rf7+ Ke8 48.Rf8+ Kd7 49.Rf7+ Ke8 50.Rf8+ Kd7 51.Rf7+ Ke8 52.Rf8+ Kd7 53.Rf7+ Ke8 54.Rf8+ Kd7 55.Rf7+ Ke8 56.Rf8+ Kd7 57.Rf7+ Ke8 58.Rf8+ Kd7 59.Rf7+ Ke8 60.Rf8+ Kd7 61.Rf7+ Ke8 62.Rf8+ Kd7 63.Rf7+ Ke8 64.Rf8+ Kd7 65.Rf7+ Ke8 66.Rf8+ Kd7 67.Rf7+ Ke8 68.Rf8+ Kd7 69.Rf7+ Ke8 70.Rf8+ Kd7 71.Rf7+ Ke8 72.Rf8+ Kd7 73.Rf7+ Ke8 74.Rf8+ Kd7 75.Rf7+ Ke8 76.Rf8+ Kd7 77.Rf7+ Ke8 78.Rf8+ Kd7 79.Rf7+ Ke8 80.Rf8+ Kd7 81.Rf7+ Ke8 82.Rf8+ Kd7 83.Rf7+ Ke8 84.Rf8+ Kd7 85.Rf7+ Ke8 86.Rf8+ Kd7 87.Rf7+ Ke8 88.Rf8+ Kd7 89.Rf7+ Ke8 90.Rf8+ Kd7 91.Rf7+ Ke8 92.Rf8+ Kd7 93.Rf7+ Ke8 94.Rf8+ Kd7 95.Rf7+ Ke8 96.Rf8+ Kd7 97.Rf7+ Ke8 98.Rf8+ Kd7 99.Rf7+ Ke8 100.Rf8+ Kd7 1/2-1/2',
                'jugador_blancas_id' => '1234567890',
                'jugador_negras_id' => '5566778899',
                'resultado' => 1.0 // Victoria blancas
            ]
        ];

        foreach ($partidasData as $index => $partidaData) {
            $partida = Partida::create([
                'ronda' => 1,
                'ronda_torneo_id' => $torneo->id,
                'participante_id' => $participantes[$index % count($participantes)]->miembro_id,
                'torneo_id' => $torneo->id,
                'mesa' => $index + 1,
                'color' => true,
                'resultado' => $partidaData['resultado'],
                'movimientos' => $partidaData['movimientos']
            ]);
        }

        // Crear algunos análisis de prueba
        $analisisData = [
            [
                'partida_id' => 1,
                'movimientos' => $partidasData[0]['movimientos'],
                'jugador_blancas_id' => $partidasData[0]['jugador_blancas_id'],
                'jugador_negras_id' => $partidasData[0]['jugador_negras_id'],
                'evaluacion_general' => 'Partida muy larga y compleja con múltiples fases. Excelente lucha táctica que terminó en tablas por repetición.',
                'errores_blancas' => 3,
                'errores_negras' => 3,
                'brillantes_blancas' => 2,
                'brillantes_negras' => 1,
                'blunders_blancas' => 1,
                'blunders_negras' => 1
            ],
            [
                'partida_id' => 2,
                'movimientos' => $partidasData[1]['movimientos'],
                'jugador_blancas_id' => $partidasData[1]['jugador_blancas_id'],
                'jugador_negras_id' => $partidasData[1]['jugador_negras_id'],
                'evaluacion_general' => 'Partida de medio juego interesante con oportunidades para ambos jugadores. Terminó en tablas.',
                'errores_blancas' => 2,
                'errores_negras' => 2,
                'brillantes_blancas' => 1,
                'brillantes_negras' => 1,
                'blunders_blancas' => 0,
                'blunders_negras' => 0
            ]
        ];

        foreach ($analisisData as $analisis) {
            AnalisisPartida::create($analisis);
        }

        echo "Datos de prueba creados exitosamente:\n";
        echo "- Torneo: " . $torneo->nombre_torneo . "\n";
        echo "- Partidas con movimientos: " . Partida::whereNotNull('movimientos')->count() . "\n";
        echo "- Análisis creados: " . AnalisisPartida::count() . "\n";
    }
}
