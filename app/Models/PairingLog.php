<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PairingLog extends Model
{
    protected $fillable = [
        'torneo_id',
        'ronda_id',
        'participante_id',
        'equipo_id',
        'tipo_decision',
        'motivo',
        'detalles'
    ];

    protected $casts = [
        'detalles' => 'array'
    ];

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class);
    }

    public function ronda(): BelongsTo
    {
        return $this->belongsTo(RondaTorneo::class);
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(ParticipanteTorneo::class);
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(EquipoTorneo::class);
    }
} 