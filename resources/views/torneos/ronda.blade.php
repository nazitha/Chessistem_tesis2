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

                {{-- Mostrar botón de "Generar Ronda" solo si NO existe ninguna ronda aún --}}
                @if($torneo->rondas->count() === 0 && !$torneo->torneo_cancelado)
                    <form method="POST" action="{{ route('torneos.rondas.store', $torneo) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-chess-knight mr-2"></i>
                            Generar Primera Ronda
                        </button>
                    </form>
                @endif

                {{-- Mostrar botón de "Generar siguiente ronda" si la ronda actual está completada, no es la última y la siguiente ronda no existe --}}
                @php
                    $siguienteRondaNum = $ronda->numero_ronda + 1;
                    $siguienteRondaExiste = $torneo->rondas->where('numero_ronda', $siguienteRondaNum)->count() > 0;
                @endphp
                @if($ronda->completada && $siguienteRondaNum <= $torneo->no_rondas && !$siguienteRondaExiste)
                    <form method="POST" action="{{ route('torneos.rondas.store', $torneo) }}" class="inline mt-2">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-forward mr-2"></i>
                            Generar Siguiente Ronda ({{ $siguienteRondaNum }})
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
        {{-- Quitar mensaje azul de recordatorio --}}
        {{-- @if($torneo->rondas->count() > 0 && !$ronda->completada)
            <div class="mb-4 p-2 bg-blue-100 text-blue-800 rounded">
                <b>Recuerda:</b> Para avanzar a la siguiente ronda, debes ingresar y guardar todos los resultados de las partidas de la ronda actual. Cuando todos los resultados estén completos, la siguiente ronda se generará automáticamente.
            </div>
        @endif --}}

        @if(session('warnings'))
            <div class="mb-4 p-2 bg-yellow-100 text-yellow-800 rounded">
                <b>Advertencias de emparejamiento:</b>
                <ul class="list-disc ml-6">
                    @foreach(session('warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-2 bg-red-200 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif
        <form method="POST" action="{{ route('torneos.rondas.resultados', $ronda->id) }}" class="mb-4">
            @csrf
            @if($errors->any())
                <div class="mb-4 p-2 bg-red-100 text-red-700 rounded">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            @if($torneo->es_por_equipos)
                @php
                    // Filtrar matches duplicados por equipos
                    $matches = $matches->unique(function($item) {
                        return $item->equipo_a_id . '-' . $item->equipo_b_id;
                    });
                @endphp

                <h2 class="text-2xl font-bold text-center my-4">RONDA {{ $ronda->numero_ronda }}</h2>

                @if($matches->count() > 0)
                    @foreach($matches as $match)
                        @php
                            // Ordenar jugadores de cada equipo por tablero (entero)
                            $jugadoresA = $match->equipoA->jugadores->filter(function($j) { return $j->tablero !== null; })->sortBy(function($j) { return (int)$j->tablero; })->values();
                            $jugadoresB = $match->equipoB->jugadores->filter(function($j) { return $j->tablero !== null; })->sortBy(function($j) { return (int)$j->tablero; })->values();
                            $tableros = $match->partidas->sortBy('tablero');
                            $puntajeA = 0;
                            $puntajeB = 0;
                            $todosConResultado = true;
                        @endphp

                        <div class="bg-gray-100 rounded-lg shadow mb-6 p-4">
                            <div class="flex justify-between items-center font-bold text-lg mb-2">
                                <span>{{ $match->equipoA->nombre }}</span>
                                <span class="text-gray-500">vs</span>
                                <span>{{ $match->equipoB->nombre ?? 'BYE' }}</span>
                            </div>
                            <table class="min-w-full mb-2">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th class="px-2 py-1 text-xs">Tablero</th>
                                        <th class="px-2 py-1 text-xs">Jugador A</th>
                                        <th class="px-2 py-1 text-xs">Elo</th>
                                        <th class="px-2 py-1 text-xs text-center">Color</th>
                                        <th class="px-2 py-1 text-xs text-center">Resultado</th>
                                        <th class="px-2 py-1 text-xs text-center">Color</th>
                                        <th class="px-2 py-1">Jugador B</th>
                                        <th class="px-2 py-1 text-xs">Elo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tableros as $partida)
                                        @php
                                            $esImpar = $partida->tablero % 2 === 1;
                                            $colorA = ($ronda->numero_ronda % 2 === 1) ? ($esImpar ? 'blancas' : 'negras') : ($esImpar ? 'negras' : 'blancas');
                                            $colorB = $colorA === 'blancas' ? 'negras' : 'blancas';

                                            // Obtener jugadores del tablero actual
                                            $jugadorA = $jugadoresA->firstWhere('tablero', $partida->tablero);
                                            $jugadorB = $jugadoresB->firstWhere('tablero', $partida->tablero);

                                            // Resultado Chess-Results
                                            $resA = $partida->resultado == 1 ? 1 : ($partida->resultado == 0.5 ? 0.5 : ($partida->resultado === null ? null : 0));
                                            $resB = $partida->resultado == 0 ? 1 : ($partida->resultado == 0.5 ? 0.5 : ($partida->resultado === null ? null : 0));

                                            if ($partida->resultado === null) $todosConResultado = false;
                                            $puntajeA += $resA ?? 0;
                                            $puntajeB += $resB ?? 0;
                                        @endphp
                                        <tr>
                                            <td class="px-2 py-1 text-center">{{ $partida->tablero }}</td>
                                            <td class="px-2 py-1">
                                                @if($jugadorA && $jugadorA->miembro)
                                                    <span class="text-blue-700">
                                                        {{ $jugadorA->miembro->nombres }} {{ $jugadorA->miembro->apellidos }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-2 py-1 text-center">{{ $jugadorA->miembro->elo->elo ?? '-' }}</td>
                                            <td class="px-2 py-1 text-center">
                                                <span style="display:inline-block;width:16px;height:16px;border-radius:3px;vertical-align:middle;@if($colorA == 'blancas')background:#fff;border:1px solid #aaa;@else background:#222;@endif"></span>
                                            </td>
                                            <td class="px-2 py-1 text-center font-bold">
                                                @if(!$ronda->completada)
                                                    <input type="text" name="resultados[{{ $partida->id }}]" class="rounded border-gray-300 text-sm w-12 text-center" placeholder="1-0/0-1/½" value="" autocomplete="off">
                                                @else
                                                    @if($partida->resultado === null)
                                                        *
                                                    @elseif($partida->resultado == 1)
                                                        1 - 0
                                                    @elseif($partida->resultado == 0)
                                                        0 - 1
                                                    @elseif($partida->resultado == 0.5)
                                                        ½ - ½
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="px-2 py-1 text-center">
                                                <span style="display:inline-block;width:16px;height:16px;border-radius:3px;vertical-align:middle;@if($colorB == 'blancas')background:#fff;border:1px solid #aaa;@else background:#222;@endif"></span>
                                            </td>
                                            <td class="px-2 py-1">
                                                @if($jugadorB && $jugadorB->miembro)
                                                    <span class="text-blue-700">
                                                        {{ $jugadorB->miembro->nombres }} {{ $jugadorB->miembro->apellidos }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-2 py-1 text-center">{{ $jugadorB->miembro->elo->elo ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($todosConResultado)
                                <div class="flex justify-end font-bold text-lg">
                                    <span>{{ $match->equipoA->nombre }} {{ $puntajeA }} - {{ $puntajeB }} {{ $match->equipoB->nombre }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-gray-500 py-8">
                        <p class="text-lg">No hay matches generados para esta ronda.</p>
                        <p class="text-sm mt-2">Genera el emparejamiento desde la vista del torneo.</p>
                    </div>
                @endif
            @else
                @if($torneo->tipo_torneo === 'Eliminación Directa' && !$torneo->es_por_equipos)
                    <div class="mb-2 p-2 bg-yellow-100 text-yellow-800 rounded text-center">
                        <b>Importante:</b> En eliminación directa no se permiten empates. Debe haber un ganador en cada partida. Solo se aceptan resultados <b>1-0</b> o <b>0-1</b>.
                    </div>
                @endif
                <div class="bg-gray-100 rounded-lg shadow p-4 mb-6">
                    <table class="min-w-full mb-2">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-2 py-1 text-xs">Tablero</th>
                                <th class="px-2 py-1 text-xs">Jugador A</th>
                                <th class="px-2 py-1 text-xs">Elo</th>
                                <th class="px-2 py-1 text-xs">Pts.</th>
                                <th class="px-2 py-1 text-xs text-center">Color</th>
                                <th class="px-2 py-1 text-xs text-center">Resultado</th>
                                <th class="px-2 py-1 text-xs text-center">Color</th>
                                <th class="px-2 py-1">Jugador B</th>
                                <th class="px-2 py-1 text-xs">Elo</th>
                                <th class="px-2 py-1 text-xs">Pts.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partidas as $partida)
                                @php
                                    $participanteA = $partida->jugadorBlancas ? $partida->jugadorBlancas->participanteTorneo()->where('torneo_id', $torneo->id)->first() : null;
                                    $participanteB = $partida->jugadorNegras ? $partida->jugadorNegras->participanteTorneo()->where('torneo_id', $torneo->id)->first() : null;
                                    $esEliminacionDirecta = $torneo->tipo_torneo === 'Eliminación Directa';
                                    // FILTRO ELIMINADO: Mostrar todas las partidas, incluso si ambos tienen 0 puntos
                                    // if ($esEliminacionDirecta && (
                                    //     ($participanteA && $participanteA->puntos == 0) &&
                                    //     ($participanteB && $participanteB->puntos == 0)
                                    // )) continue;
                                    // Determinar ganador si hay resultado
                                    $ganadorId = null;
                                    if($esEliminacionDirecta && $partida->resultado !== null) {
                                        if($partida->color && $partida->resultado == 1) {
                                            $ganadorId = $partida->jugadorBlancas ? $partida->jugadorBlancas->id : null;
                                        } elseif(!$partida->color && $partida->resultado == 0) {
                                            $ganadorId = $partida->jugadorNegras ? $partida->jugadorNegras->id : null;
                                        }
                                    }
                                    $puedeEditar = true;
                                    // if ($esEliminacionDirecta && (
                                    //     ($participanteA && $participanteA->puntos == 0) &&
                                    //     ($participanteB && $participanteB->puntos == 0)
                                    // )) {
                                    //     $puedeEditar = false;
                                    // }
                                @endphp
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $partida->mesa }}</td>
                                    <td class="px-2 py-1">
                                        @if($partida->jugadorBlancas)
                                            <span class="text-blue-700">
                                                {{ $partida->jugadorBlancas->nombres }} {{ $partida->jugadorBlancas->apellidos }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">{{ $esEliminacionDirecta ? 'Por definir' : '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 text-center">{{ $partida->jugadorBlancas->elo->elo ?? '-' }}</td>
                                    <td class="px-2 py-1 text-center">{{ $participanteA ? number_format($participanteA->puntos, 1) : '0.0' }}</td>
                                    <td class="px-2 py-1 text-center">
                                        <span style="display:inline-block;width:16px;height:16px;border-radius:3px;vertical-align:middle;background:#fff;border:1px solid #aaa;"></span>
                                    </td>
                                    <td class="px-2 py-1 text-center font-bold">
                                        @if($torneo->tipo_torneo === 'Eliminación Directa')
                                            @if(!$ronda->completada)
                                                @if($partida->jugadorNegras)
                                                    <input type="text" name="resultados[{{ $partida->id }}]" class="rounded border-gray-300 text-sm w-12 text-center" placeholder="1-0/0-1" value="{{ $partida->getResultadoTexto() !== '*' ? $partida->getResultadoTexto() : '' }}" autocomplete="off">
                                                @else
                                                    BYE
                                                @endif
                                            @else
                                                @if($partida->resultado === null)
                                                    *
                                                @elseif($partida->resultado == 1)
                                                    1 - 0
                                                @elseif($partida->resultado == 0)
                                                    0 - 1
                                                @elseif($partida->resultado == 0.5)
                                                    ½ - ½
                                                @endif
                                            @endif
                                        @else
                                            @if(!$ronda->completada)
                                                @if($partida->jugadorNegras)
                                                    <input type="text" name="resultados[{{ $partida->id }}]" class="rounded border-gray-300 text-sm w-12 text-center" placeholder="1-0/0-1/½" value="{{ $partida->getResultadoTexto() !== '*' ? $partida->getResultadoTexto() : '' }}" autocomplete="off">
                                                @else
                                                    BYE
                                                @endif
                                            @else
                                                @if($partida->resultado === null)
                                                    *
                                                @elseif($partida->resultado == 1)
                                                    1 - 0
                                                @elseif($partida->resultado == 0)
                                                    0 - 1
                                                @elseif($partida->resultado == 0.5)
                                                    ½ - ½
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 text-center">
                                        <span style="display:inline-block;width:16px;height:16px;border-radius:3px;vertical-align:middle;background:#222;"></span>
                                    </td>
                                    <td class="px-2 py-1">
                                        @if($partida->jugadorNegras)
                                            <span class="text-blue-700">
                                                {{ $partida->jugadorNegras->nombres }} {{ $partida->jugadorNegras->apellidos }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">{{ $esEliminacionDirecta ? 'Por definir' : 'BYE' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 text-center">{{ $partida->jugadorNegras ? ($partida->jugadorNegras->elo->elo ?? '-') : '-' }}</td>
                                    <td class="px-2 py-1 text-center">{{ $participanteB ? number_format($participanteB->puntos, 1) : '0.0' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-gray-500 py-4">
                                        No hay partidas para esta ronda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!$ronda->completada)
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                        Guardar Todos los Resultados
                    </button>
                </div>
            @endif
        </form>
    </div>

    @if($torneo->es_por_equipos)
        <h2 class="text-2xl font-bold text-center my-4">Tabla de Clasificación de Equipos</h2>
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Posición</th>
                    <th class="px-4 py-2 border-b">Equipo</th>
                    <th class="px-4 py-2 border-b">Puntos Ronda</th>
                    <th class="px-4 py-2 border-b">Puntos Totales</th>
                    @if($torneo->usar_buchholz)
                        <th class="px-4 py-2 border-b">Buchholz</th>
                    @endif
                    @if($torneo->usar_sonneborn_berger)
                        <th class="px-4 py-2 border-b">Sonneborn-Berger</th>
                    @endif
                    @if($torneo->usar_desempate_progresivo)
                        <th class="px-4 py-2 border-b">Progresivo</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($equipos as $index => $equipo)
                    <tr>
                        <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 border-b">{{ $equipo->nombre }}</td>
                        <td class="px-4 py-2 border-b">{{ $equipo->puntos_ronda }}</td>
                        <td class="px-4 py-2 border-b">{{ $equipo->puntos_totales }}</td>
                        @if($torneo->usar_buchholz)
                            <td class="px-4 py-2 border-b">{{ $equipo->buchholz ?? 0 }}</td>
                        @endif
                        @if($torneo->usar_sonneborn_berger)
                            <td class="px-4 py-2 border-b">{{ $equipo->sonneborn ?? 0 }}</td>
                        @endif
                        @if($torneo->usar_desempate_progresivo)
                            <td class="px-4 py-2 border-b">{{ $equipo->progresivo ?? 0 }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection 