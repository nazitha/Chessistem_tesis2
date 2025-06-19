<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditorias';
    public $timestamps = false;

    protected $fillable = [
        'correo_id',
        'tabla_afectada',
        'accion',
        'valor_previo',
        'valor_posterior',
        'fecha',
        'hora',
        'equipo'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'string'
    ];

    protected $appends = ['tiempo'];

    public function getTiempoAttribute()
    {
        return $this->fecha->format('Y-m-d') . ', ' . date('h:i:s A', strtotime($this->hora));
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'correo_id', 'correo');
    }
}