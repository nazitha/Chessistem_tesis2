<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipoTorneo extends Model
{
    protected $table = 'equipos_torneo';
    protected $fillable = [
        'torneo_id', 'nombre', 'capitan_id', 'elo_medio', 'federacion', 'logo', 'notas'
    ];

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }

    public function jugadores(): HasMany
    {
        return $this->hasMany(EquipoJugador::class, 'equipo_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(EquipoMatch::class, 'equipo_a_id');
    }

    public function capitan()
    {
        return $this->belongsTo(\App\Models\Miembro::class, 'capitan_id', 'cedula');
    }
} 