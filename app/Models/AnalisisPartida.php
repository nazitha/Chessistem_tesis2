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

    // Método para obtener el nombre del jugador o placeholder
    public function getJugadorBlancasNombreAttribute()
    {
        if ($this->jugador_blancas_id === 'PGN_MANUAL') {
            return 'PGN Manual';
        }
        
        // Si es un nombre personalizado (no es un ID de miembro)
        if (!is_numeric($this->jugador_blancas_id) && $this->jugador_blancas_id !== 'PGN_MANUAL') {
            return $this->jugador_blancas_id;
        }
        
        // Si es un ID de miembro, buscar en la tabla miembros
        return $this->jugadorBlancas->nombres ?? 'Desconocido';
    }

    public function getJugadorNegrasNombreAttribute()
    {
        if ($this->jugador_negras_id === 'PGN_MANUAL') {
            return 'PGN Manual';
        }
        
        // Si es un nombre personalizado (no es un ID de miembro)
        if (!is_numeric($this->jugador_negras_id) && $this->jugador_negras_id !== 'PGN_MANUAL') {
            return $this->jugador_negras_id;
        }
        
        // Si es un ID de miembro, buscar en la tabla miembros
        return $this->jugadorNegras->nombres ?? 'Desconocido';
    }
} 