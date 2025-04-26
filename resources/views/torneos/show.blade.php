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
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $torneo->estado_torneo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $torneo->estado_torneo ? 'Activo' : 'Finalizado' }}
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
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Participantes
                <span class="ml-2 text-sm text-gray-500">({{ $torneo->participantes->count() }} registrados)</span>
            </h3>
        </div>
        
        <div class="border-t border-gray-200">
            @if($torneo->participantes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puntos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buchholz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S-B</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progresivo</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($torneo->participantes as $participante)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $participante->miembro->nombres }} {{ $participante->miembro->apellidos }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participante->puntos }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participante->buchholz }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participante->sonneborn_berger }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participante->progresivo }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($torneo->estado_torneo && !$torneo->torneo_cancelado)
                                            <button type="button"
                                                    onclick="confirmarRetiroParticipante('{{ $participante->id }}')"
                                                    class="text-red-600 hover:text-red-900">
                                                Retirar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-4 text-center text-sm text-gray-500">
                    No hay participantes registrados en este torneo.
                </div>
            @endif
        </div>
    </div>

    <!-- Sección de Rondas -->
    @if($torneo->participantes->count() >= 2)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Rondas
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
                    <div class="overflow-x-auto">
                        @foreach($torneo->rondas as $ronda)
                            <div class="px-4 py-5 sm:p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">
                                    Ronda {{ $ronda->numero_ronda }}
                                    <span class="text-sm text-gray-500">({{ $ronda->fecha_hora->format('d/m/Y H:i') }})</span>
                                </h4>
                                <div class="space-y-4">
                                    @foreach($ronda->partidas as $partida)
                                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
                                            <div class="flex items-center space-x-4">
                                                <span class="text-sm font-medium">Mesa {{ $partida->mesa }}</span>
                                                <span class="text-sm">{{ $partida->jugadorBlancas->nombres }}</span>
                                                <span class="text-sm font-medium">vs</span>
                                                <span class="text-sm">{{ $partida->jugadorNegras ? $partida->jugadorNegras->nombres : 'BYE' }}</span>
                                            </div>
                                            @if(!$ronda->completada)
                                                <div class="flex items-center space-x-2">
                                                    <button type="button"
                                                            onclick="registrarResultado('{{ $partida->id }}', 1)"
                                                            class="text-white bg-green-500 hover:bg-green-600 px-2 py-1 rounded text-sm">
                                                        1-0
                                                    </button>
                                                    <button type="button"
                                                            onclick="registrarResultado('{{ $partida->id }}', 3)"
                                                            class="text-white bg-gray-500 hover:bg-gray-600 px-2 py-1 rounded text-sm">
                                                        ½-½
                                                    </button>
                                                    <button type="button"
                                                            onclick="registrarResultado('{{ $partida->id }}', 2)"
                                                            class="text-white bg-green-500 hover:bg-green-600 px-2 py-1 rounded text-sm">
                                                        0-1
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-sm font-medium">
                                                    {{ $partida->getResultadoTexto() }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
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
        form.action = `{{ url('torneos/' . $torneo->id . '/participantes') }}/${participanteId}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
}

function generarEmparejamientos() {
    if (confirm('¿Está seguro que desea generar los emparejamientos para la siguiente ronda?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('torneos.rondas.store', $torneo) }}`;
        form.innerHTML = `@csrf`;
        document.body.appendChild(form);
        form.submit();
    }
}

function registrarResultado(partidaId, resultado) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ url('torneos/partidas') }}/${partidaId}/resultado`;
    form.innerHTML = `
        @csrf
        <input type="hidden" name="resultado" value="${resultado}">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal-participantes').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalParticipantes();
    }
});
</script>
@endpush 