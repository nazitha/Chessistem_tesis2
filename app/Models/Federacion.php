<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Federacion extends Model
{
    protected $table = 'federaciones';
    protected $primaryKey = 'acronimo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'acronimo',
        'nombre_federacion',
        'pais_id',
        'federacion_estado'
    ];

    protected $appends = ['estado'];

    public function getEstadoAttribute()
    {
        return $this->federacion_estado ? 'Activo' : 'Inactivo';
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id', 'id_pais');
    }
}
