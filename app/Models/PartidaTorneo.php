<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartidaTorneo extends Model
{
    protected $table = 'partidas_torneo';

    protected $fillable = [
        'ronda_id',
        'jugador_blancas_id',
        'jugador_negras_id',
        'resultado',
        'mesa',
        'notas'
    ];

    protected $casts = [
        'resultado' => 'integer',
        'mesa' => 'integer'
    ];

    public function ronda(): BelongsTo
    {
        return $this->belongsTo(RondaTorneo::class, 'ronda_id');
    }

    public function jugadorBlancas(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'jugador_blancas_id', 'cedula');
    }

    public function jugadorNegras(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'jugador_negras_id', 'cedula');
    }

    public function getResultadoTexto(): string
    {
        return match($this->resultado) {
            1 => '1-0',
            2 => '0-1',
            3 => '½-½',
            default => '*'
        };
    }
} 