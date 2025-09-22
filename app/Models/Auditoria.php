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

    protected $appends = ['tiempo', 'valor_previo_formateado', 'valor_posterior_formateado'];

    public function getTiempoAttribute()
    {
        return $this->fecha->format('Y-m-d') . ', ' . date('h:i:s A', strtotime($this->hora));
    }

    public function getValorPrevioFormateadoAttribute()
    {
        return $this->formatearValorJson($this->valor_previo);
    }

    public function getValorPosteriorFormateadoAttribute()
    {
        return $this->formatearValorJson($this->valor_posterior);
    }

    private function formatearValorJson($valor)
    {
        if (!$valor || $valor === '-' || $valor === '[-]') {
            return $valor;
        }

        $decoded = json_decode($valor, true);
        if ($decoded) {
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $valor;
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'correo_id', 'correo');
    }
}