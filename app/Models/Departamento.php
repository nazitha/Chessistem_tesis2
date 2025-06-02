<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $table = 'departamentos';
    protected $primaryKey = 'id_depto';
    public $timestamps = false;

    protected $fillable = [
        'pais_id',
        'nombre_depto'
    ];

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_id', 'id_pais');
    }

    public function ciudades(): HasMany
    {
        return $this->hasMany(Ciudad::class, 'depto_id', 'id_depto');
    }

    // Scope para búsqueda por nombre y país
    public function scopePorNombreYPais($query, $nombre, $paisId)
    {
        return $query->where('nombre_depto', $nombre)
                    ->where('pais_id', $paisId);
    }
}