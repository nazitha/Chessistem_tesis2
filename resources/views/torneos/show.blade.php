@extends('layouts.app')

@php
    use App\Helpers\PermissionHelper;
    $estadoNoPermiteCambios = in_array($torneo->estado, ['Finalizado', 'Cancelado', 'Borrador']);
@endphp

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

    <!-- Detalles del Torneo -->
    <div class="card mb-6">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0 fw-bold fs-5 flex items-center">
                <span class="mr-2">📋</span> Detalles del Torneo
            </h6>
        </div>
        <div class="card-body py-3">
            <div class="space-y-6">
                <!-- Información del Torneo -->
                <div class="card mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold fs-5 flex items-center">
                            <span class="mr-2">🏁</span> Información del Torneo
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Nombre del Torneo</label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->nombre_torneo }}</div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">📅</span> Fecha de Inicio
                                </label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->fecha_inicio ? $torneo->fecha_inicio->format('d/m/Y') : 'No definida' }}</div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">🕒</span> Hora de Inicio
                                </label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->hora_inicio ? \Carbon\Carbon::parse($torneo->hora_inicio)->format('h:i A') : 'No definida' }}</div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">📍</span> Lugar
                                </label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->lugar ?? 'No definido' }}</div>
                            </div>


                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $torneo->estadoClase }}">{{ $torneo->estado }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipo Organizador -->
                <div class="card mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold fs-5 flex items-center">
                            <span class="mr-2">🧑‍⚖️</span> Equipo Organizador
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="space-y-4">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Organizador</label>
                                    <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->organizador ? $torneo->organizador->nombres . ' ' . $torneo->organizador->apellidos : 'No asignado' }}</div>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Director del Torneo</label>
                                    <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->directorTorneo ? $torneo->directorTorneo->nombres . ' ' . $torneo->directorTorneo->apellidos : 'No asignado' }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Árbitro</label>
                                    <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->arbitro ? $torneo->arbitro->nombres . ' ' . $torneo->arbitro->apellidos : 'No asignado' }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Árbitro Adjunto</label>
                                    <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->arbitroAdjunto ? $torneo->arbitroAdjunto->nombres . ' ' . $torneo->arbitroAdjunto->apellidos : 'No asignado' }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Árbitro Principal</label>
                                    <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->arbitroPrincipal ? $torneo->arbitroPrincipal->nombres . ' ' . $torneo->arbitroPrincipal->apellidos : 'No asignado' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalles Técnicos -->
                <div class="card mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold fs-5 flex items-center">
                            <span class="mr-2">📋</span> Detalles Técnicos
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">🔄</span> Número de Rondas
                                </label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->no_rondas }}</div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">🏆</span> Categoría
                                </label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->categoria ? $torneo->categoria->categoria_torneo : 'No definida' }}</div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">👥</span> Tipo de Torneo
                                </label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->es_por_equipos ? 'Por equipos' : 'Individual' }}</div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">⏱️</span> Control de Tiempo
                                </label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->controlTiempo ? $torneo->controlTiempo->formato . ' (' . $torneo->controlTiempo->control_tiempo . ')' : 'No definido' }}</div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">🧭</span> Sistema de Emparejamiento
                                </label>
                                <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">{{ $torneo->emparejamiento ? $torneo->emparejamiento->sistema : 'No definido' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Criterios de Desempate -->
                <div class="card mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold fs-5 flex items-center">
                            <span class="mr-2">🏆</span> Criterios de Desempate
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full {{ $torneo->usar_buchholz ? 'bg-green-500' : 'bg-gray-300' }} mr-3"></div>
                                <span class="text-sm text-gray-700">Buchholz: {{ $torneo->usar_buchholz ? 'Sí' : 'No' }}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full {{ $torneo->usar_sonneborn_berger ? 'bg-green-500' : 'bg-gray-300' }} mr-3"></div>
                                <span class="text-sm text-gray-700">Sonneborn-Berger: {{ $torneo->usar_sonneborn_berger ? 'Sí' : 'No' }}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full {{ $torneo->usar_desempate_progresivo ? 'bg-green-500' : 'bg-gray-300' }} mr-3"></div>
                                <span class="text-sm text-gray-700">Progresivo: {{ $torneo->usar_desempate_progresivo ? 'Sí' : 'No' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                    @if(!$estadoNoPermiteCambios)
                        <button type="button"
                                @click="open = true"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-users mr-2"></i>
                            Registrar Equipo
                        </button>
                    @endif
                    @if($torneo->es_por_equipos && $torneo->estado_torneo && !$torneo->torneo_cancelado && $torneo->equipos->count() >= 4 && !$estadoNoPermiteCambios)
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
                                    <button type="button" onclick="abrirModalVerEquipo('{{ $equipo->id }}')" title="Ver" class="inline-block text-blue-600 hover:text-blue-800 text-xl align-middle">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
                                    @if(!$estadoNoPermiteCambios)
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
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required placeholder="Ej: Caballos Negros">
                            @error('nombre')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Capitán</label>
                            <select name="capitan_id" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">-- Selecciona --</option>
                                @foreach($miembrosDisponibles as $miembro)
                                    <option value="{{ $miembro->cedula }}" {{ old('capitan_id') == $miembro->cedula ? 'selected' : '' }}>{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                @endforeach
                            </select>
                            @error('capitan_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Federación</label>
                            <input type="text" name="federacion" value="{{ old('federacion') }}" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Ej: FENACOAJ">
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
                        <textarea name="notas" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Observaciones, detalles, etc.">{{ old('notas') }}</textarea>
                        @error('notas')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div class="border-t pt-6 mt-2">
                        <label class="block text-base font-bold mb-2">Jugadores del Equipo <span class="text-red-500">*</span></label>
                        <div class="space-y-3">
                            @for($i = 0; $i < 4; $i++)
                                <div class="flex flex-col md:flex-row md:space-x-2 items-center">
                                    <select name="jugadores[{{ $i }}][miembro_id]" class="block w-full md:w-2/3 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                        <option value="">-- Selecciona jugador --</option>
                                        @foreach($miembrosDisponibles as $miembro)
                                            <option value="{{ $miembro->cedula }}" {{ old('jugadores.'.$i.'.miembro_id') == $miembro->cedula ? 'selected' : '' }}>
                                                {{ $miembro->nombres }} {{ $miembro->apellidos }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input name="jugadores[{{ $i }}][tablero]" type="number" min="1" max="4" value="{{ old('jugadores.'.$i.'.tablero') }}" class="block w-full md:w-1/3 mt-2 md:mt-0 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Tablero (1-4)" required>
                                </div>
                            @endfor
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

    @if(!$torneo->es_por_equipos && PermissionHelper::canViewModule('participantes'))
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
                @if(!$estadoNoPermiteCambios && PermissionHelper::canCreate('participantes'))
                <button type="button"
                        onclick="mostrarModalParticipantes()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-user-plus mr-2"></i>
                    Agregar Participantes
                </button>
                @endif
                @if(!$torneo->es_por_equipos && !$estadoNoPermiteCambios && $torneo->participantes->count() >= 4)
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

        <!-- Botón para mostrar controles de búsqueda -->
        <div class="px-6 pb-4">
            <div class="flex gap-2">
                <button id="btnMostrarBusquedaParticipantes" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
                <button id="btnExportarParticipantes" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    <i class="fas fa-download mr-2"></i>Exportar
                </button>
            </div>
        </div>

        <!-- Controles de búsqueda -->
        <div id="panelBusquedaParticipantes" class="mx-6 mb-4 bg-gray-50 shadow-md rounded-lg p-4 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Búsqueda de Participantes</h3>
                <button id="btnCancelarBusquedaParticipantes" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                    ✕
                </button>
            </div>
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                    <input type="text" id="buscarParticipantes" placeholder="Buscar por nombre, número, ELO..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button id="btnBuscarAvanzadaParticipantes" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                    </button>
                    <button id="btnLimpiarBusquedaParticipantes" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                        <i class="fas fa-brush mr-2"></i>Limpiar
                    </button>
                </div>
            </div>
            <!-- Panel de búsqueda avanzada -->
            <div id="panelBusquedaAvanzadaParticipantes" class="mt-4 p-4 bg-white rounded-md hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                        <input type="text" id="filtroNombreParticipante" placeholder="Filtrar por nombre" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número:</label>
                        <input type="text" id="filtroNumeroParticipante" placeholder="Filtrar por número" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Federación:</label>
                        <input type="text" id="filtroFederacionParticipante" placeholder="Filtrar por federación" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
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
                            @if($torneo->estado_torneo && !$torneo->torneo_cancelado && PermissionHelper::canDelete('participantes'))
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 w-24">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($torneo->participantes()->orderBy('numero_inicial')->get() as $index => $participante)
                            <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition-colors duration-150">
                                <td class="px-3 py-2 text-sm text-blue-600">{{ $participante->numero_inicial }}</td>
                                <td class="px-3 py-2 text-sm text-blue-600">
                                    {{ $participante->miembro->nombres }} {{ $participante->miembro->apellidos }}
                                </td>
                                <td class="px-3 py-2 text-sm text-right text-gray-900">
                                    {{ $participante->miembro->elo ?? '-' }}
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
                                                        {{ $partida->resultado == 1 ? '1' : ($partida->resultado == 0.5 ? '½' : ($partida->resultado === 0 ? '0' : '*')) }}
                                                    @else
                                                        {{ $partida->resultado == 0 ? '1' : ($partida->resultado == 0.5 ? '½' : ($partida->resultado == 1 ? '0' : '*')) }}
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
                                @if($torneo->estado_torneo && !$torneo->torneo_cancelado && PermissionHelper::canDelete('participantes'))
                                    <td class="px-3 py-2 text-sm text-center">
                                        <button type="button" 
                                                onclick="confirmarRetiroTorneo('{{ $participante->id }}', '{{ $participante->miembro->nombres }} {{ $participante->miembro->apellidos }}', '{{ $torneo->nombre_torneo }}')"
                                                class="text-red-600 hover:underline">
                                            Retirar
                                        </button>
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
            
            <!-- Controles de paginación -->
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex flex-col gap-4">
                    <!-- Selector de registros por página -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Mostrar:</span>
                            <select id="registrosPorPaginaParticipantes" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="text-sm text-gray-700">por página</span>
                        </div>
                        
                        <!-- Información de paginación -->
                        <div class="text-sm text-gray-700">
                            <span id="infoPaginacionParticipantes">Mostrando 1 a 10 de 0 resultados</span>
                        </div>
                    </div>
                    
                    <!-- Botones de paginación -->
                    <div class="flex justify-center">
                        <div class="relative z-0 inline-flex shadow-sm rounded-md">
                            <button id="btnAnteriorParticipantes" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed" aria-label="Página anterior">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <!-- Números de páginas -->
                            <div id="numerosPaginasParticipantes" class="flex items-center">
                                <!-- Se generan dinámicamente -->
                            </div>
                            
                            <button id="btnSiguienteParticipantes" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed" aria-label="Página siguiente">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
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
</div>

<!-- Modal de Agregar Participantes -->
@if(!$estadoNoPermiteCambios && PermissionHelper::canCreate('participantes'))
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
@endif

<!-- Tabla de Clasificación Final -->
@if($torneoFinalizado)
    <div class="max-w-7xl mx-auto mt-6 mb-10">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Clasificación Final
                </h3>
            </div>
            
            @if(!$torneo->es_por_equipos)
                <!-- Tabla para torneos individuales -->
                <div class="overflow-x-auto px-6 py-4">
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
                            @php
                                $posicion = 1;
                                $posicionMostrada = 1;
                                $puntosAnteriores = null;
                                $buchholzAnterior = null;
                                $sonnebornAnterior = null;
                                $progresivoAnterior = null;
                            @endphp
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
                                        {{ $participante->miembro->elo ?? '-' }}
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
            @else
                <!-- Tabla para torneos por equipos -->
                <div class="overflow-x-auto px-6 py-4">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 w-12">Pos.</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Equipo</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Capitán</th>
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
                            @php
                                $posicion = 1;
                                $posicionMostrada = 1;
                                $puntosAnteriores = null;
                                $buchholzAnterior = null;
                                $sonnebornAnterior = null;
                                $progresivoAnterior = null;
                            @endphp
                            @foreach($equipos as $equipo)
                                @php
                                    if ($puntosAnteriores !== $equipo->puntos_totales ||
                                        ($torneo->usar_buchholz && $buchholzAnterior !== $equipo->buchholz) ||
                                        ($torneo->usar_sonneborn_berger && $sonnebornAnterior !== $equipo->sonneborn) ||
                                        ($torneo->usar_desempate_progresivo && $progresivoAnterior !== $equipo->progresivo)) {
                                        $posicionMostrada = $posicion;
                                    }
                                    $puntosAnteriores = $equipo->puntos_totales;
                                    $buchholzAnterior = $equipo->buchholz;
                                    $sonnebornAnterior = $equipo->sonneborn;
                                    $progresivoAnterior = $equipo->progresivo;
                                @endphp
                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="px-3 py-2 text-sm font-medium">{{ $posicionMostrada }}</td>
                                    <td class="px-3 py-2 text-sm text-blue-600">{{ $equipo->nombre }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        {{ $equipo->capitan ? $equipo->capitan->nombres . ' ' . $equipo->capitan->apellidos : '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-right font-medium">
                                        {{ number_format($equipo->puntos_totales, 1) }}
                                    </td>
                                    @if($torneo->usar_buchholz)
                                        <td class="px-3 py-2 text-sm text-right text-gray-900">
                                            {{ number_format($equipo->buchholz, 2) }}
                                        </td>
                                    @endif
                                    @if($torneo->usar_sonneborn_berger)
                                        <td class="px-3 py-2 text-sm text-right text-gray-900">
                                            {{ number_format($equipo->sonneborn, 2) }}
                                        </td>
                                    @endif
                                    @if($torneo->usar_desempate_progresivo)
                                        <td class="px-3 py-2 text-sm text-right text-gray-900">
                                            {{ number_format($equipo->progresivo, 2) }}
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
            @endif
        </div>
    </div>
@endif

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
                                            ELO: <span class="font-semibold">{{ $jugador->miembro->elo ?? '-' }}</span> |
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
                                    <option value="{{ $miembro->cedula }}" {{ old('miembro_id') == $miembro->cedula ? 'selected' : '' }}>{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Tablero</label>
                        @php $tablerosUsados = $equipo->jugadores->pluck('tablero')->toArray(); @endphp
                        <select name="tablero" class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <option value="">-- Selecciona tablero --</option>
                            @for($t = 1; $t <= 6; $t++)
                                @if(!in_array($t, $tablerosUsados))
                                    <option value="{{ $t }}" {{ old('tablero') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endif
                            @endfor
                        </select>
                        @if(count($tablerosUsados) >= 6)
                            <div class="text-xs text-red-500 mt-1">No hay tableros disponibles (máximo 6).</div>
                        @endif
                    </div>
                    @if($equipo->jugadores->count() >= 6)
                        <div class="mb-2 text-red-600 text-sm font-semibold">Ya alcanzaste el máximo de 6 jugadores para este equipo.</div>
                        <div class="flex justify-end pt-2">
                            <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-cerrar-agregar px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold">Cerrar</button>
                        </div>
                    @else
                        @if($equipo->jugadores->count() >= 5)
                            <div class="mb-2 text-yellow-600 text-sm font-semibold">Solo puedes agregar 1 jugador adicional (máximo 6).</div>
                        @elseif($equipo->jugadores->count() >= 4)
                            <div class="mb-2 text-yellow-600 text-sm font-semibold">Puedes agregar hasta 2 jugadores adicionales (máximo 6).</div>
                        @endif
                    @endif
                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" data-equipo-id="{{ $equipo->id }}" class="btn-cerrar-agregar px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold">Cancelar</button>
                        <button type="submit" @if($equipo->jugadores->count() >= 6) disabled @endif class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-semibold flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" id="btn-agregar-jugador-{{ $equipo->id }}">
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

// Clase para manejar la tabla personalizada de participantes
class TablaParticipantesPersonalizada {
    constructor(tabla, config) {
        this.tabla = tabla;
        this.config = config;
        this.filasOriginales = Array.from(tabla.querySelectorAll('tbody tr'));
        this.filasFiltradas = [...this.filasOriginales];
        this.paginaActual = 1;
        this.registrosPorPagina = 10;
        this.inicializar();
    }

    inicializar() {
        // Configurar eventos de búsqueda
        const inputBusqueda = document.getElementById(this.config.inputBusqueda);
        if (inputBusqueda) {
            inputBusqueda.addEventListener('input', (e) => {
                this.filtrar(e.target.value);
            });
        }

        // Configurar eventos de filtros avanzados
        const filtros = ['filtroNombreParticipante', 'filtroNumeroParticipante', 'filtroFederacionParticipante'];
        filtros.forEach(filtro => {
            const elemento = document.getElementById(filtro);
            if (elemento) {
                elemento.addEventListener('input', () => this.aplicarFiltrosAvanzados());
                elemento.addEventListener('change', () => this.aplicarFiltrosAvanzados());
            }
        });

        // Configurar eventos de paginación
        const selectRegistros = document.getElementById(this.config.selectRegistros);
        if (selectRegistros) {
            // Sincronizar el selector con el valor actual
            selectRegistros.value = this.registrosPorPagina;
            
            selectRegistros.addEventListener('change', (e) => {
                this.registrosPorPagina = parseInt(e.target.value);
                this.paginaActual = 1;
                this.aplicarPaginacion();
            });
        }

        const btnAnterior = document.getElementById(this.config.btnAnterior);
        const btnSiguiente = document.getElementById(this.config.btnSiguiente);
        if (btnAnterior) btnAnterior.addEventListener('click', () => this.cambiarPagina(-1));
        if (btnSiguiente) btnSiguiente.addEventListener('click', () => this.cambiarPagina(1));

        // Configurar eventos de exportación
        const btnExportar = document.getElementById(this.config.btnExportar);
        if (btnExportar) {
            btnExportar.addEventListener('click', () => this.exportarDatos());
        }

        // Configurar eventos de búsqueda avanzada
        const btnBuscarAvanzada = document.getElementById(this.config.btnBuscarAvanzada);
        if (btnBuscarAvanzada) {
            btnBuscarAvanzada.addEventListener('click', () => this.toggleBusquedaAvanzada());
        }

        // Configurar eventos de limpiar
        const btnLimpiar = document.getElementById(this.config.btnLimpiar);
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => this.limpiarFiltros());
        }

        // Configurar eventos de mostrar/cancelar búsqueda
        const btnMostrarBusqueda = document.getElementById(this.config.btnMostrarBusqueda);
        const btnCancelarBusqueda = document.getElementById(this.config.btnCancelarBusqueda);
        if (btnMostrarBusqueda) {
            btnMostrarBusqueda.addEventListener('click', () => this.mostrarPanelBusqueda());
        }
        if (btnCancelarBusqueda) {
            btnCancelarBusqueda.addEventListener('click', () => this.cancelarBusqueda());
        }

        // Verificar estado del botón exportar y aplicar paginación inicial
        this.verificarEstadoExportar();
        this.aplicarPaginacion();
    }

    filtrar(texto) {
        const textoLower = texto.toLowerCase();
        this.filasFiltradas = this.filasOriginales.filter(fila => {
            const textoFila = fila.textContent.toLowerCase();
            return textoFila.includes(textoLower);
        });
        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }

    aplicarFiltrosAvanzados() {
        const filtroNombre = document.getElementById('filtroNombreParticipante')?.value.toLowerCase() || '';
        const filtroNumero = document.getElementById('filtroNumeroParticipante')?.value.toLowerCase() || '';
        const filtroFederacion = document.getElementById('filtroFederacionParticipante')?.value.toLowerCase() || '';

        this.filasFiltradas = this.filasOriginales.filter(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length === 0) return false;

            const numero = celdas[0]?.textContent.toLowerCase() || '';
            const nombre = celdas[1]?.textContent.toLowerCase() || '';
            const federacion = celdas[3]?.textContent.toLowerCase() || '';

            const cumpleNombre = !filtroNombre || nombre.includes(filtroNombre);
            const cumpleNumero = !filtroNumero || numero.includes(filtroNumero);
            const cumpleFederacion = !filtroFederacion || federacion.includes(filtroFederacion);

            return cumpleNombre && cumpleNumero && cumpleFederacion;
        });

        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }

    mostrarPanelBusqueda() {
        const panel = document.getElementById(this.config.panelBusqueda);
        if (panel) panel.classList.remove('hidden');
    }

    cancelarBusqueda() {
        const panel = document.getElementById(this.config.panelBusqueda);
        if (panel) panel.classList.add('hidden');
        this.limpiarFiltros();
    }

    toggleBusquedaAvanzada() {
        const panel = document.getElementById(this.config.panelBusquedaAvanzada);
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }

    limpiarFiltros() {
        // Limpiar inputs de búsqueda
        const inputBusqueda = document.getElementById(this.config.inputBusqueda);
        if (inputBusqueda) inputBusqueda.value = '';

        // Limpiar filtros avanzados
        const filtros = ['filtroNombreParticipante', 'filtroNumeroParticipante', 'filtroFederacionParticipante'];
        filtros.forEach(filtro => {
            const elemento = document.getElementById(filtro);
            if (elemento) elemento.value = '';
        });

        // Ocultar panel de búsqueda avanzada
        const panel = document.getElementById(this.config.panelBusquedaAvanzada);
        if (panel) panel.classList.add('hidden');

        // Restaurar todas las filas
        this.filasFiltradas = [...this.filasOriginales];
        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }

    cambiarPagina(direccion) {
        const totalPaginas = Math.ceil(this.filasFiltradas.length / this.registrosPorPagina);
        this.paginaActual = Math.max(1, Math.min(totalPaginas, this.paginaActual + direccion));
        this.aplicarPaginacion();
    }

    aplicarPaginacion() {
        const inicio = (this.paginaActual - 1) * this.registrosPorPagina;
        const fin = inicio + this.registrosPorPagina;
        const filasAMostrar = this.filasFiltradas.slice(inicio, fin);

        // Ocultar todas las filas
        this.filasOriginales.forEach(fila => {
            fila.style.display = 'none';
        });

        // Mostrar solo las filas de la página actual
        filasAMostrar.forEach(fila => {
            fila.style.display = '';
        });

        // Actualizar información de paginación
        const totalPaginas = Math.ceil(this.filasFiltradas.length / this.registrosPorPagina);
        const infoPaginacion = document.getElementById(this.config.infoPaginacion);
        if (infoPaginacion) {
            const inicio = (this.paginaActual - 1) * this.registrosPorPagina + 1;
            const fin = Math.min(this.paginaActual * this.registrosPorPagina, this.filasFiltradas.length);
            const total = this.filasFiltradas.length;
            infoPaginacion.textContent = `Mostrando ${inicio} a ${fin} de ${total} resultados`;
        }

        // Actualizar estado de botones
        const btnAnterior = document.getElementById(this.config.btnAnterior);
        const btnSiguiente = document.getElementById(this.config.btnSiguiente);
        if (btnAnterior) btnAnterior.disabled = this.paginaActual === 1;
        if (btnSiguiente) btnSiguiente.disabled = this.paginaActual === totalPaginas;
        
        // Generar números de páginas
        this.generarNumerosPaginas(totalPaginas);
        
        // Verificar estado del botón exportar
        this.verificarEstadoExportar();
        
        // Sincronizar el selector de registros por página
        this.sincronizarSelectorRegistros();
    }

    generarNumerosPaginas(totalPaginas) {
        const contenedor = document.getElementById('numerosPaginasParticipantes');
        if (!contenedor) return;

        // Limpiar números anteriores
        contenedor.innerHTML = '';

        if (totalPaginas <= 0) return;

        // Calcular rango de páginas a mostrar (máximo 5 páginas)
        const maxPaginasVisibles = 5;
        let inicioRango = Math.max(1, this.paginaActual - Math.floor(maxPaginasVisibles / 2));
        let finRango = Math.min(totalPaginas, inicioRango + maxPaginasVisibles - 1);

        // Ajustar inicio si estamos cerca del final
        if (finRango - inicioRango + 1 < maxPaginasVisibles) {
            inicioRango = Math.max(1, finRango - maxPaginasVisibles + 1);
        }

        // Mostrar "..." al inicio si es necesario
        if (inicioRango > 1) {
            this.crearNumeroPagina(1, false);
            if (inicioRango > 2) {
                this.crearPuntosSuspensivos();
            }
        }

        // Mostrar números de páginas
        for (let i = inicioRango; i <= finRango; i++) {
            this.crearNumeroPagina(i, i === this.paginaActual);
        }

        // Mostrar "..." al final si es necesario
        if (finRango < totalPaginas) {
            if (finRango < totalPaginas - 1) {
                this.crearPuntosSuspensivos();
            }
            this.crearNumeroPagina(totalPaginas, false);
        }
    }

    crearNumeroPagina(numero, esActual) {
        const contenedor = document.getElementById('numerosPaginasParticipantes');
        const boton = document.createElement('button');
        
        if (esActual) {
            // Página actual: fondo gris claro para distinguirla
            boton.className = 'relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 cursor-default leading-5';
        } else {
            // Página normal: estilo con hover y focus
            boton.className = 'relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150';
        }
        
        boton.textContent = numero;
        boton.onclick = () => this.irAPagina(numero);
        boton.setAttribute('aria-label', `Ir a la página ${numero}`);
        
        contenedor.appendChild(boton);
    }

    crearPuntosSuspensivos() {
        const contenedor = document.getElementById('numerosPaginasParticipantes');
        const puntos = document.createElement('span');
        puntos.className = 'relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5';
        puntos.textContent = '...';
        puntos.setAttribute('aria-disabled', 'true');
        contenedor.appendChild(puntos);
    }

    irAPagina(numero) {
        this.paginaActual = numero;
        this.aplicarPaginacion();
    }

    sincronizarSelectorRegistros() {
        const selectRegistros = document.getElementById(this.config.selectRegistros);
        if (selectRegistros) {
            selectRegistros.value = this.registrosPorPagina;
        }
    }

    verificarEstadoExportar() {
        const btnExportar = document.getElementById(this.config.btnExportar);
        if (btnExportar) {
            let tieneRegistros = false;
            
            // Si hay filtros aplicados, verificar las filas filtradas
            if (this.filasFiltradas.length !== this.filasOriginales.length) {
                tieneRegistros = this.filasFiltradas.length > 0;
            } else {
                // Si no hay filtros, verificar las filas originales en el DOM
                const filasEnTabla = this.tabla.querySelectorAll('tbody tr');
                tieneRegistros = Array.from(filasEnTabla).some(fila => {
                    // Excluir filas que contengan mensajes como "No hay participantes registrados"
                    const textoFila = fila.textContent.toLowerCase();
                    return !textoFila.includes('no hay') && !textoFila.includes('registrados') && !textoFila.includes('registradas');
                });
            }
            
            btnExportar.disabled = !tieneRegistros;
            
            if (!tieneRegistros) {
                btnExportar.classList.add('opacity-50', 'cursor-not-allowed');
                btnExportar.title = 'No hay registros para exportar';
            } else {
                btnExportar.classList.remove('opacity-50', 'cursor-not-allowed');
                btnExportar.title = 'Exportar registros';
            }
        }
    }

    exportarDatos() {
        console.log('🔥 EXPORTAR DATOS LLAMADO');
        
        // Mostrar spinner y deshabilitar botón
        const btnExportar = document.getElementById(this.config.btnExportar);
        if (btnExportar) {
            const originalText = btnExportar.innerHTML;
            btnExportar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exportando...';
            btnExportar.disabled = true;
            
            console.log('📝 Creando formulario para exportación');
            
            // Crear formulario dinámicamente para exportación
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("participantes.export") }}';
            form.target = '_self';
            console.log('URL del formulario:', form.action);
            
            document.body.appendChild(form);
            console.log('📤 Enviando formulario...');
            
            // Enviar formulario
            form.submit();
            
            // Limpiar formulario
            document.body.removeChild(form);
            
            // Restaurar botón después de un tiempo
            setTimeout(() => {
                btnExportar.innerHTML = originalText;
                btnExportar.disabled = false;
            }, 2000);
        }
    }
}

// Inicializar tabla personalizada para participantes cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tabla personalizada para participantes
    const tablaParticipantes = document.querySelector('.bg-white.shadow.overflow-hidden.sm\\:rounded-lg.mb-6.mt-8 table');
    if (tablaParticipantes) {
        new TablaParticipantesPersonalizada(tablaParticipantes, {
            inputBusqueda: 'buscarParticipantes',
            panelBusqueda: 'panelBusquedaParticipantes',
            panelBusquedaAvanzada: 'panelBusquedaAvanzadaParticipantes',
            btnMostrarBusqueda: 'btnMostrarBusquedaParticipantes',
            btnCancelarBusqueda: 'btnCancelarBusquedaParticipantes',
            btnBuscarAvanzada: 'btnBuscarAvanzadaParticipantes',
            btnLimpiar: 'btnLimpiarBusquedaParticipantes',
            btnExportar: 'btnExportarParticipantes',
            selectRegistros: 'registrosPorPaginaParticipantes',
            btnAnterior: 'btnAnteriorParticipantes',
            btnSiguiente: 'btnSiguienteParticipantes',
            infoPaginacion: 'infoPaginacionParticipantes'
        });
    }
});

// Función para confirmar retiro de participante con SweetAlert
function confirmarRetiroTorneo(participanteId, nombreMiembro, nombreTorneo) {
    Swal.fire({
        title: '¿Retirar participante?',
        html: `
            <div class="text-left">
                <p class="mb-2"><strong>Miembro:</strong> ${nombreMiembro}</p>
                <p class="mb-4"><strong>Torneo:</strong> ${nombreTorneo}</p>
                <p class="text-sm text-gray-600">Esta acción retirará al participante del torneo y no se puede deshacer.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Retirar',
        cancelButtonText: 'Cancelar',
        focusCancel: true,
        customClass: {
            popup: 'swal-popup-custom',
            title: 'swal-title-custom',
            htmlContainer: 'swal-html-custom',
            confirmButton: 'swal-confirm-custom',
            cancelButton: 'swal-cancel-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear y enviar el formulario
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('torneos.participantes.destroy', [$torneo->id, 'PARTICIPANTE_ID']) }}`.replace('PARTICIPANTE_ID', participanteId);
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<style>
/* Estilos personalizados para SweetAlert */
.swal-popup-custom {
    border-radius: 12px !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

.swal-title-custom {
    color: #1f2937 !important;
    font-size: 1.5rem !important;
    font-weight: 600 !important;
    margin-bottom: 1rem !important;
}

.swal-html-custom {
    color: #374151 !important;
    line-height: 1.5 !important;
}

.swal-confirm-custom {
    background-color: #2563eb !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 0.5rem 1rem !important;
    font-weight: 500 !important;
    transition: background-color 0.2s !important;
}

.swal-confirm-custom:hover {
    background-color: #1d4ed8 !important;
}

.swal-cancel-custom {
    background-color: #dc2626 !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 0.5rem 1rem !important;
    font-weight: 500 !important;
    transition: background-color 0.2s !important;
}

.swal-cancel-custom:hover {
    background-color: #b91c1c !important;
}
</style>
@endpush 