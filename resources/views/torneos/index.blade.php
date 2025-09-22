@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use Illuminate\Support\Facades\Log;
    use App\Services\PermissionService;
    
    // Debug de permisos
    Log::info('Vista torneos: Verificando permisos', [
        'can_create' => PermissionHelper::canCreate('torneos'),
        'can_update' => PermissionHelper::canUpdate('torneos'),
        'can_delete' => PermissionHelper::canDelete('torneos'),
        'can_view_details' => PermissionService::hasPermission('torneos.details')
    ]);
@endphp

<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center pb-4">
        <h1 class="text-2xl font-semibold">Torneos</h1>
        @if(PermissionHelper::canCreate('torneos'))
            <a href="{{ route('torneos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Torneo
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
            <button id="btnMostrarBusquedaTorneos" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
            <button id="btnExportarTorneos" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
    </div>

    <!-- Controles de búsqueda -->
    <div id="panelBusquedaTorneos" class="bg-white shadow-md rounded-lg p-4 mb-4 hidden">
        <form method="GET" action="{{ route('torneos.index') }}" id="formBusquedaTorneos">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Torneos</h3>
                <button type="button" id="btnCancelarBusquedaTorneos" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                    <input type="text" id="searchInput" name="search" value="{{ $search ?? '' }}" placeholder="Buscar por nombre, lugar, categoría..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                    <button type="button" id="btnBuscarAvanzadaTorneos" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                </button>
                    <a href="{{ route('torneos.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                        <i class="fas fa-brush mr-2"></i>Limpiar
                    </a>
            </div>
        </div>
        <!-- Panel de búsqueda avanzada -->
            <div id="panelBusquedaAvanzadaTorneos" class="mt-4 p-4 bg-gray-50 rounded-md {{ ($filtroNombre || $filtroLugar || $filtroEstado) ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                        <input type="text" id="filtroNombre" name="filtro_nombre" value="{{ $filtroNombre ?? '' }}" placeholder="Filtrar por nombre" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lugar:</label>
                        <input type="text" id="filtroLugar" name="filtro_lugar" value="{{ $filtroLugar ?? '' }}" placeholder="Filtrar por lugar" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                        <select id="filtroEstado" name="filtro_estado" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todos los estados</option>
                            <option value="Activo" {{ ($filtroEstado ?? '') == 'Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Borrador" {{ ($filtroEstado ?? '') == 'Borrador' ? 'selected' : '' }}>Borrador</option>
                            <option value="Finalizado" {{ ($filtroEstado ?? '') == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                            <option value="Cancelado" {{ ($filtroEstado ?? '') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    </div>
                </div>
            </div>
            <!-- Campos ocultos para mantener per_page -->
            <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
        </form>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lugar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        @if(PermissionHelper::hasAnyTorneoActionPermission())
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($torneos as $torneo)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $torneo->nombre_torneo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $torneo->fecha_inicio->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $torneo->lugar }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $torneo->categoria->categoria_torneo ?? 'Sin categoría' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($torneo->torneo_cancelado)
                                        bg-red-100 text-red-800
                                    @elseif($torneo->fecha_inicio && $torneo->fecha_inicio->isPast())
                                        bg-gray-100 text-gray-800
                                    @elseif(!$torneo->estado_torneo)
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-green-100 text-green-800
                                    @endif
                                ">
                                    @if($torneo->torneo_cancelado)
                                        Cancelado
                                    @elseif($torneo->fecha_inicio && $torneo->fecha_inicio->isPast())
                                        Finalizado
                                    @elseif(!$torneo->estado_torneo)
                                        Borrador
                                    @else
                                        Activo
                                    @endif
                                </span>
                            </td>
                            @if(PermissionHelper::hasAnyTorneoActionPermission())
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        @if(PermissionService::hasPermission('torneos.details'))
                                            <a href="{{ route('torneos.show', $torneo) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100 transition-colors duration-200"
                                               data-tooltip="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        
                                        @if(PermissionHelper::canUpdate('torneos'))
                                            <a href="{{ route('torneos.edit', $torneo) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-100 transition-colors duration-200"
                                               data-tooltip="Editar torneo">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if(PermissionHelper::canCancel())
                                            @if(!$torneo->torneo_cancelado && !$torneo->fecha_inicio->isPast())
                                                <button type="button"
                                                        onclick="confirmarCancelacion('{{ $torneo->id }}')"
                                                        class="text-orange-600 hover:text-orange-900 p-1 rounded-full hover:bg-orange-100 transition-colors duration-200"
                                                        data-tooltip="Cancelar torneo">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @endif
                                        @endif
                                        
                                        @if(PermissionHelper::canDelete('torneos'))
                                            <button type="button"
                                                    onclick="confirmarEliminacion('{{ $torneo->id }}')"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 transition-colors duration-200"
                                                    data-tooltip="Eliminar torneo">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                        
                                        @if(PermissionHelper::canDelete('torneos'))
                                            <form id="form-eliminar-{{ $torneo->id }}" 
                                                  action="{{ route('torneos.destroy', $torneo) }}" 
                                                  method="POST" 
                                                  class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif

                                        @if(PermissionHelper::canUpdate('torneos'))
                                            <form id="form-cancelar-{{ $torneo->id }}" 
                                                  action="{{ route('torneos.cancelar', $torneo) }}" 
                                                  method="POST" 
                                                  class="hidden">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ PermissionHelper::hasAnyTorneoActionPermission() ? '6' : '5' }}" class="px-6 py-8 text-center text-gray-500">
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
                        <select onchange="changePerPage(this.value)" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-700">por página</span>
                    </div>
                    
                    <!-- Información de paginación -->
                    <div class="text-sm text-gray-700">
                        Mostrando {{ $torneos->firstItem() ?? 0 }} a {{ $torneos->lastItem() ?? 0 }} de {{ $torneos->total() }} resultados
                    </div>
                </div>
                
                <!-- Enlaces de paginación -->
                <div class="flex justify-center">
                {{ $torneos->links('pagination.custom') }}
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
            ¿Estás seguro de que deseas eliminar este torneo? Esta acción no se puede deshacer.
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

<!-- Modal de cancelación -->
<div id="modal-cancelacion" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Cancelar Torneo</h3>
        <form id="form-cancelar-torneo" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700">Motivo de la cancelación</label>
                <textarea id="motivo_cancelacion" name="motivo_cancelacion" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
            </div>
            <div class="flex flex-row gap-4 mt-2">
                <button type="button" 
                        onclick="cerrarModalCancelacion()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    [data-tooltip] {
        position: relative;
    }

    [data-tooltip]:hover:after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 10;
    }
</style>
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

    // Control de visibilidad del panel de búsqueda
    const btnMostrarBusqueda = document.getElementById('btnMostrarBusquedaTorneos');
    const panelBusqueda = document.getElementById('panelBusquedaTorneos');
    const btnCancelarBusqueda = document.getElementById('btnCancelarBusquedaTorneos');
    const btnBuscarAvanzada = document.getElementById('btnBuscarAvanzadaTorneos');
    const panelBusquedaAvanzada = document.getElementById('panelBusquedaAvanzadaTorneos');

    // Mostrar/ocultar panel de búsqueda
    if (btnMostrarBusqueda && panelBusqueda) {
        btnMostrarBusqueda.addEventListener('click', () => {
            panelBusqueda.classList.remove('hidden');
            btnMostrarBusqueda.style.display = 'none';
        });
    }

    if (btnCancelarBusqueda && panelBusqueda) {
        btnCancelarBusqueda.addEventListener('click', () => {
            panelBusqueda.classList.add('hidden');
            if (btnMostrarBusqueda) btnMostrarBusqueda.style.display = 'inline-block';
        });
    }

    // Toggle búsqueda avanzada
    if (btnBuscarAvanzada && panelBusquedaAvanzada) {
        btnBuscarAvanzada.addEventListener('click', () => {
            panelBusquedaAvanzada.classList.toggle('hidden');
        });
    }

    // Búsqueda en tiempo real
    let searchTimeout;
    let isLoading = false;
    
    // Búsqueda principal en tiempo real
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
    }

    // Filtros avanzados en tiempo real
    const filtros = ['filtroNombre', 'filtroLugar', 'filtroEstado'];
    filtros.forEach(filtroId => {
        const elemento = document.getElementById(filtroId);
        if (elemento) {
            elemento.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch();
                }, 500);
            });
            
            elemento.addEventListener('change', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch();
                }, 500);
            });
        }
    });

    // Función para mostrar/ocultar indicador de carga
    function toggleLoading(show) {
        const tableContainer = document.querySelector('.mt-6.bg-white.rounded-lg.shadow .overflow-x-auto');
        if (tableContainer) {
            if (show) {
                tableContainer.style.opacity = '0.5';
                tableContainer.style.pointerEvents = 'none';
            } else {
                tableContainer.style.opacity = '1';
                tableContainer.style.pointerEvents = 'auto';
            }
        }
    }

    // Función para realizar la búsqueda
    function performSearch() {
        if (isLoading) return; // Evitar múltiples peticiones simultáneas
        
        isLoading = true;
        toggleLoading(true);
        
        // Obtener valores de búsqueda
        const searchValue = document.getElementById('searchInput')?.value || '';
        const filtroNombre = document.getElementById('filtroNombre')?.value || '';
        const filtroLugar = document.getElementById('filtroLugar')?.value || '';
        const filtroEstado = document.getElementById('filtroEstado')?.value || '';
        const perPage = document.querySelector('select[onchange*="changePerPage"]')?.value || '10';
        
        // Construir parámetros de búsqueda
        const params = new URLSearchParams();
        if (searchValue.trim()) params.set('search', searchValue.trim());
        if (filtroNombre.trim()) params.set('filtro_nombre', filtroNombre.trim());
        if (filtroLugar.trim()) params.set('filtro_lugar', filtroLugar.trim());
        if (filtroEstado) params.set('filtro_estado', filtroEstado);
        params.set('per_page', perPage);
        params.set('page', '1'); // Reset to first page
        
        // Realizar petición AJAX
        fetch(`{{ route('torneos.index') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Crear un elemento temporal para parsear el HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Extraer solo la tabla y paginación
            const newTable = tempDiv.querySelector('table');
            const newPagination = tempDiv.querySelector('.px-6.py-4.border-t.bg-gray-50');
            
            if (newTable) {
                // Reemplazar la tabla actual
                const currentTable = document.querySelector('table');
                if (currentTable) {
                    currentTable.parentNode.replaceChild(newTable, currentTable);
                }
            }
            
            if (newPagination) {
                // Reemplazar la paginación actual
                const currentPagination = document.querySelector('.px-6.py-4.border-t.bg-gray-50');
                if (currentPagination) {
                    currentPagination.parentNode.replaceChild(newPagination, currentPagination);
                }
            }
            
            // Actualizar la URL sin recargar la página
            const newUrl = new URL(window.location);
            newUrl.search = params.toString();
            window.history.pushState({}, '', newUrl.toString());
        })
        .catch(error => {
            console.error('Error en la búsqueda:', error);
        })
        .finally(() => {
            isLoading = false;
            toggleLoading(false);
        });
    }

    // Función global para cambiar registros por página (usada por pagination.custom)
    window.changePerPage = function(value) {
        // Actualizar el parámetro per_page en la URL actual
        const url = new URL(window.location);
        url.searchParams.set('per_page', value);
        url.searchParams.delete('page'); // Reset to first page
        
        // Realizar petición AJAX
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Crear un elemento temporal para parsear el HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Extraer solo la tabla y paginación
            const newTable = tempDiv.querySelector('table');
            const newPagination = tempDiv.querySelector('.px-6.py-4.border-t.bg-gray-50');
            
            if (newTable) {
                // Reemplazar la tabla actual
                const currentTable = document.querySelector('table');
                if (currentTable) {
                    currentTable.parentNode.replaceChild(newTable, currentTable);
                }
            }
            
            if (newPagination) {
                // Reemplazar la paginación actual
                const currentPagination = document.querySelector('.px-6.py-4.border-t.bg-gray-50');
                if (currentPagination) {
                    currentPagination.parentNode.replaceChild(newPagination, currentPagination);
                }
            }
            
            // Actualizar la URL sin recargar la página
            window.history.pushState({}, '', url.toString());
        })
        .catch(error => {
            console.error('Error al cambiar registros por página:', error);
        });
    };

    // Función para exportar datos
    const btnExportar = document.getElementById('btnExportarTorneos');
    if (btnExportar) {
        btnExportar.addEventListener('click', function() {
            // Crear CSV con todos los datos visibles
            const tabla = document.querySelector('table tbody');
            const filas = Array.from(tabla.querySelectorAll('tr'));
            
            if (filas.length === 0) {
                alert('No hay datos para exportar');
                return;
            }

            // Obtener encabezados (excluyendo columna de acciones)
            const encabezados = [];
            const thElements = document.querySelectorAll('thead th');
            thElements.forEach((th, index) => {
                if (index < thElements.length - 1 || !th.textContent.trim().includes('Acciones')) {
                    encabezados.push(th.textContent.trim());
                }
            });

            const csvContent = [
                encabezados.join(','),
                ...filas.map(fila => {
                    const celdas = Array.from(fila.querySelectorAll('td'));
                    // Excluir última celda si es columna de acciones
                    const celdasAExportar = celdas.length > 0 && celdas[celdas.length - 1].querySelector('button') ? 
                        celdas.slice(0, -1) : celdas;
                    return celdasAExportar.map(celda => `"${celda.textContent.trim().replace(/"/g, '""')}"`).join(',');
                })
            ].join('\n');

            // Descargar archivo
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'torneos.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
});

