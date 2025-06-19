<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Academia extends Model
{
    protected $table = 'academias';
    
    protected $primaryKey = 'id_academia';

    public $timestamps = false;
    
    protected $fillable = [
        'nombre_academia',
        'correo_academia',
        'telefono_academia',
        'representante_academia',
        'direccion_academia',
        'ciudad_id',
        'estado_academia'
    ];

    protected $casts = [
        'estado_academia' => 'boolean'
    ];

    public function getRouteKeyName()
    {
        return 'id_academia';
    }

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id', 'id_ciudad');
    }

    public function departamento()
    {
        return $this->ciudad->departamento();
    }

    public function pais()
    {
        return $this->ciudad->departamento->pais();
    }

    public function scopeActive($query)
    {
        return $query->where('estado_academia', true);
    }
}