<?php

namespace App\Services;

use App\Models\Torneo;
<<<<<<< HEAD
use App\Models\Participante;
use App\Models\Partida;
use Illuminate\Support\Collection;

class SwissPairingService
{
    private Torneo $torneo;
    private Collection $participantes;
    private Collection $partidasAnteriores;
=======
use App\Models\RondaTorneo;
use App\Models\ParticipanteTorneo;
use App\Models\PartidaTorneo;
use Illuminate\Support\Collection;
use App\Traits\PairingLogs;
use App\Traits\PairingValidations;

class SwissPairingService
{
    use PairingLogs, PairingValidations;

    private Torneo $torneo;
    private Collection $participantes;
    private Collection $partidasAnteriores;
    private RondaTorneo $ronda;
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b

    public function __construct(Torneo $torneo)
    {
        $this->torneo = $torneo;
<<<<<<< HEAD
        $this->participantes = $torneo->participantes()->with('miembro')->get();
        $this->partidasAnteriores = $torneo->partidas()->get();
    }

    public function generarEmparejamientos(int $ronda): array
    {
        // Ordenar participantes por puntos
        $participantesOrdenados = $this->participantes
            ->sortByDesc('puntos')
            ->values();

        $emparejamientos = [];
        $participantesEmparejados = collect();

        foreach ($participantesOrdenados as $participante) {
            if ($participantesEmparejados->contains($participante->id)) {
                continue;
            }

            // Buscar oponente
            $oponente = $this->buscarOponente(
                $participante,
                $participantesOrdenados,
                $participantesEmparejados,
                $ronda
            );

            if ($oponente) {
                $emparejamientos[] = [
                    'participante1' => $participante,
                    'participante2' => $oponente,
                    'mesa' => count($emparejamientos) + 1,
                    'ronda' => $ronda
                ];
                $participantesEmparejados->push($participante->id, $oponente->id);
            } elseif ($this->torneo->permitir_bye) {
                // Asignar bye si está permitido
                $emparejamientos[] = [
                    'participante1' => $participante,
                    'participante2' => null,
                    'mesa' => count($emparejamientos) + 1,
                    'ronda' => $ronda,
                    'bye' => true
                ];
                $participantesEmparejados->push($participante->id);
=======
        $this->participantes = $torneo->participantes()
            ->with('miembro')
            ->where('activo', true)
            ->get();
        $this->partidasAnteriores = collect();
        
        foreach ($torneo->rondas as $ronda) {
            $this->partidasAnteriores = $this->partidasAnteriores->merge($ronda->partidas);
        }
    }

    public function generarEmparejamientos(RondaTorneo $ronda): array
    {
        $this->ronda = $ronda;
        $emparejamientos = [];
        $participantesDisponibles = $this->participantes->sortByDesc('puntos');

        while ($participantesDisponibles->count() > 0) {
            $jugadorA = $participantesDisponibles->first();
            $oponenteEncontrado = false;

            foreach ($participantesDisponibles->skip(1) as $jugadorB) {
                if ($this->esOponenteValido($jugadorA, $jugadorB)) {
                    $colores = $this->determinarColores($jugadorA, $jugadorB);
                    
                    $emparejamientos[] = [
                        'blancas' => $colores['blancas'],
                        'negras' => $colores['negras']
                    ];

                    $this->logEmparejamiento(
                        $this->torneo,
                        $this->ronda,
                        $jugadorA,
                        'Emparejamiento normal',
                        ['oponente' => $jugadorB->id, 'color' => $colores['blancas']->id === $jugadorA->id ? 'blancas' : 'negras']
                    );

                    $this->logEmparejamiento(
                        $this->torneo,
                        $this->ronda,
                        $jugadorB,
                        'Emparejamiento normal',
                        ['oponente' => $jugadorA->id, 'color' => $colores['blancas']->id === $jugadorB->id ? 'blancas' : 'negras']
                    );

                    $participantesDisponibles = $participantesDisponibles->filter(function($p) use ($jugadorA, $jugadorB) {
                        return $p->id !== $jugadorA->id && $p->id !== $jugadorB->id;
                    });

                    $oponenteEncontrado = true;
                    break;
                }
            }

            if (!$oponenteEncontrado) {
                if ($this->validarByeRepetido($jugadorA)) {
                    $emparejamientos[] = [
                        'blancas' => $jugadorA,
                        'negras' => null
                    ];

                    $this->logBye(
                        $this->torneo,
                        $this->ronda,
                        $jugadorA,
                        'Asignación de bye',
                        ['rondas_anteriores' => $this->conteoByes($jugadorA)]
                    );

                    $participantesDisponibles = $participantesDisponibles->filter(function($p) use ($jugadorA) {
                        return $p->id !== $jugadorA->id;
                    });
                } else {
                    $this->logFlotamiento(
                        $this->torneo,
                        $this->ronda,
                        $jugadorA,
                        'No se encontró oponente válido',
                        ['puntos' => $jugadorA->puntos]
                    );

                    $participantesDisponibles = $participantesDisponibles->filter(function($p) use ($jugadorA) {
                        return $p->id !== $jugadorA->id;
                    });
                }
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
            }
        }

        return $emparejamientos;
    }

<<<<<<< HEAD
    private function buscarOponente(
        Participante $participante,
        Collection $participantesOrdenados,
        Collection $participantesEmparejados,
        int $ronda
    ): ?Participante {
        $diferenciaPuntosMaxima = 1;
        $intentos = 0;
        $maxIntentos = 3;

        while ($intentos < $maxIntentos) {
            foreach ($participantesOrdenados as $posibleOponente) {
                if ($this->esOponenteValido($participante, $posibleOponente, $participantesEmparejados, $ronda, $diferenciaPuntosMaxima)) {
                    return $posibleOponente;
                }
            }
            $diferenciaPuntosMaxima++;
            $intentos++;
        }

        return null;
    }

    private function esOponenteValido(
        Participante $participante,
        Participante $oponente,
        Collection $participantesEmparejados,
        int $ronda,
        int $diferenciaPuntosMaxima
    ): bool {
        // No emparejar consigo mismo
        if ($participante->id === $oponente->id) {
            return false;
        }

        // Verificar si ya está emparejado
        if ($participantesEmparejados->contains($oponente->id)) {
            return false;
        }

        // Verificar diferencia de puntos
        if (abs($participante->puntos - $oponente->puntos) > $diferenciaPuntosMaxima) {
            return false;
        }

        // Verificar emparejamientos anteriores si está configurado
        if ($this->torneo->evitar_emparejamientos_repetidos) {
            $emparejamientosAnteriores = $this->partidasAnteriores
                ->where('ronda', '<', $ronda)
                ->filter(function ($partida) use ($participante, $oponente) {
                    return ($partida->participante_id === $participante->miembro_id && 
                            $partida->oponente_id === $oponente->miembro_id) ||
                           ($partida->participante_id === $oponente->miembro_id && 
                            $partida->oponente_id === $participante->miembro_id);
                });

            if ($emparejamientosAnteriores->count() >= $this->torneo->maximo_emparejamientos_repetidos) {
                return false;
            }
        }

        // Verificar colores si está configurado
        if ($this->torneo->alternar_colores) {
            $ultimaPartida = $this->partidasAnteriores
                ->where('participante_id', $participante->miembro_id)
                ->sortByDesc('ronda')
                ->first();

            if ($ultimaPartida && $ultimaPartida->color === true) {
                // El último color fue blancas, buscar oponente que jugó negras
                $ultimaPartidaOponente = $this->partidasAnteriores
                    ->where('participante_id', $oponente->miembro_id)
                    ->sortByDesc('ronda')
                    ->first();

                if ($ultimaPartidaOponente && $ultimaPartidaOponente->color === true) {
                    return false;
                }
            }
        }

        return true;
=======
    private function esOponenteValido($jugadorA, $jugadorB): bool
    {
        return !$this->yaSeEnfrentaron($jugadorA, $jugadorB) &&
               !$this->coloresDesequilibrados($jugadorA, $jugadorB) &&
               !$this->sonDelMismoEquipo($jugadorA, $jugadorB);
    }

    private function determinarColores($jugadorA, $jugadorB): array
    {
        $blancasA = $this->conteoBlancas($jugadorA);
        $negrasA = $this->conteoNegras($jugadorA);
        $blancasB = $this->conteoBlancas($jugadorB);
        $negrasB = $this->conteoNegras($jugadorB);

        if ($blancasA > $negrasA) {
            $this->logColor(
                $this->torneo,
                $this->ronda,
                $jugadorA,
                'negras',
                'Balance de colores',
                ['blancas' => $blancasA, 'negras' => $negrasA]
            );
            return ['blancas' => $jugadorB, 'negras' => $jugadorA];
        }

        if ($blancasB > $negrasB) {
            $this->logColor(
                $this->torneo,
                $this->ronda,
                $jugadorB,
                'negras',
                'Balance de colores',
                ['blancas' => $blancasB, 'negras' => $negrasB]
            );
            return ['blancas' => $jugadorA, 'negras' => $jugadorB];
        }

        $color = rand(0, 1) ? 'blancas' : 'negras';
        $this->logColor(
            $this->torneo,
            $this->ronda,
            $jugadorA,
            $color,
            'Asignación aleatoria',
            ['blancas' => $blancasA, 'negras' => $negrasA]
        );

        return $color === 'blancas' 
            ? ['blancas' => $jugadorA, 'negras' => $jugadorB]
            : ['blancas' => $jugadorB, 'negras' => $jugadorA];
    }

    private function moverJugadorFlotante($jugador, &$grupos)
    {
        $puntajeActual = $jugador->puntos;
        $indiceActual = array_search($puntajeActual, array_keys($grupos->toArray()));
        
        if ($indiceActual < count($grupos) - 1) {
            $puntajeSiguiente = array_keys($grupos->toArray())[$indiceActual + 1];
            $grupos[$puntajeSiguiente]->push($jugador);
            $grupos[$puntajeActual] = $grupos[$puntajeActual]->filter(fn($p) => $p->id !== $jugador->id);
        }
    }

    private function asignarBye($participante)
    {
        PartidaTorneo::create([
            'ronda_id' => $this->torneo->rondas()->latest()->first()->id,
            'jugador_blancas_id' => $participante->miembro_id,
            'jugador_negras_id' => null,
            'resultado' => 1, // Victoria por bye
            'mesa' => 0 // Mesa especial para bye
        ]);
    }

    private function yaSeEnfrentaron($jugadorA, $jugadorB): bool
    {
        return $this->partidasAnteriores->contains(function($partida) use ($jugadorA, $jugadorB) {
            return ($partida->jugador_blancas_id === $jugadorA->miembro_id && 
                    $partida->jugador_negras_id === $jugadorB->miembro_id) ||
                   ($partida->jugador_blancas_id === $jugadorB->miembro_id && 
                    $partida->jugador_negras_id === $jugadorA->miembro_id);
        });
    }

    private function coloresDesequilibrados($jugadorA, $jugadorB): bool
    {
        $diferenciaA = abs($this->conteoBlancas($jugadorA) - $this->conteoNegras($jugadorA));
        $diferenciaB = abs($this->conteoBlancas($jugadorB) - $this->conteoNegras($jugadorB));
        
        return $diferenciaA > 2 || $diferenciaB > 2;
    }

    private function sonDelMismoEquipo($jugadorA, $jugadorB): bool
    {
        return $jugadorA->equipo_id && $jugadorB->equipo_id && 
               $jugadorA->equipo_id === $jugadorB->equipo_id;
    }

    private function conteoBlancas($participante): int
    {
        return $this->partidasAnteriores->where('jugador_blancas_id', $participante->miembro_id)->count();
    }

    private function conteoNegras($participante): int
    {
        return $this->partidasAnteriores->where('jugador_negras_id', $participante->miembro_id)->count();
    }

    private function conteoByes(ParticipanteTorneo $participante): int
    {
        return $this->partidasAnteriores
            ->where('jugador_blancas_id', $participante->id)
            ->whereNull('jugador_negras_id')
            ->count();
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
    }
} 