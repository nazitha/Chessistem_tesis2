@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use App\Services\PermissionService;
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center pb-4">
        <h1 class="text-2xl font-semibold">Miembros</h1>
        @if(PermissionHelper::canCreate('miembros'))
            <a href="{{ route('miembros.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Miembro
            </a>
        @endif
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
            <button id="btnMostrarBusquedaMiembros" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
            <button id="btnExportarMiembros" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
    </div>

    <!-- Controles de búsqueda -->
    <div id="panelBusquedaMiembros" class="bg-white shadow-md rounded-lg p-4 mb-4 {{ ($search || $filtroCedula || $filtroNombres || $filtroApellidos || $filtroAcademia || $filtroEstado) ? '' : 'hidden' }}">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Miembros</h3>
            <button id="btnCancelarBusquedaMiembros" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        
        <form method="GET" action="{{ route('miembros.index') }}" id="formBusquedaMiembros">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                    <input type="text" id="searchInput" name="search" value="{{ $search }}" placeholder="Buscar por cédula, nombres, apellidos..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                    <button type="button" id="btnBuscarAvanzadaMiembros" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                </button>
                    <a href="{{ route('miembros.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                        <i class="fas fa-brush mr-2"></i>Limpiar
                    </a>
                </div>
        </div>
        
        <!-- Panel de búsqueda avanzada -->
            <div id="panelBusquedaAvanzadaMiembros" class="mt-4 p-4 bg-gray-50 rounded-md {{ ($filtroCedula || $filtroNombres || $filtroApellidos || $filtroAcademia || $filtroEstado || $filtroTorneosJugados || $filtroTorneosActivos) ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cédula:</label>
                        <input type="text" id="filtroCedula" name="filtro_cedula" value="{{ $filtroCedula }}" placeholder="Filtrar por cédula" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombres:</label>
                        <input type="text" id="filtroNombres" name="filtro_nombres" value="{{ $filtroNombres }}" placeholder="Filtrar por nombres" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos:</label>
                        <input type="text" id="filtroApellidos" name="filtro_apellidos" value="{{ $filtroApellidos }}" placeholder="Filtrar por apellidos" 
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academia:</label>
                        <input type="text" id="filtroAcademia" name="filtro_academia" value="{{ $filtroAcademia }}" placeholder="Filtrar por academia" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Torneos jugados:</label>
                        <input type="number" id="filtroTorneosJugados" name="filtro_torneos_jugados" value="{{ $filtroTorneosJugados }}" placeholder="Mínimo torneos jugados" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Torneos activos:</label>
                        <input type="number" id="filtroTorneosActivos" name="filtro_torneos_activos" value="{{ $filtroTorneosActivos }}" placeholder="Mínimo torneos activos" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0">
                </div>
                </div>
            </div>
            
            <!-- Campo oculto para mantener per_page -->
            <input type="hidden" name="per_page" value="{{ $perPage }}">
        </form>
    </div>

    <!-- Contenedor de cards de miembros -->
    <div class="mt-6" id="miembros-cards-container">
        @forelse($miembros as $miembro)
            <div class="bg-white rounded-lg shadow-md mb-4 border border-gray-200">
                <!-- Encabezado -->
                <div class="px-6 py-3 bg-gray-800 text-white rounded-t-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $miembro->nombres }} {{ $miembro->apellidos }}</h3>
                            <div class="text-sm text-gray-300 mt-1">
                                <span>Cédula: {{ $miembro->cedula }}</span>
                                <span class="ml-4">Academia: {{ $miembro->academia->nombre_academia ?? 'Sin academia' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($miembro->estado_miembro)
                                    bg-green-100 text-green-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ $miembro->estado_miembro ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Cuerpo -->
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Grupo 1: Datos personales -->
                        <div class="border rounded-lg bg-gray-50 p-4 shadow-sm">
                            <h6 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-1">
                                Datos personales
                            </h6>
                            <div class="space-y-2">
                                <p class="text-sm"><b>Sexo:</b> {{ $miembro->sexo == 'M' ? 'Masculino' : 'Femenino' }}</p>
                                <p class="text-sm"><b>Fecha de nacimiento:</b> 
                                    @if($miembro->fecha_nacimiento)
                                        {{ \Carbon\Carbon::parse($miembro->fecha_nacimiento)->locale('es')->isoFormat('DD [de] MMMM [del] YYYY') }}
                                    @else
                                        -
                                    @endif
                                </p>
                                <p class="text-sm"><b>Teléfono:</b> {{ $miembro->telefono ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Grupo 2: Detalles secundarios -->
                        <div class="border rounded-lg bg-gray-50 p-4 shadow-sm">
                            <h6 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-1">
                                Detalles secundarios
                            </h6>
                            <div class="space-y-2">
                                <p class="text-sm"><b>Fecha de inscripción:</b> 
                                    @if($miembro->fecha_inscripcion)
                                        {{ \Carbon\Carbon::parse($miembro->fecha_inscripcion)->locale('es')->isoFormat('DD [de] MMMM [del] YYYY') }}
                                    @else
                                        -
                                    @endif
                                </p>
                                <p class="text-sm"><b>ELO:</b> {{ $miembro->elo ?? '-' }}</p>
                                <p class="text-sm"><b>Correo de acceso:</b> {{ $miembro->usuario->correo ?? '-' }}</p>
                                <p class="text-sm"><b>Rol:</b> 
                                    @if($miembro->usuario && $miembro->usuario->roles->isNotEmpty())
                                        {{ $miembro->usuario->roles->first()->name }}
                                    @else
                                        -
                                    @endif
                                </p>
                                <p class="text-sm"><b>Torneos jugados:</b> {{ $miembro->participacionesTorneo->count() }}</p>
                                <p class="text-sm"><b>Torneos activos a jugar:</b> 
                                    @php
                                        $torneosActivos = $miembro->participacionesTorneo()
                                            ->whereHas('torneo', function($query) {
                                                $query->where('estado_torneo', 'Activo');
                                            })
                                            ->count();
                                    @endphp
                                    {{ $torneosActivos }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer con botones -->
                @if(PermissionHelper::hasAnyMiembroActionPermission())
                    <div class="px-6 py-2 bg-gray-50 rounded-b-lg border-t">
                        <div class="flex justify-end space-x-3">
                            @if(PermissionService::hasPermission('miembros.details'))
                                <a href="{{ route('miembros.show', $miembro) }}" 
                                   class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition"
                                   data-tooltip="Ver detalles">
                                    <i class="fas fa-eye mr-1"></i>Ver detalles
                                </a>
                            @endif
                            @if(PermissionHelper::canUpdate('miembros'))
                                <a href="{{ route('miembros.edit', $miembro) }}" 
                                   class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition"
                                   data-tooltip="Editar miembro">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>
                            @endif
                            @if(PermissionHelper::canDelete('miembros'))
                                <button type="button"
                                        onclick="confirmarEliminacion('{{ $miembro->cedula }}')"
                                        class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition"
                                        data-tooltip="Eliminar miembro">
                                    <i class="fas fa-trash mr-1"></i>Eliminar
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-search text-4xl text-gray-300 mb-2"></i>
                    <p class="text-lg font-medium text-gray-500">No se encontraron resultados</p>
                    <p class="text-sm text-gray-400">Intenta ajustar los filtros de búsqueda</p>
                </div>
            </div>
        @endforelse
    </div>
        
        <!-- Paginación de Laravel -->
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="flex flex-col gap-4">
                <!-- Selector de registros por página (siempre visible) -->
            <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-700">Mostrar:</span>
                        <select onchange="changePerPageMiembros(this.value)" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                        <span class="text-sm text-gray-700">por página</span>
                    </div>
                    
                    <!-- Información de paginación -->
                    <div class="text-sm text-gray-700">
                        Mostrando {{ $miembros->firstItem() ?? 0 }} a {{ $miembros->lastItem() ?? 0 }} registros de {{ $miembros->total() }} resultados
                    </div>
                </div>
                
                <!-- Enlaces de paginación -->
                <div class="flex-1 flex items-center justify-center">
                    {{ $miembros->links('pagination.custom') }}
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
            ¿Estás seguro de que deseas eliminar este miembro? Esta acción no se puede deshacer.
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

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

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

function confirmarEliminacion(miembroId) {
    const modal = document.getElementById('modal-confirmacion');
    const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    btnConfirmar.onclick = function() {
        const form = document.getElementById('form-eliminar-' + miembroId);
        if (form) {
            form.submit();
        }
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
function performSearchMiembros() {
    if (isLoading) return;
    
    console.log('Iniciando búsqueda...');
    
    const searchInput = document.getElementById('searchInput');
    const filtroCedula = document.getElementById('filtroCedula');
    const filtroNombres = document.getElementById('filtroNombres');
    const filtroApellidos = document.getElementById('filtroApellidos');
    const filtroAcademia = document.getElementById('filtroAcademia');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroTorneosJugados = document.getElementById('filtroTorneosJugados');
    const filtroTorneosActivos = document.getElementById('filtroTorneosActivos');
    const perPageSelect = document.querySelector('select[onchange="changePerPageMiembros(this.value)"]');
    
    const params = new URLSearchParams();
    
    if (searchInput && searchInput.value.trim()) {
        params.append('search', searchInput.value.trim());
        console.log('Búsqueda general:', searchInput.value.trim());
    }
    if (filtroCedula && filtroCedula.value.trim()) {
        params.append('filtro_cedula', filtroCedula.value.trim());
    }
    if (filtroNombres && filtroNombres.value.trim()) {
        params.append('filtro_nombres', filtroNombres.value.trim());
    }
    if (filtroApellidos && filtroApellidos.value.trim()) {
        params.append('filtro_apellidos', filtroApellidos.value.trim());
    }
    if (filtroAcademia && filtroAcademia.value.trim()) {
        params.append('filtro_academia', filtroAcademia.value.trim());
    }
    if (filtroEstado && filtroEstado.value !== '') {
        params.append('filtro_estado', filtroEstado.value);
    }
    if (filtroTorneosJugados && filtroTorneosJugados.value.trim()) {
        params.append('filtro_torneos_jugados', filtroTorneosJugados.value.trim());
    }
    if (filtroTorneosActivos && filtroTorneosActivos.value.trim()) {
        params.append('filtro_torneos_activos', filtroTorneosActivos.value.trim());
    }
    if (perPageSelect && perPageSelect.value) {
        params.append('per_page', perPageSelect.value);
    }
    
    // Actualizar URL sin recargar página
    const newUrl = '{{ route("miembros.index") }}' + (params.toString() ? '?' + params.toString() : '');
    console.log('URL de búsqueda:', newUrl);
    window.history.pushState({}, '', newUrl);
    
    // Realizar petición AJAX
    toggleLoadingMiembros(true);
    
    fetch(newUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => {
        console.log('Respuesta recibida:', response.status);
        return response.text();
    })
    .then(html => {
        console.log('HTML recibido, longitud:', html.length);
        
        // Parsear la respuesta HTML
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Actualizar contenedor de cards de miembros
        const newCardsContainer = doc.querySelector('#miembros-cards-container');
        if (newCardsContainer) {
            const currentCardsContainer = document.querySelector('#miembros-cards-container');
            if (currentCardsContainer) {
                currentCardsContainer.innerHTML = newCardsContainer.innerHTML;
                console.log('Cards de miembros actualizados');
            }
        } else {
            console.log('No se encontró #miembros-cards-container en la respuesta');
        }
        
        // Actualizar paginación
        const newPaginationContainer = doc.querySelector('.px-6.py-4.border-t.bg-gray-50');
        if (newPaginationContainer) {
            const currentPaginationContainer = document.querySelector('.px-6.py-4.border-t.bg-gray-50');
            if (currentPaginationContainer) {
                currentPaginationContainer.outerHTML = newPaginationContainer.outerHTML;
                console.log('Paginación actualizada');
            }
        } else {
            console.log('No se encontró paginación en la respuesta');
        }
        
        toggleLoadingMiembros(false);
    })
    .catch(error => {
        console.error('Error en búsqueda:', error);
        toggleLoadingMiembros(false);
    });
}

// Función para cambiar registros por página
function changePerPageMiembros(value) {
    if (isLoading) return;
    
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', value);
    params.delete('page'); // Resetear a la primera página
    
    const newUrl = '{{ route("miembros.index") }}?' + params.toString();
    window.history.pushState({}, '', newUrl);
    
    toggleLoadingMiembros(true);
    
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
        
        // Actualizar contenedor de cards de miembros
        const newCardsContainer = doc.querySelector('#miembros-cards-container');
        if (newCardsContainer) {
            const currentCardsContainer = document.querySelector('#miembros-cards-container');
            if (currentCardsContainer) {
                currentCardsContainer.innerHTML = newCardsContainer.innerHTML;
            }
        }
        
        // Actualizar paginación
        const newPaginationContainer = doc.querySelector('.px-6.py-4.border-t.bg-gray-50');
        if (newPaginationContainer) {
            const currentPaginationContainer = document.querySelector('.px-6.py-4.border-t.bg-gray-50');
            if (currentPaginationContainer) {
                currentPaginationContainer.outerHTML = newPaginationContainer.outerHTML;
            }
        }
        
        toggleLoadingMiembros(false);
    })
    .catch(error => {
        console.error('Error al cambiar página:', error);
        toggleLoadingMiembros(false);
    });
}

// Función para mostrar/ocultar loading
function toggleLoadingMiembros(show) {
    isLoading = show;
    const cardsContainer = document.querySelector('#miembros-cards-container');
    if (cardsContainer) {
        cardsContainer.style.opacity = show ? '0.6' : '1';
        cardsContainer.style.pointerEvents = show ? 'none' : 'auto';
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
    const btnMostrarBusqueda = document.getElementById('btnMostrarBusquedaMiembros');
    if (btnMostrarBusqueda) {
        btnMostrarBusqueda.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaMiembros');
        if (panel) {
            panel.classList.remove('hidden');
        }
        });
    }
    
    // Cancelar búsqueda
    const btnCancelarBusqueda = document.getElementById('btnCancelarBusquedaMiembros');
    if (btnCancelarBusqueda) {
        btnCancelarBusqueda.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaMiembros');
        if (panel) {
            panel.classList.add('hidden');
        }
        });
    }
    
    // Búsqueda avanzada
    const btnBuscarAvanzada = document.getElementById('btnBuscarAvanzadaMiembros');
    if (btnBuscarAvanzada) {
        btnBuscarAvanzada.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaAvanzadaMiembros');
            if (panel) {
                panel.classList.toggle('hidden');
            }
        });
    }
    
    // Búsqueda en tiempo real con debounce
    const debouncedSearch = debounce(performSearchMiembros, 500);
    
    // Input de búsqueda principal
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debouncedSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('formBusquedaMiembros').submit();
            }
        });
    }
    
    // Filtros avanzados
    const filtros = ['filtroCedula', 'filtroNombres', 'filtroApellidos', 'filtroAcademia', 'filtroEstado', 'filtroTorneosJugados', 'filtroTorneosActivos'];
    filtros.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('input', debouncedSearch);
            elemento.addEventListener('change', debouncedSearch);
        }
    });
});

// Función para exportar miembros
document.getElementById('btnExportarMiembros').addEventListener('click', function() {
    window.location.href = '{{ url("api/export/miembros") }}';
});
</script>
@endpush

@endsection 