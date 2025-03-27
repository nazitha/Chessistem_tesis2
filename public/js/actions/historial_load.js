$(document).ready(function() {

    var load_table = 0;
    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: HISTORIAL ********************* */
    $('#historial_opcion, #historial_opcion_movil').click(function() {

        load_table++;

        if(load_table === 1)
        {
            window.tabla_historial = $('#tabla-historial').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/HistorialController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    {"data": "usuario"},
                    { "data": "correo_id" },
                    { "data": "tabla_afectada" },
                    { "data": "accion" },
                    { "data": "valor_previo" },
                    { "data": "valor_posterior" },
                    { "data": "tiempo" },
                    { "data": "equipo" }
                ],
                "language": {
                    "url": "actions/spanish.json"
                },
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        title: 'Historial de interacciones',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                    }
                ]
            }).columns.adjust().responsive.recalc();
        }

    });
    /* *************************************************************************************** */

});