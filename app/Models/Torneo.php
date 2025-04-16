<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CategoriaTorneo;
use App\Models\Miembro;
use App\Models\ControlTiempo;
use App\Models\Federacion;
use App\Models\Emparejamiento;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Torneo extends Model
{
    use HasFactory;

    protected $table = 'torneos';
    public $timestamps = false;

    protected $fillable = [
        'categoria_torneo_id',
        'miembro_id',
        'control_tiempo_id',
        'federacion_id',
        'emparejamiento_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'estado',
        'descripcion',
        'rondas',
        'ritmo_juego',
        'tiempo_espera',
        'tipo_torneo',
        'elo_minimo',
        'elo_maximo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'hora_inicio' => 'datetime',
        'estado_torneo' => 'boolean'
    ];

    public function scopeWithRelations($query)
    {
        return $query->with([
            'categoria',
            'organizador',
            'controlTiempo',
            'directorTorneo',
            'arbitroPrincipal',
            'arbitro',
            'arbitroAdjunto',
            'federacion',
            'emparejamiento'
        ]);
    }

    // Accesores
    public function getFechaFormateadaAttribute(): string
    {
       return $this->fecha_inicio->format('Y-m-d') . ', ' . $this->hora_inicio;
    }

    public function getEstadoAttribute(): string
    {
        return $this->estado_torneo ? 'Activo' : 'Finalizado';
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaTorneo::class, 'categoria_torneo_id', 'id_torneo_categoria');
    }

    public function emparejamiento(): BelongsTo
    {
        return $this->belongsTo(Emparejamiento::class);
    }

    public function organizador()
    {
        return $this->belongsTo(Miembro::class, 'miembro_id', 'cedula');
    }

    public function directorTorneo()
    {
        return $this->belongsTo(Miembro::class, 'director_torneo_id', 'cedula');
    }

    public function director()
    {
        return $this->directorTorneo();
    }

    public function arbitro()
    {
        return $this->belongsTo(Miembro::class, 'arbitro_id', 'cedula');
    }

    public function arbitroPrincipal()
    {
        return $this->belongsTo(Miembro::class, 'arbitro_principal_id', 'cedula');
    }

    public function arbitroAdjunto()
    {
        return $this->belongsTo(Miembro::class, 'arbitro_adjunto_id', 'cedula');
    }

    public function scopeActivo($query)
    {
        return $query->where('estado_torneo', true);
    }
    
    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    public function controlTiempo()
    {
        return $this->belongsTo(ControlTiempo::class, 'control_tiempo_id', 'id_control_tiempo');
    }

    public function federacion()
    {
        return $this->belongsTo(Federacion::class, 'federacion_id', 'acronimo');
    }

}