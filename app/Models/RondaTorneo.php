<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RondaTorneo extends Model
{
    protected $table = 'rondas_torneo';
<<<<<<< HEAD
=======
    protected $primaryKey = 'id';
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b

    protected $fillable = [
        'torneo_id',
        'numero_ronda',
        'fecha_hora',
        'completada'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'completada' => 'boolean'
    ];

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }

    public function partidas(): HasMany
    {
<<<<<<< HEAD
        return $this->hasMany(PartidaTorneo::class, 'ronda_id');
=======
        return $this->hasMany(PartidaTorneo::class, 'ronda_id')->orderBy('mesa');
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
    }
} 