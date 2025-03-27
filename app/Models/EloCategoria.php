<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EloCategoria extends Model
{
    protected $table = 'elo_categorias';
    protected $primaryKey = 'no_elo';
    public $timestamps = false;

    protected $fillable = [
        'categoria_elo'
    ];

    public function puntajes()
    {
        return $this->hasMany(PuntajeElo::class, 'no_categoria_elo', 'no_elo');
    }
}