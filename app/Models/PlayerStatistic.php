<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStatistic extends Model
{
    protected $fillable = [
        'participante_id',
        'torneo_id',
        'partidas_jugadas',
        'partidas_blancas',
        'partidas_negras',
        'byes_recibidos',
        'flotamientos',
        'porcentaje_blancas',
        'porcentaje_negras',
        'porcentaje_emparejamientos_repetidos'
    ];

    protected $casts = [
        'porcentaje_blancas' => 'float',
        'porcentaje_negras' => 'float',
        'porcentaje_emparejamientos_repetidos' => 'float'
    ];

    public function participante(): BelongsTo
    {
        return $this->belongsTo(ParticipanteTorneo::class);
    }

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class);
    }

    public function actualizarEstadisticas(): void
    {
        $this->partidas_jugadas = $this->partidas_blancas + $this->partidas_negras;
        
        if ($this->partidas_jugadas > 0) {
            $this->porcentaje_blancas = ($this->partidas_blancas / $this->partidas_jugadas) * 100;
            $this->porcentaje_negras = ($this->partidas_negras / $this->partidas_jugadas) * 100;
        }

        $this->save();
    }
} 