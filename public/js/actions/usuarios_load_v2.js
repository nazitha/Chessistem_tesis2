$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
     var opcion = 1;
     var search;
     var load_table = 0;
    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
     var prev_correo;
     var prev_rol;
     var prev_estado;
     var rol_text;
     var estado_text;

     var mail_log = userData.correo;
    /* ********************************** */

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: USUARIOS ********************* */
    $('#usuarios_opcion, #usuarios_opcion_movil').click(function() {
        
        load_table++;

        if(load_table === 1)
        {
            window.tabla_usuarios = $('#tabla-usuarios').DataTable({
                responsive: true,
                "ajax": {
                    "url": "/usuarios",
                    "method": "GET",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "correo" },
                    { "data": "rol.nombre" },
                    { 
                        "data": "usuario_estado",
                        "render": function(data) {
                            return data ? 'Activo' : 'Inactivo';
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-primary btn-sm" onclick="editarUsuario(${row.id_email})">Editar</button>
                                <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${row.id_email})">Eliminar</button>
                            `;
                        }
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                }
            });
        }

    });

    /* *************************************************************************************** */

    /* ********************* CARGA DEL INPUT SELECT: ROLES ********************* */
    $.ajax({
        url: 'Http/Controlllers/UserController.php',
        type: 'POST',
        dataType: 'json',
        data: { opcion: 2 },
        success: function(response) {
            if (response && Array.isArray(response)) {
                $('#select_rol_add_user').empty();

                response.forEach(function(role) {

                    $('#select_rol_add_user').append(
                        $('<option>', {
                            value: role.id,
                            text: role.rol
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

    /* ******************************* BOTÓN EDITAR PARA: USUARIO ******************************* */
    $(document).on("click", ".btnEditar_user", function(){
        $("#form_add_users").trigger("reset");
        $("#form_add_users").removeClass("was-validated");

        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_usuarios.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.correo;
        rol = datosFila.rol;
        estado = datosFila.Estado;
        
        prev_correo = search;
        prev_rol = rol;
        prev_estado = estado;

        $('#input_correo_add_user').val(search);
        $('#select_rol_add_user').val($('#select_rol_add_user option').filter((_, el) => $(el).text() === rol).val());


        const switchElement = $('#switch_add_user')[0];
        const label = $('#switchLabel');
        
        if (estado === 'Activo') {
            switchElement.checked = true;
            switchElement.style.backgroundColor = '#28a745';
            switchElement.style.borderColor = '#28a745';
            label.text("Activo");
        } else if (estado === 'Inactivo') {
            switchElement.checked = false;
            switchElement.style.backgroundColor = '#dc3545';
            switchElement.style.borderColor = '#dc3545';
            label.text("Inactivo");
        } else {
            switchElement.checked = false;
            switchElement.style.backgroundColor = '#6c757d';
            switchElement.style.borderColor = '#6c757d';
            label.text("Indefinido");
        }

        document.getElementById('div_newpass_edit').closest('div').style.display = 'flex';
        document.getElementById('div_confirmpass_edit').closest('div').style.display = 'flex';
        document.getElementById('div_setpass').closest('div').style.display = 'none';

        opcion = 6;

        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_add_users").text("Editar usuario");
        $("#modal_add_users").modal("show");
    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN EDITAR PARA: ACADEMIAS ******************************* */
    $(document).on("click", ".btnEliminar_usuario", function(){
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_usuarios.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.correo;

        console.log('usuario a eliminar: '+search);

       Swal.fire({
            title: `¿Desea eliminar al usuario con correo <b>${search}</b>?`,
            icon: 'warning',
            showDenyButton: true,
            confirmButtonText: "Eliminar",
            denyButtonText: 'Cancelar',
            html: `<p>Una vez eliminado, no podrá revertir los cambios</p>`,
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: 'Http/Controlllers/UserController.php',
                    type: 'POST',
                    data: {
                        search: search,
                        opcion: 16
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'El usuario ha sido eliminado con éxito.'
                        });

                        window.tabla_usuarios.ajax.reload();

                        correo = datosFila.correo;
                        rol_text = datosFila.rol;
                        estado_text = datosFila.Estado;

                        $.ajax({
                            url: 'Http/Controlllers/UserController.php',
                            type: 'POST',
                            data: {
                                opcion: 17,
                                mail_log: mail_log,
                                correo: correo,
                                rol_text: rol_text,
                                estado_text: estado_text
                            },
                            dataType: 'json',
                            success: function(response) {
                                window.tabla_historial.ajax.reload();
                            },
                            error: function(xhr, status, error) {
    
                            }
                        }); 

                        $.ajax({
                        //    url: 'http://192.168.100.100:3001/refresh/usuarios',
                            type: 'GET',
                            success: function() {
                                console.log('Evento de actualización enviado para usuarios');
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

    /* ********************* ENVÍO DE FORMULARIO: USUARIOS ********************* */
    $("#form_add_users").submit(function(e) {
        e.preventDefault();
    
        var correo = $.trim($("#input_correo_add_user").val());
        var last_pass = $.trim($('#input_pass_add_user').val());
        var new_pass = $.trim($('#input_pass_edit_user').val());
        var confirm_pass = $.trim($('#input_passconfirm_edit_user').val());
        var rol = $.trim($('#select_rol_add_user').val());
        var estado = $('#switch_add_user').prop('checked') ? 1 : 0;

        rol_text = $('#select_rol_add_user option:selected').text().trim();
        estado_text = estado === 1 ? 'Activo' : 'Inactivo';

        if (opcion == 6) {
            
            if (!correo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Por favor, complete todos los campos.'
                });
                return;
            }
    
            if (new_pass || confirm_pass) {
                validarContrasenas(new_pass, confirm_pass);
                if (new_pass !== confirm_pass) {
                    return;
                }
            }

            if(opcion == 4)
            {
                search = correo;
            }
    
            $.ajax({
                url: 'Http/Controlllers/UserController.php',
                type: 'POST',
                data:
                {
                    correo: correo,
                    new_pass: new_pass,
                    rol: rol,
                    estado: estado,
                    opcion: opcion,
                    search: search,
                    rol_text: rol_text,
                    estado_text: estado_text
                },
                dataType: 'json',
                success: function(response) {

                    if (response.success) {
                        window.tabla_usuarios.ajax.reload();

                        $.ajax({
                          //  url: 'http://192.168.100.100:3001/refresh/usuarios', 
                            type: 'GET',
                            success: function() {
                                console.log('Evento de actualización enviado para usuarios');
                            },
                            error: function(xhr, status, error) {
                                console.log('Error al emitir evento: ' + error);
                            }
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'El usuario con correo <strong>' + search + '</strong> ha sido actualizado con éxito.'
                        });
                        
                        console.log('pass:'+new_pass)

                        $.ajax({
                            url: 'Http/Controlllers/UserController.php',
                            type: 'POST',
                            data: 
                            {
                                correo: correo,
                                rol_text: rol_text,
                                estado_text: estado_text,
                                new_pass: new_pass,
                                opcion: 7,
                                mail_log: mail_log,
                                prev_estado: prev_estado,
                                prev_rol: prev_rol,
                                prev_correo: prev_correo
                            },
                            dataType: 'json',
                            success: function(response) {
                                // Auditoría exitosa
                            },
                            error: function(xhr, status, error) {
                                // Error en auditoría
                            }
                        });

                        $("#modal_add_users").modal("hide");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al actualizar el usuario. Intente nuevamente.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al enviar la solicitud. Intente nuevamente.'
                    });
                }
            });
    
        } else if (opcion == 4) {

            if (this.id === 'input_pass_add_user') {
                return;
            }
            
            if (this.checkValidity() === false) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }

            $.ajax({
                url: 'Http/Controlllers/UserController.php',
                type: 'POST',
                data:
                {
                    correo: correo,
                    last_pass: last_pass,
                    rol: rol,
                    estado: estado,
                    opcion: opcion,
                    search: search,
                    rol_text: rol_text,
                    estado_text: estado_text
                },
                dataType: 'json',
                success: function(response) {
    
                    if (response.success) {
                        window.tabla_usuarios.ajax.reload();
                        $.ajax({
                          //url: 'http://192.168.100.100:3001/refresh/usuarios', 
                            type: 'GET',
                            success: function() {
                                console.log('Evento de actualización enviado para usuarios');
                            },
                            error: function(xhr, status, error) {
                                console.log('Error al emitir evento: ' + error);
                            }
                        });
    
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'El usuario con correo <strong>' + correo + '</strong> ha sido ingresado con éxito.'
                        });
    
                        $.ajax({
                            url: 'Http/Controlllers/UserController.php',
                            type: 'POST',
                            data: 
                            {
                                correo: correo,
                                rol_text: rol_text,
                                estado_text: estado_text,
                                last_pass: last_pass,
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
    
                        $("#modal_add_users").modal("hide");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al crear el usuario. Intente nuevamente.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al enviar la solicitud. Verifique los datos y si el usuario ya existe.'
                    });
                }
            });
        }
    });
    /* ************************************************************************ */

    /* ********************* FUNCIÓN PARA VALIDAR QUE LAS CONTRASEÑAS SEAN IGUALES AL HACER RESET ********************* */
    function validarContrasenas(new_pass, confirm_pass) 
    {
        if (new_pass || confirm_pass) {

            if (new_pass && !confirm_pass) {
                confirm_pass = '';
                var errorMessage = document.createElement('div');
                errorMessage.classList.add('invalid-feedback');
                errorMessage.textContent = 'Por favor, confirme la nueva contraseña';
    
                if (!document.querySelector('#input_passconfirm_edit_user + .invalid-feedback')) {
                    $('#input_passconfirm_edit_user').parent().append(errorMessage);
                }
    
                $('#input_passconfirm_edit_user').addClass('is-invalid');
            }

            else if (confirm_pass && !new_pass) {
                new_pass = '';
                var errorMessage = document.createElement('div');
                errorMessage.classList.add('invalid-feedback');
                errorMessage.textContent = 'Por favor, ingrese la nueva contraseña';
    
                if (!document.querySelector('#input_pass_edit_user + .invalid-feedback')) {
                    $('#input_pass_edit_user').parent().append(errorMessage);
                }
    
                $('#input_pass_edit_user').addClass('is-invalid');
            }

            else if (new_pass && confirm_pass) {
                if (new_pass !== confirm_pass) {
                    var errorMessage = document.createElement('div');
                    errorMessage.classList.add('invalid-feedback');
                    errorMessage.textContent = 'Las contraseñas no coinciden';
    
                    if (!document.querySelector('#input_pass_edit_user + .invalid-feedback')) {
                        $('#input_pass_edit_user').parent().append(errorMessage);
                    }
                    if (!document.querySelector('#input_passconfirm_edit_user + .invalid-feedback')) {
                        $('#input_passconfirm_edit_user').parent().append(errorMessage);
                    }
    
                    $('#input_pass_edit_user').addClass('is-invalid');
                    $('#input_passconfirm_edit_user').addClass('is-invalid');
                } else {
                    $('#input_pass_edit_user').removeClass('is-invalid').removeAttr('required');
                    $('#input_passconfirm_edit_user').removeClass('is-invalid').removeAttr('required');
                    
                    $('#input_pass_edit_user').siblings('.invalid-feedback').remove();
                    $('#input_passconfirm_edit_user').siblings('.invalid-feedback').remove();
                }
            }
        } else {
            $('#input_pass_edit_user').removeAttr('required').removeClass('is-invalid');
            $('#input_passconfirm_edit_user').removeAttr('required').removeClass('is-invalid');
            
            $('#input_pass_edit_user').siblings('.invalid-feedback').remove();
            $('#input_passconfirm_edit_user').siblings('.invalid-feedback').remove();
        }
        
        $('#input_pass_edit_user').on('input', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });
    
        $('#input_passconfirm_edit_user').on('input', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });
        
        if (new_pass === confirm_pass && new_pass && confirm_pass) {
            $('#input_pass_edit_user').removeClass('is-invalid');
            $('#input_passconfirm_edit_user').removeClass('is-invalid');
            $('#input_pass_edit_user').siblings('.invalid-feedback').remove();
            $('#input_passconfirm_edit_user').siblings('.invalid-feedback').remove();
        }
    }
    /* *************************************************************************************************************** */  
});