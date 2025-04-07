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

    protected $casts = [
        'estado_abandono' => 'boolean',
        'resultado' => 'float'
    ];

    public function participante()
    {
        return $this->belongsTo(Miembro::class, 'participante_id', 'cedula');
    }

    public function torneo()
    {
        return $this->belongsTo(Torneo::class, 'torneo_id', 'id_torneo');
    }

    public function sistemaDesempate()
    {
        return $this->belongsTo(SistemaDesempate::class, 'desempate_utilizado_id', 'id_desempate');
    }
}