<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipoMatch extends Model
{
    protected $table = 'equipos_matches';
    protected $fillable = [
        'torneo_id', 'ronda', 'equipo_a_id', 'equipo_b_id', 'puntos_equipo_a', 'puntos_equipo_b', 'resultado_match', 'mesa'
    ];

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }

    public function equipoA(): BelongsTo
    {
        return $this->belongsTo(EquipoTorneo::class, 'equipo_a_id');
    }

    public function equipoB(): BelongsTo
    {
        return $this->belongsTo(EquipoTorneo::class, 'equipo_b_id');
    }

    public function partidas(): HasMany
    {
        return $this->hasMany(PartidaIndividual::class, 'equipo_match_id');
    }
} 