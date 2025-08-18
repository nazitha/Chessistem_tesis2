@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
<div class="container mx-auto py-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-5 sm:px-8 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Auditoría del Sistema</h1>
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
        <div id="panelBusquedaAuditoria" class="mx-6 mb-4 bg-gray-50 shadow-md rounded-lg p-4 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Búsqueda de Auditoría</h3>
                <button id="btnCancelarBusquedaAuditoria" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                    ✕
                </button>
            </div>
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                    <input type="text" id="buscarAuditoria" placeholder="Buscar en todos los campos..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button id="btnBuscarAvanzadaAuditoria" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        Búsqueda Avanzada
                    </button>
                    <button id="btnLimpiarBusquedaAuditoria" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                        Limpiar
                    </button>
                </div>
            </div>
            <!-- Panel de búsqueda avanzada -->
            <div id="panelBusquedaAvanzadaAuditoria" class="mt-4 p-4 bg-white rounded-md hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usuario:</label>
                        <select id="filtroUsuarioAuditoria" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                            <option value="">Todos los usuarios</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario }}">{{ $usuario }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Acción:</label>
                        <select id="filtroAccionAuditoria" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                            <option value="">Todas las acciones</option>
                            @foreach($acciones as $accion)
                                <option value="{{ $accion }}">{{ $accion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tabla:</label>
                        <select id="filtroTablaAuditoria" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                            <option value="">Todas las tablas</option>
                            @foreach($tablas as $tabla)
                                <option value="{{ $tabla }}">{{ $tabla }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha:</label>
                        <input type="date" id="filtroFechaAuditoria" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Equipo/IP:</label>
                        <input type="text" id="filtroEquipoAuditoria" placeholder="Filtrar por equipo/IP" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="tablaAuditoria">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Hora</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Usuario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Acción</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Tabla</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Equipo/IP</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Detalles</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($auditorias as $auditoria)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $auditoria->fecha }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $auditoria->hora }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $auditoria->correo_id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $auditoria->accion }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $auditoria->tabla_afectada }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $auditoria->equipo }}</td>
                            <td class="px-4 py-2 text-sm text-center">
                                <button type="button" onclick="toggleDetalle('detalle-{{ $auditoria->id }}')" class="text-blue-600 hover:underline">Ver</button>
                            </td>
                        </tr>
                        <tr id="detalle-{{ $auditoria->id }}" style="display:none; background:#f9fafb;">
                            <td colspan="7" class="px-4 py-2">
                                <b>Valor previo:</b> <pre class="whitespace-pre-wrap">{{ $auditoria->valor_previo }}</pre>
                                <b>Valor posterior:</b> <pre class="whitespace-pre-wrap">{{ $auditoria->valor_posterior }}</pre>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-gray-500">No hay registros de auditoría.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Controles de paginación -->
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-700">Mostrar:</span>
                        <select id="registrosPorPaginaAuditoria" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-700">registros por página</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="btnAnteriorAuditoria" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Anterior
                        </button>
                        <span id="infoPaginacionAuditoria" class="text-sm text-gray-700">Página 1 de 1</span>
                        <button id="btnSiguienteAuditoria" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDetalle(id) {
    var row = document.getElementById(id);
    if (row.style.display === 'none') {
        row.style.display = '';
    } else {
        row.style.display = 'none';
    }
}

