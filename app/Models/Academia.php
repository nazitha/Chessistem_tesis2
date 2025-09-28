<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id_academia';
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id_academia', $value)->firstOrFail();
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

    /**
     * Get all members (participantes) belonging to this academy
     */
    public function miembros(): HasMany
    {
        return $this->hasMany(Miembro::class, 'academia_id', 'id_academia');
    }

    /**
     * Get count of registered participants in this academy
     */
    public function getParticipantesRegistradosAttribute()
    {
        return $this->miembros()->count();
    }

    /**
     * Get count of tournaments where this academy has participated
     */
    public function getTorneosParticipadosAttribute()
    {
        // Get all members from this academy
        $miembrosIds = $this->miembros()->pluck('cedula');
        
        // Count unique tournaments where these members have participated
        return ParticipanteTorneo::whereIn('miembro_id', $miembrosIds)
            ->distinct('torneo_id')
            ->count();
    }
}