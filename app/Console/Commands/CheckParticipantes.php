<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ParticipanteTorneo;

class CheckParticipantes extends Command
{
    protected $signature = 'check:participantes {torneo_id}';
    protected $description = 'Verificar participantes de un torneo';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        
        $participantes = ParticipanteTorneo::where('torneo_id', $torneoId)->get();
        
        $this->info("Participantes del torneo {$torneoId}:");
        $this->table(
            ['ID', 'Miembro ID', 'Activo', 'Puntos'],
            $participantes->map(function($p) {
                return [
                    $p->id,
                    $p->miembro_id,
                    $p->activo ? 'Sí' : 'No',
                    $p->puntos
                ];
            })
        );
        
        $activos = $participantes->where('activo', true)->count();
        $this->info("Total participantes activos: {$activos}");
        
        return 0;
    }
} 