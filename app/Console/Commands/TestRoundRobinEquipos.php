<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;
use App\Models\EquipoTorneo;
use App\Models\EquipoMatch;
use App\Models\PartidaIndividual;

class TestRoundRobinEquipos extends Command
{
    protected $signature = 'test:roundrobin-equipos {torneo_id}';
    protected $description = 'Probar generación de Round Robin por equipos';

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
        $this->info("Es por equipos: " . ($torneo->es_por_equipos ? 'Sí' : 'No'));
        $this->info("Equipos registrados: " . $torneo->equipos()->count());
        $this->info("Rondas existentes: " . $torneo->rondas()->count());
        
        // Verificar que sea por equipos
        if (!$torneo->es_por_equipos) {
            $this->error("El torneo no está configurado como por equipos");
            return 1;
        }
        
        // Verificar sistema de emparejamiento
        $sistema = strtolower(trim($torneo->emparejamiento->sistema ?? 'round robin'));
        $this->info("Sistema detectado: {$sistema}");
        
        if (!str_contains($sistema, 'round robin')) {
            $this->error("El sistema no es 'round robin', es: {$sistema}");
            return 1;
        }
        
        // Mostrar equipos y sus jugadores
        $this->info("\n=== EQUIPOS REGISTRADOS ===");
        $equipos = $torneo->equipos()->with('jugadores.miembro')->get();
        foreach ($equipos as $equipo) {
            $this->info("Equipo: {$equipo->nombre}");
            foreach ($equipo->jugadores as $jugador) {
                $this->line("  Tablero {$jugador->tablero}: {$jugador->miembro->nombres} {$jugador->miembro->apellidos}");
            }
        }
        
        // Verificar si ya existen rondas
        if ($torneo->rondas()->count() > 0) {
            $this->warn("El torneo ya tiene rondas generadas. Se eliminarán para la prueba.");
            $torneo->rondas()->delete();
        }
        
        // Probar generación
        $this->info("\n=== GENERANDO ROUND ROBIN ===");
        try {
            $controller = new \App\Http\Controllers\TorneoRondaController();
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('generarRoundRobinEquipos');
            $method->setAccessible(true);
            $method->invoke($controller, $torneo);
            
            $this->info("Round Robin generado exitosamente!");
            
            // Mostrar resultados
            $this->info("\n=== RESULTADOS GENERADOS ===");
            $rondas = $torneo->rondas()->orderBy('numero_ronda')->get();
            $this->info("Rondas generadas: {$rondas->count()}");
            
            foreach ($rondas as $ronda) {
                $this->info("\nRonda {$ronda->numero_ronda}:");
                $matches = EquipoMatch::where('torneo_id', $torneo->id)
                    ->where('ronda', $ronda->numero_ronda)
                    ->with(['equipoA', 'equipoB'])
                    ->orderBy('mesa')
                    ->get();
                
                foreach ($matches as $match) {
                    $equipoA = $match->equipoA ? $match->equipoA->nombre : 'BYE';
                    $equipoB = $match->equipoB ? $match->equipoB->nombre : 'BYE';
                    $this->line("  Mesa {$match->mesa}: {$equipoA} vs {$equipoB}");
                    
                    // Mostrar partidas individuales
                    $partidas = $match->partidas()->with(['jugadorA', 'jugadorB'])->orderBy('tablero')->get();
                    foreach ($partidas as $partida) {
                        $jugadorA = $partida->jugadorA ? $partida->jugadorA->nombres : 'BYE';
                        $jugadorB = $partida->jugadorB ? $partida->jugadorB->nombres : 'BYE';
                        $this->line("    Tablero {$partida->tablero}: {$jugadorA} vs {$jugadorB}");
                    }
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Error al generar Round Robin: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
} 