<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
    protected $table = 'torneos';
    public $timestamps = false;

    protected $fillable = [
        'nombre_torneo',
        'fecha_inicio',
        'hora_inicio',
        'categoriaTorneo_id',
        'sistema_emparejamiento_id',
        'lugar',
        'no_rondas',
        'organizador_id',
        'director_torneo_id',
        'arbitro_id',
        'arbitro_principal_id',
        'arbitro_adjunto_id',
        'estado_torneo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'hora_inicio' => 'datetime',
        'estado_torneo' => 'boolean'
    ];

    public function scopeWithRelations($query)
    {
        return $query->with([
            'categoria',
            'organizador',
            'arbitro',
            'arbitroPrincipal',
            'arbitroAdjunto',
            'director',
            'controlTiempo',
            'sistemaEmparejamiento',
            'federacion'
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

    public function sistemaEmparejamiento()
    {
        return $this->belongsTo(Emparejamiento::class, 'sistema_emparejamiento_id', 'id_emparejamiento');
    }

    public function organizador()
    {
        return $this->belongsTo(Miembro::class, 'organizador_id', 'cedula');
    }

    public function directorTorneo()
    {
        return $this->belongsTo(Miembro::class, 'director_torneo_id', 'cedula');
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

}