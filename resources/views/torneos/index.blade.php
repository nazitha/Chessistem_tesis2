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
        <div class="flex flex-wrap gap-4 items-end">
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
            <div id="panelBusquedaAvanzadaTorneos" class="mt-4 p-4 bg-gray-50 rounded-md hidden">
            
            <!-- Primera fila: Todos los filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Participantes:</label>
                    <input type="number" id="filtroParticipantes" name="filtro_participantes" value="{{ $filtroParticipantes ?? '' }}" placeholder="Ej: 10" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md" min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Rondas disputadas:</label>
                    <input type="number" id="filtroRondas" name="filtro_rondas" value="{{ $filtroRondas ?? '' }}" placeholder="Ej: 3" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total de rondas:</label>
                    <input type="number" id="filtroRondasTotal" name="filtro_rondas_total" value="{{ $filtroRondasTotal ?? '' }}" placeholder="Ej: 7" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md" min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rondas a disputar:</label>
                    <input type="number" id="filtroRondasDisputar" name="filtro_rondas_disputar" value="{{ $filtroRondasDisputar ?? '' }}" placeholder="Ej: 4" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría:</label>
                    <select id="filtroCategoria" name="filtro_categoria" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todas las categorías</option>
                        @foreach($categoriasTorneo as $categoria)
                            <option value="{{ $categoria->id }}" {{ ($filtroCategoria ?? '') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->categoria_torneo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Federación:</label>
                    <select id="filtroFederacion" name="filtro_federacion" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todas las federaciones</option>
                        @foreach($federaciones as $federacion)
                            <option value="{{ $federacion->id_federacion }}" {{ ($filtroFederacion ?? '') == $federacion->id_federacion ? 'selected' : '' }}>
                                {{ $federacion->nombre_federacion }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organizador:</label>
                    <input type="text" id="filtroOrganizador" name="filtro_organizador" value="{{ $filtroOrganizador ?? '' }}" placeholder="Nombre del organizador" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Director:</label>
                    <input type="text" id="filtroDirector" name="filtro_director" value="{{ $filtroDirector ?? '' }}" placeholder="Nombre del director" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Árbitro:</label>
                    <input type="text" id="filtroArbitro" name="filtro_arbitro" value="{{ $filtroArbitro ?? '' }}" placeholder="Nombre del árbitro" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Árbitro Principal:</label>
                    <input type="text" id="filtroArbitroPrincipal" name="filtro_arbitro_principal" value="{{ $filtroArbitroPrincipal ?? '' }}" placeholder="Nombre del árbitro principal" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Árbitro Adjunto:</label>
                    <input type="text" id="filtroArbitroAdjunto" name="filtro_arbitro_adjunto" value="{{ $filtroArbitroAdjunto ?? '' }}" placeholder="Nombre del árbitro adjunto" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
            </div>
            <!-- Campos ocultos para mantener per_page -->
            <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
        </form>
    </div>

    <!-- Contenedor de cards para torneos -->
    <div id="torneos-cards-container" class="mt-6">
        @forelse($torneos as $torneo)
            <div class="bg-white shadow-md rounded-lg torneo-card mb-4">
                
                <!-- Header -->
                <div class="flex justify-between items-center px-6 py-3 bg-gray-800 text-white rounded-t-lg">
                    <div>
                        <h5 class="text-xl font-bold mb-1">
                            {{ $torneo->nombre_torneo }}
                        </h5>
                        <p class="text-sm text-gray-200">
                            Fecha de inicio: {{ $torneo->fecha_inicio->format('j \d\e F \d\e Y') }}, {{ $torneo->hora_inicio ? $torneo->hora_inicio->format('h:i A') : 'Sin hora' }}
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
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
                </div>

                <!-- Body -->
                <div class="px-6 py-4 text-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Detalles Generales -->
                        <div class="border rounded-lg bg-gray-50 p-4 shadow-sm">
                            <h6 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-1">
                                Detalles Generales
                            </h6>
                            <div class="space-y-2">
                                <p class="text-sm"><b>Categoría:</b> {{ $torneo->categoria->categoria_torneo ?? 'Sin categoría' }}</p>
                                <p class="text-sm"><b>Lugar:</b> {{ $torneo->lugar }}</p>
                                <p class="text-sm"><b>Total de rondas:</b> {{ $torneo->no_rondas }}</p>
                                <p class="text-sm"><b>Participantes inscritos:</b> {{ $torneo->participantes()->count() }}</p>
                                <p class="text-sm"><b>Rondas disputadas:</b> {{ $torneo->rondas()->count() }}</p>
                                <p class="text-sm"><b>Rondas a disputar:</b> {{ max(0, $torneo->no_rondas - $torneo->rondas()->count()) }}</p>
                                @if($torneo->torneo_cancelado && $torneo->motivo_cancelacion)
                                <p class="text-sm"><b>Motivo cancelación:</b> <span class="text-red-600">{{ $torneo->motivo_cancelacion }}</span></p>
                                @endif
                            </div>
                        </div>

                        <!-- Organización -->
                        <div class="border rounded-lg bg-gray-50 p-4 shadow-sm">
                            <h6 class="font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-1">
                                Organización
                            </h6>
                            <div class="space-y-2">
                                <p class="text-sm"><b>Organizador:</b> {{ $torneo->organizador ? $torneo->organizador->nombres . ' ' . $torneo->organizador->apellidos : 'Sin asignar' }}</p>
                                <p class="text-sm"><b>Director:</b> {{ $torneo->directorTorneo ? $torneo->directorTorneo->nombres . ' ' . $torneo->directorTorneo->apellidos : 'Sin asignar' }}</p>
                                <p class="text-sm"><b>Árbitro:</b> {{ $torneo->arbitro ? $torneo->arbitro->nombres . ' ' . $torneo->arbitro->apellidos : 'Sin asignar' }}</p>
                                <p class="text-sm"><b>Árbitro Principal:</b> {{ $torneo->arbitroPrincipal ? $torneo->arbitroPrincipal->nombres . ' ' . $torneo->arbitroPrincipal->apellidos : 'Sin asignar' }}</p>
                                <p class="text-sm"><b>Árbitro Adjunto:</b> {{ $torneo->arbitroAdjunto ? $torneo->arbitroAdjunto->nombres . ' ' . $torneo->arbitroAdjunto->apellidos : 'Sin asignar' }}</p>
                                <p class="text-sm"><b>Federación:</b> {{ $torneo->federacion->nombre_federacion ?? 'Sin federación' }}</p>
                                <p class="text-sm"><b>Formato:</b> {{ $torneo->controlTiempo->formato ?? 'Sin formato' }}</p>
                                <p class="text-sm"><b>Sistema:</b> {{ $torneo->emparejamiento->sistema ?? 'Sin sistema' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer con botones -->
                @if(PermissionHelper::hasAnyTorneoActionPermission())
                <div class="flex gap-2 px-6 py-2 border-t bg-gray-50 rounded-b-lg justify-end">
                    @if(PermissionService::hasPermission('torneos.details'))
                        <a href="{{ route('torneos.show', $torneo) }}" 
                           class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    @endif
                    
                    @if(PermissionHelper::canUpdate('torneos'))
                        <a href="{{ route('torneos.edit', $torneo) }}" 
                           class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endif

                    @if(PermissionHelper::canCancel())
                        @if(!$torneo->torneo_cancelado && !$torneo->fecha_inicio->isPast())
                            <button type="button"
                                    onclick="confirmarCancelacion('{{ $torneo->id }}')"
                                    class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition">
                                <i class="fas fa-ban"></i> Cancelar
                            </button>
                        @endif
                    @endif
                    
                    @if(PermissionHelper::canDelete('torneos'))
                        <button type="button"
                                onclick="confirmarEliminacion('{{ $torneo->id }}')"
                                class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition">
                            <i class="fas fa-trash"></i> Eliminar
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
    <div id="torneos-pagination" class="px-6 py-4 border-t bg-gray-50 mt-6">
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

    /* Estilos para las cards de torneos */
    .torneo-card {
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .torneo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
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
        // Asegurar que esté oculto al cargar la página
        panelBusquedaAvanzada.classList.add('hidden');
        
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
    const filtros = ['filtroNombre', 'filtroLugar', 'filtroEstado', 'filtroParticipantes', 'filtroRondas', 'filtroRondasTotal', 'filtroRondasDisputar', 'filtroCategoria', 'filtroFederacion', 'filtroOrganizador', 'filtroDirector', 'filtroArbitro', 'filtroArbitroPrincipal', 'filtroArbitroAdjunto'];
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
        const cardsContainer = document.getElementById('torneos-cards-container');
        if (cardsContainer) {
            if (show) {
                cardsContainer.style.opacity = '0.5';
                cardsContainer.style.pointerEvents = 'none';
            } else {
                cardsContainer.style.opacity = '1';
                cardsContainer.style.pointerEvents = 'auto';
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
        const filtroParticipantes = document.getElementById('filtroParticipantes')?.value || '';
        const filtroRondas = document.getElementById('filtroRondas')?.value || '';
        const filtroRondasTotal = document.getElementById('filtroRondasTotal')?.value || '';
        const filtroRondasDisputar = document.getElementById('filtroRondasDisputar')?.value || '';
        const filtroCategoria = document.getElementById('filtroCategoria')?.value || '';
        const filtroFederacion = document.getElementById('filtroFederacion')?.value || '';
        const filtroOrganizador = document.getElementById('filtroOrganizador')?.value || '';
        const filtroDirector = document.getElementById('filtroDirector')?.value || '';
        const filtroArbitro = document.getElementById('filtroArbitro')?.value || '';
        const filtroArbitroPrincipal = document.getElementById('filtroArbitroPrincipal')?.value || '';
        const filtroArbitroAdjunto = document.getElementById('filtroArbitroAdjunto')?.value || '';
        const perPage = document.querySelector('select[onchange*="changePerPage"]')?.value || '10';
        
        // Construir parámetros de búsqueda
        const params = new URLSearchParams();
        if (searchValue.trim()) params.set('search', searchValue.trim());
        if (filtroNombre.trim()) params.set('filtro_nombre', filtroNombre.trim());
        if (filtroLugar.trim()) params.set('filtro_lugar', filtroLugar.trim());
        if (filtroEstado) params.set('filtro_estado', filtroEstado);
        if (filtroParticipantes.trim()) params.set('filtro_participantes', filtroParticipantes.trim());
        if (filtroRondas.trim()) params.set('filtro_rondas', filtroRondas.trim());
        if (filtroRondasTotal.trim()) params.set('filtro_rondas_total', filtroRondasTotal.trim());
        if (filtroRondasDisputar.trim()) params.set('filtro_rondas_disputar', filtroRondasDisputar.trim());
        if (filtroCategoria) params.set('filtro_categoria', filtroCategoria);
        if (filtroFederacion) params.set('filtro_federacion', filtroFederacion);
        if (filtroOrganizador.trim()) params.set('filtro_organizador', filtroOrganizador.trim());
        if (filtroDirector.trim()) params.set('filtro_director', filtroDirector.trim());
        if (filtroArbitro.trim()) params.set('filtro_arbitro', filtroArbitro.trim());
        if (filtroArbitroPrincipal.trim()) params.set('filtro_arbitro_principal', filtroArbitroPrincipal.trim());
        if (filtroArbitroAdjunto.trim()) params.set('filtro_arbitro_adjunto', filtroArbitroAdjunto.trim());
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
            
            // Extraer solo las cards y paginación
            const newCards = tempDiv.querySelector('#torneos-cards-container');
            const newPagination = tempDiv.querySelector('#torneos-pagination');
            
            if (newCards) {
                // Reemplazar las cards actuales
                const currentCards = document.getElementById('torneos-cards-container');
                if (currentCards) {
                    currentCards.innerHTML = newCards.innerHTML;
                }
            }
            
            if (newPagination) {
                // Reemplazar la paginación actual
                const currentPagination = document.querySelector('#torneos-pagination');
                if (currentPagination) {
                    currentPagination.outerHTML = newPagination.outerHTML;
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
            
            // Extraer solo las cards y paginación
            const newCards = tempDiv.querySelector('#torneos-cards-container');
            const newPagination = tempDiv.querySelector('#torneos-pagination');
            
            if (newCards) {
                // Reemplazar las cards actuales
                const currentCards = document.getElementById('torneos-cards-container');
                if (currentCards) {
                    currentCards.innerHTML = newCards.innerHTML;
                }
            }
            
            if (newPagination) {
                // Reemplazar la paginación actual
                const currentPagination = document.querySelector('#torneos-pagination');
                if (currentPagination) {
                    currentPagination.outerHTML = newPagination.outerHTML;
                }
            }
            
            // Actualizar la URL sin recargar la página
            window.history.pushState({}, '', url.toString());
        })
        .catch(error => {
            console.error('Error al cambiar registros por página:', error);
        });
    };

});

</script>
<script>
function confirmarEliminacion(torneoId) {
    const modal = document.getElementById('modal-confirmacion');
    const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    btnConfirmar.onclick = function() {
        // Crear formulario temporal para eliminación
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${window.location.origin}/torneos/${torneoId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
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
    form.action = `${window.location.origin}/torneos/${torneoId}/cancelar`;
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
    window.location.href = '{{ url("api/export/torneos") }}';
});

</script>
@endpush 