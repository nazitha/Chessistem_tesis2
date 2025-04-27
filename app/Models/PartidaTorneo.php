<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartidaTorneo extends Model
{
    protected $table = 'partidas_torneo';
    protected $primaryKey = 'id';

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

    public function getResultadoTexto()
    {
        if ($this->resultado === null) {
            return '*';
        }

        if (!$this->jugador_negras_id) {
            return '+'; // BYE
        }

        switch ($this->resultado) {
            case 1:
                return '1-0';
            case 2:
                return '0-1';
            case 3:
                return '½-½';
            default:
                return '*';
        }
    }
} 