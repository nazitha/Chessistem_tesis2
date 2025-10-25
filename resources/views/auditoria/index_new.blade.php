@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
<div class="container mx-auto py-6">
    <div class="bg-white dark:bg-slate-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-5 sm:px-8 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Auditoría del Sistema</h1>
        </div>

        <!-- Botón para mostrar controles de búsqueda -->
        <div class="px-6 pb-4">
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
        <div id="panelBusquedaAuditoria" class="mx-6 mb-4 bg-gray-50 dark:bg-slate-700 shadow-md rounded-lg p-4 {{ ($search || $filtroUsuario || $filtroAccion || $filtroTabla || $filtroFecha || $filtroEquipo) ? '' : 'hidden' }}">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Búsqueda de Auditoría</h3>
                <button id="btnCancelarBusquedaAuditoria" class="text-gray-500 hover:text-gray-300 text-xl font-bold">
                    ✕
                </button>
            </div>
            
            <form method="GET" action="{{ route('auditoria.index') }}" id="formBusquedaAuditoria">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex-1 min-w-64">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Buscar:</label>
                        <input type="text" id="searchInput" name="search" value="{{ $search }}" placeholder="Buscar en todos los campos..." 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <div id="panelBusquedaAvanzadaAuditoria" class="mt-4 p-4 bg-white dark:bg-slate-800 rounded-md {{ ($filtroUsuario || $filtroAccion || $filtroTabla || $filtroFecha || $filtroEquipo) ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Usuario:</label>
                            <select id="filtroUsuario" name="filtro_usuario" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">
                                <option value="">Todos los usuarios</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario }}" {{ $filtroUsuario === $usuario ? 'selected' : '' }}>{{ $usuario }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Acción:</label>
                            <select id="filtroAccion" name="filtro_accion" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">
                                <option value="">Todas las acciones</option>
                                @foreach($acciones as $accion)
                                    <option value="{{ $accion }}" {{ $filtroAccion === $accion ? 'selected' : '' }}>{{ $accion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Tabla:</label>
                            <select id="filtroTabla" name="filtro_tabla" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">
                                <option value="">Todas las tablas</option>
                                @foreach($tablas as $tabla)
                                    <option value="{{ $tabla }}" {{ $filtroTabla === $tabla ? 'selected' : '' }}>{{ $tabla }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha:</label>
                            <input type="date" id="filtroFecha" name="filtro_fecha" value="{{ $filtroFecha }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Equipo/IP:</label>
                            <input type="text" id="filtroEquipo" name="filtro_equipo" value="{{ $filtroEquipo }}" placeholder="Filtrar por equipo/IP" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>
                </div>
                
                <!-- Campo oculto para mantener per_page -->
                <input type="hidden" name="per_page" value="{{ $perPage }}">
            </form>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="tablaAuditoria">
                    <thead class="bg-gray-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200">Hora</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200">Usuario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200">Acción</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200">Tabla</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200">Equipo/IP</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200">Detalles</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="tablaAuditoriaContainer">
                        @forelse($auditorias as $auditoria)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors duration-150">
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $auditoria->fecha->locale('es')->isoFormat('DD [de] MMMM [del] YYYY') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $auditoria->hora }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $auditoria->correo_id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $auditoria->accion }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $auditoria->tabla_afectada }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $auditoria->equipo }}</td>
                            <td class="px-4 py-2 text-sm text-center">
                                <button type="button" onclick="toggleDetalle('detalle-{{ $auditoria->id }}')" class="text-blue-600 hover:underline dark:text-blue-400">Ver</button>
                            </td>
                        </tr>
                        <tr id="detalle-{{ $auditoria->id }}" style="display:none;" class="bg-gray-50 dark:bg-slate-700">
                            <td colspan="7" class="px-4 py-2">
                                <b>Valor previo:</b> <pre class="whitespace-pre-wrap">{{ $auditoria->valor_previo }}</pre>
                                <b>Valor posterior:</b> <pre class="whitespace-pre-wrap">{{ $auditoria->valor_posterior }}</pre>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-300">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-search text-4xl text-gray-300 dark:text-gray-500 mb-2"></i>
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
            <div class="px-6 py-4 border-t bg-gray-50 dark:bg-slate-700">
                <div class="flex flex-col gap-4">
                    <!-- Selector de registros por página (siempre visible) -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Mostrar:</span>
                            <select onchange="changePerPageAuditoria(this.value)" class="border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">
                                <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20</option>
                                <option value="25" {{ ($perPage ?? 20) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-700 dark:text-gray-200">por página</span>
                        </div>
                        
                        <!-- Información de paginación -->
                        <div class="text-sm text-gray-700 dark:text-gray-200">
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
    </div>
</div>

@push('scripts')
<script>
function toggleDetalle(id) {
    var row = document.getElementById(id);
    if (row.style.display === 'none') {
        row.style.display = '';
    } else {
        row.style.display = 'none';
    }
}

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
        
        // Actualizar tabla de auditorías
        const newTableContainer = doc.querySelector('#tablaAuditoriaContainer');
        if (newTableContainer) {
            const currentTableContainer = document.querySelector('#tablaAuditoriaContainer');
            if (currentTableContainer) {
                currentTableContainer.innerHTML = newTableContainer.innerHTML;
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
        
        // Actualizar tabla de auditorías
        const newTableContainer = doc.querySelector('#tablaAuditoriaContainer');
        if (newTableContainer) {
            const currentTableContainer = document.querySelector('#tablaAuditoriaContainer');
            if (currentTableContainer) {
                currentTableContainer.innerHTML = newTableContainer.innerHTML;
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
    const tableContainer = document.querySelector('#tablaAuditoriaContainer').closest('.overflow-x-auto');
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
});
</script>
@endpush

@endsection
