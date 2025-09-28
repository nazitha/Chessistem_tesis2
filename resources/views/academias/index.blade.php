@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use Illuminate\Support\Facades\Log;
    
    // Verificar primero si tiene permiso de lectura
    if (!PermissionHelper::canViewModule('academias')) {
        // Si no tiene permiso de lectura, redirigir al home
        header('Location: ' . route('home'));
        exit;
    }
    
    // Debug de permisos
    Log::info('Vista academias: Verificando permisos', [
        'can_create' => PermissionHelper::canCreate('academias'),
        'can_update' => PermissionHelper::canUpdate('academias'),
        'can_delete' => PermissionHelper::canDelete('academias'),
        'has_any_action' => PermissionHelper::hasAnyActionPermission('academias')
    ]);
@endphp

<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center pb-4">
        <h1 class="text-2xl font-semibold">Academias</h1>
        @if(PermissionHelper::canCreate('academias'))
        <a href="{{ route('academias.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Academia
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
            <button id="btnMostrarBusquedaAcademias" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
            <button id="btnExportarAcademias" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
    </div>

    <!-- Controles de búsqueda -->
    <div id="panelBusquedaAcademias" class="bg-white shadow-md rounded-lg p-4 mb-4 {{ ($search || $filtroNombre || $filtroCorreo || $filtroRepresentante || $filtroCiudad || $filtroEstado) ? '' : 'hidden' }}">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Academias</h3>
            <button id="btnCancelarBusquedaAcademias" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        
        <form method="GET" action="{{ route('academias.index') }}" id="formBusquedaAcademias">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                    <input type="text" id="searchInput" name="search" value="{{ $search }}" placeholder="Buscar por nombre, correo, representante..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" id="btnBuscarAvanzadaAcademias" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                    </button>
                    <a href="{{ route('academias.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                        <i class="fas fa-brush mr-2"></i>Limpiar
                    </a>
                </div>
            </div>
            
            <!-- Panel de búsqueda avanzada -->
            <div id="panelBusquedaAvanzadaAcademias" class="mt-4 p-4 bg-gray-50 rounded-md {{ ($filtroNombre || $filtroCorreo || $filtroRepresentante || $filtroCiudad || $filtroEstado || $filtroParticipantes || $filtroTorneos) ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                        <input type="text" id="filtroNombre" name="filtro_nombre" value="{{ $filtroNombre }}" placeholder="Filtrar por nombre" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo:</label>
                        <input type="text" id="filtroCorreo" name="filtro_correo" value="{{ $filtroCorreo }}" placeholder="Filtrar por correo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Representante:</label>
                        <input type="text" id="filtroRepresentante" name="filtro_representante" value="{{ $filtroRepresentante }}" placeholder="Filtrar por representante" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad:</label>
                        <input type="text" id="filtroCiudad" name="filtro_ciudad" value="{{ $filtroCiudad }}" placeholder="Filtrar por ciudad" 
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Participantes:</label>
                        <input type="number" id="filtroParticipantes" name="filtro_participantes" value="{{ $filtroParticipantes }}" placeholder="Mínimo participantes" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Torneos:</label>
                        <input type="number" id="filtroTorneos" name="filtro_torneos" value="{{ $filtroTorneos }}" placeholder="Mínimo torneos" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0">
                    </div>
                </div>
            </div>
            
            <!-- Campo oculto para mantener per_page -->
            <input type="hidden" name="per_page" value="{{ $perPage }}">
        </form>
    </div>

    <!-- Container para las cards de academias -->
    <div id="academias-cards-container" class="mt-6">
        @forelse($academias as $academia)
            <div class="bg-white shadow-md rounded-lg academia-card mb-4">
                
                <!-- Header -->
                <div class="flex justify-between items-center px-6 py-3 bg-gray-800 text-white rounded-t-lg">
                    <div>
                        <h5 class="text-xl font-bold mb-1">
                            {{ $academia->nombre_academia }}
                        </h5>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($academia->estado_academia)
                            bg-green-100 text-green-800
                        @else
                            bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ $academia->estado_academia ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <!-- Body -->
                <div class="px-6 py-4 text-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Contacto -->
                        <div class="border rounded-lg bg-gray-50 p-4 shadow-sm">
                            <h6 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-1">
                                Contacto
                            </h6>
                            <div class="space-y-2">
                                <p class="text-sm"><b>Correo:</b> {{ $academia->correo_academia }}</p>
                                <p class="text-sm"><b>Teléfono:</b> {{ $academia->telefono_academia }}</p>
                                <p class="text-sm"><b>Representante:</b> {{ $academia->representante_academia }}</p>
                                <p class="text-sm"><b>Dirección:</b> {{ $academia->direccion_academia }}</p>
                                <p class="text-sm"><b>Ciudad:</b> 
                                    {{ $academia->ciudad ? $academia->ciudad->nombre_ciudad . ', ' . 
                                       ($academia->ciudad->departamento->nombre_depto ?? '-') . ' (' . 
                                       ($academia->ciudad->departamento->pais->nombre_pais ?? '-') . ')' : 'Sin ciudad' }}
                                </p>
                            </div>
                        </div>

                        <!-- Detalles adicionales -->
                        <div class="border rounded-lg bg-gray-50 p-4 shadow-sm">
                            <h6 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-1">
                                Detalles adicionales
                            </h6>
                            <div class="space-y-2">
                                <p class="text-sm"><b>Participantes registrados:</b> {{ $academia->participantes_registrados }}</p>
                                <p class="text-sm"><b>Torneos participados:</b> {{ $academia->torneos_participados }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer con botones -->
                @if(PermissionHelper::hasAnyAcademiaActionPermission())
                <div class="flex gap-2 px-6 py-2 border-t bg-gray-50 rounded-b-lg justify-end">
                    @if(PermissionHelper::canViewModule('academias'))
                        <a href="{{ route('academias.show', $academia) }}" 
                           class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    @endif
                    
                    @if(PermissionHelper::canUpdate('academias'))
                        <a href="{{ route('academias.edit', $academia) }}" 
                           class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endif
                    
                    @if(PermissionHelper::canDelete('academias'))
                        <button type="button"
                                onclick="confirmarEliminacion('{{ $academia->id_academia }}')"
                                class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    @endif
                    
                    @if(PermissionHelper::canDelete('academias'))
                        <form id="form-eliminar-{{ $academia->id_academia }}" 
                              action="{{ route('academias.destroy', $academia) }}" 
                              method="POST" 
                              class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
                @endif
            </div>
        @empty
            <div class="bg-white shadow-md rounded-lg p-8 text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-search text-4xl text-gray-300 mb-2"></i>
                    <p class="text-lg font-medium text-gray-500">No se encontraron resultados</p>
                    <p class="text-sm text-gray-400">Intenta ajustar los filtros de búsqueda</p>
                </div>
            </div>
        @endforelse
    </div>
        
        <!-- Paginación de Laravel -->
        <div id="academias-pagination" class="px-6 py-4 border-t bg-gray-50">
            <div class="flex flex-col gap-4">
                <!-- Selector de registros por página (siempre visible) -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-700">Mostrar:</span>
                        <select onchange="changePerPageAcademias(this.value)" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-700">por página</span>
                    </div>
                    
                    <!-- Información de paginación -->
                    <div class="text-sm text-gray-700">
                        Mostrando {{ $academias->firstItem() ?? 0 }} a {{ $academias->lastItem() ?? 0 }} registros de {{ $academias->total() }} resultados
                    </div>
                </div>
                
                <!-- Enlaces de paginación -->
                <div class="flex-1 flex items-center justify-center">
                    {{ $academias->links('pagination.custom') }}
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
            ¿Estás seguro de que deseas eliminar esta academia? Esta acción no se puede deshacer.
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

function confirmarEliminacion(academiaId) {
    const modal = document.getElementById('modal-confirmacion');
    const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    btnConfirmar.onclick = function() {
        document.querySelector(`form[action$="${academiaId}"]`).submit();
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
function performSearchAcademias() {
    if (isLoading) return;
    
    const searchInput = document.getElementById('searchInput');
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroCorreo = document.getElementById('filtroCorreo');
    const filtroRepresentante = document.getElementById('filtroRepresentante');
    const filtroCiudad = document.getElementById('filtroCiudad');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroParticipantes = document.getElementById('filtroParticipantes');
    const filtroTorneos = document.getElementById('filtroTorneos');
    const perPageSelect = document.querySelector('select[onchange="changePerPageAcademias(this.value)"]');
    
    const params = new URLSearchParams();
    
    if (searchInput && searchInput.value.trim()) {
        params.append('search', searchInput.value.trim());
    }
    if (filtroNombre && filtroNombre.value.trim()) {
        params.append('filtro_nombre', filtroNombre.value.trim());
    }
    if (filtroCorreo && filtroCorreo.value.trim()) {
        params.append('filtro_correo', filtroCorreo.value.trim());
    }
    if (filtroRepresentante && filtroRepresentante.value.trim()) {
        params.append('filtro_representante', filtroRepresentante.value.trim());
    }
    if (filtroCiudad && filtroCiudad.value.trim()) {
        params.append('filtro_ciudad', filtroCiudad.value.trim());
    }
    if (filtroEstado && filtroEstado.value !== '') {
        params.append('filtro_estado', filtroEstado.value);
    }
    if (filtroParticipantes && filtroParticipantes.value.trim()) {
        params.append('filtro_participantes', filtroParticipantes.value.trim());
    }
    if (filtroTorneos && filtroTorneos.value.trim()) {
        params.append('filtro_torneos', filtroTorneos.value.trim());
    }
    if (perPageSelect && perPageSelect.value) {
        params.append('per_page', perPageSelect.value);
    }
    
    // Actualizar URL sin recargar página
    const newUrl = '{{ route("academias.index") }}' + (params.toString() ? '?' + params.toString() : '');
    window.history.pushState({}, '', newUrl);
    
    // Realizar petición AJAX
    toggleLoadingAcademias(true);
    
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
        
        // Actualizar container de cards de academias
        const newCardsContainer = doc.querySelector('#academias-cards-container');
        if (newCardsContainer) {
            const currentCardsContainer = document.querySelector('#academias-cards-container');
            if (currentCardsContainer) {
                currentCardsContainer.innerHTML = newCardsContainer.innerHTML;
            }
        }
        
        // Actualizar paginación
        const newPaginationContainer = doc.querySelector('#academias-pagination');
        if (newPaginationContainer) {
            const currentPaginationContainer = document.querySelector('#academias-pagination');
            if (currentPaginationContainer) {
                currentPaginationContainer.outerHTML = newPaginationContainer.outerHTML;
            }
        }
        
        
        toggleLoadingAcademias(false);
    })
    .catch(error => {
        console.error('Error en búsqueda:', error);
        toggleLoadingAcademias(false);
    });
}

// Función para cambiar registros por página - igual que en Torneos
function changePerPageAcademias(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset a la primera página
    window.location.href = url.toString();
}



// Función para mostrar/ocultar loading
function toggleLoadingAcademias(show) {
    isLoading = show;
    const cardsContainer = document.querySelector('#academias-cards-container');
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
    const btnMostrarBusqueda = document.getElementById('btnMostrarBusquedaAcademias');
    if (btnMostrarBusqueda) {
        btnMostrarBusqueda.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaAcademias');
            if (panel) {
                panel.classList.remove('hidden');
            }
        });
    }
    
    // Cancelar búsqueda
    const btnCancelarBusqueda = document.getElementById('btnCancelarBusquedaAcademias');
    if (btnCancelarBusqueda) {
        btnCancelarBusqueda.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaAcademias');
            if (panel) {
                panel.classList.add('hidden');
            }
        });
    }
    
    // Búsqueda avanzada
    const btnBuscarAvanzada = document.getElementById('btnBuscarAvanzadaAcademias');
    if (btnBuscarAvanzada) {
        btnBuscarAvanzada.addEventListener('click', function() {
            const panel = document.getElementById('panelBusquedaAvanzadaAcademias');
            if (panel) {
                panel.classList.toggle('hidden');
            }
        });
    }
    
    // Búsqueda en tiempo real con debounce
    const debouncedSearch = debounce(performSearchAcademias, 500);
    
    // Input de búsqueda principal
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debouncedSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('formBusquedaAcademias').submit();
            }
        });
    }
    
    // Filtros avanzados
    const filtros = ['filtroNombre', 'filtroCorreo', 'filtroRepresentante', 'filtroCiudad', 'filtroEstado', 'filtroParticipantes', 'filtroTorneos'];
    filtros.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('input', debouncedSearch);
            elemento.addEventListener('change', debouncedSearch);
        }
    });
    
    // Botón de exportación
    const btnExportarAcademias = document.getElementById('btnExportarAcademias');
    if (btnExportarAcademias) {
        btnExportarAcademias.addEventListener('click', function() {
            window.location.href = '{{ url("api/export/academias") }}';
        });
    }
    
});
</script>
@endpush

@endsection 