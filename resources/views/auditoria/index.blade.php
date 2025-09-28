@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center pb-4">
        <h1 class="text-2xl font-semibold">Auditoría del Sistema</h1>
    </div>

    <div class="mb-4">
        <div class="flex gap-2">
            <button id="btnMostrarBusquedaAuditoria" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
            <button id="btnExportarAuditoria" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
    </div>

    <!-- Controles de búsqueda -->
    <div id="panelBusquedaAuditoria" class="bg-gray-50 shadow-md rounded-lg p-4 mb-4 {{ ($search || $filtroUsuario || $filtroAccion || $filtroTabla || $filtroFecha || $filtroEquipo) ? '' : 'hidden' }}">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Búsqueda de Auditoría</h3>
                <button id="btnCancelarBusquedaAuditoria" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                    ✕
                </button>
            </div>
            
            <form method="GET" action="{{ route('auditoria.index') }}" id="formBusquedaAuditoria">
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-64">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                        <input type="text" id="searchInput" name="search" value="{{ $search }}" placeholder="Buscar en todos los campos..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="btnBuscarAvanzadaAuditoria" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                            <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                        </button>
                        <a href="{{ route('auditoria.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                            <i class="fas fa-brush mr-2"></i>Limpiar
                        </a>
                    </div>
                </div>
                
                <!-- Panel de búsqueda avanzada -->
                <div id="panelBusquedaAvanzadaAuditoria" class="mt-4 p-4 bg-white rounded-md {{ ($filtroUsuario || $filtroAccion || $filtroTabla || $filtroFecha || $filtroEquipo) ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Usuario:</label>
                            <select id="filtroUsuario" name="filtro_usuario" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                                <option value="">Todos los usuarios</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario }}" {{ $filtroUsuario === $usuario ? 'selected' : '' }}>{{ $usuario }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Acción:</label>
                            <select id="filtroAccion" name="filtro_accion" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                                <option value="">Todas las acciones</option>
                                @foreach($acciones as $accion)
                                    <option value="{{ $accion }}" {{ $filtroAccion === $accion ? 'selected' : '' }}>{{ $accion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tabla:</label>
                            <select id="filtroTabla" name="filtro_tabla" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                                <option value="">Todas las tablas</option>
                                @foreach($tablas as $tabla)
                                    <option value="{{ $tabla }}" {{ $filtroTabla === $tabla ? 'selected' : '' }}>{{ $tabla }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha:</label>
                            <input type="date" id="filtroFecha" name="filtro_fecha" value="{{ $filtroFecha }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Equipo/IP:</label>
                            <input type="text" id="filtroEquipo" name="filtro_equipo" value="{{ $filtroEquipo }}" placeholder="Filtrar por equipo/IP" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                
                <!-- Campo oculto para mantener per_page -->
                <input type="hidden" name="per_page" value="{{ $perPage }}">
            </form>
        </div>

    <!-- Contenedor de cards para auditorías -->
    <div id="auditorias-cards-container" class="mt-6">
        @forelse($auditorias as $auditoria)
            <div class="bg-white rounded-lg shadow-md mb-4 border border-gray-200">
                <!-- Encabezado Principal -->
                <div class="px-6 py-3 bg-gray-800 text-white rounded-t-lg">
                    <div>
                        <h3 class="text-lg font-semibold">
                            {{ $auditoria->accion }} - {{ $auditoria->fecha->locale('es')->isoFormat('DD [de] MMMM [del] YYYY') }}, {{ \Carbon\Carbon::parse($auditoria->hora)->format('h:i A') }}
                        </h3>
                        <p class="text-sm text-gray-300 mt-1">
                            Equipo: {{ $auditoria->equipo }} | Usuario: {{ $auditoria->correo_id }} | Módulo afectado: {{ $auditoria->tabla_afectada }}
                        </p>
                    </div>
                </div>

                <!-- Cuerpo -->
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Grupo 1: Valor previo -->
                        <div class="border rounded-lg bg-gray-50 p-4 shadow-sm">
                            <h6 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-1">
                                Valor previo
                            </h6>
                            <div class="text-sm text-gray-800">
                                @if($auditoria->valor_previo)
                                    <pre class="whitespace-pre-wrap font-mono text-xs">{{ $auditoria->valor_previo_formateado }}</pre>
                                @else
                                    <span class="text-gray-500 italic">Sin valor previo</span>
                                @endif
                            </div>
                        </div>

                        <!-- Grupo 2: Valor posterior -->
                        <div class="border rounded-lg bg-gray-50 p-4 shadow-sm">
                            <h6 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-1">
                                Valor posterior
                            </h6>
                            <div class="text-sm text-gray-800">
                                @if($auditoria->valor_posterior)
                                    <pre class="whitespace-pre-wrap font-mono text-xs">{{ $auditoria->valor_posterior_formateado }}</pre>
                                @else
                                    <span class="text-gray-500 italic">Sin valor posterior</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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
    <div class="px-6 py-4 bg-gray-50">
        <div class="flex flex-col gap-4">
            <!-- Selector de registros por página (siempre visible) -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-700">Mostrar:</span>
                    <select onchange="changePerPageAuditoria(this.value)" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
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
                    Mostrando {{ $auditorias->firstItem() ?? 0 }} a {{ $auditorias->lastItem() ?? 0 }} registros de {{ $auditorias->total() }} resultados
                </div>
            </div>
            
            <!-- Enlaces de paginación -->
            <div class="flex-1 flex items-center justify-center">
                {{ $auditorias->links('pagination.custom') }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>

// Variables globales para control de búsqueda
let searchTimeout;
let isLoading = false;

// Función para realizar búsqueda AJAX
function performSearchAuditoria() {
    if (isLoading) return;
    
    const searchInput = document.getElementById('searchInput');
    const filtroUsuario = document.getElementById('filtroUsuario');
    const filtroAccion = document.getElementById('filtroAccion');
    const filtroTabla = document.getElementById('filtroTabla');
    const filtroFecha = document.getElementById('filtroFecha');
    const filtroEquipo = document.getElementById('filtroEquipo');
    const perPageSelect = document.querySelector('select[onchange="changePerPageAuditoria(this.value)"]');
    
    const params = new URLSearchParams();
    
    if (searchInput && searchInput.value.trim()) {
        params.append('search', searchInput.value.trim());
    }
    if (filtroUsuario && filtroUsuario.value) {
        params.append('filtro_usuario', filtroUsuario.value);
    }
    if (filtroAccion && filtroAccion.value) {
        params.append('filtro_accion', filtroAccion.value);
    }
    if (filtroTabla && filtroTabla.value) {
        params.append('filtro_tabla', filtroTabla.value);
    }
    if (filtroFecha && filtroFecha.value) {
        params.append('filtro_fecha', filtroFecha.value);
    }
    if (filtroEquipo && filtroEquipo.value.trim()) {
        params.append('filtro_equipo', filtroEquipo.value.trim());
    }
    if (perPageSelect && perPageSelect.value) {
        params.append('per_page', perPageSelect.value);
    }
    
    // Actualizar URL sin recargar página
    const newUrl = '{{ route("auditoria.index") }}' + (params.toString() ? '?' + params.toString() : '');
    window.history.pushState({}, '', newUrl);
    
    // Realizar petición AJAX
    toggleLoadingAuditoria(true);
    
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
        
        // Actualizar cards de auditorías
        const newCardsContainer = doc.querySelector('#auditorias-cards-container');
        if (newCardsContainer) {
            const currentCardsContainer = document.querySelector('#auditorias-cards-container');
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
        
        toggleLoadingAuditoria(false);
    })
    .catch(error => {
        console.error('Error en búsqueda:', error);
        toggleLoadingAuditoria(false);
    });
}

// Función para cambiar registros por página
function changePerPageAuditoria(value) {
    if (isLoading) return;
    
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', value);
    params.delete('page'); // Resetear a la primera página
    
    const newUrl = '{{ route("auditoria.index") }}?' + params.toString();
    window.history.pushState({}, '', newUrl);
    
    toggleLoadingAuditoria(true);
    
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
        
        // Actualizar cards de auditorías
        const newCardsContainer = doc.querySelector('#auditorias-cards-container');
        if (newCardsContainer) {
            const currentCardsContainer = document.querySelector('#auditorias-cards-container');
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
        
        toggleLoadingAuditoria(false);
    })
    .catch(error => {
        console.error('Error al cambiar página:', error);
        toggleLoadingAuditoria(false);
    });
}

// Función para mostrar/ocultar loading
function toggleLoadingAuditoria(show) {
    isLoading = show;
    const cardsContainer = document.querySelector('#auditorias-cards-container');
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
    const btnMostrarBusqueda = document.getElementById('btnMostrarBusquedaAuditoria');
    if (btnMostrarBusqueda) {
        btnMostrarBusqueda.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaAuditoria');
            if (panel) {
                panel.classList.remove('hidden');
            }
        });
    }
    
    // Cancelar búsqueda
    const btnCancelarBusqueda = document.getElementById('btnCancelarBusquedaAuditoria');
    if (btnCancelarBusqueda) {
        btnCancelarBusqueda.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaAuditoria');
            if (panel) {
                panel.classList.add('hidden');
            }
        });
    }
    
    // Búsqueda avanzada
    const btnBuscarAvanzada = document.getElementById('btnBuscarAvanzadaAuditoria');
    if (btnBuscarAvanzada) {
        btnBuscarAvanzada.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaAvanzadaAuditoria');
            if (panel) {
                panel.classList.toggle('hidden');
            }
        });
    }
    
    // Búsqueda en tiempo real con debounce
    const debouncedSearch = debounce(performSearchAuditoria, 500);
    
    // Input de búsqueda principal
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debouncedSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('formBusquedaAuditoria').submit();
            }
        });
    }
    
    // Filtros avanzados
    const filtros = ['filtroUsuario', 'filtroAccion', 'filtroTabla', 'filtroFecha', 'filtroEquipo'];
    filtros.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('input', debouncedSearch);
            elemento.addEventListener('change', debouncedSearch);
        }
    });

    // Función para exportar auditorías
    document.getElementById('btnExportarAuditoria').addEventListener('click', function() {
        window.location.href = '{{ url("api/export/auditorias") }}';
    });
});
</script>
@endpush

@endsection
