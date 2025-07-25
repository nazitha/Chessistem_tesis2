<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalisisPartida extends Model
{
    use HasFactory;

    protected $table = 'analisis_partidas';
    protected $fillable = [
        'partida_id',
        'movimientos',
        'jugador_blancas_id',
        'jugador_negras_id',
        'evaluacion_general',
        'errores_blancas',
        'errores_negras',
        'brillantes_blancas',
        'brillantes_negras',
        'blunders_blancas',
        'blunders_negras',
    ];

    public function partida()
    {
        return $this->belongsTo(Partida::class, 'partida_id');
    }

    public function jugadorBlancas()
    {
        return $this->belongsTo(Miembro::class, 'jugador_blancas_id');
    }

    public function jugadorNegras()
    {
        return $this->belongsTo(Miembro::class, 'jugador_negras_id');
    }
} 