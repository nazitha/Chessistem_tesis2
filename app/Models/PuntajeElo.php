<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntajeElo extends Model
{
    protected $table = 'puntajes_elo';
    public $timestamps = false;

    protected $fillable = [
        'fide_id_miembro',
        'no_categoria_elo',
        'elo'
    ];

    public function fide()
    {
        return $this->belongsTo(Fide::class, 'fide_id_miembro', 'fide_id');
    }

    public function categoria()
    {
        return $this->belongsTo(EloCategoria::class, 'no_categoria_elo', 'no_elo');
    }
}