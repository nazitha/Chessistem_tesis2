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
            @php
                $detalles = [
                    ['label' => 'Fecha y Hora', 'value' => ($torneo->fecha_inicio ? $torneo->fecha_inicio->format('d/m/Y') : 'No definida') . ($torneo->hora_inicio ? ' a las ' . $torneo->hora_inicio : '')],
                    ['label' => 'Lugar', 'value' => $torneo->lugar ?? 'No definido'],
                    ['label' => 'Categoría', 'value' => $torneo->categoria ? $torneo->categoria->categoria_torneo : 'No definida'],
                    ['label' => 'Tipo de Torneo', 'value' => $torneo->es_por_equipos ? 'Por equipos' : 'Individual'],
                    ['label' => 'Estado', 'value' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $torneo->estadoClase . '">' . $torneo->estado . '</span>'],
                ];
            @endphp
            <dl>
                @foreach($detalles as $i => $detalle)
                    <div class="{{ $i % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">{{ $detalle['label'] }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{!! $detalle['value'] !!}</dd>
                    </div>
                @endforeach

                <!-- Organizadores -->
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

                <!-- Detalles Técnicos -->
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

                <!-- Criterios de Desempate -->
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

    @if($torneo->es_por_equipos)
    <!-- Modal Registrar Equipo (Tailwind + AlpineJS) -->
    <div x-data="{ open: {{ $errors->any() && old('modal_origen') === 'equipo' ? 'true' : 'false' }} }" @keydown.escape.window="open = false">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 mt-8">
            <div class="px-6 py-5 sm:px-8 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Equipos Registrados
                    <span class="ml-2 text-sm text-gray-500">({{ $torneo->equipos->count() }} equipos)</span>
                </h3>
                <div class="flex gap-2 items-center">
                    @if($torneo->estado_torneo && !$torneo->torneo_cancelado)
                        <button type="button"
                                @click="open = true"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-users mr-2"></i>
                            Registrar Equipo
                        </button>
                    @endif
                    @if(!$torneo->es_por_equipos && $torneo->estado_torneo && !$torneo->torneo_cancelado && $torneo->participantes->count() >= 4)
                        <form method="POST" action="{{ route('torneos.rondas.store', $torneo) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-chess-board mr-2"></i>
                                Generar Emparejamiento
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto px-6 sm:px-8 py-4">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 w-12">No.</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Nombre del Equipo</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Capitán</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Federación</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Jugadores</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($torneo->equipos as $index => $equipo)
                            <tr>
                                <td class="px-3 py-2">{{ $index + 1 }}</td>
                                <td class="px-3 py-2">{{ $equipo->nombre }}</td>
                                <td class="px-3 py-2">{{ $equipo->capitan ? $equipo->capitan->nombres . ' ' . $equipo->capitan->apellidos : '-' }}</td>
                                <td class="px-3 py-2">{{ $equipo->federacion ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if(is_countable($equipo->jugadores) && count($equipo->jugadores))
                                        <ul class="list-disc ml-4">
                                            @foreach($equipo->jugadores as $jugador)
                                                <li>
                                                    {{ $jugador->miembro->nombres }} {{ $jugador->miembro->apellidos }} (Tablero {{ $jugador->tablero }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400">Sin jugadores</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 space-x-2">
                                    <a href="{{ route('equipos.show', [$torneo->id, $equipo->id]) }}" title="Ver" class="inline-block text-blue-600 hover:text-blue-800 text-xl align-middle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-editar-equipo inline-block text-yellow-600 hover:text-yellow-800 text-xl align-middle" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('equipos.destroy', [$torneo->id, $equipo->id]) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar equipo completo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar" class="inline-block text-red-600 hover:text-red-800 text-xl align-middle">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @if($torneo->estado_torneo && !$torneo->torneo_cancelado)
                                        <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-agregar-jugador inline-block text-green-600 hover:text-green-800 text-xl align-middle" title="Agregar jugador">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-3 py-2 text-center text-gray-400">No hay equipos registrados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal de registro de equipo -->
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-40" style="display: none;">
            <div @click.away="open = false" class="bg-white rounded-xl shadow-2xl w-full max-w-md sm:max-w-lg md:max-w-2xl p-4 sm:p-8 relative flex flex-col max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Registrar Equipo</h2>
                    <button type="button" class="btn-cerrar-editar text-gray-400 hover:text-gray-600 text-3xl leading-none" @click="open = false">&times;</button>
                </div>
                @if($errors->any() && old('modal_origen') === 'equipo')
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('equipos.store', $torneo->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="modal_origen" value="equipo">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nombre del Equipo <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required placeholder="Ej: Caballos Negros">
                            @error('nombre')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Capitán</label>
                            <select name="capitan_id" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">-- Selecciona --</option>
                                @foreach($miembrosDisponibles as $miembro)
                                    <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                @endforeach
                            </select>
                            @error('capitan_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Federación</label>
                            <input type="text" name="federacion" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Ej: FENACOAJ">
                            @error('federacion')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Logo</label>
                            <input type="file" name="logo" accept="image/*" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @error('logo')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Notas</label>
                        <textarea name="notas" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Observaciones, detalles, etc."></textarea>
                        @error('notas')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div class="border-t pt-6 mt-2">
                        <label class="block text-base font-bold mb-2">Jugadores del Equipo <span class="text-red-500">*</span></label>
                        <div class="space-y-3">
                            <template x-for="i in 4" :key="i">
                                <div class="flex flex-col md:flex-row md:space-x-2 items-center">
                                    <select :name="'jugadores[' + (i-1) + '][miembro_id]'" class="block w-full md:w-2/3 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                        <option value="">-- Selecciona jugador --</option>
                                        @foreach($miembrosDisponibles as $miembro)
                                            <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                        @endforeach
                                    </select>
                                    <input :name="'jugadores[' + (i-1) + '][tablero]'" type="number" min="1" class="block w-full md:w-1/3 mt-2 md:mt-0 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Tablero" required>
                                </div>
                            </template>
                            <p class="text-xs text-gray-500 mt-1">Puedes agregar más jugadores después de crear el equipo.</p>
                        </div>
                        @error('jugadores')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" class="btn-cerrar-editar px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold" @click="open = false">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-semibold flex items-center gap-2">
                            <i class="fas fa-save"></i> Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if(!$torneo->es_por_equipos)
    <!-- Sección de Participantes -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 mt-8">
        @php
            $posicion = 1;
            $posicionMostrada = 1;
        @endphp
        <div class="px-6 py-5 sm:px-8 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Participantes
                <span class="ml-2 text-sm text-gray-500">({{ $torneo->participantes->count() }} registrados)</span>
            </h3>
            <div class="flex gap-2">
                <button type="button"
                        onclick="mostrarModalParticipantes()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-user-plus mr-2"></i>
                    Agregar Participantes
                </button>
                @if(!$torneo->es_por_equipos && $torneo->estado_torneo && !$torneo->torneo_cancelado && $torneo->participantes->count() >= 4)
                    <form method="POST" action="{{ route('torneos.rondas.store', $torneo) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-chess-board mr-2"></i>
                            Generar Emparejamiento
                        </button>
                    </form>
                @endif
            </div>
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
                        @foreach($torneo->participantes()->orderBy('numero_inicial')->get() as $index => $participante)
                            <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="px-3 py-2 text-sm text-blue-600">{{ $participante->numero_inicial }}</td>
                                <td class="px-3 py-2 text-sm text-blue-600">
                                    {{ $participante->miembro->nombres }} {{ $participante->miembro->apellidos }}
                                </td>
                                <td class="px-3 py-2 text-sm text-right text-gray-900">
                                    {{ $participante->miembro->elo->elo ?? '-' }}
                                </td>
                                <td class="px-3 py-2 text-sm text-center text-gray-900">
                                    {{ $participante->miembro->fide->fed_id ?? 'NCA' }}
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
                                            $oponenteParticipante = $oponente ? $torneo->participantes()
                                                ->where('miembro_id', $oponente->cedula)
                                                ->first() : null;
                                            $oponenteNumero = $oponenteParticipante ? $oponenteParticipante->numero_inicial : null;
                                        }
                                    @endphp
                                    <td class="px-3 py-2 text-sm text-center {{ !$partida || $partida->resultado === null ? 'text-gray-500' : 'text-gray-900' }}">
                                        @if($partida)
                                            @if($oponente)
                                                {{ $oponenteNumero }}{{ $esBlancas ? 'w' : 'b' }}
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
                                        @endif
                                    </td>
                                @endforeach
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
                                    <td class="px-3 py-2 text-sm text-center">
                                        <form action="{{ route('torneos.participantes.destroy', [$torneo->id, $participante->id]) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas retirar este participante?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Retirar</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                            @php
                                $posicion++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Sección de Rondas -->
    @if($torneo->rondas->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Rondas y Resultados
                    <span class="ml-2 text-sm text-gray-500">({{ $torneo->rondas->count() }} de {{ $torneo->no_rondas }})</span>
                </h3>

            </div>
            <div class="border-t border-gray-200">
                <div class="p-4 flex flex-wrap gap-2">
                    @foreach($torneo->rondas as $ronda)
                        <a href="{{ route('torneos.rondas.show', [$torneo, $ronda]) }}"
                           class="px-4 py-2 rounded bg-gray-200 text-blue-700 hover:bg-blue-100 font-semibold">
                            Rd.{{ $ronda->numero_ronda }}
                        </a>
                    @endforeach
                </div>
                @if($torneo->rondas->count() === 0)
                    <div class="px-6 py-4 text-center text-sm text-gray-500">
                        No se han generado rondas aún.
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Tabla de Clasificación Final -->
    @if(!$torneo->es_por_equipos && $torneo->rondas->count() == $torneo->no_rondas)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Clasificación Final
                </h3>
            </div>
            <div class="border-t border-gray-200">
                @php
                    $posicion = 1;
                    $posicionMostrada = 1;
                    $puntosAnteriores = null;
                    $buchholzAnterior = null;
                    $sonnebornAnterior = null;
                    $progresivoAnterior = null;
                @endphp
                <div class="overflow-x-auto px-6 sm:px-8 py-4">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 w-12">Pos.</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 w-12">No.</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Nombre</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Elo</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-600 w-16">FED</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Pts.</th>
                                @if($torneo->usar_buchholz)
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Buchholz</th>
                                @endif
                                @if($torneo->usar_sonneborn_berger)
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">S-B</th>
                                @endif
                                @if($torneo->usar_desempate_progresivo)
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-16">Prog.</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($torneo->participantes()
                                ->orderByDesc('puntos')
                                ->orderByDesc('buchholz')
                                ->orderByDesc('sonneborn_berger')
                                ->orderByDesc('progresivo')
                                ->get() as $participante)
                                @php
                                    if ($puntosAnteriores !== $participante->puntos ||
                                        ($torneo->usar_buchholz && $buchholzAnterior !== $participante->buchholz) ||
                                        ($torneo->usar_sonneborn_berger && $sonnebornAnterior !== $participante->sonneborn_berger) ||
                                        ($torneo->usar_desempate_progresivo && $progresivoAnterior !== $participante->progresivo)) {
                                        $posicionMostrada = $posicion;
                                    }
                                    $puntosAnteriores = $participante->puntos;
                                    $buchholzAnterior = $participante->buchholz;
                                    $sonnebornAnterior = $participante->sonneborn_berger;
                                    $progresivoAnterior = $participante->progresivo;
                                @endphp
                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="px-3 py-2 text-sm font-medium">{{ $posicionMostrada }}</td>
                                    <td class="px-3 py-2 text-sm text-blue-600">{{ $participante->numero_inicial }}</td>
                                    <td class="px-3 py-2 text-sm text-blue-600">
                                        {{ $participante->miembro->nombres }} {{ $participante->miembro->apellidos }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-right text-gray-900">
                                        {{ $participante->miembro->elo->elo ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-center text-gray-900">
                                        {{ $participante->miembro->fide->fed_id ?? 'NCA' }}
                                    </td>
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
                                </tr>
                                @php
                                    $posicion++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($torneo->es_por_equipos && isset($equipos) && $equipos->count() > 0)
        <h2 class="text-2xl font-bold text-center my-4">Tabla de Clasificación de Equipos</h2>
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Posición</th>
                    <th class="px-4 py-2 border-b">Equipo</th>
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

@if(!$torneo->es_por_equipos)
<!-- Modal Agregar Participantes -->
<div id="modal-participantes" class="fixed inset-0 bg-gray-900 bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative flex flex-col">
        <h2 class="text-xl font-bold mb-4">Agregar Participantes</h2>
        <form method="POST" action="{{ route('torneos.participantes.store', $torneo->id) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Seleccionar Participantes</label>
                <select id="participantes" name="participantes[]" class="w-full rounded border-gray-300" multiple required>
                    @foreach($miembrosDisponibles as $miembro)
                        <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" onclick="cerrarModalParticipantes()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Agregar Participantes</button>
            </div>
        </form>
        <button onclick="cerrarModalParticipantes()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
    </div>
</div>
@endif

{{-- Corrección de botones y validación de tablero --}}
@if($torneo->es_por_equipos)
    @foreach($torneo->equipos as $equipo)
        <div id="modal-editar-equipo-{{ $equipo->id }}" class="fixed inset-0 bg-gray-900 bg-opacity-40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md sm:max-w-lg md:max-w-2xl p-4 sm:p-8 relative flex flex-col max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Editar Equipo</h2>
                    <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-cerrar-editar text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
                </div>
                <form method="POST" action="{{ route('equipos.update', [$torneo->id, $equipo->id]) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nombre del Equipo <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" value="{{ $equipo->nombre }}" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Capitán</label>
                            <select name="capitan_id" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">-- Selecciona --</option>
                                @foreach($miembrosDisponibles as $miembro)
                                    <option value="{{ $miembro->cedula }}" @if($equipo->capitan_id == $miembro->cedula) selected @endif>{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Federación</label>
                            <input type="text" name="federacion" value="{{ $equipo->federacion }}" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Logo</label>
                            <input type="file" name="logo" accept="image/*" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @if($equipo->logo)
                                <img src="{{ asset('storage/' . $equipo->logo) }}" alt="Logo actual" class="h-10 mt-2">
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Notas</label>
                        <textarea name="notas" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ $equipo->notas }}</textarea>
                    </div>
                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-cerrar-editar px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-yellow-600 text-white hover:bg-yellow-700 font-semibold flex items-center gap-2">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal VER EQUIPO -->
        <div id="modal-ver-equipo-{{ $equipo->id }}" class="fixed inset-0 bg-gray-900 bg-opacity-40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md sm:max-w-lg md:max-w-2xl p-4 sm:p-8 relative flex flex-col max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Detalles del Equipo</h2>
                    <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-cerrar-ver text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
                </div>
                <div class="space-y-4">
                    <div>
                        <span class="font-semibold">Nombre del Equipo:</span> {{ $equipo->nombre }}
                    </div>
                    <div>
                        <span class="font-semibold">Capitán:</span> {{ $equipo->capitan ? $equipo->capitan->nombres . ' ' . $equipo->capitan->apellidos : '-' }}
                    </div>
                    <div>
                        <span class="font-semibold">Federación:</span> {{ $equipo->federacion ?? '-' }}
                    </div>
                    <div>
                        <span class="font-semibold">Notas:</span> {{ $equipo->notas ?? '-' }}
                    </div>
                    @if($equipo->logo)
                        <div>
                            <span class="font-semibold">Logo:</span><br>
                            <img src="{{ asset('storage/' . $equipo->logo) }}" alt="Logo del equipo" class="h-16 mt-2">
                        </div>
                    @endif
                    <div>
                        <span class="font-semibold">Jugadores:</span>
                        @if($equipo->jugadores->count())
                            <ul class="mt-2 space-y-2">
                                @foreach($equipo->jugadores as $jugador)
                                    <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b pb-2">
                                        <div>
                                            <span class="font-medium">{{ $jugador->miembro->nombres }} {{ $jugador->miembro->apellidos }}</span>
                                            <span class="text-gray-500">(Tablero {{ $jugador->tablero }})</span>
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            ELO: <span class="font-semibold">{{ $jugador->miembro->elo->elo ?? '-' }}</span> |
                                            FED: <span class="font-semibold">{{ $jugador->miembro->fide->fed_id ?? 'NCA' }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-400">Sin jugadores</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal AGREGAR JUGADOR -->
        <div id="modal-agregar-jugador-{{ $equipo->id }}" class="fixed inset-0 bg-gray-900 bg-opacity-40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md sm:max-w-lg md:max-w-2xl p-4 sm:p-8 relative flex flex-col max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Agregar jugador a {{ $equipo->nombre }}</h2>
                    <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-cerrar-agregar text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
                </div>
                <form method="POST" action="{{ route('equipos.addJugador', [$torneo->id, $equipo->id]) }}" id="form-agregar-jugador-{{ $equipo->id }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Jugador</label>
                        <select name="miembro_id" class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <option value="">-- Selecciona jugador --</option>
                            @foreach($miembrosDisponibles as $miembro)
                                @if(!$equipo->jugadores->pluck('miembro_id')->contains($miembro->cedula))
                                    <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Tablero</label>
                        <input name="tablero" type="number" min="1" class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required placeholder="Ej: 1">
                        <div class="text-xs text-red-500 mt-1 hidden" id="tablero-ocupado-{{ $equipo->id }}">Ese tablero ya está ocupado en este equipo.</div>
                    </div>
                    @if($equipo->jugadores->count() == 9)
                        <div class="mb-2 text-yellow-600 text-sm font-semibold">Este será el último jugador permitido para este equipo.</div>
                    @endif
                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-cerrar-agregar px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-semibold flex items-center gap-2" id="btn-agregar-jugador-{{ $equipo->id }}">
                            <i class="fas fa-user-plus"></i> Agregar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif
<script>
function abrirModalEditarEquipo(equipoId) {
    document.getElementById('modal-editar-equipo-' + equipoId).style.display = 'flex';
}
function cerrarModalEditarEquipo(equipoId) {
    document.getElementById('modal-editar-equipo-' + equipoId).style.display = 'none';
}
function abrirModalVerEquipo(equipoId) {
    document.getElementById('modal-ver-equipo-' + equipoId).style.display = 'flex';
}
function cerrarModalVerEquipo(equipoId) {
    document.getElementById('modal-ver-equipo-' + equipoId).style.display = 'none';
}
function abrirModalAgregarJugador(equipoId) {
    document.getElementById('modal-agregar-jugador-' + equipoId).style.display = 'flex';
}
function cerrarModalAgregarJugador(equipoId) {
    document.getElementById('modal-agregar-jugador-' + equipoId).style.display = 'none';
}
// Reemplaza el onclick del botón de ver
const verBtns = document.querySelectorAll('a[title="Ver"]');
verBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const equipoId = this.getAttribute('href').split('/').pop();
        abrirModalVerEquipo(equipoId);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Botones de editar equipo
    document.querySelectorAll('.btn-editar-equipo').forEach(btn => {
        btn.addEventListener('click', function() {
            const equipoId = this.getAttribute('data-equipo-id');
            abrirModalEditarEquipo(equipoId);
        });
    });

    // Botones de agregar jugador
    document.querySelectorAll('.btn-agregar-jugador').forEach(btn => {
        btn.addEventListener('click', function() {
            const equipoId = this.getAttribute('data-equipo-id');
            abrirModalAgregarJugador(equipoId);
        });
    });

    // Botones de cerrar modal editar
    document.querySelectorAll('.btn-cerrar-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const equipoId = this.getAttribute('data-equipo-id');
            cerrarModalEditarEquipo(equipoId);
        });
    });

    // Botones de cerrar modal ver
    document.querySelectorAll('.btn-cerrar-ver').forEach(btn => {
        btn.addEventListener('click', function() {
            const equipoId = this.getAttribute('data-equipo-id');
            cerrarModalVerEquipo(equipoId);
        });
    });

    // Botones de cerrar modal agregar
    document.querySelectorAll('.btn-cerrar-agregar').forEach(btn => {
        btn.addEventListener('click', function() {
            const equipoId = this.getAttribute('data-equipo-id');
            cerrarModalAgregarJugador(equipoId);
        });
    });
});
</script>
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

// Validación visual para evitar jugadores repetidos en selects del modal de equipos
function deshabilitarJugadoresRepetidos() {
    const selects = document.querySelectorAll('[name^="jugadores"][name$="[miembro_id]"]');
    const seleccionados = Array.from(selects).map(s => s.value).filter(v => v);
    selects.forEach(select => {
        Array.from(select.options).forEach(option => {
            if (option.value && seleccionados.includes(option.value) && select.value !== option.value) {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    });
}
document.addEventListener('change', function(e) {
    if (e.target && e.target.name && e.target.name.match(/^jugadores\[\d+\]\[miembro_id\]$/)) {
        deshabilitarJugadoresRepetidos();
    }
});
document.addEventListener('DOMContentLoaded', deshabilitarJugadoresRepetidos);
</script>
@endpush 