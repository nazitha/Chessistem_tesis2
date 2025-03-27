<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fide extends Model
{
    protected $table = 'fides';
    protected $primaryKey = 'fide_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'fide_id',
        'cedula_ajedrecista_id',
        'fed_id',
        'titulo',
        'fide_estado'
    ];

    public function miembro()
    {
        return $this->belongsTo(Miembro::class, 'cedula_ajedrecista_id', 'cedula');
    }

    public function federacion()
    {
        return $this->belongsTo(Federacion::class, 'fed_id', 'acronimo');
    }

    public function puntajesElo()
    {
        return $this->hasMany(PuntajeElo::class, 'fide_id_miembro', 'fide_id');
    }

    // Accessors para los diferentes tipos de ELO
    public function getEloBlitzAttribute()
    {
        return $this->puntajesElo->where('no_categoria_elo', 3)->first()->elo ?? null;
    }

    public function getEloClasicoAttribute()
    {
        return $this->puntajesElo->where('no_categoria_elo', 1)->first()->elo ?? null;
    }

    public function getEloRapidoAttribute()
    {
        return $this->puntajesElo->where('no_categoria_elo', 2)->first()->elo ?? null;
    }
}