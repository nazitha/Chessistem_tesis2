@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ $torneo->nombre_torneo }}
            </h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ route('torneos.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver
            </a>

            @if($torneo->estado_torneo && !$torneo->torneo_cancelado)
                <button type="button"
                        onclick="mostrarModalParticipantes()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-user-plus mr-2"></i>
                    Agregar Participantes
                </button>
            @endif

            @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 4)
                <a href="{{ route('torneos.edit', $torneo) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <i class="fas fa-edit mr-2"></i>
                    Editar Torneo
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Información del Torneo
            </h3>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Fecha y Hora</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $torneo->fecha_inicio ? $torneo->fecha_inicio->format('d/m/Y') : 'No definida' }} 
                        {{ $torneo->hora_inicio ? ' a las ' . $torneo->hora_inicio : '' }}
                    </dd>
                </div>

                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Lugar</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $torneo->lugar ?? 'No definido' }}</dd>
                </div>

                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $torneo->categoria ? $torneo->categoria->categoria_torneo : 'No definida' }}
                    </dd>
                </div>

                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $torneo->estadoClase }}">
                            {{ $torneo->estado }}
                        </span>
                    </dd>
                </div>

                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Organizadores</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                            @if($torneo->organizador)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Organizador:</span>
                                    <span class="ml-2">{{ $torneo->organizador->nombres }} {{ $torneo->organizador->apellidos }}</span>
                                </div>
                            </li>
                            @endif
                            
                            @if($torneo->directorTorneo)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Director del Torneo:</span>
                                    <span class="ml-2">{{ $torneo->directorTorneo->nombres }} {{ $torneo->directorTorneo->apellidos }}</span>
                                </div>
                            </li>
                            @endif

                            @if($torneo->arbitroPrincipal)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Árbitro Principal:</span>
                                    <span class="ml-2">{{ $torneo->arbitroPrincipal->nombres }} {{ $torneo->arbitroPrincipal->apellidos }}</span>
                                </div>
                            </li>
                            @endif

                            @if($torneo->arbitro)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Árbitro:</span>
                                    <span class="ml-2">{{ $torneo->arbitro->nombres }} {{ $torneo->arbitro->apellidos }}</span>
                                </div>
                            </li>
                            @endif

                            @if($torneo->arbitroAdjunto)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Árbitro Adjunto:</span>
                                    <span class="ml-2">{{ $torneo->arbitroAdjunto->nombres }} {{ $torneo->arbitroAdjunto->apellidos }}</span>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </dd>
                </div>

                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Detalles Técnicos</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Número de Rondas:</span>
                                    <span class="ml-2">{{ $torneo->no_rondas }}</span>
                                </div>
                            </li>
                            
                            @if($torneo->controlTiempo)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Control de Tiempo:</span>
                                    <span class="ml-2">{{ $torneo->controlTiempo->formato }} ({{ $torneo->controlTiempo->control_tiempo }})</span>
                                </div>
                            </li>
                            @endif

                            @if($torneo->emparejamiento)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Sistema de Emparejamiento:</span>
                                    <span class="ml-2">{{ $torneo->emparejamiento->sistema }}</span>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </dd>
                </div>

                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Criterios de Desempate</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Buchholz:</span>
                                    <span class="ml-2">{{ $torneo->usar_buchholz ? 'Sí' : 'No' }}</span>
                                </div>
                            </li>
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Sonneborn-Berger:</span>
                                    <span class="ml-2">{{ $torneo->usar_sonneborn_berger ? 'Sí' : 'No' }}</span>
                                </div>
                            </li>
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <span class="font-medium">Progresivo:</span>
                                    <span class="ml-2">{{ $torneo->usar_desempate_progresivo ? 'Sí' : 'No' }}</span>
                                </div>
                            </li>
                        </ul>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Sección de Participantes -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 mt-8">
        <div class="px-6 py-5 sm:px-8 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Participantes
                <span class="ml-2 text-sm text-gray-500">({{ $torneo->participantes->count() }} registrados)</span>
            </h3>
            @if($torneo->estado_torneo && !$torneo->torneo_cancelado)
                <button type="button"
                        onclick="mostrarModalParticipantes()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-user-plus mr-2"></i>
                    Agregar Participantes
                </button>
            @endif
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto px-6 sm:px-8 py-4">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 w-12">No.</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Nombre</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Elo</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 w-16">FED</th>
                            @for ($i = 1; $i <= $torneo->no_rondas; $i++)
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 w-16">{{ $i }}.Rd</th>
                            @endfor
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Pts.</th>
                            @if($torneo->usar_buchholz)
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Des 1</th>
                            @endif
                            @if($torneo->usar_sonneborn_berger)
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Des 2</th>
                            @endif
                            @if($torneo->usar_desempate_progresivo)
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Des 3</th>
                            @endif
                            @if($torneo->estado_torneo && !$torneo->torneo_cancelado)
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-24">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($torneo->participantes()->orderBy('posicion')->get() as $index => $participante)
                            <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="px-3 py-2 text-sm text-blue-600">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 text-sm text-blue-600">
                                    {{ $participante->miembro->nombres }} {{ $participante->miembro->apellidos }}
                                </td>
                                <td class="px-3 py-2 text-sm text-right text-gray-900">
                                    {{ $participante->miembro->elo ?? '' }}
                                </td>
                                <td class="px-3 py-2 text-sm text-center text-gray-900">
                                    {{ $participante->miembro->federacion ?? 'NCA' }}
                                </td>
                                @foreach($torneo->rondas as $ronda)
                                    @php
                                        $partida = $ronda->partidas->first(function($p) use ($participante) {
                                            return $p->jugador_blancas_id === $participante->miembro_id || 
                                                   $p->jugador_negras_id === $participante->miembro_id;
                                        });
                                        
                                        if ($partida) {
                                            $esBlancas = $partida->jugador_blancas_id === $participante->miembro_id;
                                            $oponente = $esBlancas ? $partida->jugadorNegras : $partida->jugadorBlancas;
                                            $oponenteIndex = $torneo->participantes()
                                                ->where('miembro_id', $oponente ? $oponente->cedula : null)
                                                ->first();
                                            $oponenteNumero = $oponenteIndex ? $torneo->participantes()
                                                ->where('puntos', '>', $oponenteIndex->puntos)
                                                ->count() + 1 : null;
                                        }
                                    @endphp
                                    <td class="px-3 py-2 text-sm text-center {{ !$partida || $partida->resultado === null ? 'text-gray-500' : 'text-gray-900' }}">
                                        @if($partida)
                                            @if($oponente)
                                                {{ $oponenteNumero }}{{ $esBlancas ? 'b' : 'w' }}
                                                @if($partida->resultado !== null)
                                                    @if($esBlancas)
                                                        {{ $partida->resultado === 1 ? '1' : ($partida->resultado === 2 ? '0' : '½') }}
                                                    @else
                                                        {{ $partida->resultado === 2 ? '1' : ($partida->resultado === 1 ? '0' : '½') }}
                                                    @endif
                                                @else
                                                    *
                                                @endif
                                            @elseif(!$partida->jugador_negras_id)
                                                +
                                            @else
                                                -0
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                                @for($i = $torneo->rondas->count() + 1; $i <= $torneo->no_rondas; $i++)
                                    <td class="px-3 py-2 text-sm text-center text-gray-500">-</td>
                                @endfor
                                <td class="px-3 py-2 text-sm text-right font-medium">
                                    {{ number_format($participante->puntos, 1) }}
                                </td>
                                @if($torneo->usar_buchholz)
                                    <td class="px-3 py-2 text-sm text-right text-gray-900">
                                        {{ number_format($participante->buchholz, 2) }}
                                    </td>
                                @endif
                                @if($torneo->usar_sonneborn_berger)
                                    <td class="px-3 py-2 text-sm text-right text-gray-900">
                                        {{ number_format($participante->sonneborn_berger, 2) }}
                                    </td>
                                @endif
                                @if($torneo->usar_desempate_progresivo)
                                    <td class="px-3 py-2 text-sm text-right text-gray-900">
                                        {{ number_format($participante->progresivo, 2) }}
                                    </td>
                                @endif
                                @if($torneo->estado_torneo && !$torneo->torneo_cancelado)
                                    <td class="px-3 py-2 text-sm text-right">
                                        <button type="button"
                                                onclick="confirmarRetiroParticipante('{{ $participante->id }}')"
                                                class="text-red-600 hover:text-red-900">
                                            Retirar
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sección de Rondas -->
    @if($torneo->participantes->count() >= 2)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Rondas y Resultados
                    <span class="ml-2 text-sm text-gray-500">({{ $torneo->rondas->count() }} de {{ $torneo->no_rondas }})</span>
                </h3>
                @if($torneo->estado_torneo && !$torneo->torneo_cancelado && $torneo->rondas->count() < $torneo->no_rondas)
                    <button type="button"
                            onclick="generarEmparejamientos()"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-chess-knight mr-2"></i>
                        Generar Ronda {{ $torneo->rondas->count() + 1 }}
                    </button>
                @endif
            </div>

            <div class="border-t border-gray-200">
                @if($torneo->rondas->count() > 0)
                    @foreach($torneo->rondas as $ronda)
                        <div class="px-4 py-5 sm:p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">
                                Ronda {{ $ronda->numero_ronda }}
                                <span class="text-sm text-gray-500">({{ $ronda->fecha_hora->format('d/m/Y H:i') }})</span>
                            </h4>
                            <div class="overflow-x-auto">
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
                                            @foreach($ronda->partidas as $partida)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 text-sm text-gray-500">{{ $partida->mesa }}</td>
                                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                                    <td class="px-3 py-2 text-sm font-medium text-blue-600">
                                                        {{ $partida->jugadorBlancas->nombres }} {{ $partida->jugadorBlancas->apellidos }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-center text-gray-500">
                                                        {{ $partida->jugadorBlancas->elo ?? '0' }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-center text-gray-500">
                                                        @php
                                                            $participanteTorneo = $partida->jugadorBlancas->participanteTorneo()->where('torneo_id', $torneo->id)->first();
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
                                                        {{ $partida->jugadorNegras ? ($partida->jugadorNegras->elo ?? '0') : '-' }}
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
                    @endforeach
                @else
                    <div class="px-6 py-4 text-center text-sm text-gray-500">
                        No se han generado rondas aún.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Modal de Agregar Participantes -->
<div id="modal-participantes" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Agregar Participantes</h3>
            <button type="button" onclick="cerrarModalParticipantes()" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Cerrar</span>
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="form-participantes" action="{{ route('torneos.participantes.store', $torneo) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="participantes" class="block text-sm font-medium text-gray-700">
                        Seleccionar Participantes
                    </label>
                    <select id="participantes" name="participantes[]" 
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            multiple>
                        @foreach($miembrosDisponibles as $miembro)
                            <option value="{{ $miembro->cedula }}">
                                {{ $miembro->nombres }} {{ $miembro->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                        onclick="cerrarModalParticipantes()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Agregar Participantes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#participantes').select2({
        placeholder: 'Seleccione los participantes',
        allowClear: true
    });
});

