$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
     var opcion = 1;
     var search_t;
     var search_p;
     var load_table = 0;
    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
     var prev_participante;
     var prev_torneo;

     var mail_log = userData.correo;
    /* ********************************** */

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: FEDERACIONES ********************* */
    $('#inscripciones_opcion, #inscripciones_opcion_movil').click(function() {

        load_table++;

        if(load_table === 1)
        {
            window.tabla_inscripciones = $('#tabla-inscripciones').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/InscripcionController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    {"data": "torneo_id"},
                    { "data": "torneo" },
                    { "data": "participante" },
                    { "data": "cedula" },
                    { "data": "federacion" },
                    { "data": "estado" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-light btnEditar_inscripciones"
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
                                <button 
                                    class="btn btn-danger btn-sm btnEliminar_inscripciones"
                                    style="
                                        background-color: #1e2936;
                                        color: white;
                                        border: 1px solid transparent;
                                        padding: 0.375rem 0.75rem;
                                        font-size: 1rem;
                                        font-weight: 400;
                                        line-height: 1.5;
                                        border-radius: 0 0.25rem 0.25rem 0; /* Bordes redondeados a la derecha */
                                        text-align: center;
                                        vertical-align: middle;
                                        cursor: pointer;
                                        transition: background-color 0.3s ease;
                                        display: inline-block; /* Asegura que los botones estén en línea */
                                        margin-left: -1px; /* Elimina el espacio entre los botones */
                                    "
                                    onmouseover="this.style.backgroundColor='#374151'"
                                    onmouseout="this.style.backgroundColor='#1e2936'"
                                >
                                    Eliminar
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
                        text: 'Inscribir',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
                            $("#form_inscripciones").trigger("reset");
                            $("#form_inscripciones").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_inscripciones").text("Realizar inscripción");
        
                            /* ********************* CARGA DEL INPUT SELECT: TORNEOS ********************* */
                            $.ajax({
                                url: 'Http/Controlllers/InscripcionController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_torneos_inscripciones').empty();
        
                                        $('#select_torneos_inscripciones').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un torneo...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                            
                                        response.forEach(function(torneo) {
                            
                                            $('#select_torneos_inscripciones').append(
                                                $('<option>', {
                                                    value: torneo.id_torneo,
                                                    text: torneo.torneo
                                                })
                                            );
                                        });
                                    } else {
                                        console.error('No se recibieron datos válidos');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error en la llamada AJAX: ' + error);
                                }
                            });
                            /* ************************************************************************* */
        
                            /* ********************* CARGA DEL INPUT SELECT: PARTICIPANTES ********************* */
                            $.ajax({
                                url: 'Http/Controlllers/InscripcionController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 3 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_participantes_inscripciones').empty();
        
                                        $('#select_participantes_inscripciones').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un participante...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                            
                                        response.forEach(function(miembro) {
                            
                                            $('#select_participantes_inscripciones').append(
                                                $('<option>', {
                                                    value: miembro.cedula,
                                                    text: miembro.miembro
                                                })
                                            );
                                        });
                                    } else {
                                        console.error('No se recibieron datos válidos');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error en la llamada AJAX: ' + error);
                                }
                            });
                            /* ************************************************************************* */
        
                            opcion = 4;
        
                            $('#modal_inscripciones').modal('show');
                        }
                    },
                    {
                        text: 'Importar participantes',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
                            const inputFile = document.createElement('input');
                            inputFile.type = 'file';
                            inputFile.accept = '.csv';
                            
                            inputFile.click();
                            
                            inputFile.onchange = function (event) {
                                const file = event.target.files[0];
                                if (file) {
                                    const formData = new FormData();
                                    formData.append('csvFile', file);
                            
                                    $.ajax({
                                        url: 'Http/Controlllers/ImportarInscripcionesController.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        success: function (response) {
                                            const result = JSON.parse(response);
                            
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Importación finalizada',
                                                html: `
                                                    <div style="text-align: left; line-height: 1.6; margin: 0 auto; display: inline-block;">
                                                        <b>Registros encontrados:</b> ${result.registrosEncontrados}<br>
                                                        <b>Registros existentes:</b> ${result.registrosExistentes}<br>
                                                        <b>Registros incompletos:</b> ${result.registrosIncompletos}<br>
                                                        <b>Errores:</b> ${result.errores}<br>
                                                        <hr>
                                                        <b>Total registros insertados:</b> ${result.registrosInsertados}<br>
                                                        <b>Total registros no insertados:</b> ${result.registrosNoInsertados}
                                                    </div>
                                                `,
                                                customClass: {
                                                    popup: 'custom-swal-popup'
                                                }
                                            });

                                            window.tabla_inscripciones.ajax.reload();
                                            
                                            $.ajax({
                                               // url: 'http://192.168.100.100:3001/refresh/inscripciones', 
                                                type: 'GET',
                                                success: function() {
                                                    //console.log('Evento de actualización enviado para academias');
                                                },
                                                error: function(xhr, status, error) {
                                                    //console.log('Error al emitir evento: ' + error);
                                                }
                                            });
                                        },
                                        error: function () {
                                            alert('Hubo un error al procesar el archivo.');
                                        }
                                    });
                                }
                            };
                            
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        title: 'Lista de incripciones',
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
    /* *************************************************************************************** */

    /* ******************************* BOTÓN EDITAR PARA: FEDERACIONES ******************************* */
    $(document).on("click", ".btnEditar_inscripciones", function(){
        $("#form_inscripciones").trigger("reset");
        $("#form_inscripciones").removeClass("was-validated");
    
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_inscripciones.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search_t = datosFila.torneo_id;
        search_p = datosFila.cedula;

        prev_participante

        /* ********************* CARGA DEL INPUT SELECT: TORNEOS ********************* */
        $.ajax({
            url: 'Http/Controlllers/InscripcionController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 2 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_torneos_inscripciones').empty();
                    
                    let select_t = $('#select_torneos_inscripciones');
                    
                    response.forEach(function (torneo) {
                        let option = $('<option>', {
                            value: torneo.id_torneo,
                            text: torneo.torneo
                        });
                    
                        select_t.append(option);
                    
                        if (torneo.id_torneo === search_t) {
                            option.prop('selected', true);
                            prev_torneo = $.trim($('#select_torneos_inscripciones option:selected').text());
                        }
                    });
                    
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });
        /* ************************************************************************* */

        /* ********************* CARGA DEL INPUT SELECT: PARTICIPANTES ********************* */
        $.ajax({
            url: 'Http/Controlllers/InscripcionController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 3 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_participantes_inscripciones').empty();
                    
                    let select_p = $('#select_participantes_inscripciones');

                    response.forEach(function (miembro) {
                        let option = $('<option>', {
                            value: miembro.cedula,
                            text: miembro.miembro
                        });
                    
                        select_p.append(option);
                    
                        if (miembro.cedula === search_p) {
                            option.prop('selected', true);
                            prev_participante = $.trim($('#select_participantes_inscripciones option:selected').text());
                        }
                    });
                    
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });
        /* ************************************************************************* */
    
        opcion = 6;
    
        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_inscripciones").text("Editar inscripción");
        $("#modal_inscripciones").modal("show");
    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN ELIMINAR PARA: FEDERACIONES ******************************* */
    $(document).on("click", ".btnEliminar_inscripciones", function(){
    
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_inscripciones.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search_t = datosFila.torneo_id;
        search_p = datosFila.cedula;

        var participante_text = datosFila.participante;
        var torneo_text = datosFila.torneo;       

        Swal.fire({
            title: "Advertencia",
            html: '¿Desea eliminar al participante <strong>' + participante_text + '</strong> del torneo <strong>' + torneo_text + '</strong>? Una vez eliminado, no podrá recuperar el registro',
            showDenyButton: true,
            icon: "warning",
            confirmButtonText: "Continuar",
            denyButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: 'Http/Controlllers/InscripcionController.php',
                    type: 'POST',
                    data: { 
                            opcion: 8, 
                            search_t: search_t,
                            search_p: search_p 
                        },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'El participante <strong>' + participante_text + '</strong> ha sido retirado del torneo <strong>' + torneo_text + '</strong>'
                        });

                        $.ajax({
                          //  url: 'http://192.168.100.100:3001/refresh/inscripciones', 
                            type: 'GET',
                            success: function() {
                                //console.log('Evento de actualización enviado para academias');
                            },
                            error: function(xhr, status, error) {
                                //console.log('Error al emitir evento: ' + error);
                            }
                        });

                        window.tabla_inscripciones.ajax.reload();

                        $.ajax({
                            url: 'Http/Controlllers/InscripcionController.php',
                            type: 'POST',
                            data: {
                                opcion: 9,
                                participante_text: participante_text,
                                torneo_text: torneo_text,
                                mail_log: mail_log
                            },
                            dataType: 'json',
                            success: function(response) {
                                window.tabla_historial.ajax.reload();
                            },
                            error: function(xhr, status, error) {
                                // Error en auditoría
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la llamada AJAX: ' + error);
                        console.log('Respuesta del servidor:', xhr.responseText);
                    }
                });
            }
        });
    
    })
    /* ****************************************************************************************** */

    /* ********************* ENVÍO DE FORMULARIO: FEDERACIONES ********************* */
    $("#form_inscripciones").submit(function(e) {
        e.preventDefault();
    
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var participante_text = $.trim($('#select_participantes_inscripciones option:selected').text());
        var participante_value = $.trim($('#select_participantes_inscripciones option:selected').val());
        var torneo_text = $.trim($('#select_torneos_inscripciones option:selected').text());
        var torneo_value = $.trim($('#select_torneos_inscripciones option:selected').val());

        $.ajax({
            url: 'Http/Controlllers/InscripcionController.php',
            type: 'POST',
            data: {
                participante_value: participante_value,
                torneo_value: torneo_value,
                opcion: opcion,
                search_p: search_p,
                search_t: search_t
            },
            dataType: 'json',
            success: function(response) {

                $.ajax({
                  //  url: 'http://192.168.100.100:3001/refresh/inscripciones', 
                    type: 'GET',
                    success: function() {
                        //console.log('Evento de actualización enviado para academias');
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error al emitir evento: ' + error);
                    }
                });

                if(opcion == 4)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El participante <strong>' + participante_text + '</strong> ha sido inscrito al torneo <strong>' + torneo_text + '</strong>.'
                    });

                    window.tabla_inscripciones.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/InscripcionController.php',
                        type: 'POST',
                        data: 
                        {
                            participante_text: participante_text,
                            torneo_text: torneo_text,
                            opcion: 5,
                            mail_log: mail_log
                        },
                        dataType: 'json',
                        success: function(response) {
                            window.tabla_historial.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Error en auditoría
                        }
                    });

                    $("#modal_inscripciones").modal("hide");
                }
                else if(opcion == 6)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'La inscripción del participante <strong>' + participante_text + '</strong> del torneo <strong>' + torneo_text + '</strong> ha sido actualizada con éxito.'
                    });
                    
                    window.tabla_inscripciones.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/InscripcionController.php',
                        type: 'POST',
                        data: {
                            participante_text: participante_text,
                            torneo_text: torneo_text,
                            prev_participante: prev_participante,
                            prev_torneo: prev_torneo,
                            opcion: 7,
                            mail_log: mail_log
                        },
                        dataType: 'json',
                        success: function(response) {
                            window.tabla_historial.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Error en auditoría
                        }
                    });
                    $("#modal_inscripciones").modal("hide");
                }

            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si el participante ya está escrito en el torneo.'
                });
            }
        });

    });
    /* ************************************************************************ */

});