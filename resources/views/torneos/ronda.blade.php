@extends('layouts.app')

@section('content')
<div class="container mx-auto py-4">
    <!-- Detalle del torneo -->
    <div class="bg-white shadow rounded-lg p-4 mb-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-blue-700 mb-1">{{ $torneo->nombre }}</h2>
                <div class="text-gray-600 text-sm">
                    <span class="mr-4"><b>Fecha:</b> {{ $torneo->fecha_inicio->format('d/m/Y') }}</span>
                    <span class="mr-4"><b>Lugar:</b> {{ $torneo->lugar }}</span>
                    <span class="mr-4"><b>Tipo:</b> {{ $torneo->es_por_equipos ? 'Por equipos' : 'Individual' }}</span>
                    <span class="mr-4"><b>Estado:</b> {{ $torneo->estado }}</span>
                </div>
            </div>
            <div class="mt-2 md:mt-0 flex flex-col items-end">
                <a href="{{ route('torneos.show', $torneo) }}" class="text-blue-500 hover:underline mb-2">&larr; Volver al torneo</a>
                @if($torneo->estado_torneo && !$torneo->torneo_cancelado && $ronda->numero_ronda == $torneo->rondas->max('numero_ronda') && $ronda->numero_ronda < $torneo->no_rondas)
                    <form method="POST" action="{{ route('torneos.rondas.store', $torneo) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-chess-knight mr-2"></i>
                            Generar Ronda {{ $ronda->numero_ronda + 1 }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Navegación de rondas -->
    <div class="bg-white shadow rounded-lg p-4 mb-4">
        <div class="flex flex-wrap gap-2">
            @foreach($rondas as $r)
                <a href="{{ route('torneos.rondas.show', [$torneo, $r]) }}"
                   class="px-4 py-2 rounded {{ $r->id === $ronda->id ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 text-blue-700 hover:bg-blue-100' }}">
                    Rd.{{ $r->numero_ronda }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Partidas de la ronda -->
    <div class="bg-white shadow rounded-lg p-4 mb-4">
        <h3 class="text-lg font-semibold mb-2">Partidas de la Ronda {{ $ronda->numero_ronda }}</h3>
        <form method="POST" action="{{ route('torneos.rondas.resultados', $ronda->id) }}" class="mb-4">
            @csrf
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">M.</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Blancas</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Elo</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Pts.</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Resultado</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Pts.</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Negras</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Elo</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">No.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partidas as $partida)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $partida->mesa }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2 text-sm font-medium text-blue-600">
                                {{ $partida->jugadorBlancas->nombres ?? '-' }} {{ $partida->jugadorBlancas->apellidos ?? '' }}
                            </td>
                            <td class="px-3 py-2 text-sm text-center text-gray-500">
                                {{ $partida->jugadorBlancas->elo->elo ?? '0' }}
                            </td>
                            <td class="px-3 py-2 text-sm text-center text-gray-500">
                                @php
                                    $participanteTorneo = $partida->jugadorBlancas ? $partida->jugadorBlancas->participanteTorneo()->where('torneo_id', $torneo->id)->first() : null;
                                @endphp
                                {{ $participanteTorneo ? number_format($participanteTorneo->puntos, 1) : '0.0' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if(!$ronda->completada)
                                    <input type="text" 
                                           name="resultados[{{ $partida->id }}]" 
                                           class="rounded border-gray-300 text-sm w-20" 
                                           placeholder="1-0/0-1/½/1/2"
                                           value="{{ $partida->getResultadoTexto() !== '*' ? $partida->getResultadoTexto() : '' }}"
                                           autocomplete="off">
                                @else
                                    <span class="text-sm font-medium">{{ $partida->getResultadoTexto() }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-center text-gray-500">
                                @php
                                    $participanteTorneoNegras = $partida->jugadorNegras ? $partida->jugadorNegras->participanteTorneo()->where('torneo_id', $torneo->id)->first() : null;
                                @endphp
                                {{ $participanteTorneoNegras ? number_format($participanteTorneoNegras->puntos, 1) : '0.0' }}
                            </td>
                            <td class="px-3 py-2 text-sm font-medium text-blue-600">
                                @if($partida->jugadorNegras)
                                    {{ $partida->jugadorNegras->nombres }} {{ $partida->jugadorNegras->apellidos }}
                                @else
                                    <span class="text-gray-500">BYE</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-center text-gray-500">
                                {{ $partida->jugadorNegras ? ($partida->jugadorNegras->elo->elo ?? '0') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{ $partida->jugadorNegras ? ($loop->iteration + 1) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if(!$ronda->completada)
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                        Guardar Todos los Resultados
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection 