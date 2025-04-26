<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    protected $table = 'miembros';
    protected $primaryKey = 'cedula';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'sexo',
        'fecha_nacimiento',
        'fecha_inscripcion',
        'estado_miembro',
        'correo_sistema_id'
    ];
    
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_inscripcion' => 'date',
        'estado_miembro' => 'boolean'
    ];
    
    public function getFormattedSexoAttribute(): string
    {
        return $this->sexo == 'M' ? 'Masculino' : 'Femenino';
    }
    
    public function getFormattedCiudadAttribute(): ?string
    {
        if (!$this->ciudad) return null;
        
        return "{$this->ciudad->nombre_ciudad}, " .
               ($this->ciudad->departamento->nombre_depto ?? '-') . " (" .
               ($this->ciudad->departamento->pais->nombre_pais ?? '-') . ")";
    }
    
    public function auditorias()
    {
        return $this->hasMany(Auditoria::class, 'correo_id', 'correo_sistema_id');
    }

    public function fide()
    {
        return $this->hasOne(Fide::class, 'cedula_ajedrecista_id', 'cedula');
    }

    public function torneosOrganizados()
    {
        return $this->hasMany(Torneo::class, 'organizador_id', 'cedula');
    }

    public function torneosDirigidos()
    {
        return $this->hasMany(Torneo::class, 'director_torneo_id', 'cedula');
    }

    public function torneosArbitrados()
    {
        return $this->hasMany(Torneo::class, 'arbitro_id', 'cedula');
    }

    public function participacionesTorneo()
    {
        return $this->hasMany(ParticipanteTorneo::class, 'miembro_id', 'cedula');
    }

    public function scopeWithUsuarioRol($query)
    {   
      return $query->with(['usuario.rol']);
    }

    public function scopeExcluirRol($query, $rolId)
    {
      return $query->whereHas('usuario', fn($q) => $q->where('rol_id', '!=', $rolId));
    }

}
