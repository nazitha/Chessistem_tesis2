$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
    var opcion = 1;
    var search;
    var load_table = 0;
    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
    var mail_log = userData.correo;

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: ACADEMIAS ********************* */
    $('#fide_opcion, #fide_opcion_movil').click(function() {

        load_table++;

        if(load_table === 1)
        {
            window.tabla_fide = $('#tabla-fide').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/FideController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "fide_id" },
                    { "data": "cedula" },
                    { "data": "nombre" },
                    { "data": "fed_id" },
                    { "data": "titulo" },
                    { "data": "elo_blitz" },
                    { "data": "elo_clasico" },
                    { "data": "elo_rapido" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-danger btn-sm btnEditar_fide"
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
                                    class="btn btn-danger btn-sm btnEliminar_fide"
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
                        text: 'Agregar',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
                            $("#form_fides").trigger("reset");
                            $("#form_fides").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_fides").text("Agregar FIDE");
        
                            $('#select_titulo_fide').empty();
              
                            $('#select_titulo_fide').append(
                              '<option value="" selected disabled hidden>Seleccione un título...</option>'
                            );
        
                            $('#select_titulo_fide').append(
                                '<option value="" disabled>(No hay registros para mostrar)</option>'
                            );
        
                            //CARGA DE DATOS EN EL SELECT FEDERACIONES
                            $.ajax({
                                url: 'Http/Controlllers/FideController.php',
                                type: 'POST',
                                data: { opcion: 2 },
                                dataType: 'json',
                                success: function(response) {
        
                                  $('#select_federacion_fide').empty();
                            
                                  $('#select_federacion_fide').append(
                                    '<option value="" selected disabled hidden>Seleccione una federación...</option>'
                                  );
                            
                                  response.forEach(function(federacion) {
                                    $('#select_federacion_fide').append(
                                      `<option value="${federacion.acronimo}">${federacion.federacion}</option>`
                                    );
                                  });
                                },
                                error: function(xhr, status, error) {
                                  console.error('Error en la solicitud AJAX:', error);
                                }
                            });
        
                            //CARGA DE DATOS EN EL SELECT AJEDRECISTA
                            $.ajax({
                                url: 'Http/Controlllers/FideController.php',
                                type: 'POST',
                                data: { opcion: 3 },
                                dataType: 'json',
                                success: function(response) {
        
                                    $('#select_identificacion_fide').empty();
        
                                    if (response.length === 0) {
                                        $('#select_identificacion_fide').append(
                                            '<option value="" selected disabled hidden>Seleccione un ajedrecista...</option>'
                                        );
        
                                        $('#select_identificacion_fide').append(
                                            '<option value="" disabled>(No hay registros que mostrar)</option>'
                                        );
                                    } else {
                                        $('#select_identificacion_fide').append(
                                            '<option value="" selected disabled hidden>Seleccione un ajedrecista...</option>'
                                        );
        
                                        response.forEach(function(miembro) {
                                            $('#select_identificacion_fide').append(
                                                `<option value="${miembro.cedula}" data-sexo="${miembro.sexo}">${miembro.miembro}</option>`
                                            );
                                        });
                                    }
        
        
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error en la solicitud AJAX:', error);
                                }
                            });
        
                            opcion = 4;
        
                            $('#modal_fides').modal('show');
                        }
                    },
                    {
                        text: 'Importar FIDES',
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
                                        url: 'Http/Controlllers/ImportarFidesController.php',
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

                                            window.tabla_fide.ajax.reload();
                                            
                                            $.ajax({
                                                //url: 'http://192.168.100.100:3001/refresh/fide', 
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
                                            alert('Error al cargar el archivo.');
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
                        title: 'Lista de FIDES',
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

    var prev_fide_id;
    var prev_identificacion;
    var prev_federacion;
    var prev_titulo;
    var prev_blitz;
    var prev_clasico;
    var prev_rapido;

    /* ******************************* BOTÓN EDITAR PARA: FIDES ******************************* */
    $(document).on("click", ".btnEditar_fide", function(){
        $("#form_fides").trigger("reset");
        $("#form_fides").removeClass("was-validated");
        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_fide.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.fide_id;

        $('#input_id_fide').val(datosFila.fide_id);
        $('#input_blitz_fide').val(datosFila.elo_blitz);
        $('#input_clasico_fide').val(datosFila.elo_clasico);
        $('#input_rapido_fide').val(datosFila.elo_rapido);

        prev_fide_id = $.trim($("#input_id_fide").val());
        prev_blitz = $.trim($("#input_blitz_fide").val());
        prev_clasico = $.trim($("#input_clasico_fide").val());
        prev_rapido = $.trim($("#input_rapido_fide").val());
        
        const select_ident = document.getElementById('select_identificacion_fide');
        const select_fed = document.getElementById('select_federacion_fide');
        const select_titl = document.getElementById('select_titulo_fide');

        $('#select_identificacion_fide').empty();
        $('#select_federacion_fide').empty();
        $('#select_titulo_fide').empty();
        
        //CARGA DE DATOS EN EL SELECT FEDERACIONES
        $.ajax({
            url: 'Http/Controlllers/FideController.php',
            type: 'POST',
            data: { opcion: 2 },
            dataType: 'json',
            success: function(response) {

                $('#select_federacion_fide').empty();
                    
                $('#select_federacion_fide').append(
                '<option value="" selected disabled hidden>Seleccione una federación...</option>'
                );
                    
                response.forEach(function(federacion) {
                $('#select_federacion_fide').append(
                    `<option value="${federacion.acronimo}">${federacion.federacion}</option>`
                );
                });

                const select = document.getElementById('select_federacion_fide');
            
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === datosFila.fed_id) {
                        select.selectedIndex = i;
                        break;
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });

        //CARGA DE DATOS EN EL SELECT AJEDRECISTA
        $.ajax({
            url: 'Http/Controlllers/FideController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 6, search: search },
            success: function(response) {

                const fideData = response[0];

                //CARGA DE DATOS EN EL SELECT AJEDRECISTA
                $.ajax({
                    url: 'Http/Controlllers/FideController.php',
                    type: 'POST',
                    data: { opcion: 3 },
                    dataType: 'json',
                    success: function(response) {

                        if (response.length === 0) {

                            $('#select_identificacion_fide').append(
                                `<option value="${fideData.cedula}" data-sexo="${fideData.sexo}">${fideData.miembro}</option>`
                            );

                            $('#select_identificacion_fide').append(
                                '<option value="" disabled>(No hay registros que mostrar)</option>'
                            );

                            cargarTitulos(fideData.sexo);
                            $('#select_titulo_fide').val(fideData.titulo);
                        } else {
                            $('#select_identificacion_fide').append(
                                `<option value="${fideData.cedula}" data-sexo="${fideData.sexo}">${fideData.miembro}</option>`
                            );

                            response.forEach(function(miembro) {
                                $('#select_identificacion_fide').append(
                                    `<option value="${miembro.cedula}" data-sexo="${miembro.sexo}">${miembro.miembro}</option>`
                                );
                            });

                            cargarTitulos(fideData.sexo);
                            $('#select_titulo_fide').val(fideData.titulo);
                        }

                        prev_identificacion = $.trim($('#select_identificacion_fide option:selected').text());
                        prev_titulo = $.trim($('#select_titulo_fide option:selected').text());
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la solicitud AJAX:', error);
                    }
                });
                    
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
                console.log('Respuesta del servidor:', xhr.responseText);
            }
        });

        opcion = 7;
        $('#modal_fides').modal('show');
    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN EDITAR PARA: FIDES ******************************* */
    $(document).on("click", ".btnEliminar_fide", function(){
        
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_fide.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.fide_id;

        var fide_id = datosFila.fide_id;;
        var identificacion_text = datosFila.nombre;
        var federacion_text = datosFila.fed_id;
        var titulo_text = datosFila.titulo;
        var elo_blitz = datosFila.elo_blitz;
        var elo_clasico = datosFila.elo_clasico;
        var elo_rapido = datosFila.elo_rapido;

        Swal.fire({
            title: `¿Desea eliminar el FIDE con ID <b>${fide_id}</b> del ajedrecista <b>${identificacion_text}</b>?`,
            text: "Una vez eliminado, no podrá recuperar el registro.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'Http/Controlllers/FideController.php',
                    type: 'POST',
                    data: {
                        opcion: 9,
                        search: search
                    },
                    dataType: 'json',
                    success: function(response) {
        
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'El FIDE del miembro <strong>' + identificacion_text +'</strong> ha sido eliminado con éxito.'
                        });
    
                        window.tabla_fide.ajax.reload();
    
                        $.ajax({
                            url: 'Http/Controlllers/FideController.php',
                            type: 'POST',
                            data: {
                                fide_id: fide_id,
                                identificacion_text: identificacion_text,
                                federacion_text: federacion_text,
                                titulo_text: titulo_text,
                                elo_blitz: elo_blitz,
                                elo_clasico: elo_clasico,
                                elo_rapido: elo_rapido,
                                opcion: 10,
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

                        $.ajax({
                         //   url: 'http://192.168.100.100:3001/refresh/fide', 
                            type: 'GET',
                            success: function() {
                                //console.log('Evento de actualización enviado para academias');
                            },
                            error: function(xhr, status, error) {
                                //console.log('Error al emitir evento: ' + error);
                            }
                        });
        
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si el FIDE ya existe.'
                        });
                    }
                });
            }
        });
        

    })
    /* ****************************************************************************************** */


    /* ********************* ENVÍO DE FORMULARIO: FIDE ********************* */
    $("#form_fides").submit(function(e) {
        e.preventDefault();
    
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var fide_id = $.trim($("#input_id_fide").val());

        var identificacion_val = $.trim($("#select_identificacion_fide").val());
        var identificacion_text = $.trim($('#select_identificacion_fide option:selected').text());

        var federacion_val = $.trim($("#select_federacion_fide").val());
        var federacion_text = $.trim($('#select_federacion_fide option:selected').text());

        var titulo_val = $.trim($("#select_titulo_fide").val());
        var titulo_text = $.trim($('#select_titulo_fide option:selected').text());

        var elo_blitz = $.trim($("#input_blitz_fide").val());
        var elo_clasico = $.trim($("#input_clasico_fide").val());
        var elo_rapido = $.trim($("#input_rapido_fide").val());

        $.ajax({
            url: 'Http/Controlllers/FideController.php',
            type: 'POST',
            data: {
                fide_id: fide_id,
                identificacion_val: identificacion_val,
                federacion_val: federacion_val,
                titulo_val: titulo_val,
                elo_blitz: elo_blitz,
                elo_clasico: elo_clasico,
                elo_rapido: elo_rapido,
                opcion: opcion,
                search: search
            },
            dataType: 'json',
            success: function(response) {

                if(opcion == 4)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El FIDE del miembro <strong>' + identificacion_text +'</strong> ha sido agregado con éxito.'
                    });

                    window.tabla_fide.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/FideController.php',
                        type: 'POST',
                        data: {
                            fide_id: fide_id,
                            identificacion_text: identificacion_text,
                            federacion_text: federacion_text,
                            titulo_text: titulo_text,
                            elo_blitz: elo_blitz,
                            elo_clasico: elo_clasico,
                            elo_rapido: elo_rapido,
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

                    $("#modal_fides").modal("hide");
                }
                else if(opcion == 7)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El FIDE del miembto <strong>' + identificacion_text + '</strong> ha sido editado con éxito.'
                    });
                    
                    window.tabla_fide.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/FideController.php',
                        type: 'POST',
                        data: {
                            fide_id: fide_id,
                            identificacion_text: identificacion_text,
                            federacion_text: federacion_text,
                            titulo_text: titulo_text,
                            elo_blitz: elo_blitz,
                            elo_clasico: elo_clasico,
                            elo_rapido: elo_rapido,
                            opcion: 8,
                            mail_log: mail_log,
                            prev_fide_id: prev_fide_id,
                            prev_identificacion: prev_identificacion,
                            prev_federacion: prev_federacion,
                            prev_titulo: prev_titulo,
                            prev_blitz: prev_blitz,
                            prev_clasico: prev_clasico,
                            prev_rapido: prev_rapido
                        },
                        dataType: 'json',
                        success: function(response) {
                            window.tabla_historial.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Error en auditoría
                        }
                    });
                    $("#modal_fides").modal("hide");
                }

                $.ajax({
                  // url: 'http://192.168.100.100:3001/refresh/fide', 
                    type: 'GET',
                    success: function() {
                        //console.log('Evento de actualización enviado para academias');
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error al emitir evento: ' + error);
                    }
                });

            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si el FIDE ya existe.'
                });
            }
        });
    
    });
    /* ************************************************************************ */

    /* ******************************* CARGA DINÁMICA EN EL SELECT: TÍTULOS ******************************* */
    $('#select_identificacion_fide').on('change', function() {
        const cedula = $(this).val();
        const sexo = $(this).find(':selected').data('sexo');
    
        $('#select_titulo_fide').prop('disabled', false);
    
        cargarTitulos(sexo);
    });

    function cargarTitulos(sexo) {
        const titulos = {
          M: [
            { value: 'GM', texto: 'Gran Maestro (GM)' },
            { value: 'IM', texto: 'Maestro Internacional (IM)' },
            { value: 'FM', texto: 'Maestro FIDE (FM)' },
            { value: 'CM', texto: 'Candidato a Maestro (CM)' }
          ],
          F: [
            { value: 'WGM', texto: 'Gran Maestra Femenina (WGM)' },
            { value: 'WIM', texto: 'Maestra Internacional Femenina (WIM)' },
            { value: 'WFM', texto: 'Maestra FIDE Femenina (WFM)' },
            { value: 'WCM', texto: 'Candidata a Maestra Femenina (WCM)' }
          ]
        };
      
        $('#select_titulo_fide').empty();
      
        $('#select_titulo_fide').append(
          '<option value="" selected disabled hidden>Seleccione un título...</option>'
        );
      
        titulos[sexo].forEach(function(titulo) {
          $('#select_titulo_fide').append(
            `<option value="${titulo.value}">${titulo.texto}</option>`
          );
        });
    }
    /* **************************************************************************************************** */
});