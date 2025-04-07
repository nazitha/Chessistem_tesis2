<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaDesempate extends Model
{
    protected $table = 'sistemas_desempate';
    protected $primaryKey = 'id_desempate';
    public $timestamps = false;

    protected $fillable = ['sistema_desempate'];

    public function partidas()
    {
        return $this->hasMany(Partida::class, 'desempate_utilizado_id', 'id_desempate');
    }
} 