function mostrarModalParticipantes() {
    const modal = document.getElementById('modal-participantes');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModalParticipantes() {
    const modal = document.getElementById('modal-participantes');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function confirmarRetiroParticipante(participanteId) {
    if (confirm('¿Está seguro que desea retirar a este participante del torneo?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/torneos/{{ $torneo->id }}/participantes/${participanteId}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
}

function generarEmparejamientos() {
    if (confirm('¿Está seguro que desea generar los emparejamientos para la siguiente ronda?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('torneos.rondas.store', ['torneo' => $torneo->id]) }}`;
        form.innerHTML = `@csrf`;
        document.body.appendChild(form);
        form.submit();
    }
}

function registrarResultado(partidaId, resultado, buttonElement) {
    // Deshabilitar todos los botones del grupo
    const botonesGrupo = buttonElement.parentElement.querySelectorAll('button');
    botonesGrupo.forEach(btn => {
        btn.classList.remove('bg-green-600', 'bg-blue-600');
        if (btn !== buttonElement) {
            btn.classList.add('bg-gray-500');
            btn.classList.add('opacity-50');
        }
    });

    // Resaltar el botón seleccionado
    buttonElement.classList.remove('bg-gray-500', 'opacity-50');
    buttonElement.classList.add('bg-blue-600');

    // Crear el formulario para enviar
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/torneos/partidas/${partidaId}/resultado`;
    
    // Agregar CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);

    // Agregar el resultado
    const resultadoInput = document.createElement('input');
    resultadoInput.type = 'hidden';
    resultadoInput.name = 'resultado';
    resultadoInput.value = resultado;
    form.appendChild(resultadoInput);

    // Enviar el formulario
    document.body.appendChild(form);
    form.submit();
}

function getResultadoTexto(resultado) {
    switch(parseInt(resultado)) {
        case 1: return '1-0';
        case 2: return '0-1';
        case 3: return '½-½';
        default: return '*';
    }
}

function verificarRondaCompletada() {
    // Verificar si quedan botones de resultado en la ronda actual
    const botonesRestantes = document.querySelectorAll('.ronda-actual .resultado-buttons');
    if (botonesRestantes.length === 0) {
        // Mostrar el botón de generar siguiente ronda si corresponde
        const btnGenerarRonda = document.querySelector('.btn-generar-ronda');
        if (btnGenerarRonda) {
            btnGenerarRonda.classList.remove('hidden');
        }
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal-participantes').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalParticipantes();
    }
});
</script>
@endpush 