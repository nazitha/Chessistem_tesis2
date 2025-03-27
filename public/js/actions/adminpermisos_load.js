$(document).ready(function() {

    /* ***** VARIABLES PARA AUDITAR ***** */
    var load_table = 0;
    var mail_log = userData.correo;

    $('#usuarios_opcion, #usuarios_opcion_movil').click(function() {
        
        load_table++;

        if(load_table === 1)
        {
            window.tabla_asigpermis = $('#tabla-asigpermisos').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/UserController.php",
                    "method": "POST",
                    "data": { opcion: 9 },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "rol" },
                    { "data": "permisos" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-danger btn-sm btnEditar_adminpermis"
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
                                   Asignar
                                </button>
                                <button 
                                    class="btn btn-danger btn-sm btnEliminar_adminpermis"
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
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        title: 'Lista de permisos asignados a roles',
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

    /* ******************************* BOTÓN ELIMINAR PARA: ASIGNACIÓN DE PERMISOS ******************************* */
    $(document).on("click", ".btnEliminar_adminpermis", function(){
        $("#form_asigpermis").trigger("reset");
        $("#form_asigpermis").removeClass("was-validated");
        $('#submit_btn_asigpermis').text('Remover permiso');
    
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_asigpermis.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        rolpermis = datosFila.rol;
        permisotext = datosFila.permisos;

        $.ajax({
            url: 'Http/Controlllers/UserController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 10, search: rolpermis },
            success: function(response) {

                if (response && Array.isArray(response)) {
                    $('#select_rol_asigpermis').empty();
                    $('#select_permiso_asigpermis').empty();

                    $('#select_rol_asigpermis').append(
                        $('<option>', {
                            value: response[0].rol_id,
                            text: response[0].rol
                        })
                    );

                    response.forEach(function(permiso) {
                        $('#select_permiso_asigpermis').append(
                            $('<option>', {
                                value: permiso.permiso_id,
                                text: permiso.permiso
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
    
        opcion = 14;
    
        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_asigpermis").text("Remover permisos");
        $("#modal_asigpermis").modal("show");
    })
    /* ******************************************************************************************************* */

    /* ******************************* BOTÓN EDITAR PARA: ASIGNACIÓN DE PERMISOS ******************************* */
    $(document).on("click", ".btnEditar_adminpermis", function(){
        $("#form_asigpermis").trigger("reset");
        $("#form_asigpermis").removeClass("was-validated");
        $('#submit_btn_asigpermis').text('Asignar permiso');

        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_asigpermis.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        rolpermis = datosFila.rol;

        $.ajax({
            url: 'Http/Controlllers/UserController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 11, search: rolpermis },
            success: function(response) {

                if (response && Array.isArray(response)) {
                    $('#select_rol_asigpermis').empty();
                    $('#select_permiso_asigpermis').empty();

                    $('#select_rol_asigpermis').append(
                        $('<option>', {
                            value: response[0].rol_id,
                            text: response[0].rol
                        })
                    );

                    response.forEach(function(permiso) {
                        $('#select_permiso_asigpermis').append(
                            $('<option>', {
                                value: permiso.permiso_id,
                                text: permiso.permiso
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
    
        opcion = 12;
    
        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_asigpermis").text("Asignar permisos");
        $("#modal_asigpermis").modal("show");
    })
    /* ******************************************************************************************************* */
    
    /* ********************* ENVÍO DE FORMULARIO: NUEVA CIUDAD ********************* */
    $("#form_asigpermis").submit(function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        
        var rolvalue = document.getElementById("select_rol_asigpermis").value;
        var permisovalue = document.getElementById("select_permiso_asigpermis").value;

        var roltext = document.getElementById("select_rol_asigpermis").options[document.getElementById("select_rol_asigpermis").selectedIndex].text;
        var permisotext = document.getElementById("select_permiso_asigpermis").options[document.getElementById("select_permiso_asigpermis").selectedIndex].text;
    
        if(opcion == 12)
        {
            $.ajax({
                url: 'Http/Controlllers/UserController.php',
                type: 'POST',
                data: {
                    roltext: roltext,
                    permisotext: permisotext,
                    rolvalue: rolvalue,
                    permisovalue: permisovalue,
                    opcion: opcion
                },
                dataType: 'json',
                success: function(response) {
    
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El permiso ha sido asignado con éxito.'
                    });
                    
                    $.ajax({
                      //  url: 'http://192.168.100.100:3001/refresh/asigpermis',
                        type: 'GET',
                        success: function() {
                            console.log('Evento de actualización enviado para asigpermis');
                        },
                        error: function(xhr, status, error) {
                            console.log('Error al emitir evento: ' + error);
                        }
                    });
    
                    $('#modal_asigpermis').modal('hide');
                    window.tabla_asigpermis.ajax.reload();
        
                    $.ajax({
                        url: 'Http/Controlllers/UserController.php',
                        type: 'POST',
                        data: {
                            roltext: roltext,
                            permisotext: permisotext,
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
        else if (opcion == 14)
        {
            Swal.fire({
                title: `¿Desea remover el permiso <b>${permisotext}</b> del rol <b>${roltext}</b>?`,
                icon: 'warning',
                showDenyButton: true,
                confirmButtonText: "Remover",
                denyButtonText: 'Cancelar',
                html: `<p>Una vez eliminado, no podrá revertir los cambios</p>`,
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: 'Http/Controlllers/UserController.php',
                        type: 'POST',
                        data: {
                            roltext: roltext,
                            permisotext: permisotext,
                            rolvalue: rolvalue,
                            permisovalue: permisovalue,
                            opcion: opcion
                        },
                        dataType: 'json',
                        success: function(response) {
            
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                html: 'El permiso ha sido removido con éxito.'
                            });

                            $.ajax({
                              //  url: 'http://192.168.100.100:3001/refresh/asigpermis',
                                type: 'GET',
                                success: function() {
                                    console.log('Evento de actualización enviado para asigpermis');
                                },
                                error: function(xhr, status, error) {
                                    console.log('Error al emitir evento: ' + error);
                                }
                            });
            
                            $('#modal_asigpermis').modal('hide');
                            window.tabla_asigpermis.ajax.reload();
                
                            $.ajax({
                                url: 'Http/Controlllers/UserController.php',
                                type: 'POST',
                                data: {
                                    roltext: roltext,
                                    permisotext: permisotext,
                                    opcion: 15,
                                    mail_log: mail_log
                                },
                                dataType: 'json',
                                success: function(response) {
                                    window.tabla_historial.ajax.reload();
                                },
                                error: function(xhr, status, error) {
        
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
        }

    
    });
    /* ************************************************************************ */

});