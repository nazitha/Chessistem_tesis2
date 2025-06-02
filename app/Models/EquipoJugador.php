<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipoJugador extends Model
{
    protected $table = 'equipo_jugadores';
    protected $fillable = [
        'equipo_id', 'miembro_id', 'tablero'
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(EquipoTorneo::class, 'equipo_id');
    }

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'miembro_id', 'cedula');
    }
} 