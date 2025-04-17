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

class Torneo extends Model
{
    use HasFactory;

    protected $table = 'torneos';
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
        'usar_buchholz',
        'usar_sonneborn_berger',
        'usar_desempate_progresivo',
        'numero_minimo_participantes',
        'permitir_bye',
        'alternar_colores',
        'evitar_emparejamientos_repetidos',
        'maximo_emparejamientos_repetidos'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'estado_torneo' => 'boolean',
        'usar_buchholz' => 'boolean',
        'usar_sonneborn_berger' => 'boolean',
        'usar_desempate_progresivo' => 'boolean',
        'permitir_bye' => 'boolean',
        'evitar_emparejamientos_repetidos' => 'boolean',
        'alternar_colores' => 'boolean'
    ];

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
        return $this->estado_torneo ? 'Activo' : 'Finalizado';
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaTorneo::class, 'categoriaTorneo_id', 'id_torneo_categoria');
    }

    public function emparejamiento(): BelongsTo
    {
        return $this->belongsTo(Emparejamiento::class);
    }

    public function organizador()
    {
        return $this->belongsTo(Miembro::class, 'organizador_id', 'cedula');
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