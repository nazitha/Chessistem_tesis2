<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;
use App\Models\RondaTorneo;
use App\Services\SwissPairingService;

class TestSwiss extends Command
{
    protected $signature = 'test:swiss {torneo_id}';
    protected $description = 'Probar generación de emparejamientos suizos';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        
        $torneo = Torneo::find($torneoId);
        if (!$torneo) {
            $this->error("Torneo {$torneoId} no encontrado");
            return 1;
        }
        
        $this->info("=== DIAGNÓSTICO TORNEO ===");
        $this->info("Nombre: {$torneo->nombre_torneo}");
        $this->info("Sistema: " . ($torneo->emparejamiento->sistema ?? 'No definido'));
        $this->info("Es equipos: " . ($torneo->es_por_equipos ? 'Sí' : 'No'));
        $this->info("Participantes activos: " . $torneo->participantes()->where('activo', true)->count());
        $this->info("Rondas existentes: " . $torneo->rondas()->count());
        
        // Verificar sistema de emparejamiento
        $sistema = strtolower(trim($torneo->emparejamiento->sistema ?? 'suizo'));
        $this->info("Sistema detectado: {$sistema}");
        
        if (!str_contains($sistema, 'suizo')) {
            $this->error("El sistema no contiene 'suizo', es: {$sistema}");
            return 1;
        }
        
        // Crear ronda de prueba
        $this->info("\n=== CREANDO RONDA DE PRUEBA ===");
        $ronda = RondaTorneo::create([
            'torneo_id' => $torneo->id,
            'numero_ronda' => 1,
            'fecha_hora' => now()
        ]);
        
        $this->info("Ronda creada con ID: {$ronda->id}");
        
        // Probar SwissPairingService
        $this->info("\n=== PROBANDO SWISS PAIRING SERVICE ===");
        try {
            $service = new SwissPairingService($torneo);
            $emparejamientos = $service->generarEmparejamientos($ronda);
            
            $this->info("Emparejamientos generados: " . count($emparejamientos));
            
            foreach ($emparejamientos as $index => $emparejamiento) {
                $blancas = $emparejamiento['blancas']->miembro->nombres ?? 'BYE';
                $negras = $emparejamiento['negras'] ? $emparejamiento['negras']->miembro->nombres : 'BYE';
                $this->line("Mesa " . ($index + 1) . ": {$blancas} vs {$negras}");
            }
            
        } catch (\Exception $e) {
            $this->error("Error en SwissPairingService: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
        
        // Limpiar ronda de prueba
        $ronda->delete();
        $this->info("\nRonda de prueba eliminada");
        
        return 0;
    }
} 