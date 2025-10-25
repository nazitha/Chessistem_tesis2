// JavaScript principal para Chessistem
import './bootstrap';
import './dinamic_login';

// Funcionalidades globales
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes
    console.log('Chessistem app loaded');
});

// Highcharts tema dinámico
function applyHighchartsTheme(dark) {
    if (!window.Highcharts) return;
    const base = dark ? {
        chart: { backgroundColor: '#0f172a' },
        title: { style: { color: '#e5e7eb' } },
        xAxis: { labels: { style: { color: '#cbd5e1' } }, gridLineColor: '#1f2937' },
        yAxis: { labels: { style: { color: '#cbd5e1' } }, gridLineColor: '#1f2937' },
        legend: { itemStyle: { color: '#e5e7eb' } },
    } : {
        chart: { backgroundColor: '#ffffff' },
        title: { style: { color: '#0f172a' } },
        xAxis: { labels: { style: { color: '#334155' } }, gridLineColor: '#e5e7eb' },
        yAxis: { labels: { style: { color: '#334155' } }, gridLineColor: '#e5e7eb' },
        legend: { itemStyle: { color: '#0f172a' } },
    };
    window.Highcharts.setOptions(base);
}

window.addEventListener('theme:changed', function(e) {
    applyHighchartsTheme(!!(e && e.detail && e.detail.dark));
});

window.addEventListener('DOMContentLoaded', function() {
    const dark = document.documentElement.classList.contains('dark');
    applyHighchartsTheme(dark);
});