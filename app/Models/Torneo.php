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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Torneo extends Model
{
    use HasFactory;

    protected $table = 'torneos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'categoriaTorneo_id',
        'organizador_id',
        'control_tiempo_id',
        'director_torneo_id',
        'arbitro_principal_id',
        'arbitro_id',
        'arbitro_adjunto_id',
        'federacion_id',
        'sistema_emparejamiento_id',
        'nombre_torneo',
        'fecha_inicio',
        'hora_inicio',
        'lugar',
        'no_rondas',
        'estado_torneo',
        'torneo_cancelado',
        'motivo_cancelacion',
        'usar_buchholz',
        'usar_sonneborn_berger',
        'usar_desempate_progresivo',
        'numero_minimo_participantes',
        'permitir_bye',
        'alternar_colores',
        'evitar_emparejamientos_repetidos',
        'maximo_emparejamientos_repetidos',
        'es_por_equipos',
        'max_byes_por_jugador',
        'diferencia_maxima_puntos'
    ];

    protected $casts = [
        'estado_torneo' => 'boolean',
        'torneo_cancelado' => 'boolean',
        'usar_buchholz' => 'boolean',
        'usar_sonneborn_berger' => 'boolean',
        'usar_desempate_progresivo' => 'boolean',
        'permitir_bye' => 'boolean',
        'alternar_colores' => 'boolean',
        'evitar_emparejamientos_repetidos' => 'boolean',
        'es_por_equipos' => 'boolean',
        'fecha_inicio' => 'date',
        'hora_inicio' => 'datetime'
    ];

    protected $attributes = [
        'estado_torneo' => true,
        'torneo_cancelado' => false,
        'usar_buchholz' => false,
        'usar_sonneborn_berger' => false,
        'usar_desempate_progresivo' => false,
        'numero_minimo_participantes' => 4,
        'permitir_bye' => true,
        'alternar_colores' => true,
        'evitar_emparejamientos_repetidos' => true,
        'maximo_emparejamientos_repetidos' => 1,
        'es_por_equipos' => false,
        'max_byes_por_jugador' => 1,
        'diferencia_maxima_puntos' => 2
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($torneo) {
            // Verificar si la fecha del torneo ya pasó
            if ($torneo->fecha_inicio && $torneo->fecha_inicio->startOfDay()->isPast()) {
                $torneo->estado_torneo = false;
            }
        });
    }

    // Mutator para hora_inicio
    public function setHoraInicioAttribute($value)
    {
        $this->attributes['hora_inicio'] = $value ? date('H:i:s', strtotime($value)) : null;
    }

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
        if ($this->torneo_cancelado) {
            return 'Cancelado';
        }
        
        if ($this->fecha_inicio && $this->fecha_inicio->startOfDay()->isPast()) {
            return 'Finalizado';
        }
        
        return $this->estado_torneo ? 'Activo' : 'Inactivo';
    }

    public function getEstadoClaseAttribute(): string
    {
        if ($this->torneo_cancelado) {
            return 'bg-red-100 text-red-800';
        }
        
        if ($this->fecha_inicio && $this->fecha_inicio->startOfDay()->isPast()) {
            return 'bg-gray-100 text-gray-800';
        }
        
        return $this->estado_torneo ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
    }

    public function participantes()
    {
        return $this->hasMany(ParticipanteTorneo::class, 'torneo_id');
    }

    public function rondas()
    {
        return $this->hasMany(RondaTorneo::class, 'torneo_id')->orderBy('numero_ronda');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaTorneo::class, 'categoriaTorneo_id', 'id_torneo_categoria');
    }

    public function organizador()
    {
        return $this->belongsTo(Miembro::class, 'organizador_id', 'cedula');
    }

    public function directorTorneo()
    {
        return $this->belongsTo(Miembro::class, 'director_torneo_id', 'cedula');
    }

    public function arbitroPrincipal()
    {
        return $this->belongsTo(Miembro::class, 'arbitro_principal_id', 'cedula');
    }

    public function arbitro()
    {
        return $this->belongsTo(Miembro::class, 'arbitro_id', 'cedula');
    }

    public function arbitroAdjunto()
    {
        return $this->belongsTo(Miembro::class, 'arbitro_adjunto_id', 'cedula');
    }

    public function controlTiempo()
    {
        return $this->belongsTo(ControlTiempo::class, 'control_tiempo_id', 'id_control_tiempo');
    }

    public function emparejamiento()
    {
        return $this->belongsTo(Emparejamiento::class, 'sistema_emparejamiento_id', 'id_emparejamiento');
    }

    public function federacion()
    {
        return $this->belongsTo(Federacion::class, 'federacion_id', 'acronimo');
    }

    public function getTipoTorneoAttribute()
    {
        if (!$this->sistema_emparejamiento_id) {
            return null;
        }
        
        $sistema = $this->emparejamiento;
        if (!$sistema) {
            return null;
        }
        
        return trim($sistema->sistema);
    }

    public function getMiembrosDisponiblesAttribute()
    {
        $participantesIds = $this->participantes()->pluck('miembro_id');
        return Miembro::where('estado_miembro', true)
            ->whereNotIn('cedula', $participantesIds)
            ->get();
    }

    public function equipos()
    {
        return $this->hasMany(EquipoTorneo::class, 'torneo_id');
    }

    public function equipoMatches()
    {
        return $this->hasMany(EquipoMatch::class, 'torneo_id');
    }

    public function partidas()
    {
        return $this->hasManyThrough(
            PartidaTorneo::class,
            RondaTorneo::class,
            'torneo_id',    // Foreign key on RondaTorneo table
            'ronda_id',     // Foreign key on PartidaTorneo table
            'id',           // Local key on Torneo table
            'id'            // Local key on RondaTorneo table
        );
    }
}