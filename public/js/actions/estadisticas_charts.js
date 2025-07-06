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

// Datos de ejemplo para el gráfico de Historial de Partidas
const datosHistorialPartidas = {
    partidasJugadas: 45,
    victorias: 28,
    empates: 12,
    derrotas: 5,
    ratingELO: 1450,
    progresoMensual: [
        { mes: 'Ene', rating: 1200, partidas: 8 },
        { mes: 'Feb', rating: 1250, partidas: 12 },
        { mes: 'Mar', rating: 1280, partidas: 10 },
        { mes: 'Abr', rating: 1320, partidas: 15 },
        { mes: 'May', rating: 1380, partidas: 18 },
        { mes: 'Jun', rating: 1420, partidas: 20 },
        { mes: 'Jul', rating: 1450, partidas: 22 },
        { mes: 'Ago', rating: 1480, partidas: 25 },
        { mes: 'Sep', rating: 1520, partidas: 28 },
        { mes: 'Oct', rating: 1550, partidas: 32 },
        { mes: 'Nov', rating: 1580, partidas: 35 },
        { mes: 'Dic', rating: 1620, partidas: 40 }
    ]
};

// Datos de ejemplo para el gráfico de Estadísticas de Torneos
const datosEstadisticasTorneos = {
    torneosOrganizados: 12,
    participantesTotales: 156,
    partidasJugadas: 89,
    promedioParticipantes: 13,
    rendimientoMensual: [
        { mes: 'Ene', torneos: 1, participantes: 12, partidas: 8 },
        { mes: 'Feb', torneos: 2, participantes: 24, partidas: 15 },
        { mes: 'Mar', torneos: 1, participantes: 18, partidas: 12 },
        { mes: 'Abr', torneos: 3, participantes: 42, partidas: 28 },
        { mes: 'May', torneos: 2, participantes: 26, partidas: 18 },
        { mes: 'Jun', torneos: 4, participantes: 58, partidas: 35 },
        { mes: 'Jul', torneos: 3, participantes: 38, partidas: 25 },
        { mes: 'Ago', torneos: 5, participantes: 72, partidas: 45 },
        { mes: 'Sep', torneos: 4, participantes: 56, partidas: 38 },
        { mes: 'Oct', torneos: 6, participantes: 84, partidas: 52 },
        { mes: 'Nov', torneos: 5, participantes: 68, partidas: 42 },
        { mes: 'Dic', torneos: 7, participantes: 96, partidas: 58 }
    ]
};

/**
 * Inicializa el gráfico de Historial de Partidas
 */
function inicializarGraficoHistorialPartidas() {
    const container = document.getElementById('grafico-historial-partidas');
    if (!container) return;

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
            text: 'Progreso del Rating ELO',
            style: {
                fontSize: '16px',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Evolución mensual del rating ELO'
        },
        xAxis: {
            categories: datosHistorialPartidas.progresoMensual.map(item => item.mes),
            title: {
                text: 'Mes'
            }
        },
        yAxis: {
            type: 'logarithmic',
            title: {
                text: 'Rating ELO'
            },
            minorTickInterval: 0.1
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' +
                       '<span style="color:' + this.series.color + '">' + this.series.name + '</span>: ' +
                       '<b>' + this.y + '</b> ELO';
            }
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
            name: 'Rating ELO',
            data: datosHistorialPartidas.progresoMensual.map(item => item.rating),
            color: '#0D6EFD',
            lineWidth: 3,
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
            enabled: false
        }
    });
}

/**
 * Inicializa el gráfico de Estadísticas de Torneos
 */
function inicializarGraficoEstadisticasTorneos() {
    const container = document.getElementById('grafico-estadisticas-torneos');
    if (!container) return;

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
            text: 'Rendimiento de Torneos',
            style: {
                fontSize: '16px',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Estadísticas mensuales de torneos organizados'
        },
        xAxis: {
            categories: datosEstadisticasTorneos.rendimientoMensual.map(item => item.mes),
            title: {
                text: 'Mes'
            }
        },
        yAxis: [{
            title: {
                text: 'Número de Torneos',
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
                text: 'Número de Participantes',
                style: {
                    color: '#00A651'
                }
            },
            labels: {
                style: {
                    color: '#00A651'
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true,
            formatter: function() {
                let tooltip = '<b>' + this.x + '</b><br/>';
                this.points.forEach(function(point) {
                    tooltip += '<span style="color:' + point.color + '">' + 
                              point.series.name + '</span>: <b>' + point.y + '</b><br/>';
                });
                return tooltip;
            }
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
            name: 'Torneos Organizados',
            data: datosEstadisticasTorneos.rendimientoMensual.map(item => item.torneos),
            color: '#FF6B00',
            lineWidth: 3,
            marker: {
                enabled: true,
                radius: 4
            }
        }, {
            name: 'Participantes',
            data: datosEstadisticasTorneos.rendimientoMensual.map(item => item.participantes),
            color: '#00A651',
            lineWidth: 3,
            marker: {
                enabled: true,
                radius: 4
            },
            yAxis: 1
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
            enabled: false
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
        inicializarGraficoHistorialPartidas();
    }

    if (document.getElementById('grafico-estadisticas-torneos')) {
        inicializarGraficoEstadisticasTorneos();
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