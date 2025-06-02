$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
    var opcion = 1;
    var search;

    var load_table = 0;
    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
    var mail_log = userData.correo;

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: PARTIDAS ********************* */

    $('#partidas_opcion').click(function() {

        load_table++;

        if(load_table === 1)
        {
            window.tabla_partidas = $('#tabla-partidas').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/PartidaController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "no_partida" },
                    { "data": "participante_id" },
                    { "data": "torneo_id" },
                    { "data": "ronda" },
                    { "data": "mesa" },
                    { "data": "color" },
                    { "data": "tiempo" },
                    { "data": "desempate_utilizado_id" },
                    { "data": "estado_abandono" },
                    { "data": "resultado" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-danger btn-sm btnEditar_partida"
                                    style="
                                        background-color: #1e2936;
                                        color: white;
                                        border: 1px solid transparent;
                                        padding: 0.375rem 0.75rem;
                                        font-size: 1rem;
                                        font-weight: 400;
                                        line-height: 1.5;
                                        border-radius: 0.25rem 0 0 0.25rem; /* Bordes redondeados a la izquierda */
                                        text-align: center;
                                        vertical-align: middle;
                                        cursor: pointer;
                                        transition: background-color 0.3s ease;
                                        display: inline-block; /* Asegura que los botones estén en línea */
                                        margin-right: -1px; /* Elimina el espacio entre los botones */
                                    "
                                    onmouseover="this.style.backgroundColor='#374151'"
                                    onmouseout="this.style.backgroundColor='#1e2936'"
                                >
                                   Editar
                                </button>
                            `;
                        }
                    }
                ],
                "language": {
                    "url": "actions/spanish.json"
                },
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        text: 'Emparejar',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
                            $("#form_partidasbusqueda").trigger("reset");
                            $("#form_partidasbusqueda").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_partidasbusqueda").text("Torneo a emparejar");
        
                            $.ajax({
                                url: 'Http/Controlllers/PartidaController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    $('#select_torneo_partida').empty();
        
                                    $('#select_torneo_partida').append(
                                        $('<option>', {
                                            value: '',
                                            text: 'Seleccione un torneo...',
                                            disabled: true,
                                            selected: true
                                        })
                                    );

                                    if (response && Array.isArray(response)) {
                        
                                        response.forEach(function(torneo) {
                        
                                            $('#select_torneo_partida').append(
                                                $('<option>', {
                                                    value: torneo.torneo_id,
                                                    text: torneo.torneo
                                                })
                                            );
                                        });
                                    } else {
                                        console.error('No se recibieron datos válidos');
                                        $('#select_torneo_partida').append(
                                            $('<option>', {
                                                text: 'No hay torneos para emparejar',
                                                value: ''
                                            })
                                        );
                                        
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error en la llamada AJAX: ' + error);
                                }
                            });
        
                            $('#modal_partidasbusqueda').modal('show');
                        }
                    },
                    {
                        text: 'Importar partidas',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {


                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        title: 'Lista de partidas',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        exportOptions: {
                            columns: ':not(:last-child)'  // Excluir la última columna
                        }
                    }
                ]
            }).columns.adjust().responsive.recalc();
        }

    });

    /* ******************************* BOTÓN EDITAR PARA: PARTIDAS ******************************* */
    $(document).on("click", ".btnEditar_partida", function(){

    })
    /* ****************************************************************************************** */

    /* ********************* ENVÍO DE FORMULARIO: PARTIDAS ********************* */
    $("#form_partidasbusqueda").submit(function(e) {
        e.preventDefault();
    
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
    
        var torneo_val = $('#select_torneo_partida').val();
        var torneo_text = $('#select_torneo_partida').find('option:selected').text();
    
        //console.log('Valor seleccionado (value):', torneo_val);
        //console.log('Texto seleccionado (text):', torneo_text);

        $.ajax({
            url: 'sHttp/Controlllers/AcademiaController.php',
            type: 'POST',
            data: {
                torneo_val: torneo_val,
                torneo_text: torneo_text
            },
            dataType: 'json',
            success: function(response) {

            },
            error: function(xhr, status, error) {

            }
        });

        $('#modal_partidasbusqueda').modal('hide');
    });
    /* ************************************************************************ */
})