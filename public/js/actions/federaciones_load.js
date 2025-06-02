$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
     var opcion = 1;
     var search;
     var load_table = 0;
    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
     var prev_acronimo;
     var prev_federacion;
     var prev_estado;
     var prev_pais;

     var mail_log = userData.correo;
    /* ********************************** */

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: FEDERACIONES ********************* */
    $('#federaciones_opcion_movil, #federaciones_opcion').click(function() {
        
        load_table++;

        if(load_table === 1)
        {
            window.tabla_federaciones = $('#tabla-federeaciones').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/FederacionController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    {"data": "acronimo"},
                    { "data": "nombre_federacion" },
                    { "data": "nombre_pais" },
                    { "data": "estado" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-light btnEditar_federaciones"
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
                                    class="btn btn-danger btn-sm btnEliminar_federacion"
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
                            $("#form_federaciones").trigger("reset");
                            $("#form_federaciones").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_federaciones").text("Agregar federación");
        
                            opcion = 4;
        
                            document.getElementById('switch_estado_federacion').checked = true;
                            cambiarColorSwitch(document.getElementById('switch_estado_federacion'));
                            const label = document.getElementById("switchLabel_federaciones");
                            label.textContent = "Activo";
        
                            $('#modal_federaciones').modal('show');
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        title: 'Lista de federaciones',
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

    /* ********************* CARGA DEL INPUT SELECT: PAISES ********************* */
    $.ajax({
        url: 'Http/Controlllers/FederacionController.php',
        type: 'POST',
        dataType: 'json',
        data: { opcion: 2 },
        success: function(response) {
            if (response && Array.isArray(response)) {
                $('#select_pais_federaciones').empty();

                $('#select_pais_federaciones').append(
                    $('<option>', {
                        value: '',
                        text: 'Seleccione un país...',
                        disabled: true,
                        selected: true
                    })
                );
    
                response.forEach(function(pais) {
    
                    $('#select_pais_federaciones').append(
                        $('<option>', {
                            value: pais.id_pais,
                            text: pais.nombre_pais
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

    /* ******************************* BOTÓN EDITAR PARA: FEDERACIONES ******************************* */
    $(document).on("click", ".btnEditar_federaciones", function(){
        $("#form_federaciones").trigger("reset");
        $("#form_federaciones").removeClass("was-validated");
    
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_federaciones.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.acronimo;

        $('#input_acronimo_federaciones').val(datosFila.acronimo);
        $('#input_federacion').val(datosFila.nombre_federacion);

        const select = document.getElementById('select_pais_federaciones');

        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].text === datosFila.nombre_pais) {
                select.selectedIndex = i;
                break;
            }
        }

        const switchElement = $('#switch_estado_federacion')[0];
        const label = $('#switchLabel_federaciones');
            
        if (datosFila.estado === 'Activo') {
            switchElement.checked = true;
            switchElement.style.backgroundColor = '#28a745';
            switchElement.style.borderColor = '#28a745';
            label.text("Activo");

            prev_estado = 'Activo';
        } else if (datosFila.estado === 'Inactivo') {
            switchElement.checked = false;
            switchElement.style.backgroundColor = '#dc3545';
            switchElement.style.borderColor = '#dc3545';
            label.text("Inactivo");

            prev_estado = 'Inactivo';
        } else {
            switchElement.checked = false;
            switchElement.style.backgroundColor = '#6c757d';
            switchElement.style.borderColor = '#6c757d';
            label.text("Indefinido");
        }

        prev_acronimo = datosFila.acronimo;
        prev_federacion = datosFila.nombre_federacion;
        prev_pais = $.trim($('#select_pais_federaciones option:selected').text());
    
        opcion = 6;
    
        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_federaciones").text("Editar federación");
        $("#modal_federaciones").modal("show");
    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN ELIMINAR PARA: FEDERACIONES ******************************* */
    $(document).on("click", ".btnEliminar_federacion", function(){
    
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_federaciones.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.acronimo;

        Swal.fire({
            title: "Advertencia",
            html: '¿Desea eliminar la federación con acrónimo <strong>' + search + '</strong>? Una vez eliminada, no podrá recuperarla',
            showDenyButton: true,
            icon: "warning",
            confirmButtonText: "Continuar",
            denyButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: 'Http/Controlllers/FederacionController.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { opcion: 8, search: search },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'La federación con acrónimo <strong>' + search + '</strong> ha sido eliminada con éxito.'
                        });

                        $.ajax({
                           // url: 'http://192.168.100.100:3001/refresh/federaciones', 
                            type: 'GET',
                            success: function() {
                                //console.log('Evento de actualización enviado para academias');
                            },
                            error: function(xhr, status, error) {
                                //console.log('Error al emitir evento: ' + error);
                            }
                        });

                        window.tabla_federaciones.ajax.reload();

                        $.ajax({
                            url: 'Http/Controlllers/FederacionController.php',
                            type: 'POST',
                            data: {
                                opcion: 9,
                                search: search,
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
    $("#form_federaciones").submit(function(e) {
        e.preventDefault();
    
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
    
        var acronimo = $.trim($("#input_acronimo_federaciones").val());
        var federacion = $.trim($('#input_federacion').val());
        var pais = $.trim($('#select_pais_federaciones').val());
        var estado = $('#switch_estado_federacion').prop('checked') ? 1 : 0;

        var pais_text = $.trim($('#select_pais_federaciones option:selected').text());

        if(estado == 0)
        {
            var estado_text = 'Inactivo';
        }
        else if(estado == 1)
        {
            var estado_text = 'Activo';
        }

        $.ajax({
            url: 'Http/Controlllers/FederacionController.php',
            type: 'POST',
            data: {
                acronimo: acronimo,
                federacion: federacion,
                pais: pais,
                estado: estado,
                opcion: opcion,
                search: search
            },
            dataType: 'json',
            success: function(response) {

                $.ajax({
                   // url: 'http://192.168.100.100:3001/refresh/federaciones', 
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
                        html: 'La federación con acrónimo <strong>' + response.acronimo + '</strong> ha sido ingresada con éxito.'
                    });

                    window.tabla_federaciones.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/FederacionController.php',
                        type: 'POST',
                        data: {
                            acronimo: acronimo,
                            federacion: federacion,
                            pais_text: pais_text,
                            estado_text: estado_text,
                            opcion: 5,
                            search: search,
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

                    $("#modal_federaciones").modal("hide");
                }
                else if(opcion == 6)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'La federación con acrónimo <strong>' + response.acronimo + '</strong> ha sido actualizada con éxito.'
                    });
                    
                    window.tabla_federaciones.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/FederacionController.php',
                        type: 'POST',
                        data: {
                            acronimo: acronimo,
                            federacion: federacion,
                            pais_text: pais_text,
                            estado_text: estado_text,
                            opcion: 7,
                            search: search,
                            mail_log: mail_log,
                            prev_acronimo: prev_acronimo,
                            prev_federacion: prev_estado,
                            prev_pais: prev_pais,
                            prev_estado: prev_estado
                        },
                        dataType: 'json',
                        success: function(response) {
                            window.tabla_historial.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Error en auditoría
                        }
                    });
                    $("#modal_federaciones").modal("hide");
                }

            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si la federación ya existe.'
                });
            }
        });

    });
    /* ************************************************************************ */

    /* ********************* NUEVO PAÍS ********************* */
    $('#nuevo_pais').click(function() {
        $.ajax({
            url: 'Http/Controlllers/FederacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 2 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_paises').empty();
        
                    response.forEach(function(pais) {
        
                        $('#select_paises').append(
                            $('<option>', {
                                value: pais.id_pais,
                                text: pais.nombre_pais
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

        $("#form_add_pais").trigger("reset");
        $("#form_add_pais").removeClass("was-validated");
        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_add_pais").text("Agregar país");

        opcion = 4;

        $('#modal_pais').modal('show');
    });
    /* ************************************************************************ */

    /* ********************* ENVÍO DE FORMULARIO: NUEVO PAÍS ********************* */
    $("#form_add_pais").submit(function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        
        var pais_add = $.trim($("#input_nuevo_pais").val());
    
        $.ajax({
            url: 'Http/Controlllers/FederacionController.php',
            type: 'POST',
            data: {
                pais_add: pais_add,
                opcion: 10
            },
            dataType: 'json',
            success: function(response) {

                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    html: 'El país ingresado ha sido ingresado con éxito'
                });

                $('#modal_pais').modal('hide');

                $.ajax({
                    url: 'Http/Controlllers/FederacionController.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { opcion: 2 },
                    success: function(response) {
                        if (response && Array.isArray(response)) {
                            $('#select_pais_federaciones').empty();
            
                            $('#select_pais_federaciones').append(
                                $('<option>', {
                                    value: '',
                                    text: 'Seleccione un país...',
                                    disabled: true,
                                    selected: true
                                })
                            );
                
                            response.forEach(function(pais) {
                
                                $('#select_pais_federaciones').append(
                                    $('<option>', {
                                        value: pais.id_pais,
                                        text: pais.nombre_pais
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
    
                $.ajax({
                    url: 'Http/Controlllers/FederacionController.php',
                    type: 'POST',
                    data: {
                        pais_add: pais_add,
                        opcion: 11,
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si el país ya existe.'
                });
            }
        });
    
    });
    /* ************************************************************************ */
});