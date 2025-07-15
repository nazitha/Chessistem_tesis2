<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;
use App\Models\Emparejamiento;
use Illuminate\Support\Facades\DB;

class DiagnosticoTorneos extends Command
{
    protected $signature = 'diagnostico:torneos {torneo_id?}';
    protected $description = 'Diagnosticar configuración de todos los torneos';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        if ($torneoId) {
            $torneo = \App\Models\Torneo::with(['emparejamiento', 'equipos'])->find($torneoId);
            if (!$torneo) {
                $this->error("Torneo $torneoId no encontrado");
                return;
            }
            $this->info('=== DIAGNÓSTICO DE TORNEO ===');
            $this->line("ID: {$torneo->id}");
            $this->line("Nombre: {$torneo->nombre_torneo}");
            $this->line("Es por equipos: " . ($torneo->es_por_equipos ? 'Sí' : 'No'));
            $this->line("Número de rondas: {$torneo->no_rondas}");
            $this->line("Estado: {$torneo->estado}");
            $this->line("Sistema: " . ($torneo->emparejamiento ? $torneo->emparejamiento->sistema : 'No definido'));
            $this->line("Equipos registrados: " . $torneo->equipos->count());
            
            // Probar relación rondas
            $this->line("--- PRUEBA RELACIÓN RONDAS ---");
            $this->line("Rondas via relación: " . $torneo->rondas()->count());
            $this->line("Rondas en BD directo: " . DB::table('rondas_torneo')->where('torneo_id', $torneo->id)->count());
            
            // Mostrar detalles de las rondas
            $rondasBD = DB::table('rondas_torneo')->where('torneo_id', $torneo->id)->get();
            $this->line("Rondas en BD:");
            foreach ($rondasBD as $ronda) {
                $this->line("  - ID: {$ronda->id}, Número: {$ronda->numero_ronda}, Completada: " . ($ronda->completada ? 'Sí' : 'No'));
            }
            
            $this->line("Rondas existentes: " . $torneo->rondas()->count());
        } else {
            $this->info('=== DIAGNÓSTICO DE TORNEO ===');
            
            $torneos = Torneo::all();
            
            foreach ($torneos as $torneo) {
                $this->line("ID: {$torneo->id}");
                $this->line("Nombre: {$torneo->nombre_torneo}");
                $this->line("Es por equipos: " . ($torneo->es_por_equipos ? 'Sí' : 'No'));
                $this->line("Número de rondas: {$torneo->no_rondas}");
                $this->line("Estado: {$torneo->estado}");
                
                // Obtener sistema de emparejamiento
                $emparejamiento = Emparejamiento::find($torneo->sistema_emparejamiento_id);
                $sistema = $emparejamiento ? $emparejamiento->sistema : 'No definido';
                $this->line("Sistema: {$sistema}");
                
                // Contar equipos
                $equiposCount = $torneo->equipos()->count();
                $this->line("Equipos registrados: {$equiposCount}");
                
                // Contar rondas existentes
                $rondasCount = $torneo->rondas()->count();
                $this->line("Rondas existentes: {$rondasCount}");
                
                $this->line('---');
            }
        }
    }
} 