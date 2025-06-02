<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Academia extends Model
{
    protected $table = 'academias';
    
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

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function departamento()
    {
        return $this->throughCiudad()->hasDepartamento();
    }
    
    protected function throughCiudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function scopeActive($query)
    {
    return $query->where('estado_academia', true);
    }
}