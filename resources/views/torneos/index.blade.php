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
                <i class="fas fa-plus mr-2"></i>
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
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Torneos</h3>
            <button id="btnCancelarBusquedaTorneos" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                <input type="text" id="buscarTorneos" placeholder="Buscar por nombre, lugar, categoría..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button id="btnBuscarAvanzadaTorneos" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                    Búsqueda Avanzada
                </button>
                <button id="btnLimpiarBusquedaTorneos" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                    Limpiar
                </button>
            </div>
        </div>
        <!-- Panel de búsqueda avanzada -->
        <div id="panelBusquedaAvanzadaTorneos" class="mt-4 p-4 bg-gray-50 rounded-md hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                    <input type="text" id="filtroNombre" placeholder="Filtrar por nombre" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lugar:</label>
                    <input type="text" id="filtroLugar" placeholder="Filtrar por lugar" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                    <select id="filtroEstado" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Borrador">Borrador</option>
                        <option value="Finalizado">Finalizado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>
            </div>
        </div>
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
                            <td colspan="{{ PermissionHelper::hasAnyTorneoActionPermission() ? '6' : '5' }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                No hay torneos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Controles de paginación -->
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-700">Mostrar:</span>
                    <select id="registrosPorPaginaTorneos" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">registros por página</span>
                </div>
                <div class="flex items-center gap-2">
                    <button id="btnAnteriorTorneos" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        Anterior
                    </button>
                    <span id="infoPaginacionTorneos" class="text-sm text-gray-700">Página 1 de 1</span>
                    <button id="btnSiguienteTorneos" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        Siguiente
                    </button>
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
// Clase para manejar la tabla personalizada
class TablaPersonalizada {
    constructor(tabla, config) {
        this.tabla = tabla;
        this.config = config;
        this.filasOriginales = Array.from(tabla.querySelectorAll('tbody tr'));
        this.filasFiltradas = [...this.filasOriginales];
        this.paginaActual = 1;
        this.registrosPorPagina = 10;
        this.inicializar();
    }

