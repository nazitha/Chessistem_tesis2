@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
@endphp

<div class="max-w-full mx-auto px-4">
    <div class="flex justify-between items-center pb-4">
        <h1 class="text-2xl font-semibold">Participantes</h1>
    </div>

    @if(session('success'))
        <div class="mt-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700" id="success-alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700" id="error-alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Botón para mostrar controles de búsqueda -->
    <div class="mb-4">
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
    <div id="panelBusquedaParticipantes" class="bg-white shadow-md rounded-lg p-4 mb-4 {{ ($search || $filtroMiembro || $filtroTorneo || $filtroEstado || $filtroPuntos || $filtroPosicion) ? '' : 'hidden' }}">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Participantes</h3>
            <button id="btnCancelarBusquedaParticipantes" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        
        <form method="GET" action="{{ route('participantes.index') }}" id="formBusquedaParticipantes">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                    <input type="text" id="searchInput" name="search" value="{{ $search }}" placeholder="Buscar por miembro, torneo, número..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" id="btnBuscarAvanzadaParticipantes" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                    </button>
                    <a href="{{ route('participantes.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                        <i class="fas fa-brush mr-2"></i>Limpiar
                    </a>
                </div>
            </div>
            
            <!-- Panel de búsqueda avanzada -->
            <div id="panelBusquedaAvanzadaParticipantes" class="mt-4 p-4 bg-gray-50 rounded-md {{ ($filtroMiembro || $filtroTorneo || $filtroEstado || $filtroPuntos || $filtroPosicion) ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Miembro:</label>
                        <input type="text" id="filtroMiembro" name="filtro_miembro" value="{{ $filtroMiembro }}" placeholder="Filtrar por miembro" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Torneo:</label>
                        <input type="text" id="filtroTorneo" name="filtro_torneo" value="{{ $filtroTorneo }}" placeholder="Filtrar por torneo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                        <select id="filtroEstado" name="filtro_estado" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                            <option value="">Todos</option>
                            <option value="1" {{ $filtroEstado === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ $filtroEstado === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Puntos mínimos:</label>
                        <input type="number" id="filtroPuntos" name="filtro_puntos" value="{{ $filtroPuntos }}" placeholder="Puntos mínimos" 
                               step="0.5" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Posición máxima:</label>
                        <input type="number" id="filtroPosicion" name="filtro_posicion" value="{{ $filtroPosicion }}" placeholder="Posición máxima" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
            </div>
            
            <!-- Campo oculto para mantener per_page -->
            <input type="hidden" name="per_page" value="{{ $perPage }}">
        </form>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Miembro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Torneo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puntos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        @if(PermissionHelper::canDelete('participantes'))
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tablaParticipantesContainer">
                    @forelse($participantes as $participante)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $participante->miembro->nombres }} {{ $participante->miembro->apellidos }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $participante->miembro->cedula }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $participante->torneo->nombre_torneo }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $participante->torneo->fecha_inicio->locale('es')->isoFormat('DD [de] MMMM [del] YYYY') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $participante->numero_inicial ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($participante->puntos, 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $participante->posicion ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($participante->activo)
                                        bg-green-100 text-green-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ $participante->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $participante->created_at->locale('es')->isoFormat('DD [de] MMMM [del] YYYY') }}
                            </td>
                            @if(PermissionHelper::canDelete('participantes'))
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('participantes.show', $participante) }}" 
                                       class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100 transition-colors duration-200"
                                       data-tooltip="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('participantes.destroy', $participante) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="confirmarEliminacion('{{ $participante->id }}')"
                                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 transition-colors duration-200"
                                                data-tooltip="Eliminar participante">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ PermissionHelper::canDelete('participantes') ? '8' : '7' }}" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-search text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-lg font-medium">No se encontraron resultados</p>
                                    <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación de Laravel -->
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="flex flex-col gap-4">
                <!-- Selector de registros por página (siempre visible) -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-700">Mostrar:</span>
                        <select onchange="changePerPageParticipantes(this.value)" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                            <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20</option>
                            <option value="25" {{ ($perPage ?? 20) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-700">por página</span>
                    </div>
                    
                    <!-- Información de paginación -->
                    <div class="text-sm text-gray-700">
                        Mostrando {{ $participantes->firstItem() ?? 0 }} a {{ $participantes->lastItem() ?? 0 }} registros de {{ $participantes->total() }} resultados
                    </div>
                </div>
                
                <!-- Enlaces de paginación -->
                <div class="flex-1 flex items-center justify-center">
                    {{ $participantes->links('pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div id="modal-confirmacion" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar eliminación</h3>
        <p class="text-sm text-gray-500 mb-4">
            ¿Estás seguro de que deseas eliminar este participante? Esta acción no se puede deshacer.
        </p>
        <div class="flex justify-end space-x-3">
            <button type="button" 
                    onclick="cerrarModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancelar
            </button>
            <button type="button"
                    id="btn-confirmar-eliminacion"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Eliminar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ocultar alertas después de 3 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('#success-alert, #error-alert');
        alerts.forEach(alert => {
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 3000);
});

function confirmarEliminacion(participanteId) {
    const modal = document.getElementById('modal-confirmacion');
    const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    btnConfirmar.onclick = function() {
        document.querySelector(`form[action*="${participanteId}"]`).submit();
    };
}

function cerrarModal() {
    const modal = document.getElementById('modal-confirmacion');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Variables globales para control de búsqueda
let searchTimeout;
let isLoading = false;

// Función para realizar búsqueda AJAX
function performSearchParticipantes() {
    if (isLoading) return;
    
    const searchInput = document.getElementById('searchInput');
    const filtroMiembro = document.getElementById('filtroMiembro');
    const filtroTorneo = document.getElementById('filtroTorneo');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroPuntos = document.getElementById('filtroPuntos');
    const filtroPosicion = document.getElementById('filtroPosicion');
    const perPageSelect = document.querySelector('select[onchange="changePerPageParticipantes(this.value)"]');
    
    const params = new URLSearchParams();
    
    if (searchInput && searchInput.value.trim()) {
        params.append('search', searchInput.value.trim());
    }
    if (filtroMiembro && filtroMiembro.value.trim()) {
        params.append('filtro_miembro', filtroMiembro.value.trim());
    }
    if (filtroTorneo && filtroTorneo.value.trim()) {
        params.append('filtro_torneo', filtroTorneo.value.trim());
    }
    if (filtroEstado && filtroEstado.value !== '') {
        params.append('filtro_estado', filtroEstado.value);
    }
    if (filtroPuntos && filtroPuntos.value) {
        params.append('filtro_puntos', filtroPuntos.value);
    }
    if (filtroPosicion && filtroPosicion.value) {
        params.append('filtro_posicion', filtroPosicion.value);
    }
    if (perPageSelect && perPageSelect.value) {
        params.append('per_page', perPageSelect.value);
    }
    
    // Actualizar URL sin recargar página
    const newUrl = '{{ route("participantes.index") }}' + (params.toString() ? '?' + params.toString() : '');
    window.history.pushState({}, '', newUrl);
    
    // Realizar petición AJAX
    toggleLoadingParticipantes(true);
    
    fetch(newUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parsear la respuesta HTML
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Actualizar tabla de participantes
        const newTableContainer = doc.querySelector('#tablaParticipantesContainer');
        if (newTableContainer) {
            const currentTableContainer = document.querySelector('#tablaParticipantesContainer');
            if (currentTableContainer) {
                currentTableContainer.innerHTML = newTableContainer.innerHTML;
            }
        }
        
        // Actualizar paginación completa (incluyendo el texto "Mostrando n a m...")
        const newPaginationContainer = doc.querySelector('.px-6.py-4.border-t.bg-gray-50');
        if (newPaginationContainer) {
            const currentPaginationContainer = document.querySelector('.px-6.py-4.border-t.bg-gray-50');
            if (currentPaginationContainer) {
                currentPaginationContainer.outerHTML = newPaginationContainer.outerHTML;
            }
        }
        
        toggleLoadingParticipantes(false);
    })
    .catch(error => {
        console.error('Error en búsqueda:', error);
        toggleLoadingParticipantes(false);
    });
}

// Función para cambiar registros por página
function changePerPageParticipantes(value) {
    if (isLoading) return;
    
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', value);
    params.delete('page'); // Resetear a la primera página
    
    const newUrl = '{{ route("participantes.index") }}?' + params.toString();
    window.history.pushState({}, '', newUrl);
    
    toggleLoadingParticipantes(true);
    
    fetch(newUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Actualizar tabla de participantes
        const newTableContainer = doc.querySelector('#tablaParticipantesContainer');
        if (newTableContainer) {
            const currentTableContainer = document.querySelector('#tablaParticipantesContainer');
            if (currentTableContainer) {
                currentTableContainer.innerHTML = newTableContainer.innerHTML;
            }
        }
        
        // Actualizar paginación completa (incluyendo el texto "Mostrando n a m...")
        const newPaginationContainer = doc.querySelector('.px-6.py-4.border-t.bg-gray-50');
        if (newPaginationContainer) {
            const currentPaginationContainer = document.querySelector('.px-6.py-4.border-t.bg-gray-50');
            if (currentPaginationContainer) {
                currentPaginationContainer.outerHTML = newPaginationContainer.outerHTML;
            }
        }
        
        toggleLoadingParticipantes(false);
    })
    .catch(error => {
        console.error('Error al cambiar página:', error);
        toggleLoadingParticipantes(false);
    });
}

// Función para mostrar/ocultar loading
function toggleLoadingParticipantes(show) {
    isLoading = show;
    const tableContainer = document.querySelector('#tablaParticipantesContainer').closest('.overflow-x-auto');
    if (tableContainer) {
        tableContainer.style.opacity = show ? '0.6' : '1';
        tableContainer.style.pointerEvents = show ? 'none' : 'auto';
    }
}

// Función debounce para optimizar búsquedas
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar panel de búsqueda
    const btnMostrarBusqueda = document.getElementById('btnMostrarBusquedaParticipantes');
    if (btnMostrarBusqueda) {
        btnMostrarBusqueda.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaParticipantes');
            if (panel) {
                panel.classList.remove('hidden');
            }
        });
    }
    
    // Cancelar búsqueda
    const btnCancelarBusqueda = document.getElementById('btnCancelarBusquedaParticipantes');
    if (btnCancelarBusqueda) {
        btnCancelarBusqueda.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaParticipantes');
            if (panel) {
                panel.classList.add('hidden');
            }
        });
    }
    
    // Búsqueda avanzada
    const btnBuscarAvanzada = document.getElementById('btnBuscarAvanzadaParticipantes');
    if (btnBuscarAvanzada) {
        btnBuscarAvanzada.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaAvanzadaParticipantes');
            if (panel) {
                panel.classList.toggle('hidden');
            }
        });
    }
    
    // Búsqueda en tiempo real con debounce
    const debouncedSearch = debounce(performSearchParticipantes, 500);
    
    // Input de búsqueda principal
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debouncedSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('formBusquedaParticipantes').submit();
            }
        });
    }
    
    // Filtros avanzados
    const filtros = ['filtroMiembro', 'filtroTorneo', 'filtroEstado', 'filtroPuntos', 'filtroPosicion'];
    filtros.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('input', debouncedSearch);
            elemento.addEventListener('change', debouncedSearch);
        }
    });
});
</script>
@endpush

@endsection
