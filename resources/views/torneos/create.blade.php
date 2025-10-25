@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Crear Nuevo Torneo</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Complete la información del torneo. Los campos marcados con * son obligatorios.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('torneos.store') }}" method="POST" id="formCrearTorneo" class="needs-validation" novalidate>
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
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
                                        <label for="nombre_torneo" class="block text-sm font-medium text-gray-700">
                                            Nombre del Torneo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="nombre_torneo" id="nombre_torneo" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 @error('nombre_torneo') is-invalid @enderror"
                                               placeholder="Ejemplo: Torneo Nacional de Ajedrez 2024"
                                               required>
                                        @error('nombre_torneo')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor ingrese el nombre del torneo.
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">📅</span> Fecha de Inicio <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 @error('fecha_inicio') is-invalid @enderror"
                                               required>
                                        @error('fecha_inicio')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione la fecha de inicio.
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="hora_inicio" class="block text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">🕒</span> Hora de Inicio <span class="text-danger">*</span>
                                        </label>
                                        <input type="time" name="hora_inicio" id="hora_inicio" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 @error('hora_inicio') is-invalid @enderror"
                                               required>
                                        @error('hora_inicio')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione la hora de inicio.
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="lugar" class="block text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">📍</span> Lugar <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="lugar" id="lugar" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 @error('lugar') is-invalid @enderror"
                                               placeholder="Ejemplo: Biblioteca Nacional, Managua"
                                               required>
                                        @error('lugar')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor ingrese el lugar del torneo.
                                            </div>
                                        @enderror
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
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="organizador_id" class="block text-sm font-medium text-gray-700">Organizador <span class="text-danger">*</span></label>
                                        <select name="organizador_id" id="organizador_id" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('organizador_id') is-invalid @enderror"
                                                required>
                                            <option value="">Seleccione un organizador</option>
                                            @foreach($miembros as $miembro)
                                                <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                            @endforeach
                                        </select>
                                        @error('organizador_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione un organizador.
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="director_torneo_id" class="block text-sm font-medium text-gray-700">Director del Torneo <span class="text-danger">*</span></label>
                                        <select name="director_torneo_id" id="director_torneo_id" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('director_torneo_id') is-invalid @enderror"
                                                required>
                                            <option value="">Seleccione un director</option>
                                            @foreach($miembros as $miembro)
                                                <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                            @endforeach
                                        </select>
                                        @error('director_torneo_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione un director del torneo.
                                            </div>
                                        @enderror
                                    </div>

                                </div>

                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="arbitro_id" class="block text-sm font-medium text-gray-700">Árbitro <span class="text-danger">*</span></label>
                                        <select name="arbitro_id" id="arbitro_id" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('arbitro_id') is-invalid @enderror"
                                                required>
                                            <option value="">Seleccione un árbitro</option>
                                            @foreach($miembros as $miembro)
                                                <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                            @endforeach
                                        </select>
                                        @error('arbitro_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione un árbitro.
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="arbitro_adjunto_id" class="block text-sm font-medium text-gray-700">Árbitro Adjunto <span class="text-danger">*</span></label>
                                        <select name="arbitro_adjunto_id" id="arbitro_adjunto_id" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('arbitro_adjunto_id') is-invalid @enderror"
                                                required>
                                            <option value="">Seleccione un árbitro adjunto</option>
                                            @foreach($miembros as $miembro)
                                                <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                            @endforeach
                                        </select>
                                        @error('arbitro_adjunto_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione un árbitro adjunto.
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="arbitro_principal_id" class="block text-sm font-medium text-gray-700">Árbitro Principal <span class="text-danger">*</span></label>
                                        <select name="arbitro_principal_id" id="arbitro_principal_id" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('arbitro_principal_id') is-invalid @enderror"
                                                required>
                                            <option value="">Seleccione un árbitro principal</option>
                                            @foreach($miembros as $miembro)
                                                <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                            @endforeach
                                        </select>
                                        @error('arbitro_principal_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione un árbitro principal.
                                            </div>
                                        @enderror
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
                                        <label for="no_rondas" class="block text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">🔄</span> Número de Rondas <span class="text-danger">*</span>
                                            <span class="ml-1 text-gray-500 text-xs">(Mínimo 3, máximo 9)</span>
                                        </label>
                                        <input type="number" name="no_rondas" id="no_rondas" min="3" max="9"
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md py-2 @error('no_rondas') is-invalid @enderror"
                                               style="-moz-appearance: textfield;"
                                               required>
                                        @error('no_rondas')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor ingrese el número de rondas (3-9).
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="categoriaTorneo_id" class="block text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">🏆</span> Categoría <span class="text-danger">*</span>
                                        </label>
                                        <select name="categoriaTorneo_id" id="categoriaTorneo_id" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('categoriaTorneo_id') is-invalid @enderror"
                                                required>
                                            <option value="">Seleccione una categoría</option>
                                            @foreach($categorias as $categoria)
                                                <option value="{{ $categoria->id_torneo_categoria }}">{{ $categoria->categoria_torneo }}</option>
                                            @endforeach
                                        </select>
                                        @error('categoriaTorneo_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione una categoría.
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="sistema_emparejamiento_id" class="block text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">🧭</span> Sistema de Emparejamiento <span class="text-danger">*</span>
                                        </label>
                                        <select name="sistema_emparejamiento_id" id="sistema_emparejamiento_id" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('sistema_emparejamiento_id') is-invalid @enderror"
                                                required>
                                            <option value="">Seleccione un sistema</option>
                                            @foreach($emparejamientos as $sistema)
                                                <option value="{{ $sistema->id_emparejamiento }}">{{ $sistema->sistema }}</option>
                                            @endforeach
                                        </select>
                                        @error('sistema_emparejamiento_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione un sistema de emparejamiento.
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="control_tiempo_id" class="block text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">⏱️</span> Control de Tiempo <span class="text-danger">*</span>
                                        </label>
                                        <select name="control_tiempo_id" id="control_tiempo_id" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('control_tiempo_id') is-invalid @enderror"
                                                required>
                                            <option value="">Seleccione un control de tiempo</option>
                                            @foreach($controlesTiempo as $control)
                                                <option value="{{ $control->id_control_tiempo }}">{{ $control->formato }} ({{ $control->control_tiempo }})</option>
                                            @endforeach
                                        </select>
                                        @error('control_tiempo_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Por favor seleccione un control de tiempo.
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="es_por_equipos" class="block text-sm font-medium text-gray-700 flex items-center">
                                            <span class="mr-2">👥</span> ¿Torneo por equipos?
                                        </label>
                                        <select name="es_por_equipos" id="es_por_equipos"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                required>
                                            <option value="0" selected>Individual</option>
                                            <option value="1">Por equipos</option>
                                        </select>
                                        @error('es_por_equipos')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Criterios de Desempate -->
                        <div class="card mb-4">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold fs-5 flex items-center">
                                    <span class="mr-2">🏆</span> Criterios de Desempate
                                    <button type="button" class="ml-2 text-gray-400 hover:text-gray-500" 
                                            data-tooltip-target="criterios-info">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="usar_buchholz" id="usar_buchholz" value="1"
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        <label for="usar_buchholz" class="ml-2 block text-sm text-gray-700">Buchholz</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="usar_sonneborn_berger" id="usar_sonneborn_berger" value="1"
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        <label for="usar_sonneborn_berger" class="ml-2 block text-sm text-gray-700">Sonneborn-Berger</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="usar_desempate_progresivo" id="usar_desempate_progresivo" value="1"
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        <label for="usar_desempate_progresivo" class="ml-2 block text-sm text-gray-700">Progresivo</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos ocultos con valores por defecto -->
                        <input type="hidden" name="estado_torneo" value="1">
                        <input type="hidden" name="permitir_bye" value="1">
                        <input type="hidden" name="alternar_colores" value="1">
                        <input type="hidden" name="evitar_emparejamientos_repetidos" value="1">
                        <input type="hidden" name="maximo_emparejamientos_repetidos" value="1">
                        <input type="hidden" name="numero_minimo_participantes" value="4">
                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                        <button type="button" id="btnGuardarBorrador"
                                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Guardar como borrador
                        </button>
                        <button type="button" id="btnVistaPrevia"
                                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Vista previa
                        </button>
                        <a href="{{ route('torneos.index') }}" 
                           class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Crear Torneo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tooltip para criterios de desempate -->
<div id="criterios-info" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
    <div class="p-2">
        <p class="font-bold mb-2">Criterios de Desempate:</p>
        <ul class="list-disc pl-4 space-y-1">
            <li>Buchholz: Suma de los puntos de los oponentes</li>
            <li>Sonneborn-Berger: Suma de puntos de oponentes vencidos + mitad de puntos de oponentes empatados</li>
            <li>Progresivo: Suma acumulativa de puntos ronda a ronda</li>
        </ul>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div id="modalVistaPrevia" class="fixed inset-0 bg-gray-900 bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 relative">
        <h2 class="text-2xl font-bold mb-4">Vista previa del torneo</h2>
        <table class="w-full text-sm mb-4">
            <tbody>
                <tr><td class="font-semibold pr-2">Nombre:</td><td id="prev-nombre"></td></tr>
                <tr><td class="font-semibold pr-2">Fecha de inicio:</td><td id="prev-fecha"></td></tr>
                <tr><td class="font-semibold pr-2">Hora de inicio:</td><td id="prev-hora"></td></tr>
                <tr><td class="font-semibold pr-2">Lugar:</td><td id="prev-lugar"></td></tr>
                <tr><td class="font-semibold pr-2">Organizador:</td><td id="prev-organizador"></td></tr>
                <tr><td class="font-semibold pr-2">Director:</td><td id="prev-director"></td></tr>
                <tr><td class="font-semibold pr-2">Árbitro Principal:</td><td id="prev-arbitro-principal"></td></tr>
                <tr><td class="font-semibold pr-2">Árbitro:</td><td id="prev-arbitro"></td></tr>
                <tr><td class="font-semibold pr-2">Árbitro Adjunto:</td><td id="prev-arbitro-adjunto"></td></tr>
                <tr><td class="font-semibold pr-2">Categoría:</td><td id="prev-categoria"></td></tr>
                <tr><td class="font-semibold pr-2">N° Rondas:</td><td id="prev-rondas"></td></tr>
                <tr><td class="font-semibold pr-2">Sistema de Emparejamiento:</td><td id="prev-sistema"></td></tr>
                <tr><td class="font-semibold pr-2">Control de Tiempo:</td><td id="prev-control"></td></tr>
                <tr><td class="font-semibold pr-2">Criterios de Desempate:</td><td id="prev-desempate"></td></tr>
            </tbody>
        </table>
        <div class="flex justify-end space-x-2">
            <button type="button" onclick="cerrarModalVistaPrevia()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 font-semibold">Cerrar</button>
        </div>
        <button onclick="cerrarModalVistaPrevia()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
    </div>
</div>
@endsection

@push('styles')
<style>
.tooltip {
    position: absolute;
    z-index: 1070;
    display: block;
    margin: 0;
    font-family: inherit;
    font-style: normal;
    font-weight: 400;
    line-height: 1.5;
    text-align: left;
    text-decoration: none;
    text-shadow: none;
    text-transform: none;
    letter-spacing: normal;
    word-break: normal;
    word-spacing: normal;
    white-space: normal;
    line-break: auto;
    font-size: .875rem;
    word-wrap: break-word;
    opacity: 0;
}

/* Ocultar botones de incremento/decremento en inputs number */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}

/* Definir bordes más claros para inputs */
input[type="text"], 
input[type="date"], 
input[type="time"], 
input[type="number"] {
    border: 1px solid #d1d5db !important;
    border-radius: 0.375rem;
    padding-left: 12px !important;
}

/* Altura específica para número de rondas - igual que los selects */
input#no_rondas {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
}

input[type="text"]:focus, 
input[type="date"]:focus, 
input[type="time"]:focus, 
input[type="number"]:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de fechas
    const fechaInicio = document.getElementById('fecha_inicio');
    const horaInicio = document.getElementById('hora_inicio');
    const formCrearTorneo = document.getElementById('formCrearTorneo');

    // Establecer fecha mínima como hoy
    fechaInicio.min = new Date().toISOString().split('T')[0];
    horaInicio.value = '09:00';

    // Validación en tiempo real
    const campos = ['nombre_torneo', 'lugar', 'no_rondas'];
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        const error = document.getElementById('error_' + campo);
        
        elemento.addEventListener('input', function() {
            if (!this.value) {
                error.textContent = 'Este campo es obligatorio';
                error.classList.remove('hidden');
                this.classList.add('border-red-500');
            } else {
                error.classList.add('hidden');
                this.classList.remove('border-red-500');
            }
        });
    });

    // Validación específica para número de rondas
    const noRondasInput = document.getElementById('no_rondas');
    if (noRondasInput) {
        noRondasInput.addEventListener('input', function() {
            let value = this.value;
            
            // Solo permitir números
            value = value.replace(/[^0-9]/g, '');
            
            // Limitar a un solo dígito (3-9)
            if (value.length > 1) {
                value = value.slice(0, 1);
            }
            
            // Validar rango
            if (value && (parseInt(value) < 3 || parseInt(value) > 9)) {
                value = '';
            }
            
            this.value = value;
        });

        // Prevenir entrada de caracteres no numéricos
        noRondasInput.addEventListener('keydown', function(e) {
            // Permitir teclas de control (backspace, delete, tab, escape, enter)
            if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                // Permitir Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            // Asegurar que es un número y está en el rango 3-9
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        // Prevenir pegado de texto no numérico
        noRondasInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numericValue = paste.replace(/[^0-9]/g, '');
            if (numericValue && parseInt(numericValue) >= 3 && parseInt(numericValue) <= 9) {
                this.value = numericValue;
            }
        });
    }

    // Vista previa mejorada
    document.getElementById('btnVistaPrevia').addEventListener('click', function() {
        // Obtener valores del formulario
        const getText = (id) => {
            const el = document.getElementById(id);
            return el ? el.options ? el.options[el.selectedIndex]?.text : el.value : '';
        };
        document.getElementById('prev-nombre').textContent = getText('nombre_torneo');
        document.getElementById('prev-fecha').textContent = getText('fecha_inicio');
        document.getElementById('prev-hora').textContent = getText('hora_inicio');
        document.getElementById('prev-lugar').textContent = getText('lugar');
        document.getElementById('prev-organizador').textContent = getText('organizador_id');
        document.getElementById('prev-director').textContent = getText('director_torneo_id');
        document.getElementById('prev-arbitro-principal').textContent = getText('arbitro_principal_id');
        document.getElementById('prev-arbitro').textContent = getText('arbitro_id');
        document.getElementById('prev-arbitro-adjunto').textContent = getText('arbitro_adjunto_id');
        document.getElementById('prev-categoria').textContent = getText('categoriaTorneo_id');
        document.getElementById('prev-rondas').textContent = getText('no_rondas');
        document.getElementById('prev-sistema').textContent = getText('sistema_emparejamiento_id');
        document.getElementById('prev-control').textContent = getText('control_tiempo_id');
        // Criterios de desempate
        let desempate = [];
        if(document.getElementById('usar_buchholz').checked) desempate.push('Buchholz');
        if(document.getElementById('usar_sonneborn_berger').checked) desempate.push('Sonneborn-Berger');
        if(document.getElementById('usar_desempate_progresivo').checked) desempate.push('Progresivo');
        document.getElementById('prev-desempate').textContent = desempate.length ? desempate.join(', ') : 'Ninguno';
        // Mostrar modal
        document.getElementById('modalVistaPrevia').classList.remove('hidden');
        document.getElementById('modalVistaPrevia').classList.add('flex');
    });
    window.cerrarModalVistaPrevia = function() {
        document.getElementById('modalVistaPrevia').classList.add('hidden');
        document.getElementById('modalVistaPrevia').classList.remove('flex');
    }

    // Guardar como borrador
    document.getElementById('btnGuardarBorrador').addEventListener('click', function() {
        const form = document.getElementById('formCrearTorneo');
        let input = document.getElementById('input_borrador');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'borrador';
            input.id = 'input_borrador';
            form.appendChild(input);
        }
        input.value = '1';
        form.submit();
    });

    // Tooltip para criterios de desempate
    const tooltip = document.getElementById('criterios-info');
    const tooltipButton = document.querySelector('[data-tooltip-target="criterios-info"]');

    tooltipButton.addEventListener('mouseenter', () => {
        tooltip.classList.remove('invisible', 'opacity-0');
        const rect = tooltipButton.getBoundingClientRect();
        tooltip.style.top = `${rect.bottom + 5}px`;
        tooltip.style.left = `${rect.left}px`;
    });

    tooltipButton.addEventListener('mouseleave', () => {
        tooltip.classList.add('invisible', 'opacity-0');
    });
    
    // Validación de Bootstrap
    (function() {
        'use strict';
        
        // Obtener todos los formularios que necesitan validación
        const forms = document.querySelectorAll('.needs-validation');
        
        // Iterar sobre cada formulario
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    })();
});
</script>
@endpush 