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
    $('#usuarios_opcion, #usuarios_opcion_movil').click(function(e) {
        // Solo ejecutar si el elemento no tiene href (no es un enlace)
        if (!$(this).attr('href')) {
            e.preventDefault();
            
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
                                    <button class="btn btn-primary btn-sm btnEditar_user" data-id="${row.id_email}">Editar</button>
                                    <button class="btn btn-danger btn-sm btnEliminar_usuario" data-id="${row.id_email}">Eliminar</button>
                                `;
                            }
                        }
                    ],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                    }
                });
            }
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
        // $("#modal_add_users").modal("show");
    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN EDITAR PARA: ACADEMIAS ******************************* */
    // Evento delegado para eliminar usuario
    $(document).on("click", ".btnEliminar_usuario", function(){
        const id = $(this).data('id');
        eliminarUsuario(id);
    });
    /* ****************************************************************************************** */

    /* ********************* ENVÍO DE FORMULARIO: USUARIOS ********************* */
    $("#form_add_users").submit(function(e) {
        e.preventDefault();

        var correo = $.trim($("#input_correo_add_user").val());
        var contrasena = $.trim($('#input_pass_add_user').val());
        var rol_id = $.trim($('#select_rol_add_user').val());
        var usuario_estado = $('#switch_add_user').prop('checked') ? true : false;
        var confirm_pass = $.trim($('#input_passconfirm_edit_user').val());

        // Validación básica
        if (!correo || !contrasena || !rol_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Por favor, complete todos los campos obligatorios.'
            });
            return;
        }
        if (contrasena !== confirm_pass) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Las contraseñas no coinciden.'
            });
            return;
        }

        // Enviar como JSON
        fetch('/usuarios', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                correo: correo,
                contrasena: contrasena,
                contrasena_confirmation: confirm_pass,
                rol_id: rol_id,
                usuario_estado: usuario_estado
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    html: 'El usuario con correo <strong>' + correo + '</strong> ha sido ingresado con éxito.'
                });
                // Recargar la tabla de usuarios si aplica
                if (window.tabla_usuarios) window.tabla_usuarios.ajax.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Hubo un problema al crear el usuario.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al enviar la solicitud. Intente nuevamente.'
            });
        });
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