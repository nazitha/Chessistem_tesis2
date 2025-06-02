<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipanteTorneo extends Model
{
    protected $table = 'participantes_torneo';

    protected $fillable = [
        'torneo_id',
        'miembro_id',
<<<<<<< HEAD
=======
        'numero_inicial',
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
        'puntos',
        'posicion',
        'buchholz',
        'sonneborn_berger',
        'progresivo',
        'activo'
    ];

    protected $casts = [
        'puntos' => 'float',
        'buchholz' => 'float',
        'sonneborn_berger' => 'float',
        'progresivo' => 'float',
<<<<<<< HEAD
        'activo' => 'boolean'
=======
        'activo' => 'boolean',
        'numero_inicial' => 'integer'
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
    ];

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'miembro_id', 'cedula');
    }
} 