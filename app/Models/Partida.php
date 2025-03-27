<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $table = 'partidas';
    public $timestamps = false;

    protected $fillable = [
        'participante_id',
        'torneo_id',
        'ronda',
        'mesa',
        'color',
        'tiempo',
        'desempate_utilizado_id',
        'estado_abandono',
        'resultado'
    ];

    public function participante()
    {
        return $this->belongsTo(Participante::class);
    }

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}