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
                <div class="mt-4 space-y-4">
                    <button type="button" id="btnDuplicarTorneo" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Duplicar torneo anterior
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('torneos.store') }}" method="POST" id="formCrearTorneo">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <!-- Información del Torneo -->
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                                <span class="mr-2">🏁</span> Información del Torneo
                            </h3>
                        </div>

                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="nombre_torneo" class="block text-sm font-medium text-gray-700">
                                    Nombre del Torneo *
                                </label>
                                <input type="text" name="nombre_torneo" id="nombre_torneo" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Ejemplo: Torneo Nacional de Ajedrez 2024"
                                       required>
                                <div class="text-xs text-red-600 hidden" id="error_nombre_torneo"></div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">📅</span> Fecha de Inicio *
                                </label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       required>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="hora_inicio" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">🕒</span> Hora de Inicio *
                                </label>
                                <input type="time" name="hora_inicio" id="hora_inicio" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       required>
                            </div>

                            <div class="col-span-6">
                                <label for="lugar" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">📍</span> Lugar *
                                </label>
                                <input type="text" name="lugar" id="lugar" 
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Ejemplo: Biblioteca Nacional, Managua"
                                       required>
                            </div>
                        </div>

                        <!-- Equipo Organizador -->
                        <div class="mt-8 border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                                <span class="mr-2">🧑‍⚖️</span> Equipo Organizador
                            </h3>
                        </div>

                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="organizador_id" class="block text-sm font-medium text-gray-700">Organizador *</label>
                                <select name="organizador_id" id="organizador_id" 
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>
                                    <option value="">Seleccione un organizador</option>
                                    @foreach($miembros as $miembro)
                                        <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="director_torneo_id" class="block text-sm font-medium text-gray-700">Director del Torneo *</label>
                                <select name="director_torneo_id" id="director_torneo_id" 
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>
                                    <option value="">Seleccione un director</option>
                                    @foreach($miembros as $miembro)
                                        <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="arbitro_principal_id" class="block text-sm font-medium text-gray-700">Árbitro Principal *</label>
                                <select name="arbitro_principal_id" id="arbitro_principal_id" 
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>
                                    <option value="">Seleccione un árbitro principal</option>
                                    @foreach($miembros as $miembro)
                                        <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="arbitro_id" class="block text-sm font-medium text-gray-700">Árbitro *</label>
                                <select name="arbitro_id" id="arbitro_id" 
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>
                                    <option value="">Seleccione un árbitro</option>
                                    @foreach($miembros as $miembro)
                                        <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="arbitro_adjunto_id" class="block text-sm font-medium text-gray-700">Árbitro Adjunto *</label>
                                <select name="arbitro_adjunto_id" id="arbitro_adjunto_id" 
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>
                                    <option value="">Seleccione un árbitro adjunto</option>
                                    @foreach($miembros as $miembro)
                                        <option value="{{ $miembro->cedula }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Detalles Técnicos -->
                        <div class="mt-8 border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                                <span class="mr-2">📋</span> Detalles Técnicos
                            </h3>
                        </div>

                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="no_rondas" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">🔄</span> Número de Rondas *
                                    <span class="ml-1 text-gray-500 text-xs">(Mínimo 3, máximo 9)</span>
                                </label>
                                <input type="number" name="no_rondas" id="no_rondas" min="3" max="9"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       required>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="categoriaTorneo_id" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">🏆</span> Categoría *
                                </label>
                                <select name="categoriaTorneo_id" id="categoriaTorneo_id" 
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id_torneo_categoria }}">{{ $categoria->categoria_torneo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="sistema_emparejamiento_id" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">🧭</span> Sistema de Emparejamiento *
                                </label>
                                <select name="sistema_emparejamiento_id" id="sistema_emparejamiento_id" 
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>
                                    <option value="">Seleccione un sistema</option>
                                    @foreach($emparejamientos as $sistema)
                                        <option value="{{ $sistema->id_emparejamiento }}">{{ $sistema->sistema }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="control_tiempo_id" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <span class="mr-2">⏱️</span> Control de Tiempo *
                                </label>
                                <select name="control_tiempo_id" id="control_tiempo_id" 
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>
                                    <option value="">Seleccione un control de tiempo</option>
                                    @foreach($controlesTiempo as $control)
                                        <option value="{{ $control->id_control_tiempo }}">{{ $control->formato }} ({{ $control->control_tiempo }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Criterios de Desempate -->
                        <div class="mt-8 border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                                <span class="mr-2">🏆</span> Criterios de Desempate
                                <button type="button" class="ml-2 text-gray-400 hover:text-gray-500" 
                                        data-tooltip-target="criterios-info">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </h3>
                        </div>

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

    // Vista previa
    document.getElementById('btnVistaPrevia').addEventListener('click', function() {
        // Implementar lógica de vista previa
        alert('Funcionalidad de vista previa en desarrollo');
    });

    // Guardar como borrador
    document.getElementById('btnGuardarBorrador').addEventListener('click', function() {
        // Implementar lógica de guardado como borrador
        alert('Funcionalidad de guardado como borrador en desarrollo');
    });

    // Duplicar torneo
    document.getElementById('btnDuplicarTorneo').addEventListener('click', function() {
        // Implementar lógica de duplicado de torneo
        alert('Funcionalidad de duplicado de torneo en desarrollo');
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
});
</script>
@endpush 