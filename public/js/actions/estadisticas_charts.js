/**
 * Script para manejar los gráficos de estadísticas en el dashboard
 * Utiliza Highcharts para mostrar estadísticas de torneos y partidas
 */

// Configuración de idioma español para Highcharts
Highcharts.setOptions({
    lang: {
        months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        shortMonths: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        exportButtonTitle: 'Exportar',
        printButtonTitle: 'Imprimir',
        rangeSelectorFrom: 'Desde',
        rangeSelectorTo: 'Hasta',
        rangeSelectorZoom: 'Período',
        downloadPNG: 'Descargar imagen PNG',
        downloadJPEG: 'Descargar imagen JPEG',
        downloadPDF: 'Descargar documento PDF',
        downloadSVG: 'Descargar imagen SVG',
        downloadCSV: 'Descargar CSV',
        downloadXLS: 'Descargar XLS',
        viewFullscreen: 'Ver en pantalla completa',
        printChart: 'Imprimir gráfico',
        resetZoom: 'Restablecer zoom',
        resetZoomTitle: 'Restablecer zoom nivel 1:1',
        thousandsSep: '.',
        decimalPoint: ',',
        loading: 'Cargando...',
        noData: 'No hay datos para mostrar',
        drillUpText: '← Volver a {series.name}',
        invalidDate: 'Fecha inválida',
        numericSymbols: ['k', 'M', 'G', 'T', 'P', 'E'],
        resetZoom: 'Restablecer zoom',
        resetZoomTitle: 'Restablecer zoom nivel 1:1',
        shortMonths: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        shortWeekdays: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
        weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
    }
});

// Variable global para almacenar los datos reales de estadísticas personales
let datosEstadisticasPersonales = {
    victorias: 0,
    derrotas: 0,
    empates: 0,
    totalPartidas: 0,
    torneosParticipados: 0,
    torneosJugados: 0,
    rendimientoMensual: []
};

// Variable global para almacenar los datos reales de estadísticas de torneos
let datosEstadisticasTorneos = {
    torneosOrganizados: 0,
    participantesTotales: 0,
    partidasJugadas: 0,
    promedioParticipantes: 0,
    rendimientoMensual: []
};

/**
 * Carga los datos reales de estadísticas de torneos desde el servidor
 */
async function cargarEstadisticasTorneos() {
    try {
        const response = await fetch('/api/estadisticas/mensuales');
        const result = await response.json();
        
        if (result.success) {
            datosEstadisticasTorneos.rendimientoMensual = result.data;
            
            // Calcular totales
            datosEstadisticasTorneos.torneosOrganizados = result.data.reduce((sum, item) => sum + item.torneos, 0);
            datosEstadisticasTorneos.participantesTotales = result.data.reduce((sum, item) => sum + item.participantes, 0);
            datosEstadisticasTorneos.promedioParticipantes = result.data.length > 0 ? 
                Math.round(datosEstadisticasTorneos.participantesTotales / datosEstadisticasTorneos.torneosOrganizados) : 0;
            
            // Inicializar el gráfico con los datos reales
            inicializarGraficoEstadisticasTorneos();
        } else {
            console.error('Error al cargar estadísticas:', result.message);
        }
    } catch (error) {
        console.error('Error al cargar estadísticas de torneos:', error);
    }
}

/**
 * Carga los datos reales de estadísticas personales desde el servidor
 */
async function cargarEstadisticasPersonales() {
    try {
        const response = await fetch('/api/estadisticas/personales');
        const result = await response.json();
        
        if (result.success) {
            datosEstadisticasPersonales.rendimientoMensual = result.data;
            
            // Calcular totales
            datosEstadisticasPersonales.victorias = result.data.reduce((sum, item) => sum + item.victorias, 0);
            datosEstadisticasPersonales.derrotas = result.data.reduce((sum, item) => sum + item.derrotas, 0);
            datosEstadisticasPersonales.empates = result.data.reduce((sum, item) => sum + item.empates, 0);
            datosEstadisticasPersonales.totalPartidas = result.data.reduce((sum, item) => sum + item.total_partidas, 0);
            datosEstadisticasPersonales.torneosParticipados = result.data.reduce((sum, item) => sum + item.torneos_participados, 0);
            datosEstadisticasPersonales.torneosJugados = result.data.reduce((sum, item) => sum + item.torneos_jugados, 0);
            
            // Inicializar el gráfico con los datos reales
            inicializarGraficoHistorialPartidas();
        } else {
            console.error('Error al cargar estadísticas personales:', result.message);
            // Si hay error, mostrar mensaje en el contenedor
            const container = document.getElementById('grafico-historial-partidas');
            if (container) {
                container.innerHTML = '<div class="text-center text-gray-500 mt-8">' + result.message + '</div>';
            }
        }
    } catch (error) {
        console.error('Error al cargar estadísticas personales:', error);
        // Si hay error, mostrar mensaje en el contenedor
        const container = document.getElementById('grafico-historial-partidas');
        if (container) {
            container.innerHTML = '<div class="text-center text-gray-500 mt-8">Error al cargar estadísticas personales</div>';
        }
    }
}

