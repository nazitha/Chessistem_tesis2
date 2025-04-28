<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartidaIndividual extends Model
{
    protected $table = 'partidas_individuales';
    protected $fillable = [
        'equipo_match_id', 'jugador_a_id', 'jugador_b_id', 'tablero', 'resultado'
    ];

    public function equipoMatch(): BelongsTo
    {
        return $this->belongsTo(EquipoMatch::class, 'equipo_match_id');
    }

    public function jugadorA(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'jugador_a_id', 'cedula');
    }

    public function jugadorB(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'jugador_b_id', 'cedula');
    }
} 