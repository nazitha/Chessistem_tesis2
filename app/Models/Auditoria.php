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

    protected $appends = ['tiempo'];

    public function getTiempoAttribute()
    {
        return $this->fecha . ', ' . date('h:i:s A', strtotime($this->hora));
    }

    public function miembro()
    {
        return $this->belongsTo(Miembro::class, 'correo_id', 'correo_sistema_id');
    }
}