/**
 * Inicializa el gráfico de Historial de Partidas
 */
function inicializarGraficoHistorialPartidas() {
    const container = document.getElementById('grafico-historial-partidas');
    if (!container) return;

    // Verificar si hay datos
    if (datosEstadisticasPersonales.rendimientoMensual.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-500 mt-8">No hay datos de partidas disponibles</div>';
        return;
    }

    // Crear el gráfico
    Highcharts.chart('grafico-historial-partidas', {
        chart: {
            type: 'line',
            height: 400,
            style: {
                fontFamily: 'Inter, sans-serif'
            }
        },
        title: {
            text: 'Historial de partidas',
            style: {
                fontSize: '16px',
                fontWeight: 'bold'
            }
        },
        xAxis: {
            categories: datosEstadisticasPersonales.rendimientoMensual.map(item => item.mes),
            title: {
                text: 'Mes'
            },
            labels: {
                formatter: function() {
                    return this.value;
                }
            }
        },
        yAxis: {
            title: {
                text: 'Cantidad de Partidas'
            }
        },
        tooltip: {
            shared: true
        },
        legend: {
            enabled: true,
            align: 'center',
            verticalAlign: 'bottom',
            layout: 'horizontal',
            itemStyle: {
                fontSize: '12px'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: false
                },
                enableMouseTracking: true,
                marker: {
                    enabled: true,
                    radius: 4
                }
            }
        },
        series: [{
            name: 'Victorias',
            data: datosEstadisticasPersonales.rendimientoMensual.map(item => item.victorias),
            color: '#28A745', // Verde
            lineWidth: 2,
            marker: {
                enabled: true,
                radius: 4
            }
        }, {
            name: 'Derrotas',
            data: datosEstadisticasPersonales.rendimientoMensual.map(item => item.derrotas),
            color: '#DC3545', // Rojo
            lineWidth: 2,
            marker: {
                enabled: true,
                radius: 4
            }
        }, {
            name: 'Empates',
            data: datosEstadisticasPersonales.rendimientoMensual.map(item => item.empates),
            color: '#6C757D', // Gris
            lineWidth: 2,
            marker: {
                enabled: true,
                radius: 4
            }
        }, {
            name: 'Torneos inscritos',
            data: datosEstadisticasPersonales.rendimientoMensual.map(item => item.total_partidas),
            color: '#007BFF', // Azul
            lineWidth: 3,
            marker: {
                enabled: true,
                radius: 4
            }
        }, {
            name: 'Partidas jugadas',
            data: datosEstadisticasPersonales.rendimientoMensual.map(item => item.torneos_jugados),
            color: '#FF8C00', // Naranja
            lineWidth: 2,
            marker: {
                enabled: true,
                radius: 4
            }
        }],
        exporting: {
            enabled: true,
            buttons: {
                contextButton: {
                    menuItems: [
                        'viewFullscreen',
                        'separator',
                        'downloadPNG',
                        'downloadJPEG',
                        'downloadPDF',
                        'downloadSVG',
                        'separator',
                        'downloadCSV',
                        'downloadXLS'
                    ]
                }
            }
        },
        credits: {
            enabled: true,
            text: function() {
                const fechaActual = new Date();
                const fechaInicio = new Date();
                fechaInicio.setFullYear(fechaInicio.getFullYear() - 1);
                
                const mesInicio = fechaInicio.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
                const mesFin = fechaActual.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
                
                return `Período: ${mesInicio} - ${mesFin}`;
            }(),
            style: {
                fontSize: '12px',
                color: '#666'
            },
            position: {
                align: 'center',
                verticalAlign: 'bottom',
                x: 0,
                y: -10
            }
        }
    });
}

/**
 * Inicializa el gráfico de Estadísticas de Torneos
 */
