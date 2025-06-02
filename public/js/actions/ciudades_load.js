$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
     var opcion = 1;
     var search;
     var load_table = 0;
    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
     var prev_ciudad;
     var prev_departamento;
     var prev_pais;

     var mail_log = userData.correo;
    /* ********************************** */

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: CIUDADES ********************* */
    $('#ciudades_opcion, #ciudades_opcion_movil').click(function() {

        load_table++;

        if(load_table === 1)
        {
            window.tabla_ciudades = $('#tabla-ciudades').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/CiudadController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    {"data": "nombre_pais"},
                    { "data": "nombre_depto" },
                    { "data": "nombre_ciudad" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-light btnEditar_ciudades"
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
                                    class="btn btn-danger btn-sm btnEliminar_ciudades"
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
                        text: 'Agregar ciudad',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
        
                            $.ajax({
                                url: 'Http/Controlllers/FederacionController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_paises_ciudades').empty();
        
                                        $('#select_paises_ciudades').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un país...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                            
                                        response.forEach(function(pais) {
                            
                                            $('#select_paises_ciudades').append(
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
                    
                            $("#form_add_ciudades").trigger("reset");
                            $("#form_add_ciudades").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_add_ciudades").text("Agregar ciudad");
                    
                            opcion = 8;
                    
                            $('#modal_ciudades').modal('show');
        
                        }
                    },
                    {
                        text: 'Agregar departamento',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
                            opcion = 6;
                            $.ajax({
                                url: 'Http/Controlllers/FederacionController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_paises_depto').empty();
        
                                        $('#select_paises_depto').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un país...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                            
                                        response.forEach(function(pais) {
                            
                                            $('#select_paises_depto').append(
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
                    
                            $("#form_add_depto").trigger("reset");
                            $("#form_add_depto").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_add_depto").text("Agregar departamento");
                    
                            $('#modal_depto').modal('show');
        
                        }
                    },
                    {
                        text: 'Agregar pais',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {
                            $.ajax({
                                url: 'Http/Controlllers/FederacionController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_paises_opcciudades').empty();
        
                                        $('#select_paises_opcciudades').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Paises registrados...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                            
                                        response.forEach(function(pais) {
                            
                                            $('#select_paises_opcciudades').append(
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
                    
                            $("#form_add_pais_ciudades").trigger("reset");
                            $("#form_add_pais_ciudades").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_add_pais").text("Agregar país");
                    
                            opcion = 4;
                    
                            $('#modal_pais_ciudadessopc').modal('show');
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        title: 'Lista de ciudades',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        exportOptions: {
                            columns: ':not(:last-child)'  // Excluir la última columna
                        }
                    }
                ],
                "pageLength": 15
            }).columns.adjust().responsive.recalc();
        }

    });

    /* *************************************************************************************** */

    /* ********************* MODAL NUEVO PAÍS (desde el form de DEPARTAMENTO) ********************* */
    $('#nuevo_pais_depto').click(function() {
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

    /* ********************* MODAL NUEVO PAIS (desde el form de CIUDAD) ********************* */
    $('#nuevo_pais_ciudad').click(function() {
        $.ajax({
            url: 'Http/Controlllers/FederacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 2 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_paises').empty();

                    $('#select_paises').append(
                        $('<option>', {
                            value: '',
                            text: 'Seleccione un país...',
                            disabled: true,
                            selected: true
                        })
                    );
        
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

    /* ********************* MODAL NUEVO DEPARTAMENTO (desde el form de CIUDAD) ********************* */
    $('#nuevo_depto_ciudad').click(function() {
        $.ajax({
            url: 'Http/Controlllers/FederacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 2 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_paises_depto').empty();

                    $('#select_paises_depto').append(
                        $('<option>', {
                            value: '',
                            text: 'Seleccione un país...',
                            disabled: true,
                            selected: true
                        })
                    );
        
                    response.forEach(function(pais) {
        
                        $('#select_paises_depto').append(
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

        $("#form_add_depto").trigger("reset");
        $("#form_add_depto").removeClass("was-validated");
        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_add_depto").text("Agregar departamento");

        opcion = 6;

        $('#modal_depto').modal('show');
    });
    /* ************************************************************************ */

    /* ********************* ENVÍO DE FORMULARIO: NUEVO PAÍS ********************* */
    $("#form_add_pais_ciudades").submit(function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        
        var pais_add = $.trim($("#input_nuevo_pais_opcciudades").val());
    
        $.ajax({
            url: 'Http/Controlllers/CiudadController.php',
            type: 'POST',
            data: {
                pais_add: pais_add,
                opcion: opcion,
                search: search
            },
            dataType: 'json',
            success: function(response) {

                $.ajax({
                   // url: 'http://192.168.100.100:3001/refresh/ciudades', 
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
                        html: 'El país ha sido agregado con éxito'
                    });
    
                    $('#modal_pais_ciudadessopc').modal('hide');
                    window.tabla_ciudades.ajax.reload();
        
                    $.ajax({
                        url: 'Http/Controlllers/CiudadController.php',
                        type: 'POST',
                        data: {
                            pais_add: pais_add,
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

                }
                else if(opcion == 11)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El país ha sido actualizado con éxito'
                    });
    
                    $('#modal_pais_ciudadessopc').modal('hide');
                    window.tabla_ciudades.ajax.reload();
        
                    $.ajax({
                        url: 'Http/Controlllers/CiudadController.php',
                        type: 'POST',
                        data: {
                            pais_add: pais_add,
                            opcion: 10,
                            mail_log: mail_log,
                            prev_pais: prev_pais
                        },
                        dataType: 'json',
                        success: function(response) {
                            window.tabla_historial.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Error en auditoría
                        }
                    });
                }
    
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

    /* ********************* ENVÍO DE FORMULARIO: NUEVO DEPARTAMENTO ********************* */
    $("#form_add_depto").submit(function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        
        var pais_num = document.getElementById("select_paises_depto").value;
        var pais_add = document.getElementById("select_paises_depto").options[document.getElementById("select_paises_depto").selectedIndex].text;
        var depto = $.trim($("#input_nuevo_depto").val());

        $.ajax({
            url: 'Http/Controlllers/CiudadController.php',
            type: 'POST',
            data: {
                pais_num: pais_num,
                depto: depto,
                opcion: opcion,
                prev_pais: prev_pais,
                prev_departamento: prev_departamento
            },
            dataType: 'json',
            success: function(response) {

                $.ajax({
                  //  url: 'http://192.168.100.100:3001/refresh/ciudades', 
                    type: 'GET',
                    success: function() {
                        //console.log('Evento de actualización enviado para academias');
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error al emitir evento: ' + error);
                    }
                });

                if(opcion == 6)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El departamento ha sido ingresado con éxito'
                    });
    
                    $('#modal_depto').modal('hide');
                    window.tabla_ciudades.ajax.reload();
        
                    $.ajax({
                        url: 'Http/Controlllers/CiudadController.php',
                        type: 'POST',
                        data: {
                            pais_add: pais_add,
                            depto: depto,
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
                }
                else if(opcion == 12)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El departamento ha sido actualizado con éxito'
                    });
    
                    $('#modal_depto').modal('hide');
                    window.tabla_ciudades.ajax.reload();
        
                    $.ajax({
                        url: 'Http/Controlllers/CiudadController.php',
                        type: 'POST',
                        data: {
                            pais_num: pais_num,
                            pais_add: pais_add,
                            prev_departamento: prev_departamento,
                            prev_pais: prev_pais,
                            depto: depto,
                            opcion: 13,
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
                }

    
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si el departamento ya existe.'
                });
            }
        });
    
    });
    /* ************************************************************************ */

    /* ********************* ENVÍO DE FORMULARIO: NUEVA CIUDAD ********************* */
    $("#form_add_ciudades").submit(function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        
        var pais_num = document.getElementById("select_paises_ciudades").value;
        var depto_num = document.getElementById("select_departamento_ciudades").value;

        var pais_add = document.getElementById("select_paises_ciudades").options[document.getElementById("select_paises_ciudades").selectedIndex].text;
        var depto = document.getElementById("select_departamento_ciudades").options[document.getElementById("select_departamento_ciudades").selectedIndex].text;

        var ciudad = $.trim($("#input_nueva_ciudad").val());
        
        $.ajax({
            url: 'Http/Controlllers/CiudadController.php',
            type: 'POST',
            data: {
                pais_num: pais_num,
                depto_num: depto_num,
                ciudad: ciudad,
                opcion: opcion,
                prev_pais: prev_pais,
                prev_departamento: prev_departamento,
                prev_ciudad: prev_ciudad
            },
            dataType: 'json',
            success: function(response) {

                $.ajax({
                   // url: 'http://192.168.100.100:3001/refresh/ciudades', 
                    type: 'GET',
                    success: function() {
                        //console.log('Evento de actualización enviado para academias');
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error al emitir evento: ' + error);
                    }
                });

                if(opcion == 8)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'La ciudad ha sido ingresado con éxito'
                    });
    
                    $('#modal_ciudades').modal('hide');
                    window.tabla_ciudades.ajax.reload();
        
                    $.ajax({
                        url: 'Http/Controlllers/CiudadController.php',
                        type: 'POST',
                        data: {
                            pais_add: pais_add,
                            depto: depto,
                            ciudad: ciudad,
                            opcion: 9,
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
                }
                else if(opcion == 14)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'La ciudad ha sido actualizada con éxito'
                    });
    
                    $('#modal_ciudades').modal('hide');
                    window.tabla_ciudades.ajax.reload();
        
                    $.ajax({
                        url: 'Http/Controlllers/CiudadController.php',
                        type: 'POST',
                        data: {
                            pais_add: pais_add,
                            depto: depto,
                            ciudad: ciudad,
                            opcion: 15,
                            mail_log: mail_log,
                            prev_pais: prev_pais,
                            prev_departamento: prev_departamento,
                            prev_ciudad: prev_ciudad
                        },
                        dataType: 'json',
                        success: function(response) {
                            window.tabla_historial.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Error en auditoría
                        }
                    });
                }
    
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si la ciudad ya existe.'
                });
            }
        });
    
    });
    /* ************************************************************************ */

    /* ********************* CARGA DINAMICA DE DEPTOS AL SELECCIONAR UN PAIS EN EL MODAL: CIUDADES ********************* */
    $('#select_paises_ciudades').change(function() {
        var paisId = $(this).val();

        if (paisId) {
            $.ajax({
                url: 'Http/Controlllers/CiudadController.php',
                type: 'POST',
                dataType: 'json',
                data: { opcion: 3, search: paisId },
                success: function(response) {

                    if (response && Array.isArray(response)) {
                        $('#select_departamento_ciudades').empty();

                        $('#select_departamento_ciudades').append(
                            $('<option>', {
                                value: '',
                                text: 'Seleccione un departamento...',
                                disabled: true,
                                selected: true
                            })
                        );

                        response.forEach(function(departamento) {
                            $('#select_departamento_ciudades').append(
                                $('<option>', {
                                    value: departamento.id_depto,
                                    text: departamento.nombre_depto
                                })
                            );
                        });
                    } else {
                        console.error('No se recibieron datos válidos para los departamentos');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la llamada AJAX: ' + error);
                }
            });
        } else {
            $('#select_departamento_ciudades').empty();
            $('#select_departamento_ciudades').append(
                $('<option>', {
                    value: '',
                    text: 'Seleccione un departamento...',
                    disabled: true,
                    selected: true
                }),
                $('<option>', {
                    value: '',
                    text: '(No hay departamentos. Seleccione un país para cargar los departamentos)',
                    disabled: false,
                    selected: false
                })
            );
        }
    });
    /* **************************************************************************************************************** */

    /* ******************************* BOTÓN EDITAR PARA: CIUDADES ******************************* */
    $(document).on("click", ".btnEditar_ciudades", function(){
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_ciudades.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.nombre_pais;

        pais_add = datosFila.nombre_pais;
        depto = datosFila.nombre_depto;
        ciudad = datosFila.nombre_ciudad;
        
        prev_pais= datosFila.nombre_pais;
        prev_departamento = datosFila.nombre_depto;
        prev_ciudad = datosFila.nombre_ciudad;
        
        pais_add = pais_add === '-' ? null : pais_add;
        depto = depto === '-' ? null : depto;
        ciudad = ciudad === '-' ? null : ciudad;

        Swal.fire({
            title: '¿Qué deseas editar?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Editar Ciudad',
            denyButtonText: 'Editar Departamento',
            cancelButtonText: 'Editar País',
            icon: "question",
            customClass: {
              confirmButton: 'btn-editar',
              denyButton: 'btn-editar',
              cancelButton: 'btn-editar'
            },
            didOpen: () => {
              const confirmBtn = document.querySelector('.swal2-confirm');
              const denyBtn = document.querySelector('.swal2-deny');
              const cancelBtn = document.querySelector('.swal2-cancel');
          
              confirmBtn.style.backgroundColor = '#1e2936';
              denyBtn.style.backgroundColor = '#1e2936';
              cancelBtn.style.backgroundColor = '#1e2936';
            },
            showCloseButton: true,
            closeButtonHtml: '&times;',
            closeButtonAriaLabel: 'Cerrar',
          }).then((result) => {

            if (result.dismiss !== Swal.DismissReason.close) {
                
                if (result.dismiss === Swal.DismissReason.backdrop) {
                    return;
                }

                //CIUDADES
                if (result.isConfirmed) 
                {
                    $.ajax({
                        url: 'Http/Controlllers/FederacionController.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { opcion: 2 },
                        success: function(response) {
                            if (response && Array.isArray(response)) {
                                $('#select_paises_ciudades').empty();

                                $('#select_paises_ciudades').append(
                                    $('<option>', {
                                        value: '',
                                        text: 'Seleccione un país...',
                                        disabled: true,
                                        selected: true
                                    })
                                );
                    
                                response.forEach(function(pais) {
                    
                                    $('#select_paises_ciudades').append(
                                        $('<option>', {
                                            value: pais.id_pais,
                                            text: pais.nombre_pais
                                        })
                                    );
                                });

                                var paisId = $('#select_paises_ciudades option').filter(function() {
                                    return $(this).text() === pais_add;
                                }).prop('selected', true).val();

                                $.ajax({
                                    url: 'Http/Controlllers/CiudadController.php',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: { opcion: 3, search: paisId },
                                    success: function(response) {
                    
                                        if (response && Array.isArray(response)) {
                                            $('#select_departamento_ciudades').empty();
                    
                                            $('#select_departamento_ciudades').append(
                                                $('<option>', {
                                                    value: '',
                                                    text: 'Seleccione un departamento...',
                                                    disabled: true,
                                                    selected: true
                                                })
                                            );
                    
                                            response.forEach(function(departamento) {
                                                $('#select_departamento_ciudades').append(
                                                    $('<option>', {
                                                        value: departamento.id_depto,
                                                        text: departamento.nombre_depto
                                                    })
                                                );
                                            });

                                            $('#select_departamento_ciudades option').filter(function() {
                                                return $(this).text() === depto;
                                            }).prop('selected', true);
                                        } else {
                                            console.error('No se recibieron datos válidos para los departamentos');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error en la llamada AJAX: ' + error);
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
            
                    $("#form_add_ciudades").trigger("reset");
                    $("#form_add_ciudades").removeClass("was-validated");
                    $(".modal-header").css("background-color", "#1e2936");
                    $(".modal-header").css("color", "#ffffff");
                    $("#title_add_ciudades").text("Editar ciudad");
            
                    opcion = 14;
            
                    $('#modal_ciudades').modal('show');
                    $('#input_nueva_ciudad').val(ciudad);
                } 

                //DEPARTAMENTO
                else if (result.isDenied) 
                {
                    opcion = 12;
                    $.ajax({
                        url: 'Http/Controlllers/FederacionController.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { opcion: 2 },
                        success: function(response) {
                            if (response && Array.isArray(response)) {
                                $('#select_paises_depto').empty();

                                $('#select_paises_depto').append(
                                    $('<option>', {
                                        value: '',
                                        text: 'Seleccione un país...',
                                        disabled: true,
                                        selected: true
                                    })
                                );
                    
                                response.forEach(function(pais) {
                    
                                    $('#select_paises_depto').append(
                                        $('<option>', {
                                            value: pais.id_pais,
                                            text: pais.nombre_pais
                                        })
                                    );
                                });

                                $('#select_paises_depto option').filter(function() {
                                    return $(this).text() === pais_add;
                                }).prop('selected', true);
                            } else {
                                console.error('No se recibieron datos válidos');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error en la llamada AJAX: ' + error);
                        }
                    });
            
                    $("#form_add_depto").trigger("reset");
                    $("#form_add_depto").removeClass("was-validated");
                    $(".modal-header").css("background-color", "#1e2936");
                    $(".modal-header").css("color", "#ffffff");
                    $("#title_add_depto").text("Editar departamento");
            
                    $('#modal_depto').modal('show');

                    $('#input_nuevo_depto').val(depto);
                } 

                //PAIS
                else if (result.isDismissed) 
                {
                    $("#form_add_pais_ciudades").trigger("reset");
                    $("#form_add_pais_ciudades").removeClass("was-validated");
                    $(".modal-header").css("background-color", "#1e2936");
                    $(".modal-header").css("color", "#ffffff");
                    $("#title_add_pais").text("Editar país");
            
                    opcion = 11;

                    $.ajax({
                        url: 'Http/Controlllers/FederacionController.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { opcion: 2 },
                        success: function(response) {
                            if (response && Array.isArray(response)) {
                                $('#select_paises_opcciudades').empty();

                                $('#select_paises_opcciudades').append(
                                    $('<option>', {
                                        value: '',
                                        text: 'Paises registrados...',
                                        disabled: true,
                                        selected: true
                                    })
                                );
                    
                                response.forEach(function(pais) {
                    
                                    $('#select_paises_opcciudades').append(
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
            
                    $('#modal_pais_ciudadessopc').modal('show');

                    $('#input_nuevo_pais_opcciudades').val(search);
                }

            }
          });
          
    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN ELIMINAR PARA: CIUDADES ******************************* */
    $(document).on("click", ".btnEliminar_ciudades", function(){
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_ciudades.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.nombre_pais;

        pais_add = datosFila.nombre_pais;
        depto = datosFila.nombre_depto;
        ciudad = datosFila.nombre_ciudad;
        
        pais_add = pais_add === '-' ? null : pais_add;
        depto = depto === '-' ? null : depto;
        ciudad = ciudad === '-' ? null : ciudad;

        Swal.fire({
            title: '¿Qué desea eliminar?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Eliminar Ciudad',
            denyButtonText: 'Eliminar Departamento',
            cancelButtonText: 'Eliminar País',
            icon: "question",
            customClass: {
              confirmButton: 'btn-editar',
              denyButton: 'btn-editar',
              cancelButton: 'btn-editar'
            },
            didOpen: () => {
              const confirmBtn = document.querySelector('.swal2-confirm');
              const denyBtn = document.querySelector('.swal2-deny');
              const cancelBtn = document.querySelector('.swal2-cancel');
          
              confirmBtn.style.backgroundColor = '#1e2936';
              denyBtn.style.backgroundColor = '#1e2936';
              cancelBtn.style.backgroundColor = '#1e2936';
            },
            showCloseButton: true,
            closeButtonHtml: '&times;',
            closeButtonAriaLabel: 'Cerrar',
          }).then((result) => {

            if (result.dismiss !== Swal.DismissReason.close) {
                
                if (result.dismiss === Swal.DismissReason.backdrop) {
                    return;
                }
                //CIUDAD
                if (result.isConfirmed) 
                {
                    Swal.fire({
                        title: '¿Desea eliminar esta ciudad?',
                        html: 'Está a punto de eliminar la ciudad: <strong>' + ciudad + '</strong>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'btn-eliminar',
                            cancelButton: 'btn-cancelar'
                        },
                        didOpen: () => {
                            const confirmBtn = document.querySelector('.swal2-confirm');
                            const cancelBtn = document.querySelector('.swal2-cancel');
                            confirmBtn.style.backgroundColor = '#ff4747';
                            cancelBtn.style.backgroundColor = '#1e2936';
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'Http/Controlllers/CiudadController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 20, pais_add: pais_add, depto: depto, ciudad: ciudad },
                                success: function(response) {
                                    $.ajax({
                                      //  url: 'http://192.168.100.100:3001/refresh/ciudades', 
                                        type: 'GET',
                                        success: function() {
                                            //console.log('Evento de actualización enviado para ciudades');
                                        },
                                        error: function(xhr, status, error) {
                                            //console.log('Error al emitir evento: ' + error);
                                        }
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        html: 'La ciudad ha sido eliminada con éxito'
                                    });
                                    window.tabla_ciudades.ajax.reload();

                                    $.ajax({
                                        url: 'Http/Controlllers/CiudadController.php',
                                        type: 'POST',
                                        data: {
                                            pais_add: pais_add,
                                            depto: depto, 
                                            ciudad: ciudad,
                                            opcion: 21,
                                            mail_log: mail_log,
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
                                        title: 'ERROR',
                                        html: 'Hubo un problema al eliminar el registro'
                                    });
                                    console.error('Error en la llamada AJAX: ' + error);
                                }
                            });
                        }
                    });   
                } 
                //DEPARTAMENTO
                else if (result.isDenied) 
                {
                    Swal.fire({
                        title: '¿Desea eliminar este departamento?',
                        html: 'Está a punto de eliminar el departamento: <strong>' + depto + '</strong>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'btn-eliminar',
                            cancelButton: 'btn-cancelar'
                        },
                        didOpen: () => {
                            const confirmBtn = document.querySelector('.swal2-confirm');
                            const cancelBtn = document.querySelector('.swal2-cancel');
                            confirmBtn.style.backgroundColor = '#ff4747';
                            cancelBtn.style.backgroundColor = '#1e2936';
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'Http/Controlllers/CiudadController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 18, pais_add: pais_add, depto: depto},
                                success: function(response) {
                                    $.ajax({
                                      //  url: 'http://192.168.100.100:3001/refresh/ciudades', 
                                        type: 'GET',
                                        success: function() {
                                            //console.log('Evento de actualización enviado para ciudades');
                                        },
                                        error: function(xhr, status, error) {
                                            //console.log('Error al emitir evento: ' + error);
                                        }
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        html: 'El departamento ha sido eliminado con éxito'
                                    });
                                    window.tabla_ciudades.ajax.reload();
                                    
                                    $.ajax({
                                        url: 'Http/Controlllers/CiudadController.php',
                                        type: 'POST',
                                        data: {
                                            pais_add: pais_add,
                                            depto: depto,
                                            opcion: 19,
                                            mail_log: mail_log,
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
                                        title: 'ERROR',
                                        html: 'Hubo un problema al eliminar el registro'
                                    });
                                    console.error('Error en la llamada AJAX: ' + error);
                                }
                            });
                        }
                    });
                } 
                //PAIS 
                else if (result.isDismissed)
                {
                    Swal.fire({
                        title: '¿Desea eliminar este país?',
                        html: 'Está a punto de eliminar el país: <strong>' + pais_add + '</strong>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'btn-eliminar',
                            cancelButton: 'btn-cancelar'
                        },
                        didOpen: () => {
                            const confirmBtn = document.querySelector('.swal2-confirm');
                            const cancelBtn = document.querySelector('.swal2-cancel');
                            confirmBtn.style.backgroundColor = '#ff4747';
                            cancelBtn.style.backgroundColor = '#1e2936';
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'Http/Controlllers/CiudadController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 16, pais_add: pais_add },
                                success: function(response) {
                                    $.ajax({
                                        //url: 'http://192.168.100.100:3001/refresh/ciudades', 
                                        type: 'GET',
                                        success: function() {
                                            //console.log('Evento de actualización enviado para ciudades');
                                        },
                                        error: function(xhr, status, error) {
                                            //console.log('Error al emitir evento: ' + error);
                                        }
                                    });
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        html: 'El país ha sido eliminado con éxito'
                                    });
                                    window.tabla_ciudades.ajax.reload();

                                    $.ajax({
                                        url: 'Http/Controlllers/CiudadController.php',
                                        type: 'POST',
                                        data: {
                                            pais_add: pais_add,
                                            opcion: 17,
                                            mail_log: mail_log,
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
                                        title: 'ERROR',
                                        html: 'Hubo un problema al eliminar el registro'
                                    });
                                    console.error('Error en la llamada AJAX: ' + error);
                                }
                            });
                        }
                    });         
                }
            }

          });
          
    })
    /* ****************************************************************************************** */
});