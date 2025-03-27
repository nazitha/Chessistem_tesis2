<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    protected $table = 'participantes';
    public $timestamps = false;

    protected $fillable = ['participante_id', 'torneo_id'];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class, 'torneo_id', 'id_torneo');
    }

    public function miembro()
    {
        return $this->belongsTo(Miembro::class, 'participante_id', 'cedula');
    }
}