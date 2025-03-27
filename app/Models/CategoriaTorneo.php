<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaTorneo extends Model
{
    protected $table = 'categorias_torneo';
    protected $primaryKey = 'id_torneo_categoria';
    public $timestamps = false;

    protected $fillable = ['categoria_torneo'];

    public function torneos()
    {
        return $this->hasMany(Torneo::class, 'categoriaTorneo_id', 'id_torneo_categoria');
    }
}