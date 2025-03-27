<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ciudad extends Model
{
    protected $table = 'ciudades';
    protected $primaryKey = 'id_ciudad';
    public $timestamps = false;

    protected $fillable = [
        'depto_id',
        'nombre_ciudad'
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'depto_id', 'id_depto');
    }

    // Scope para búsqueda por nombre y departamento
    public function scopePorNombreYDepartamento($query, $nombre, $deptoId)
    {
        return $query->where('nombre_ciudad', $nombre)
                    ->where('depto_id', $deptoId);
    }
}