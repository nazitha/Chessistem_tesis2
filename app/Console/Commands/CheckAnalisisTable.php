<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckAnalisisTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:analisis-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if analisis_partidas table exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking analisis_partidas table...');
        
        if (Schema::hasTable('analisis_partidas')) {
            $this->info('✅ Table analisis_partidas exists!');
            
            $columns = Schema::getColumnListing('analisis_partidas');
            $this->info('Columns: ' . implode(', ', $columns));
            
            $count = DB::table('analisis_partidas')->count();
            $this->info("Records count: $count");
            
            // Probar crear un análisis
            $this->info('Testing analysis creation...');
            try {
                $analisis = \App\Models\AnalisisPartida::create([
                    'partida_id' => null,
                    'movimientos' => '1.e4 e5 2.Nf3 Nc6 3.Bb5 a6 4.Ba4 Nf6 5.O-O Be7 6.Re1 b5 7.Bb3 d6 8.c3 O-O 9.h3 Nb8 10.d4 Nbd7 1/2-1/2',
                    'jugador_blancas_id' => 'Carlos Magno',
                    'jugador_negras_id' => 'Ana María',
                    'evaluacion_general' => 'Test evaluation',
                    'errores_blancas' => 2,
                    'errores_negras' => 2,
                    'brillantes_blancas' => 1,
                    'brillantes_negras' => 1,
                    'blunders_blancas' => 0,
                    'blunders_negras' => 0,
                ]);
                $this->info("✅ Test analysis created with ID: {$analisis->id}");
                
                // Probar los accessors
                $this->info("✅ Blancas: {$analisis->jugador_blancas_nombre}");
                $this->info("✅ Negras: {$analisis->jugador_negras_nombre}");
                
                // Limpiar el test
                $analisis->delete();
                $this->info('✅ Test analysis cleaned up');
            } catch (\Exception $e) {
                $this->error("❌ Error creating test analysis: " . $e->getMessage());
            }
            
            // Probar el método analizarMovimientos
            $this->info('Testing analizarMovimientos method...');
            try {
                $controller = new \App\Http\Controllers\AnalisisPartidaController();
                $reflection = new \ReflectionClass($controller);
                $method = $reflection->getMethod('analizarMovimientos');
                $method->setAccessible(true);
                
                $pgn = '1.e4 e5 2.Nf3 Nc6 3.Bb5 a6 4.Ba4 Nf6 5.O-O Be7 6.Re1 b5 7.Bb3 d6 8.c3 O-O 9.h3 Nb8 10.d4 Nbd7 1/2-1/2';
                $result = $method->invoke($controller, $pgn);
                
                $this->info('✅ analizarMovimientos result:');
                foreach ($result as $key => $value) {
                    $this->info("  $key: $value");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error testing analizarMovimientos: " . $e->getMessage());
            }
        } else {
            $this->error('❌ Table analisis_partidas does not exist!');
        }
        
        return 0;
    }
}