function confirmarEliminacion(torneoId) {
    const modal = document.getElementById('modal-confirmacion');
    const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    btnConfirmar.onclick = function() {
        document.getElementById('form-eliminar-' + torneoId).submit();
    };
}

function cerrarModal() {
    const modal = document.getElementById('modal-confirmacion');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('modal-confirmacion').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

function confirmarCancelacion(torneoId) {
    const modal = document.getElementById('modal-cancelacion');
    const form = document.getElementById('form-cancelar-torneo');
    const motivoInput = document.getElementById('motivo_cancelacion');
    // Cambia la acción del formulario al torneo correcto
    form.action = `/torneos/${torneoId}/cancelar`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    motivoInput.value = '';
}

function cerrarModalCancelacion() {
    const modal = document.getElementById('modal-cancelacion');
    const motivoInput = document.getElementById('motivo_cancelacion');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    motivoInput.value = '';
}

// Cerrar modal de cancelación al hacer clic fuera
document.getElementById('modal-cancelacion').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalCancelacion();
    }
});

// Función para cambiar elementos por página
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset a la primera página
    window.location.href = url.toString();
}

// Función para exportar torneos
document.getElementById('btnExportarTorneos').addEventListener('click', function() {
    // Solo registrar auditoría
    fetch('{{ route("torneos.export") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    });
});

</script>
@endpush 