function inicializarGraficoEstadisticasTorneos() {
    const container = document.getElementById('grafico-estadisticas-torneos');
    if (!container) return;

    // Verificar si hay datos
    if (datosEstadisticasTorneos.rendimientoMensual.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-500 mt-8">No hay datos de torneos disponibles</div>';
        return;
    }

    // Crear el gráfico
    Highcharts.chart('grafico-estadisticas-torneos', {
        chart: {
            type: 'line',
            height: 400,
            style: {
                fontFamily: 'Inter, sans-serif'
            }
        },
        title: {
            text: 'Estadísticas Completas de Torneos',
            style: {
                fontSize: '16px',
                fontWeight: 'bold'
            }
        },
        xAxis: {
            categories: datosEstadisticasTorneos.rendimientoMensual.map(item => item.mes),
            title: {
                text: 'Mes'
            }
        },
        yAxis: [{
            title: {
                text: 'Cantidad de Torneos',
                style: {
                    color: '#FF6B00'
                }
            },
            labels: {
                style: {
                    color: '#FF6B00'
                }
            }
        }, {
            title: {
                text: 'Cantidad de Participantes',
                style: {
                    color: '#6F42C1'
                }
            },
            labels: {
                style: {
                    color: '#6F42C1'
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            enabled: true
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: false
                },
                enableMouseTracking: true
            }
        },
        series: [{
            name: 'Total Torneos',
            data: datosEstadisticasTorneos.rendimientoMensual.map(item => item.torneos),
            color: '#FF6B00',
            lineWidth: 3,
            marker: {
                enabled: true,
                radius: 4
            }
        }, {
            name: 'Torneos Completados',
            data: datosEstadisticasTorneos.rendimientoMensual.map(item => item.completados),
            color: '#28A745',
            lineWidth: 2,
            marker: {
                enabled: true,
                radius: 3
            }
        }, {
            name: 'Torneos en Curso',
            data: datosEstadisticasTorneos.rendimientoMensual.map(item => item.en_curso),
            color: '#007BFF',
            lineWidth: 2,
            marker: {
                enabled: true,
                radius: 3
            }
        }, {
            name: 'Torneos Pendientes',
            data: datosEstadisticasTorneos.rendimientoMensual.map(item => item.pendientes),
            color: '#FFC107',
            lineWidth: 2,
            marker: {
                enabled: true,
                radius: 3
            }
        }, {
            name: 'Total Participantes',
            data: datosEstadisticasTorneos.rendimientoMensual.map(item => item.participantes),
            color: '#6F42C1',
            lineWidth: 3,
            marker: {
                enabled: true,
                radius: 4
            },
            yAxis: 1
        }],
        legend: {
            enabled: true,
            align: 'center',
            verticalAlign: 'bottom',
            layout: 'horizontal',
            itemStyle: {
                fontSize: '12px'
            }
        },
        exporting: {
            enabled: true,
            buttons: {
                contextButton: {
                    menuItems: [
                        'viewFullscreen',
                        'separator',
                        'downloadPNG',
                        'downloadJPEG',
                        'downloadPDF',
                        'downloadSVG',
                        'separator',
                        'downloadCSV',
                        'downloadXLS'
                    ]
                }
            }
        },
        credits: {
            enabled: true,
            text: function() {
                const fechaActual = new Date();
                const fechaInicio = new Date();
                fechaInicio.setFullYear(fechaInicio.getFullYear() - 1);
                
                const mesInicio = fechaInicio.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
                const mesFin = fechaActual.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
                
                return `Período: ${mesInicio} - ${mesFin}`;
            }(),
            style: {
                fontSize: '12px',
                color: '#666'
            },
            position: {
                align: 'center',
                verticalAlign: 'bottom',
                x: 0,
                y: -10
            }
        }
    });
}

/**
 * Función principal para inicializar todos los gráficos
 */
function inicializarGraficosEstadisticas() {
    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(inicializarGraficosEstadisticas, 100);
        });
        return;
    }

    // Inicializar gráficos si existen los contenedores
    if (document.getElementById('grafico-historial-partidas')) {
        // Cargar datos reales y luego inicializar el gráfico
        cargarEstadisticasPersonales();
    }

    if (document.getElementById('grafico-estadisticas-torneos')) {
        // Cargar datos reales y luego inicializar el gráfico
        cargarEstadisticasTorneos();
    }
}

// Inicializar gráficos cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    inicializarGraficosEstadisticas();
});

// Exportar funciones para uso global
window.inicializarGraficosEstadisticas = inicializarGraficosEstadisticas;
window.inicializarGraficoHistorialPartidas = inicializarGraficoHistorialPartidas;
window.inicializarGraficoEstadisticasTorneos = inicializarGraficoEstadisticasTorneos;
window.cargarEstadisticasTorneos = cargarEstadisticasTorneos;
window.cargarEstadisticasPersonales = cargarEstadisticasPersonales; 