    inicializar() {
        // Configurar eventos de búsqueda
        const inputBusqueda = document.getElementById(this.config.inputBusqueda);
        if (inputBusqueda) {
            inputBusqueda.addEventListener('input', (e) => {
                this.filtrar(e.target.value);
            });
        }

        // Configurar eventos de filtros avanzados
        const filtros = ['filtroNombre', 'filtroLugar', 'filtroEstado'];
        filtros.forEach(filtro => {
            const elemento = document.getElementById(filtro);
            if (elemento) {
                elemento.addEventListener('input', () => this.aplicarFiltrosAvanzados());
                elemento.addEventListener('change', () => this.aplicarFiltrosAvanzados());
            }
        });

        // Configurar eventos de paginación
        const selectRegistros = document.getElementById(this.config.selectRegistros);
        if (selectRegistros) {
            selectRegistros.addEventListener('change', (e) => {
                this.registrosPorPagina = parseInt(e.target.value);
                this.paginaActual = 1;
                this.aplicarPaginacion();
            });
        }

        const btnAnterior = document.getElementById(this.config.btnAnterior);
        const btnSiguiente = document.getElementById(this.config.btnSiguiente);
        if (btnAnterior) btnAnterior.addEventListener('click', () => this.cambiarPagina(-1));
        if (btnSiguiente) btnSiguiente.addEventListener('click', () => this.cambiarPagina(1));

        // Configurar eventos de exportación
        const btnExportar = document.getElementById(this.config.btnExportar);
        if (btnExportar) {
            btnExportar.addEventListener('click', () => this.exportarDatos());
        }

        // Configurar eventos de búsqueda avanzada
        const btnBuscarAvanzada = document.getElementById(this.config.btnBuscarAvanzada);
        if (btnBuscarAvanzada) {
            btnBuscarAvanzada.addEventListener('click', () => this.toggleBusquedaAvanzada());
        }

        // Configurar eventos de limpiar
        const btnLimpiar = document.getElementById(this.config.btnLimpiar);
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => this.limpiarFiltros());
        }

        // Configurar eventos de mostrar/cancelar búsqueda
        const btnMostrarBusqueda = document.getElementById(this.config.btnMostrarBusqueda);
        const btnCancelarBusqueda = document.getElementById(this.config.btnCancelarBusqueda);
        if (btnMostrarBusqueda) {
            btnMostrarBusqueda.addEventListener('click', () => this.mostrarPanelBusqueda());
        }
        if (btnCancelarBusqueda) {
            btnCancelarBusqueda.addEventListener('click', () => this.cancelarBusqueda());
        }

        // Verificar estado del botón exportar y aplicar paginación inicial
        this.verificarEstadoExportar();
        this.aplicarPaginacion();
    }

    filtrar(texto) {
        const textoLower = texto.toLowerCase();
        this.filasFiltradas = this.filasOriginales.filter(fila => {
            const textoFila = fila.textContent.toLowerCase();
            return textoFila.includes(textoLower);
        });
        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }

    aplicarFiltrosAvanzados() {
        const filtroNombre = document.getElementById('filtroNombre')?.value.toLowerCase() || '';
        const filtroLugar = document.getElementById('filtroLugar')?.value.toLowerCase() || '';
        const filtroEstado = document.getElementById('filtroEstado')?.value || '';

        this.filasFiltradas = this.filasOriginales.filter(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length === 0) return false;

            const nombre = celdas[0]?.textContent.toLowerCase() || '';
            const lugar = celdas[2]?.textContent.toLowerCase() || '';
            const estado = celdas[4]?.textContent.trim() || '';

            const cumpleNombre = !filtroNombre || nombre.includes(filtroNombre);
            const cumpleLugar = !filtroLugar || lugar.includes(filtroLugar);
            const cumpleEstado = !filtroEstado || estado === filtroEstado;

            return cumpleNombre && cumpleLugar && cumpleEstado;
        });

        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }

    mostrarPanelBusqueda() {
        const panel = document.getElementById(this.config.panelBusqueda);
        if (panel) panel.classList.remove('hidden');
    }

    cancelarBusqueda() {
        const panel = document.getElementById(this.config.panelBusqueda);
        if (panel) panel.classList.add('hidden');
        this.limpiarFiltros();
    }

    toggleBusquedaAvanzada() {
        const panel = document.getElementById(this.config.panelBusquedaAvanzada);
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }

    limpiarFiltros() {
        // Limpiar inputs de búsqueda
        const inputBusqueda = document.getElementById(this.config.inputBusqueda);
        if (inputBusqueda) inputBusqueda.value = '';

        // Limpiar filtros avanzados
        const filtros = ['filtroNombre', 'filtroLugar', 'filtroEstado'];
        filtros.forEach(filtro => {
            const elemento = document.getElementById(filtro);
            if (elemento) elemento.value = '';
        });

        // Ocultar panel de búsqueda avanzada
        const panel = document.getElementById(this.config.panelBusquedaAvanzada);
        if (panel) panel.classList.add('hidden');

        // Restaurar todas las filas
        this.filasFiltradas = [...this.filasOriginales];
        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }

    cambiarPagina(direccion) {
        const totalPaginas = Math.ceil(this.filasFiltradas.length / this.registrosPorPagina);
        this.paginaActual = Math.max(1, Math.min(totalPaginas, this.paginaActual + direccion));
        this.aplicarPaginacion();
    }

    aplicarPaginacion() {
        const inicio = (this.paginaActual - 1) * this.registrosPorPagina;
        const fin = inicio + this.registrosPorPagina;
        const filasAMostrar = this.filasFiltradas.slice(inicio, fin);

        // Ocultar todas las filas
        this.filasOriginales.forEach(fila => {
            fila.style.display = 'none';
        });

        // Mostrar solo las filas de la página actual
        filasAMostrar.forEach(fila => {
            fila.style.display = '';
        });

        // Actualizar información de paginación
        const totalPaginas = Math.ceil(this.filasFiltradas.length / this.registrosPorPagina);
        const infoPaginacion = document.getElementById(this.config.infoPaginacion);
        if (infoPaginacion) {
            infoPaginacion.textContent = `Página ${this.paginaActual} de ${totalPaginas}`;
        }

        // Actualizar estado de botones
        const btnAnterior = document.getElementById(this.config.btnAnterior);
        const btnSiguiente = document.getElementById(this.config.btnSiguiente);
        if (btnAnterior) btnAnterior.disabled = this.paginaActual === 1;
        if (btnSiguiente) btnSiguiente.disabled = this.paginaActual === totalPaginas;
        
        // Verificar estado del botón exportar
        this.verificarEstadoExportar();
    }

    verificarEstadoExportar() {
        const btnExportar = document.getElementById(this.config.btnExportar);
        if (btnExportar) {
            let tieneRegistros = false;
            
            // Si hay filtros aplicados, verificar las filas filtradas
            if (this.filasFiltradas.length !== this.filasOriginales.length) {
                tieneRegistros = this.filasFiltradas.length > 0;
            } else {
                // Si no hay filtros, verificar las filas originales en el DOM
                const filasEnTabla = this.tabla.querySelectorAll('tbody tr');
                tieneRegistros = Array.from(filasEnTabla).some(fila => {
                    // Excluir filas que contengan mensajes como "No hay torneos registrados"
                    const textoFila = fila.textContent.toLowerCase();
                    return !textoFila.includes('no hay') && !textoFila.includes('registrados') && !textoFila.includes('registradas');
                });
            }
            
            btnExportar.disabled = !tieneRegistros;
            
            if (!tieneRegistros) {
                btnExportar.classList.add('opacity-50', 'cursor-not-allowed');
                btnExportar.title = 'No hay registros para exportar';
            } else {
                btnExportar.classList.remove('opacity-50', 'cursor-not-allowed');
                btnExportar.title = 'Exportar registros';
            }
        }
    }

    exportarDatos() {
        // Obtener las filas filtradas (visibles)
        const filasAExportar = this.filasFiltradas;
        
        // Obtener los encabezados de la tabla (excluyendo la columna de acciones)
        const encabezados = [];
        const filasEncabezado = this.tabla.querySelectorAll('thead th');
        filasEncabezado.forEach((th, index) => {
            // Excluir la última columna si es "Acciones"
            if (index < filasEncabezado.length - 1 || !th.textContent.trim().includes('Acciones')) {
                encabezados.push(th.textContent.trim());
            }
        });
        
        // Preparar los datos para exportar
        const datos = [];
        filasAExportar.forEach(fila => {
            const filaDatos = [];
            const celdas = fila.querySelectorAll('td');
            
            // Excluir la última celda si es la columna de acciones
            const celdasAExportar = celdas.length > 0 && celdas[celdas.length - 1].querySelector('button') ? 
                Array.from(celdas).slice(0, -1) : Array.from(celdas);
            
            celdasAExportar.forEach(celda => {
                filaDatos.push(celda.textContent.trim());
            });
            
            datos.push(filaDatos);
        });
        
        // Crear el contenido CSV con BOM para UTF-8
        const BOM = '\uFEFF'; // Byte Order Mark para UTF-8
        let csvContent = BOM + encabezados.join(',') + '\n';
        datos.forEach(fila => {
            csvContent += fila.join(',') + '\n';
        });
        
        // Crear y descargar el archivo con codificación UTF-8
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'torneos_exportados.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

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

    // Inicializar tabla personalizada para torneos
    const tablaTorneos = document.querySelector('.bg-white.rounded-lg.shadow table');
    if (tablaTorneos) {
        new TablaPersonalizada(tablaTorneos, {
            inputBusqueda: 'buscarTorneos',
            panelBusqueda: 'panelBusquedaTorneos',
            panelBusquedaAvanzada: 'panelBusquedaAvanzadaTorneos',
            btnMostrarBusqueda: 'btnMostrarBusquedaTorneos',
            btnCancelarBusqueda: 'btnCancelarBusquedaTorneos',
            btnBuscarAvanzada: 'btnBuscarAvanzadaTorneos',
            btnLimpiar: 'btnLimpiarBusquedaTorneos',
            btnExportar: 'btnExportarTorneos',
            selectRegistros: 'registrosPorPaginaTorneos',
            btnAnterior: 'btnAnteriorTorneos',
            btnSiguiente: 'btnSiguienteTorneos',
            infoPaginacion: 'infoPaginacionTorneos'
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
</script>
@endpush 