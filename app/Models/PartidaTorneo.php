<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RondaTorneo;
use App\Models\Miembro;
use Illuminate\Support\Facades\Log;

class PartidaTorneo extends Model
{
    protected $table = 'partidas_torneo';
    protected $primaryKey = 'id';

    protected $fillable = [
        'ronda_id',
        'jugador_blancas_id',
        'jugador_negras_id',
        'resultado',
        'mesa',
        'notas'
    ];

    protected $casts = [
        'resultado' => 'integer',
        'mesa' => 'integer'
    ];

    protected $with = ['jugadorBlancas', 'jugadorNegras'];

    public function ronda(): BelongsTo
    {
        return $this->belongsTo(RondaTorneo::class, 'ronda_id');
    }

    public function jugadorBlancas(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'jugador_blancas_id', 'cedula');
    }

    public function jugadorNegras(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'jugador_negras_id', 'cedula');
    }

    public function getResultadoTexto()
    {
        if ($this->resultado === null) {
            return '*';
        }

        if (!$this->jugador_negras_id) {
            return '+'; // BYE
        }

        switch ($this->resultado) {
            case 1:
                return '1-0'; // Victoria blancas
            case 2:
                return '0-1'; // Victoria negras
            case 3:
                return '½-½'; // Tablas
            default:
                return '*';
        }
    }

    public function setResultadoFromTexto($texto, $esEliminacionDirecta = false)
    {
        $texto = trim($texto);
        Log::info('Procesando resultado texto: ' . $texto);
        
        // Normalizar formatos alternativos
        $texto = str_replace(['1/2', '0.5', '½'], '0.5', $texto);
        
        // Remover espacios entre caracteres
        $texto = str_replace(' ', '', $texto);
        
        if ($esEliminacionDirecta && in_array($texto, ['0.5-0.5', '0.5', '=', '½'])) {
            throw new \InvalidArgumentException('No se permiten empates en eliminación directa. Debe haber un ganador.');
        }

        switch ($texto) {
            case '1-0':
            case '10':
            case '1':
                $this->resultado = 1;
                break;
            case '0-1':
            case '01':
            case '0':
                $this->resultado = 0;
                break;
            case '0.5-0.5':
            case '0.5':
            case '=':
                $this->resultado = 0.5;
                break;
            case '':
            case '*':
                $this->resultado = null;
                break;
            default:
                Log::error('Formato de resultado inválido: ' . $texto);
                throw new \InvalidArgumentException('Formato de resultado inválido. Use 1-0, 0-1 o deje vacío.');
        }
        
        Log::info('Resultado establecido a: ' . $this->resultado);
    }
} 