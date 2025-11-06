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

    // Abrir modal en modo CREAR
    $(document).on('click', '#btnNuevoUsuario', function() {
        $('#modal_user_title').text('Nuevo usuario');
        $('#btn_submit_user').text('Crear usuario');
        $('#form_add_users')[0].reset();
        setSwitchState(true); // Por defecto activo
        $('#modal_add_users').removeClass('hidden');
        $('#form_add_users').removeData('edit-id');
        $('#form_add_users').removeData('original-correo');
        
        // Mostrar campos de contraseña para CREATE
        $('#div_pass_add_user').show();
        $('#div_passconfirm_add_user').show();
        $('#input_pass_add_user').prop('required', true);
        $('#input_passconfirm_add_user').prop('required', true);
        $('#input_pass_add_user').val('');
        $('#input_passconfirm_add_user').val('');
    });

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
            console.error('Error al cargar roles:', error);
        }
    });

    /* *************************************************************************************** */

    /* ********************* CARGA DEL INPUT SELECT: ESTADOS ********************* */
    $('#select_estado_add_user').empty();
    $('#select_estado_add_user').append(
        $('<option>', {
            value: 1,
            text: 'Activo'
        })
    );
    $('#select_estado_add_user').append(
        $('<option>', {
            value: 0,
            text: 'Inactivo'
        })
    );

    /* *************************************************************************************** */

    /* ********************* FUNCIONES PARA EL SWITCH DE ESTADO ********************* */
    function initUserStatusSwitch() {
        $('#switch_button').off('click').on('click', function() {
            const checkbox = $('#switch_add_user');
            const isChecked = checkbox.is(':checked');
            
            checkbox.prop('checked', !isChecked);
            setSwitchState(!isChecked);
        });
    }

    function setSwitchState(isActive) {
        const checkbox = $('#switch_add_user');
        const thumb = $('#switch_thumb');
        const label = $('#switchLabel');
        
        checkbox.prop('checked', isActive);
        
        if (isActive) {
            thumb.removeClass('translate-x-1').addClass('translate-x-6');
            $('#switch_add_user').removeClass('bg-gray-200').addClass('bg-green-500');
            label.text('Activo').removeClass('text-red-600').addClass('text-green-600');
        } else {
            thumb.removeClass('translate-x-6').addClass('translate-x-1');
            $('#switch_add_user').removeClass('bg-green-500').addClass('bg-gray-200');
            label.text('Inactivo').removeClass('text-green-600').addClass('text-red-600');
        }
    }

    function getSwitchState() {
        return $('#switch_add_user').is(':checked');
    }

    // Inicializar el switch
    initUserStatusSwitch();

    /* *************************************************************************************** */

    /* ********************* FUNCIONES PARA MOSTRAR/OCULTAR CAMPOS DE CONTRASEÑA ********************* */
    function togglePasswordFields(isEdit) {
        if (isEdit) {
            $('#div_pass_add_user').show();
            $('#div_passconfirm_add_user').show();
            $('#input_pass_add_user').prop('required', true);
            $('#input_passconfirm_add_user').prop('required', true);
            $('#input_pass_add_user').val('********');
            $('#input_passconfirm_add_user').val('********');
        } else {
            $('#div_pass_add_user').hide();
            $('#div_passconfirm_add_user').hide();
            $('#input_pass_add_user').prop('required', false);
            $('#input_passconfirm_add_user').prop('required', false);
            $('#input_pass_add_user').val('');
            $('#input_passconfirm_add_user').val('');
        }
    }

    /* *************************************************************************************** */

    /* ********************* FUNCIÓN PARA MOSTRAR ALERTAS SWEETALERT2 ********************* */
    function showSwalAlert(type, title, html) {
        Swal.fire({
            icon: type,
            title: title,
            html: html,
            confirmButtonColor: '#282c34',
            confirmButtonText: 'Aceptar'
        });
    }

    /* *************************************************************************************** */

    /* ********************* EVENTOS PARA EL MODAL DE USUARIOS ********************* */

    // Abrir modal en modo EDITAR
    $(document).on('click', '.btnEditar_user', function() {
        const id = $(this).data('id');
        const row = window.tabla_usuarios.row($(this).closest('tr')).data();
        if (!row) return;
        
        $('#modal_user_title').text('Editar usuario');
        $('#btn_submit_user').text('Guardar cambios');
        $('#input_correo_add_user').val(row.correo);
        $('#select_rol_add_user').val(row.rol.id || row.rol_id);
        setSwitchState(row.usuario_estado);
        $('#modal_add_users').removeClass('hidden');
        $('#form_add_users').data('edit-id', id);
        $('#form_add_users').data('original-correo', row.correo); // Guardar correo original
        
        // Mostrar campos de contraseña para UPDATE con ********
        $('#div_pass_add_user').show();
        $('#div_passconfirm_add_user').show();
        $('#input_pass_add_user').prop('required', true);
        $('#input_passconfirm_add_user').prop('required', true);
        $('#input_pass_add_user').val('********');
        $('#input_passconfirm_add_user').val('********');
    });

    // Cancelar cierra el modal
    $(document).on('click', '.btn-cancelar, .close-modal', function() {
        $('#modal_add_users').addClass('hidden');
        $('#form_add_users')[0].reset();
        $('#form_add_users').removeData('edit-id');
        $('#form_add_users').removeData('original-correo');
    });

    // Submit CREATE/UPDATE
    $('#form_add_users').off('submit').on('submit', function(e) {
        e.preventDefault();
        const isEdit = $(this).data('edit-id') !== undefined;
        const id = $(this).data('edit-id');
        const correo = $('#input_correo_add_user').val();
        const rol_id = $('#select_rol_add_user').val();
        const usuario_estado = getSwitchState() ? 1 : 0;
        let contrasena = $('#input_pass_add_user').val();
        let contrasena_confirmation = $('#input_passconfirm_add_user').val();
        
        // Validaciones
        if (!correo || !rol_id) {
            showSwalAlert('error', 'Campos requeridos', 'Por favor, complete todos los campos.');
            return;
        }
        
        if (isEdit) {
            // Validaciones específicas para UPDATE
            if (!contrasena || !contrasena_confirmation) {
                showSwalAlert('error', 'Campos requeridos', 'Por favor, complete ambos campos de contraseña.');
                return;
            }
            if (contrasena !== contrasena_confirmation) {
                showSwalAlert('error', 'Contraseñas no coinciden', 'Por favor, verifica que ambas contraseñas sean iguales.');
                return;
            }
            // Si los campos de contraseña siguen con ********, no actualizar contraseña
            if (contrasena === '********' && contrasena_confirmation === '********') {
                contrasena = null;
                contrasena_confirmation = null;
            }
        }
        
        const payload = {
            rol_id,
            usuario_estado,
        };
        
        // Solo incluir correo si es CREATE o si cambió en UPDATE
        if (!isEdit || correo !== $(this).data('original-correo')) {
            payload.correo = correo;
        }
        
        if (isEdit && contrasena && contrasena !== '********') {
            payload.contrasena = contrasena;
            payload.contrasena_confirmation = contrasena_confirmation;
        }
        
        if (!isEdit) {
            // En CREATE siempre enviar contraseña y su confirmación
            payload.contrasena = contrasena;
            payload.contrasena_confirmation = contrasena_confirmation;
        }
        
        const url = isEdit ? `/usuarios/${id}` : '/usuarios';
        const method = isEdit ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(errorData => {
                    throw new Error(errorData.error || errorData.message || 'Error en la solicitud');
                });
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showSwalAlert('success', isEdit ? 'Usuario actualizado' : 'Usuario creado', `El usuario <strong>${correo}</strong> ha sido ${isEdit ? 'actualizado' : 'creado'} con éxito.`);
                $('#modal_add_users').addClass('hidden');
                $('#form_add_users')[0].reset();
                $('#form_add_users').removeData('edit-id');
                $('#form_add_users').removeData('original-correo');
                
                // Recargar la tabla de usuarios
                if (window.tabla_usuarios) {
                    window.tabla_usuarios.ajax.reload();
                } else {
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            } else {
                showSwalAlert('error', 'Error', data.message || 'Ocurrió un error.');
            }
        })
        .catch((error) => {
            showSwalAlert('error', 'Error', error.message || 'Ocurrió un error al procesar la solicitud.');
        });
    });

    // Eliminar usuario
    $(document).on('click', '.btnEliminar_usuario', function() {
        const id = $(this).data('id');
        const row = window.tabla_usuarios.row($(this).closest('tr')).data();
        const correo = row ? row.correo : 'usuario';
        
        Swal.fire({
            icon: 'warning',
            title: '¿Desea eliminar al usuario?',
            html: `¿Desea eliminar al usuario <strong>${correo}</strong>?`,
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#2563eb',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/usuarios/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showSwalAlert('success', 'Usuario eliminado', `El usuario <strong>${correo}</strong> ha sido eliminado con éxito.`);
                        
                        // Recargar la tabla de usuarios
                        if (window.tabla_usuarios) {
                            window.tabla_usuarios.ajax.reload();
                        } else {
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    } else {
                        showSwalAlert('error', 'Error', data.message || 'No se pudo eliminar el usuario.');
                    }
                })
                .catch(() => {
                    showSwalAlert('error', 'Error', 'Ocurrió un error al eliminar el usuario.');
                });
            }
        });
    });

    /* *************************************************************************************** */

    /* ********************* VALIDACIÓN DE CONTRASEÑAS ********************* */
    function validarContrasenas(new_pass, confirm_pass) 
    {
        if (new_pass.length < 6) {
            return { valido: false, mensaje: 'La contraseña debe tener al menos 6 caracteres' };
        }
        
        if (new_pass !== confirm_pass) {
            return { valido: false, mensaje: 'Las contraseñas no coinciden' };
        }
        
        return { valido: true, mensaje: '' };
    }

    /* *************************************************************************************** */

    /* ********************* EVENTOS PARA EL SWITCH ********************* */
    $(document).on('change', '#switch_add_user', function() {
        setSwitchState($(this).is(':checked'));
    });

    /* *************************************************************************************** */
});