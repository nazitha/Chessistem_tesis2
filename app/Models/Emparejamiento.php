<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emparejamiento extends Model
{
    protected $table = 'sistemas_de_emparejamiento';
    public $timestamps = false;
    protected $primaryKey = 'id_emparejamiento';

    protected $fillable = [
        'sistema',
        'descripcion'
    ];

    public function torneos()
    {
        return $this->hasMany(Torneo::class, 'sistema_emparejamiento_id', 'id_emparejamiento');
    }
}