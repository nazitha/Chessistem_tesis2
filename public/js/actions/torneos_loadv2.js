$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
    var opcion = 1;
    var search;
    var search_1;
    var search_2;
    var search_f;
    var load = 0;

    var load_table = 0;

    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
    var mail_log = userData.correo;

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: ACADEMIAS ********************* */
    $('#torneos_y_partidas_opcion, #torneos_y_partidas_opcion_movil').click(function() {
        load_table++;

        if(load_table === 1)
        {
            window.tabla_torneos = $('#tabla-torneos').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/TorneoController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "nombre_torneo" },
                    { "data": "fecha" },
                    { "data": "categoria_torneo" },
                    { "data": "formato" },
                    { "data": "emparejamiento" },
                    { "data": "lugar" },
                    { "data": "no_rondas" },
                    { "data": "federacion_id" },
                    { "data": "organizador" },
                    { "data": "director_torneo" },
                    { "data": "arbitro" },
                    { "data": "arbitro_principal" },
                    { "data": "arbitro_adjunto" },
                    { "data": "estado" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-danger btn-sm btnEditar_torneo"
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
                                    class="btn btn-danger btn-sm btnEliminar_torneo"
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
                        text: 'Crear torneo',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
                            $("#form_torneos").trigger("reset");
                            $("#form_torneos").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_torneos").text("Nuevo torneo");
        
                            $('#modal_torneos').modal('show');
        
                            //CARGA DE DATOS EN EL SELECT FEDERACIONES
                            $.ajax({
                                url: 'Http/Controlllers/TorneoController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_federacion_torneos').empty();
                    
                                        $('#select_federacion_torneos').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione una federación...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(federacion) {
                        
                                            $('#select_federacion_torneos').append(
                                                $('<option>', {
                                                    value: federacion.acronimo,
                                                    text: federacion.federacion
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
        
                            //CARGA DE DATOS EN EL SELECT ORGANIZADOR
                            $.ajax({
                                url: 'Http/Controlllers/TorneoController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 3 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_organizador_torneos').empty();
                    
                                        $('#select_organizador_torneos').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un organizador...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(organizador) {
                        
                                            $('#select_organizador_torneos').append(
                                                $('<option>', {
                                                    value: organizador.cedula,
                                                    text: organizador.miembro
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
        
                            //CARGA DE DATOS EN EL SELECT DIRECTOR
                            $.ajax({
                                url: 'Http/Controlllers/TorneoController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 3 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_director_torneos').empty();
                    
                                        $('#select_director_torneos').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un director...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(director) {
                        
                                            $('#select_director_torneos').append(
                                                $('<option>', {
                                                    value: director.cedula,
                                                    text: director.miembro
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
        
                            //CARGA DE DATOS EN EL SELECT ÁRBITRO
                            $.ajax({
                                url: 'Http/Controlllers/TorneoController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 3 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_arbitro_torneos').empty();
                    
                                        $('#select_arbitro_torneos').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un árbitro...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(arbitro) {
                        
                                            $('#select_arbitro_torneos').append(
                                                $('<option>', {
                                                    value: arbitro.cedula,
                                                    text: arbitro.miembro
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
        
                            //CARGA DE DATOS EN EL SELECT ÁRBITRO PRINCIPAL
                            $.ajax({
                                url: 'Http/Controlllers/TorneoController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 3 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_arbitrop_torneos').empty();
                    
                                        $('#select_arbitrop_torneos').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un árbitro principal...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(arbitrop) {
                        
                                            $('#select_arbitrop_torneos').append(
                                                $('<option>', {
                                                    value: arbitrop.cedula,
                                                    text: arbitrop.miembro
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
        
                            //CARGA DE DATOS EN EL SELECT ÁRBITRO ADJUNTO
                            $.ajax({
                                url: 'Http/Controlllers/TorneoController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 3 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_arbitroadj_torneos').empty();
                    
                                        $('#select_arbitroadj_torneos').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un árbitro adjunto...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(arbitroadj) {
                        
                                            $('#select_arbitroadj_torneos').append(
                                                $('<option>', {
                                                    value: arbitroadj.cedula,
                                                    text: arbitroadj.miembro
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
        
                            //CARGA DE DATOS EN EL SELECT CATEGORÍAS DE TORNEOS
                            $.ajax({
                                url: 'Http/Controlllers/TorneoController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 4 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_categoria_torneos').empty();
                    
                                        $('#select_categoria_torneos').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione una categoría...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(categoria) {
                        
                                            $('#select_categoria_torneos').append(
                                                $('<option>', {
                                                    value: categoria.id_torneo_categoria,
                                                    text: categoria.categoria_torneo
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
    
                            //CARGA DE DATOS EN EL SELECT EMPAREJAMIENTO
                            $.ajax({
                                url: 'Http/Controlllers/TorneoController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 12 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_emparejamiento_torneos').empty();
                    
                                        $('#select_emparejamiento_torneos').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un sistema...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(sistemas) {
                        
                                            $('#select_emparejamiento_torneos').append(
                                                $('<option>', {
                                                    value: sistemas.id_emparejamiento,
                                                    text: sistemas.sistema
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
        
                            opcion = 6;
                        }
                    },
                    {
                        text: 'Importar torneos',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
                            var fileInput = document.createElement('input');
                            fileInput.type = 'file';
                            fileInput.accept = '.csv';
                    
                            fileInput.click();
                    
                            fileInput.onchange = function (event) {
                                var file = event.target.files[0];
                                
                                if (file) {
                                    var formData = new FormData();
                                    formData.append('file', file);
                    
                                    $.ajax({
                                        url: 'Http/Controlllers/ImportarTorneosController.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        success: function(response) {
                                    
                                            // Verificar si la respuesta no está vacía
                                            if (response.trim() === '') {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: 'La respuesta del servidor está vacía.'
                                                });
                                                return;
                                            }
                                    
                                            try {
                                                var result = JSON.parse(response);
                                    
                                                // Verificar que la respuesta tenga los datos esperados
                                                if (result && result.registrosEncontrados !== undefined) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Importación finalizada',
                                                        html: `
                                                            <b>Registros encontrados:</b> ${result.registrosEncontrados}<br>
                                                            <b>Registros existentes:</b> ${result.registrosExistentes}<br>
                                                            <b>Registros incompletos:</b> ${result.registrosIncompletos}<br>
                                                            <b>Errores:</b> ${result.errores}<br>
                                                            <hr>
                                                            <b>Total registros insertados:</b> ${result.registrosInsertados}<br>
                                                            <b>Total registros no insertados:</b> ${result.registrosNoInsertados}
                                                        `
                                                    });
                                    
                                                    window.tabla_torneos.ajax.reload();
                                                    $.ajax({
                                                     //   url: 'http://192.168.100.100:3001/refresh/torneos', 
                                                        type: 'GET',
                                                        success: function() {
                                                            console.log('Evento de actualización enviado para torneos');
                                                        },
                                                        error: function(xhr, status, error) {
                                                            console.log('Error al emitir evento: ' + error);
                                                        }
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: 'La respuesta del servidor no contiene los datos esperados.'
                                                    });
                                                }
                                            } catch (e) {
                                                // Si ocurre un error al parsear el JSON
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error al parsear los datos',
                                                    text: 'Hubo un problema al procesar la respuesta del servidor.'
                                                });
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'Ocurrió un error al importar el archivo. Intenta de nuevo.'
                                            });
                                        }
                                    });
                                    
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'No se seleccionó un archivo',
                                        text: 'Por favor selecciona un archivo .csv para continuar.'
                                    });
                                }
                            };
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        title: 'Lista de torneos existentes',
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

    var prev_torneo;
    var prev_fecha;
    var prev_hora;
    var prev_categoria;
    var prev_formato;
    var prev_lugar;
    var prev_rondas;
    var prev_federacion;
    var prev_organizador;
    var prev_director;
    var prev_arbitro;
    var prev_arbitroprin;
    var prev_arbitroadjunt;
    var prev_estado;
    var prev_emparejamiento;

    /* ******************************* BOTÓN EDITAR PARA: ACADEMIAS ******************************* */
    $(document).on("click", ".btnEditar_torneo", function(){
        $("#form_torneos").trigger("reset");
        $("#form_torneos").removeClass("was-validated");
        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_torneos").text("Editar torneo");

        opcion = 8;
        load = 1;

        $('#modal_torneos').modal('show');
        
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_torneos.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search_1 = datosFila.nombre_torneo;
        search_2 = datosFila.fecha;
        categoria = datosFila.categoria_torneo;
        formato = datosFila.formato;
        lugar = datosFila.lugar;
        rondas = datosFila.no_rondas;
        federacion = datosFila.federacion_id;
        organizador = datosFila.organizador;
        director = datosFila.director_torneo;
        arbitro = datosFila.arbitro;
        arbitroprin = datosFila.arbitro_principal;
        arbitroadjunt = datosFila.arbitro_adjunto;
        estado = datosFila.estado;
        emparejamiento = datosFila.emparejamiento;

        prev_torneo = search_1;

        const [fecha, hora] = search_2.split(', ').map(item => item.trim());

        search_f = fecha;

        prev_torneo = search_1;
        prev_fecha = fecha;
        prev_hora = hora;
        prev_categoria = categoria;
        prev_formato = formato;
        prev_lugar = lugar;
        prev_rondas = rondas;
        prev_federacion = federacion;
        prev_organizador = organizador;
        prev_director = director;
        prev_arbitro = arbitro;
        prev_arbitroprin = arbitroprin;
        prev_arbitroadjunt = arbitroadjunt;
        prev_estado = estado;
        prev_emparejamiento = emparejamiento;

        $('#input_nombre_torneo').val(search_1);
        $('#input_fecha_torneo').val(fecha);
        $('#input_hora_torneo').val(hora);
        $('#input_lugar_torneo').val(lugar);
        $('#input_rondas_torneo').val(rondas);

        const label = document.getElementById("switchLabel_torneo");
        const switch_estado = document.getElementById("switch_estado_torneo");
        if (estado === 'Activo') {
            $('#switch_estado_torneo').prop('checked', true);
            switch_estado.style.backgroundColor = '#28a745';
            switch_estado.style.borderColor = '#28a745';
            label.textContent = "Activo";
        } else if (estado === 'Finalizado') {
            $('#switch_estado_torneo').prop('checked', false);
            switch_estado.style.backgroundColor = '#dc3545';
            switch_estado.style.borderColor = '#dc3545';
            label.textContent = "Finalizado";
        }

        //CARGA DE DATOS EN EL SELECT FEDERACIONES
        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 2 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_federacion_torneos').empty();
                
                    response.forEach(function(federacion) {
                
                        $('#select_federacion_torneos').append(
                            $('<option>', {
                                value: federacion.acronimo,
                                text: federacion.federacion
                            })
                        );
                    });

                    $('#select_federacion_torneos').val(federacion);
            
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

        //CARGA DE DATOS EN EL SELECT ORGANIZADOR
        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 3 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_organizador_torneos').empty();
                
                    response.forEach(function(organizador) {
                
                        $('#select_organizador_torneos').append(
                            $('<option>', {
                                value: organizador.cedula,
                                text: organizador.miembro
                            })
                        );
                    });

                    $('#select_organizador_torneos option').filter(function() {
                        return $(this).text() === organizador;
                    }).prop('selected', true);
            
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

        //CARGA DE DATOS EN EL SELECT DIRECTOR
        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 3 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_director_torneos').empty();
                
                    response.forEach(function(director) {
                
                        $('#select_director_torneos').append(
                            $('<option>', {
                                value: director.cedula,
                                text: director.miembro
                            })
                        );
                    });

                    $('#select_director_torneos option').filter(function() {
                        return $(this).text() === director;
                    }).prop('selected', true);
            
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

        //CARGA DE DATOS EN EL SELECT ÁRBITRO
        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 3 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_arbitro_torneos').empty();           
                
                    response.forEach(function(arbitro) {
                
                        $('#select_arbitro_torneos').append(
                            $('<option>', {
                                value: arbitro.cedula,
                                text: arbitro.miembro
                            })
                        );
                    });

                    $('#select_arbitro_torneos option').filter(function() {
                        return $(this).text() === arbitro;
                    }).prop('selected', true);
            
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

        //CARGA DE DATOS EN EL SELECT ÁRBITRO PRINCIPAL
        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 3 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_arbitrop_torneos').empty();
            
                    response.forEach(function(arbitrop) {
                
                        $('#select_arbitrop_torneos').append(
                            $('<option>', {
                                value: arbitrop.cedula,
                                text: arbitrop.miembro
                            })
                        );
                    });

                    $('#select_arbitrop_torneos option').filter(function() {
                        return $(this).text() === arbitroprin;
                    }).prop('selected', true);
            
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

        //CARGA DE DATOS EN EL SELECT ÁRBITRO ADJUNTO
        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 3 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_arbitroadj_torneos').empty();
                
                    response.forEach(function(arbitroadj) {
                
                        $('#select_arbitroadj_torneos').append(
                            $('<option>', {
                                value: arbitroadj.cedula,
                                text: arbitroadj.miembro
                            })
                        );
                    });

                    $('#select_arbitroadj_torneos option').filter(function() {
                        return $(this).text() === arbitroadjunt;
                    }).prop('selected', true);
            
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

        //CARGA DE DATOS EN EL SELECT CATEGORÍAS DE TORNEOS
        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 4 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_categoria_torneos').empty();
                
                    response.forEach(function(categoria) {
                
                        $('#select_categoria_torneos').append(
                            $('<option>', {
                                value: categoria.id_torneo_categoria,
                                text: categoria.categoria_torneo
                            })
                        );
                    });

                    $('#select_categoria_torneos option').filter(function() {
                        return $(this).text() === categoria;
                    }).prop('selected', true);

                    cargar_formatos(categoria);
            
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

        //CARGA DE DATOS EN EL SELECT EMPAREJAMIENTO
        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 12 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_emparejamiento_torneos').empty();
                    
                    $('#select_emparejamiento_torneos').append(
                        $('<option>', {
                            value: '',
                            text: 'Seleccione un sistema...',
                            disabled: true,
                            selected: true
                        })
                    );
                        
                    response.forEach(function(sistemas) {
                        
                        $('#select_emparejamiento_torneos').append(
                            $('<option>', {
                                value: sistemas.id_emparejamiento,
                                text: sistemas.sistema
                            })
                        );
                    });

                    $('#select_emparejamiento_torneos option').filter(function() {
                        return $(this).text() === emparejamiento;
                    }).prop('selected', true);
                    
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN EDITAR PARA: ACADEMIAS ******************************* */
    $(document).on("click", ".btnEliminar_torneo", function(){
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_torneos.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search_1 = datosFila.nombre_torneo;
        search_2 = datosFila.fecha;
        categoria = datosFila.categoria_torneo;
        formato = datosFila.formato;
        lugar = datosFila.lugar;
        rondas = datosFila.no_rondas;
        federacion = datosFila.federacion_id;
        organizador = datosFila.organizador;
        director = datosFila.director_torneo;
        arbitro = datosFila.arbitro;
        arbitroprin = datosFila.arbitro_principal;
        arbitroadjunt = datosFila.arbitro_adjunto;
        estado = datosFila.estado;

        prev_torneo = search_1;

        const [fecha, hora] = search_2.split(', ').map(item => item.trim());

        search_f = fecha;

        prev_torneo = search_1;
        prev_fecha = fecha;
        prev_hora = hora;
        prev_categoria = categoria;
        prev_formato = formato;
        prev_lugar = lugar;
        prev_rondas = rondas;
        prev_federacion = federacion;
        prev_organizador = organizador;
        prev_director = director;
        prev_arbitro = arbitro;
        prev_arbitroprin = arbitroprin;
        prev_arbitroadjunt = arbitroadjunt;
        prev_estado = estado;

       Swal.fire({
            title: `¿Desea eliminar el torneo <b>${search_1}</b> programado para el <b>${prev_fecha}</b> a las <b>${prev_hora}</b>?`,
            icon: 'warning',
            showDenyButton: true,
            confirmButtonText: "Eliminar",
            denyButtonText: 'Cancelar',
            html: `<p>Una vez eliminado, no podrá revertir los cambios</p>`,
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: 'Http/Controlllers/TorneoController.php',
                    type: 'POST',
                    data: {
                        search_1: search_1,
                        search_f: search_f,
                        opcion: 10
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'El torneo ha sido eliminado con éxito.'
                        });

                        window.tabla_torneos.ajax.reload();

                        $.ajax({
                            url: 'Http/Controlllers/TorneoController.php',
                            type: 'POST',
                            data: {
                                opcion: 11,
                                mail_log: mail_log,
                                prev_torneo: prev_torneo,
                                prev_fecha: prev_fecha,
                                prev_hora: prev_hora,
                                prev_categoria: prev_categoria,
                                prev_formato: prev_formato,
                                prev_lugar: prev_lugar,
                                prev_rondas: prev_rondas,
                                prev_federacion: prev_federacion,
                                prev_organizador: prev_organizador,
                                prev_director: prev_director,
                                prev_arbitro: prev_arbitro,
                                prev_arbitroprin: prev_arbitroprin,
                                prev_arbitroadjunt: prev_arbitroadjunt,
                                prev_estado: prev_estado
                            },
                            dataType: 'json',
                            success: function(response) {
                                window.tabla_historial.ajax.reload();
                            },
                            error: function(xhr, status, error) {
    
                            }
                        }); 

                        $.ajax({
                        //    url: 'http://192.168.100.100:3001/refresh/torneos',
                            type: 'GET',
                            success: function() {
                                console.log('Evento de actualización enviado para torneos');
                            },
                            error: function(xhr, status, error) {
                                console.log('Error al emitir evento: ' + error);
                            }
                        });
      
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si la ciudad ya existe.'
                        });
                    }
                });
            }
        }); 
    })
    /* ****************************************************************************************** */

    /* ********************* ENVÍO DE FORMULARIO: ACADEMIAS ********************* */
    $("#form_torneos").submit(function(e) {
        e.preventDefault();
    
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
    
        var torneo = $.trim($("#input_nombre_torneo").val());
        var fecha = $.trim($('#input_fecha_torneo').val());
        var hora = $.trim($('#input_hora_torneo').val());

        var categoriaValue = $('#select_categoria_torneos').val();
        var categoriaText = $('#select_categoria_torneos option:selected').text();

        var formatoValue = $('#select_formato_torneos').val();
        var formatoText = $('#select_formato_torneos option:selected').text();

        var lugar = $.trim($('#input_lugar_torneo').val());
        var rondas = $.trim($('#input_rondas_torneo').val());

        var federacionValue = $('#select_federacion_torneos').val();
        var federacionText = $('#select_federacion_torneos option:selected').text();

        var organizadorValue = $('#select_organizador_torneos').val();
        var organizadorText = $('#select_organizador_torneos option:selected').text();

        var directorValue = $('#select_director_torneos').val();
        var directorText = $('#select_director_torneos option:selected').text();

        var arbitroValue = $('#select_arbitro_torneos').val();
        var arbitroText = $('#select_arbitro_torneos option:selected').text();

        var arbitropValue = $('#select_arbitrop_torneos').val();
        var arbitropText = $('#select_arbitrop_torneos option:selected').text();

        var arbitroadjValue = $('#select_arbitroadj_torneos').val();
        var arbitroadjText = $('#select_arbitroadj_torneos option:selected').text();

        var emparejamientoValue = $('#select_emparejamiento_torneos').val();
        var emparejamientoText = $('#select_emparejamiento_torneos option:selected').text();

        var estado = $('#switch_estado_torneo').prop('checked') ? 1 : 0;
        var estado_text;

        if(federacionText === 'Seleccione una federación...')
        {
            federacionText = '-';
        }
        if(formatoText === 'Seleccione un formato...')
        {
            formatoText = '-';
        }

        if(estado == 0)
        {
            estado_text = 'Finalizado';
        }
        else if(estado == 1)
        {
            estado_text = 'Activo';
        }

        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            data: {
                torneo: torneo,
                fecha: fecha,
                hora: hora,
                categoriaValue: categoriaValue,
                formatoValue: formatoValue,
                lugar: lugar,
                rondas: rondas,
                federacionValue: federacionValue,
                organizadorValue: organizadorValue,
                directorValue: directorValue,
                arbitroValue: arbitroValue,
                arbitropValue: arbitropValue,
                arbitroadjValue: arbitroadjValue,
                emparejamientoValue: emparejamientoValue,
                estado: estado,
                opcion: opcion,
                search_1: search_1,
                search_f: search_f
            },
            dataType: 'json',
            success: function(response) {

                $.ajax({
           //         url: 'http://192.168.100.100:3001/refresh/torneos', 
                    type: 'GET',
                    success: function() {
                        console.log('Evento de actualización enviado para torneos');
                    },
                    error: function(xhr, status, error) {
                        console.log('Error al emitir evento: ' + error);
                    }
                });
                

                if(opcion == 6)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El torneo <strong>' + torneo + '</strong> programado para la fecha <strong>' + fecha + '</strong> a las <strong>' +
                        (function() {
                            var hora24 = hora;
                            var partes = hora24.split(':');
                            var horas = parseInt(partes[0], 10);
                            var minutos = partes[1];
                            var sufijo = horas >= 12 ? 'p. m.' : 'a. m.';
                            horas = horas % 12 || 12;
                            return horas + ':' + minutos + ' ' + sufijo;
                        })() +
                        '</strong> ha sido creado con éxito.'
                    });

                    window.tabla_torneos.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/TorneoController.php',
                        type: 'POST',
                        data: {
                            torneo: torneo,
                            fecha: fecha,
                            hora: hora,
                            categoriaText: categoriaText,
                            formatoText: formatoText,
                            lugar: lugar,
                            rondas: rondas,
                            federacionText: federacionText,
                            organizadorText: organizadorText,
                            directorText: directorText,
                            arbitroText: arbitroText,
                            arbitropText: arbitropText,
                            arbitroadjText: arbitroadjText,
                            emparejamientoText: emparejamientoText,
                            estado_text: estado_text,
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

                    $("#modal_torneos").modal("hide");
                }
                else if(opcion == 8)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El torneo <strong>' + torneo + '</strong> programado para la fecha <strong>' + fecha + '</strong> a las <strong>' +
                        (function() {
                            var hora24 = hora;
                            var partes = hora24.split(':');
                            var horas = parseInt(partes[0], 10);
                            var minutos = partes[1];
                            var sufijo = horas >= 12 ? 'p. m.' : 'a. m.';
                            horas = horas % 12 || 12;
                            return horas + ':' + minutos + ' ' + sufijo;
                        })() +
                        '</strong> ha sido actualizado con éxito.'
                    });
                    
                    window.tabla_torneos.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/TorneoController.php',
                        type: 'POST',
                        data: {
                            torneo: torneo,
                            fecha: fecha,
                            hora: hora,
                            categoriaText: categoriaText,
                            formatoText: formatoText,
                            lugar: lugar,
                            rondas: rondas,
                            federacionText: federacionText,
                            organizadorText: organizadorText,
                            directorText: directorText,
                            arbitroText: arbitroText,
                            arbitropText: arbitropText,
                            arbitroadjText: arbitroadjText,
                            estado_text: estado_text,
                            emparejamientoText: emparejamientoText,
                            opcion: 9,
                            mail_log: mail_log,
                            prev_torneo: prev_torneo,
                            prev_fecha: prev_fecha,
                            prev_hora: prev_hora,
                            prev_categoria: prev_categoria,
                            prev_formato: prev_formato,
                            prev_lugar: prev_lugar,
                            prev_rondas: prev_rondas,
                            prev_federacion: prev_federacion,
                            prev_organizador: prev_organizador,
                            prev_director: prev_director,
                            prev_arbitro: prev_arbitro,
                            prev_arbitroprin: prev_arbitroprin,
                            prev_arbitroadjunt: prev_arbitroadjunt,
                            prev_estado: prev_estado,
                            prev_emparejamiento: prev_emparejamiento
                        },
                        dataType: 'json',
                        success: function(response) {
                            window.tabla_historial.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Error en auditoría
                        }
                    });
                    $("#modal_torneos").modal("hide");
                }

            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si el torneo programado para esa fecha ya existe.'
                });
            }
        });

    });
    /* ************************************************************************ */

    /* ******************************* CARGA DINÁMICA DE DATOS DEL INPUT SELECT: FORMATOS ******************************* */
    $('#select_categoria_torneos').on('change', function() {
        var categoria_torneo = $(this).find('option:selected').text();
        cargar_formatos(categoria_torneo);
    });

    function cargar_formatos(categoria_torneo) {
        search = categoria_torneo;

        $.ajax({
            url: 'Http/Controlllers/TorneoController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 5, search: search },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_formato_torneos').empty();

                    $('#select_formato_torneos').append(
                        $('<option>', {
                            value: '',
                            text: 'Seleccione un formato...',
                            disabled: true,
                            selected: true
                        })
                    );
    
                    response.forEach(function(formatos) {
    
                        $('#select_formato_torneos').append(
                            $('<option>', {
                                value: formatos.control_tiempo_id,
                                text: formatos.formato
                            })
                        );
                    });

                    if(load == 1)
                    {
                        $('#select_formato_torneos option').filter(function() {
                            return $(this).text() === formato;
                        }).prop('selected', true);

                        load = 0;
                    }

                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });
    }

<<<<<<< HEAD
=======
    const modalParticipantes = document.getElementById('modal-participantes');
    if (modalParticipantes) {
        modalParticipantes.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalParticipantes();
            }
        });
    }

>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
});