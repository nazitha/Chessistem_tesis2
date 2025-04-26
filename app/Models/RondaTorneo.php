<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RondaTorneo extends Model
{
    protected $table = 'rondas_torneo';

    protected $fillable = [
        'torneo_id',
        'numero_ronda',
        'fecha_hora',
        'completada'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'completada' => 'boolean'
    ];

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }

    public function partidas(): HasMany
    {
        return $this->hasMany(PartidaTorneo::class, 'ronda_id');
    }
} 