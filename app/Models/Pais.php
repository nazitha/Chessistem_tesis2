<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pais extends Model
{
    protected $table = 'paises';
    protected $primaryKey = 'id_pais';
    public $timestamps = false;

    protected $fillable = [
        'nombre_pais'
    ];

    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class, 'pais_id', 'id_pais');
    }

    // Scope para búsqueda por nombre
    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre_pais', $nombre);
    }
}