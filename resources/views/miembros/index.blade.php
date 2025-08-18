@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use App\Services\PermissionService;
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="max-w-full mx-auto px-4">
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
    <div id="panelBusquedaMiembros" class="bg-white shadow-md rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Miembros</h3>
            <button id="btnCancelarBusquedaMiembros" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                <input type="text" id="buscarMiembros" placeholder="Buscar por cédula, nombres, apellidos, academia..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button id="btnBuscarAvanzadaMiembros" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                    Búsqueda Avanzada
                </button>
                <button id="btnLimpiarBusquedaMiembros" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                    Limpiar
                </button>
            </div>
        </div>
        
        <!-- Panel de búsqueda avanzada -->
        <div id="panelBusquedaAvanzadaMiembros" class="mt-4 p-4 bg-gray-50 rounded-md hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cédula:</label>
                    <input type="text" id="filtroCedula" placeholder="Filtrar por cédula" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombres:</label>
                    <input type="text" id="filtroNombres" placeholder="Filtrar por nombres" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos:</label>
                    <input type="text" id="filtroApellidos" placeholder="Filtrar por apellidos" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sexo:</label>
                    <select id="filtroSexo" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todos</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                    <select id="filtroEstado" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todos</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academia:</label>
                    <input type="text" id="filtroAcademia" placeholder="Filtrar por academia" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cédula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellidos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sexo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de nacimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de inscripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                        @if(PermissionHelper::hasAnyMiembroActionPermission())
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($miembros as $miembro)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $miembro->cedula }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $miembro->nombres }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $miembro->apellidos }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->sexo == 'M' ? 'Masculino' : 'Femenino' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->fecha_nacimiento ? \Carbon\Carbon::parse($miembro->fecha_nacimiento)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->fecha_inscripcion ? \Carbon\Carbon::parse($miembro->fecha_inscripcion)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($miembro->estado_miembro)
                                        bg-green-100 text-green-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ $miembro->estado_miembro ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->academia->nombre_academia ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->correo_sistema_id ?? '-' }}</td>
                            @if(PermissionHelper::hasAnyMiembroActionPermission())
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        @if(PermissionService::hasPermission('miembros.details'))
                                            <a href="{{ route('miembros.show', $miembro) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100 transition-colors duration-200"
                                               data-tooltip="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        @if(PermissionHelper::canUpdate('miembros'))
                                            <a href="{{ route('miembros.edit', $miembro) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-100 transition-colors duration-200"
                                               data-tooltip="Editar miembro">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(PermissionHelper::canDelete('miembros'))
                                            <button type="button"
                                                    onclick="confirmarEliminacion('{{ $miembro->cedula }}')"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 transition-colors duration-200"
                                                    data-tooltip="Eliminar miembro">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                                No hay miembros registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($miembros instanceof \Illuminate\Pagination\LengthAwarePaginator && $miembros->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $miembros->links() }}
            </div>
        @endif
        
        <!-- Controles de paginación personalizados -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <label class="text-sm text-gray-700 mr-2">Mostrar:</label>
                    <select id="registrosPorPaginaMiembros" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-700 ml-2">registros por página</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="btnAnteriorMiembros" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Anterior
                    </button>
                    <span id="infoPaginacionMiembros" class="text-sm text-gray-700">
                        Página <span id="paginaActualMiembros">1</span> de <span id="totalPaginasMiembros">1</span>
                    </span>
                    <button id="btnSiguienteMiembros" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed">
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

// Sistema de búsqueda y paginación personalizado para miembros
class TablaPersonalizada {
    constructor(tablaElement, config) {
        this.tabla = tablaElement;
        this.tbody = this.tabla.querySelector('tbody');
        this.filasOriginales = Array.from(this.tbody.querySelectorAll('tr'));
        this.filasFiltradas = [...this.filasOriginales];
        this.paginaActual = 1;
        this.registrosPorPagina = 10;
        this.config = config;
        
        this.inicializar();
    }
    
    inicializar() {
        this.configurarEventos();
        this.verificarEstadoExportar();
        this.aplicarPaginacion();
    }
    
