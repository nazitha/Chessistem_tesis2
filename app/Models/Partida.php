<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $table = 'partidas';
    protected $primaryKey = 'no_partida';
    public $timestamps = false;

    protected $fillable = [
        'ronda',
        'ronda_torneo_id',
        'participante_id',
        'torneo_id',
        'mesa',
        'color',
        'tiempo',
        'desempate_utilizado_id',
        'estado_abandono',
        'resultado',
        'movimientos'
    ];

    protected $casts = [
        'color' => 'boolean',
        'estado_abandono' => 'boolean',
        'resultado' => 'double',
        'tiempo' => 'datetime'
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class, 'torneo_id', 'id_torneo');
    }

    public function participante()
    {
        return $this->belongsTo(Miembro::class, 'participante_id', 'cedula');
    }

    public function sistemaDesempate()
    {
        return $this->belongsTo(SistemaDesempate::class, 'desempate_utilizado_id', 'id_sistema_desempate');
    }
}