// Clase para manejar la tabla personalizada de auditoría
class TablaAuditoriaPersonalizada {
    constructor(tabla, config) {
        this.tabla = tabla;
        this.config = config;
        this.filasOriginales = Array.from(tabla.querySelectorAll('tbody tr')).filter(fila => !fila.id.includes('detalle-'));
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
        const filtros = ['filtroUsuarioAuditoria', 'filtroAccionAuditoria', 'filtroTablaAuditoria', 'filtroFechaAuditoria', 'filtroEquipoAuditoria'];
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
        const filtroUsuario = document.getElementById('filtroUsuarioAuditoria')?.value.toLowerCase() || '';
        const filtroAccion = document.getElementById('filtroAccionAuditoria')?.value.toLowerCase() || '';
        const filtroTabla = document.getElementById('filtroTablaAuditoria')?.value.toLowerCase() || '';
        const filtroFecha = document.getElementById('filtroFechaAuditoria')?.value || '';
        const filtroEquipo = document.getElementById('filtroEquipoAuditoria')?.value.toLowerCase() || '';

        this.filasFiltradas = this.filasOriginales.filter(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length === 0) return false;

            const fecha = celdas[0]?.textContent.trim() || '';
            const hora = celdas[1]?.textContent.trim() || '';
            const usuario = celdas[2]?.textContent.toLowerCase() || '';
            const accion = celdas[3]?.textContent.toLowerCase() || '';
            const tabla = celdas[4]?.textContent.toLowerCase() || '';
            const equipo = celdas[5]?.textContent.toLowerCase() || '';

            const cumpleUsuario = !filtroUsuario || usuario.includes(filtroUsuario);
            const cumpleAccion = !filtroAccion || accion.includes(filtroAccion);
            const cumpleTabla = !filtroTabla || tabla.includes(filtroTabla);
            const cumpleFecha = !filtroFecha || fecha === filtroFecha;
            const cumpleEquipo = !filtroEquipo || equipo.includes(filtroEquipo);

            return cumpleUsuario && cumpleAccion && cumpleTabla && cumpleFecha && cumpleEquipo;
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
        const filtros = ['filtroUsuarioAuditoria', 'filtroAccionAuditoria', 'filtroTablaAuditoria', 'filtroFechaAuditoria', 'filtroEquipoAuditoria'];
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
                    // Excluir filas que contengan mensajes como "No hay registros de auditoría"
                    const textoFila = fila.textContent.toLowerCase();
                    return !textoFila.includes('no hay') && !textoFila.includes('registros') && !textoFila.includes('auditoría');
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
        
        // Definir encabezados personalizados para la exportación
        const encabezados = [
            'Fecha', 'Hora', 'Usuario', 'Acción', 'Tabla', 'Equipo/IP', 'Valor Previo', 'Valor Posterior'
        ];
        
        // Preparar los datos para exportar
        const datos = [];
        filasAExportar.forEach(fila => {
            const filaDatos = [];
            const celdas = fila.querySelectorAll('td');
            
            // Obtener los datos básicos (excluyendo la columna de detalles)
            for (let i = 0; i < 6; i++) {
                if (celdas[i]) {
                    filaDatos.push(celdas[i].textContent.trim());
                }
            }
            
            // Buscar la fila de detalles correspondiente
            const filaId = fila.querySelector('button')?.getAttribute('onclick')?.match(/detalle-(\d+)/)?.[1];
            if (filaId) {
                const filaDetalle = document.getElementById(`detalle-${filaId}`);
                if (filaDetalle) {
                    const contenidoDetalle = filaDetalle.textContent;
                    
                    // Extraer valor previo
                    const valorPrevioMatch = contenidoDetalle.match(/Valor previo:(.*?)Valor posterior:/s);
                    const valorPrevio = valorPrevioMatch ? valorPrevioMatch[1].trim() : '';
                    
                    // Extraer valor posterior
                    const valorPosteriorMatch = contenidoDetalle.match(/Valor posterior:(.*?)$/s);
                    const valorPosterior = valorPosteriorMatch ? valorPosteriorMatch[1].trim() : '';
                    
                    filaDatos.push(valorPrevio);
                    filaDatos.push(valorPosterior);
                } else {
                    filaDatos.push('', ''); // Valores vacíos si no se encuentra la fila de detalles
                }
            } else {
                filaDatos.push('', ''); // Valores vacíos si no hay ID de fila
            }
            
            datos.push(filaDatos);
        });
        
        // Función para escapar valores CSV
        const escaparCSV = (valor) => {
            if (valor === null || valor === undefined) return '';
            const stringValor = String(valor);
            // Si contiene comas, comillas o saltos de línea, envolver en comillas
            if (stringValor.includes(',') || stringValor.includes('"') || stringValor.includes('\n') || stringValor.includes('\r')) {
                // Escapar comillas dobles duplicándolas
                return '"' + stringValor.replace(/"/g, '""') + '"';
            }
            return stringValor;
        };

        // Crear el contenido CSV con BOM para UTF-8
        const BOM = '\uFEFF'; // Byte Order Mark para UTF-8
        let csvContent = BOM + encabezados.map(escaparCSV).join(',') + '\n';
        datos.forEach(fila => {
            csvContent += fila.map(escaparCSV).join(',') + '\n';
        });
        
        // Crear y descargar el archivo con codificación UTF-8
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'auditoria_exportada.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Inicializar tabla personalizada para auditoría cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tabla personalizada para auditoría
    const tablaAuditoria = document.getElementById('tablaAuditoria');
    if (tablaAuditoria) {
        new TablaAuditoriaPersonalizada(tablaAuditoria, {
            inputBusqueda: 'buscarAuditoria',
            panelBusqueda: 'panelBusquedaAuditoria',
            panelBusquedaAvanzada: 'panelBusquedaAvanzadaAuditoria',
            btnMostrarBusqueda: 'btnMostrarBusquedaAuditoria',
            btnCancelarBusqueda: 'btnCancelarBusquedaAuditoria',
            btnBuscarAvanzada: 'btnBuscarAvanzadaAuditoria',
            btnLimpiar: 'btnLimpiarBusquedaAuditoria',
            btnExportar: 'btnExportarAuditoria',
            selectRegistros: 'registrosPorPaginaAuditoria',
            btnAnterior: 'btnAnteriorAuditoria',
            btnSiguiente: 'btnSiguienteAuditoria',
            infoPaginacion: 'infoPaginacionAuditoria'
        });
    }
});
</script>
@endsection 