    configurarEventos() {
        // Registros por página
        const selectRegistros = document.getElementById(this.config.selectRegistros);
        if (selectRegistros) {
            selectRegistros.addEventListener('change', (e) => {
                this.registrosPorPagina = parseInt(e.target.value);
                this.paginaActual = 1;
                this.aplicarPaginacion();
            });
        }
        
        // Botón de exportación
        const btnExportar = document.getElementById(this.config.btnExportar);
        if (btnExportar) {
            btnExportar.addEventListener('click', () => {
                this.exportarDatos();
            });
        }
        
        // Botones de paginación
        const btnAnterior = document.getElementById(this.config.btnAnterior);
        const btnSiguiente = document.getElementById(this.config.btnSiguiente);
        
        if (btnAnterior) {
            btnAnterior.addEventListener('click', () => {
                if (this.paginaActual > 1) {
                    this.paginaActual--;
                    this.aplicarPaginacion();
                }
            });
        }
        
        if (btnSiguiente) {
            btnSiguiente.addEventListener('click', () => {
                const totalPaginas = Math.ceil(this.filasFiltradas.length / this.registrosPorPagina);
                if (this.paginaActual < totalPaginas) {
                    this.paginaActual++;
                    this.aplicarPaginacion();
                }
            });
        }
        
        // Botón para mostrar panel de búsqueda
        const btnMostrarBusqueda = document.getElementById(this.config.btnMostrarBusqueda);
        if (btnMostrarBusqueda) {
            btnMostrarBusqueda.addEventListener('click', () => {
                this.mostrarPanelBusqueda();
            });
        }
        
        // Botón para cancelar búsqueda
        const btnCancelarBusqueda = document.getElementById(this.config.btnCancelarBusqueda);
        if (btnCancelarBusqueda) {
            btnCancelarBusqueda.addEventListener('click', () => {
                this.cancelarBusqueda();
            });
        }
        
        // Búsqueda general
        const inputBusqueda = document.getElementById(this.config.inputBusqueda);
        if (inputBusqueda) {
            inputBusqueda.addEventListener('input', (e) => {
                this.filtrar(e.target.value);
            });
        }
        
        // Búsqueda avanzada
        const btnBuscarAvanzada = document.getElementById(this.config.btnBuscarAvanzada);
        if (btnBuscarAvanzada) {
            btnBuscarAvanzada.addEventListener('click', () => {
                this.toggleBusquedaAvanzada();
            });
        }
        
        // Limpiar búsqueda
        const btnLimpiar = document.getElementById(this.config.btnLimpiar);
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => {
                this.limpiarFiltros();
            });
        }
        
        // Filtros avanzados
        if (this.config.filtrosAvanzados) {
            this.config.filtrosAvanzados.forEach(filtro => {
                const elemento = document.getElementById(filtro.id);
                if (elemento) {
                    elemento.addEventListener('input', () => {
                        this.aplicarFiltrosAvanzados();
                    });
                }
            });
        }
    }
    
    mostrarPanelBusqueda() {
        const panel = document.getElementById(this.config.panelBusqueda);
        if (panel) {
            panel.classList.remove('hidden');
        }
    }
    
    cancelarBusqueda() {
        this.limpiarFiltros();
        const panel = document.getElementById(this.config.panelBusqueda);
        if (panel) {
            panel.classList.add('hidden');
        }
    }
    
    filtrar(texto) {
        this.filasFiltradas = this.filasOriginales.filter(fila => {
            const textoFila = fila.textContent.toLowerCase();
            return textoFila.includes(texto.toLowerCase());
        });
        
        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }
    
    aplicarFiltrosAvanzados() {
        const filtros = {};
        
        if (this.config.filtrosAvanzados) {
            this.config.filtrosAvanzados.forEach(filtro => {
                const elemento = document.getElementById(filtro.id);
                if (elemento && elemento.value) {
                    filtros[filtro.campo] = elemento.value.toLowerCase();
                }
            });
        }
        
        this.filasFiltradas = this.filasOriginales.filter(fila => {
            const celdas = fila.querySelectorAll('td');
            
            for (const [campo, valor] of Object.entries(filtros)) {
                const indice = this.config.filtrosAvanzados.find(f => f.campo === campo)?.indice;
                if (indice !== undefined && celdas[indice]) {
                    const textoCelda = celdas[indice].textContent.toLowerCase();
                    if (!textoCelda.includes(valor)) {
                        return false;
                    }
                }
            }
            
            return true;
        });
        
        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }
    
    toggleBusquedaAvanzada() {
        const panel = document.getElementById(this.config.panelBusquedaAvanzada);
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }
    
    limpiarFiltros() {
        // Limpiar búsqueda general
        const inputBusqueda = document.getElementById(this.config.inputBusqueda);
        if (inputBusqueda) {
            inputBusqueda.value = '';
        }
        
        // Limpiar filtros avanzados
        if (this.config.filtrosAvanzados) {
            this.config.filtrosAvanzados.forEach(filtro => {
                const elemento = document.getElementById(filtro.id);
                if (elemento) {
                    elemento.value = '';
                }
            });
        }
        
        // Ocultar panel de búsqueda avanzada
        const panelAvanzado = document.getElementById(this.config.panelBusquedaAvanzada);
        if (panelAvanzado) {
            panelAvanzado.classList.add('hidden');
        }
        
        // Restaurar todas las filas y reinicializar paginación
        this.filasFiltradas = [...this.filasOriginales];
        this.paginaActual = 1;
        this.aplicarPaginacion();
        this.verificarEstadoExportar();
    }
    
    aplicarPaginacion() {
        // Ocultar todas las filas primero
        this.filasOriginales.forEach(fila => {
            fila.style.display = 'none';
        });
        
        // Calcular qué filas mostrar
        const inicio = (this.paginaActual - 1) * this.registrosPorPagina;
        const fin = inicio + this.registrosPorPagina;
        const filasAMostrar = this.filasFiltradas.slice(inicio, fin);
        
        // Mostrar solo las filas de la página actual
        filasAMostrar.forEach(fila => {
            fila.style.display = '';
        });
        
        // Actualizar información de paginación
        const totalPaginas = Math.ceil(this.filasFiltradas.length / this.registrosPorPagina);
        
        const paginaActual = document.getElementById(this.config.paginaActual);
        const totalPaginasElement = document.getElementById(this.config.totalPaginas);
        const btnAnterior = document.getElementById(this.config.btnAnterior);
        const btnSiguiente = document.getElementById(this.config.btnSiguiente);
        
        if (paginaActual) paginaActual.textContent = this.paginaActual;
        if (totalPaginasElement) totalPaginasElement.textContent = totalPaginas;
        
        if (btnAnterior) {
            const puedeAnterior = this.paginaActual > 1;
            btnAnterior.disabled = !puedeAnterior;
            if (!puedeAnterior) {
                btnAnterior.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnAnterior.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
        
        if (btnSiguiente) {
            const puedeSiguiente = this.paginaActual < totalPaginas;
            btnSiguiente.disabled = !puedeSiguiente;
            if (!puedeSiguiente) {
                btnSiguiente.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnSiguiente.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
        
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
                    // Excluir filas que contengan mensajes como "No hay miembros registrados"
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
        link.setAttribute('download', 'miembros_exportados.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Inicializar tabla de miembros cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Tabla de miembros
    const tablaMiembros = document.querySelector('.bg-white.rounded-lg.shadow table');
    if (tablaMiembros) {
        new TablaPersonalizada(tablaMiembros, {
            inputBusqueda: 'buscarMiembros',
            btnMostrarBusqueda: 'btnMostrarBusquedaMiembros',
            btnCancelarBusqueda: 'btnCancelarBusquedaMiembros',
            btnBuscarAvanzada: 'btnBuscarAvanzadaMiembros',
            btnLimpiar: 'btnLimpiarBusquedaMiembros',
            btnExportar: 'btnExportarMiembros',
            panelBusqueda: 'panelBusquedaMiembros',
            panelBusquedaAvanzada: 'panelBusquedaAvanzadaMiembros',
            selectRegistros: 'registrosPorPaginaMiembros',
            btnAnterior: 'btnAnteriorMiembros',
            btnSiguiente: 'btnSiguienteMiembros',
            paginaActual: 'paginaActualMiembros',
            totalPaginas: 'totalPaginasMiembros',
            filtrosAvanzados: [
                { id: 'filtroCedula', campo: 'cedula', indice: 0 },
                { id: 'filtroNombres', campo: 'nombres', indice: 1 },
                { id: 'filtroApellidos', campo: 'apellidos', indice: 2 },
                { id: 'filtroSexo', campo: 'sexo', indice: 3 },
                { id: 'filtroEstado', campo: 'estado', indice: 6 },
                { id: 'filtroAcademia', campo: 'academia', indice: 7 }
            ]
        });
    }
});
</script>
@endpush

@endsection 