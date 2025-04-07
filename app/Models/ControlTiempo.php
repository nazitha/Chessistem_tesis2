<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlTiempo extends Model
{
    protected $table = 'controles_tiempo';
    protected $primaryKey = 'id_control_tiempo';
    public $timestamps = false;

    protected $fillable = [
        'formato',
        'control_tiempo',
        'descrip_control_tiempo'
    ];

    public function categoriasTorneo()
    {
        return $this->belongsToMany(
            CategoriaTorneo::class,
            'control_tiempo_torneos',
            'control_tiempo_id',
            'categorias_torneo_id'
        );
    }
} 