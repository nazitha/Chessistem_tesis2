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

<div class="max-w-full mx-auto px-4">
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
    <div id="panelBusquedaAcademias" class="bg-white shadow-md rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Academias</h3>
            <button id="btnCancelarBusquedaAcademias" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                <input type="text" id="buscarAcademias" placeholder="Buscar por nombre, correo, representante..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button id="btnBuscarAvanzadaAcademias" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                    Búsqueda Avanzada
                </button>
                <button id="btnLimpiarBusquedaAcademias" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                    Limpiar
                </button>
            </div>
        </div>
        <!-- Panel de búsqueda avanzada -->
        <div id="panelBusquedaAvanzadaAcademias" class="mt-4 p-4 bg-gray-50 rounded-md hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                    <input type="text" id="filtroNombre" placeholder="Filtrar por nombre" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo:</label>
                    <input type="text" id="filtroCorreo" placeholder="Filtrar por correo" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                    <select id="filtroEstado" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Representante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        @if(PermissionHelper::hasAnyAcademiaActionPermission())
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($academias as $academia)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $academia->nombre_academia }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $academia->correo_academia }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $academia->telefono_academia }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $academia->representante_academia }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $academia->direccion_academia }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $academia->ciudad ? $academia->ciudad->nombre_ciudad . ', ' . 
                                   ($academia->ciudad->departamento->nombre_depto ?? '-') . ' (' . 
                                   ($academia->ciudad->departamento->pais->nombre_pais ?? '-') . ')' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($academia->estado_academia)
                                        bg-green-100 text-green-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ $academia->estado_academia ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            @if(PermissionHelper::hasAnyAcademiaActionPermission())
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    @if(PermissionHelper::canViewModule('academias'))
                                        <a href="{{ route('academias.show', $academia) }}" 
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100 transition-colors duration-200"
                                           data-tooltip="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    
                                    @if(PermissionHelper::canUpdate('academias'))
                                        <a href="{{ route('academias.edit', $academia) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-100 transition-colors duration-200"
                                           data-tooltip="Editar academia">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                        @if(PermissionHelper::canDelete('academias'))
                                    <form action="{{ route('academias.destroy', $academia) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="confirmarEliminacion('{{ $academia->id_academia }}')"
                                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 transition-colors duration-200"
                                                data-tooltip="Eliminar academia">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                        @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ PermissionHelper::hasAnyAcademiaActionPermission() ? '8' : '7' }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                No hay academias registradas
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
                    <select id="registrosPorPaginaAcademias" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">registros por página</span>
                </div>
                <div class="flex items-center gap-2">
                    <button id="btnAnteriorAcademias" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        Anterior
                    </button>
                    <span id="infoPaginacionAcademias" class="text-sm text-gray-700">Página 1 de 1</span>
                    <button id="btnSiguienteAcademias" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed">
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
        const filtros = ['filtroNombre', 'filtroCorreo', 'filtroEstado'];
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
            const filtroCorreo = document.getElementById('filtroCorreo')?.value.toLowerCase() || '';
            const filtroEstado = document.getElementById('filtroEstado')?.value || '';

            this.filasFiltradas = this.filasOriginales.filter(fila => {
                const celdas = fila.querySelectorAll('td');
                if (celdas.length === 0) return false;

                const nombre = celdas[0]?.textContent.toLowerCase() || '';
                const correo = celdas[1]?.textContent.toLowerCase() || '';
                const estado = celdas[6]?.textContent.trim() || '';

                const cumpleNombre = !filtroNombre || nombre.includes(filtroNombre);
                const cumpleCorreo = !filtroCorreo || correo.includes(filtroCorreo);
                const cumpleEstado = !filtroEstado || estado === filtroEstado;

                return cumpleNombre && cumpleCorreo && cumpleEstado;
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
            const filtros = ['filtroNombre', 'filtroCorreo', 'filtroEstado'];
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
                        // Excluir filas que contengan mensajes como "No hay academias registradas"
                        const textoFila = fila.textContent.toLowerCase();
                        return !textoFila.includes('no hay') && !textoFila.includes('registradas') && !textoFila.includes('registrados');
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
        link.setAttribute('download', 'academias_exportadas.csv');
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

    // Inicializar tabla personalizada para academias
    const tablaAcademias = document.querySelector('.bg-white.rounded-lg.shadow table');
    if (tablaAcademias) {
        new TablaPersonalizada(tablaAcademias, {
            inputBusqueda: 'buscarAcademias',
            panelBusqueda: 'panelBusquedaAcademias',
            panelBusquedaAvanzada: 'panelBusquedaAvanzadaAcademias',
            btnMostrarBusqueda: 'btnMostrarBusquedaAcademias',
            btnCancelarBusqueda: 'btnCancelarBusquedaAcademias',
            btnBuscarAvanzada: 'btnBuscarAvanzadaAcademias',
            btnLimpiar: 'btnLimpiarBusquedaAcademias',
            btnExportar: 'btnExportarAcademias',
            selectRegistros: 'registrosPorPaginaAcademias',
            btnAnterior: 'btnAnteriorAcademias',
            btnSiguiente: 'btnSiguienteAcademias',
            infoPaginacion: 'infoPaginacionAcademias'
        });
    }
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
</script>
@endpush

@endsection 