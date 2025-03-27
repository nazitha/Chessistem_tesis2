$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
    var opcion = 1;
    var search;
    var load_table = 0;
    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
    var mail_log = userData.correo;

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: ACADEMIAS ********************* */
    $('#miembros_opcion, #miembros_opcion_movil').click(function() {

        load_table++;

        if(load_table === 1)
        {
            window.tabla_miembros = $('#tabla-miembros').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controlllers/MiembroController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "cedula" },
                    { "data": "nombres" },
                    { "data": "apellidos" },
                    { "data": "correo_sistema_id" },
                    { "data": "rol" },
                    { "data": "academia_id" },
                    { "data": "estado_miembro" },
                    { "data": "sexo" },
                    { "data": "fecha_nacimiento" },
                    { "data": "ciudad_id" },
                    { "data": "telefono" },
                    { "data": "fecha_inscripcion" },
                    { "data": "club" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-danger btn-sm btnEditar_miembro"
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
                                    class="btn btn-danger btn-sm btnEliminar_miembro"
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
                            $("#form_miembros").trigger("reset");
                            $("#form_miembros").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_miembros").text("Agregar miembro");
        
                            /* ********** SELECT ACADEMIAS ********** */
                            $.ajax({
                                url: 'Http/Controlllers/MiembroController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_academia_miembros').empty();
        
                                        $('#select_academia_miembros').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione una academia...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(academia) {
                        
                                            $('#select_academia_miembros').append(
                                                $('<option>', {
                                                    value: academia.nombre_academia,
                                                    text: academia.nombre_academia
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
                            /* ************************************* */
        
                            /* ********** SELECT PROCEDENCIA ********** */
                            $.ajax({
                                url: 'Http/Controlllers/AcademiaController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_ciudad_miembros').empty();
        
                                        $('#select_ciudad_miembros').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione una ciudad...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(ciudad) {
                        
                                            $('#select_ciudad_miembros').append(
                                                $('<option>', {
                                                    value: ciudad.id_ciudad,
                                                    text: ciudad.opc
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
                            /* ************************************* */
        
                            /* ********** SELECT CORREOS ********** */
                            $.ajax({
                                url: 'Http/Controlllers/MiembroController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 3 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_correos_miembros').empty();
        
                                        $('#select_correos_miembros').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione un correo...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(correos) {
                        
                                            $('#select_correos_miembros').append(
                                                $('<option>', {
                                                    value: correos.correo,
                                                    text: correos.correo
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
                            /* ************************************* */
        
                            opcion = 4;
        
                            const switchElement = $('#switch_estado_miembro')[0];
                            const label = $('#switchLabel_miembro');
                                
                            switchElement.checked = true;
                            switchElement.style.backgroundColor = '#28a745';
                            switchElement.style.borderColor = '#28a745';
                            label.text("Activo");
        
                            $('#modal_miembros').modal('show');
                        }
                    },
                    {
                        text: 'Importar miembros',
                        className: 'btn btn-custom',
                        style: 'background-color: #1e2936; color: white; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; ' +
                               'font-weight: 400; line-height: 1.5; border-radius: 0.25rem 0 0 0.25rem; text-align: center; vertical-align: middle; ' +
                               'cursor: pointer; transition: background-color 0.3s ease;',
                        action: function (e, dt, node, config) {

                            let fileInput = document.createElement("input");
                            fileInput.type = "file";
                            fileInput.accept = ".csv";
                    
                            fileInput.addEventListener("change", function(event) {
                                let file = event.target.files[0];
                    
                                if (file) {
                                    let formData = new FormData();
                                    formData.append("file", file);
                    
                                    $.ajax({
                                        url: 'Http/Controlllers/ImportarMiembrosController.php',
                                        type: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        success: function(response) {
                                            let result = JSON.parse(response);
                    
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

                                            window.tabla_miembros.ajax.reload();
                                            $.ajax({
                                            //   url: 'http://192.168.100.100:3001/refresh/miembros', 
                                                type: 'GET',
                                                success: function() {
                                                    //console.log('Evento de actualización enviado para academias');
                                                },
                                                error: function(xhr, status, error) {
                                                    //console.log('Error al emitir evento: ' + error);
                                                }
                                            });
                                        },
                                        error: function() {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'Hubo un problema al procesar el archivo.'
                                            });
                                        }
                                    });
                                }
                            });
                    
                            fileInput.click();
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        title: 'Lista de miembros',
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

    var prev_identificacion;
    var prev_nombre;
    var prev_apellido;
    var prev_sexo;
    var prev_correo;
    var prev_academia;
    var prev_fnacimiento;
    var prev_ciudad;
    var prev_telefono;
    var prev_finscripcion;
    var prev_club;
    var prev_estado;


    /* ******************************* BOTÓN EDITAR PARA: MIEMBROS ******************************* */
    $(document).on("click", ".btnEditar_miembro", function(){
        $("#form_miembros").trigger("reset");
        $("#form_miembros").removeClass("was-validated");

        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_miembros.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.cedula;

        $('#input_identificacion_miembro').val(datosFila.cedula);
        $('#input_nombre_miembros').val(datosFila.nombres);
        $('#input_apellidos_miembros').val(datosFila.apellidos);
        $('#input_phone_miembros').val(datosFila.telefono);
        $('#input_club_miembros').val(datosFila.club);

        if (datosFila.fecha_nacimiento && datosFila.fecha_nacimiento !== null) {
            var fechanacimiento = datosFila.fecha_nacimiento.split('-');
            var fechanacimientoFormateada = fechanacimiento[2] + '-' + fechanacimiento[1] + '-' + fechanacimiento[0];
            $('#input_fechanacimiento_miembros').val(fechanacimientoFormateada);
        }
        
        if (datosFila.fecha_inscripcion && datosFila.fecha_inscripcion !== null) {
            var fechainscripcion = datosFila.fecha_inscripcion.split('-');
            var fechainscripcionFormateada = fechainscripcion[2] + '-' + fechainscripcion[1] + '-' + fechainscripcion[0];
            $('#input_fechainscrip_miembros').val(fechainscripcionFormateada);
        }        

        const select = document.getElementById('select_sexo_miembros');

        $('#select_correos_miembros').empty();

        if (!datosFila.correo_sistema_id) 
        {
            $('#select_correos_miembros').append(
                $('<option>', {
                    value: '',
                    text: 'Seleccione un correo...',
                    disabled: true,
                    selected: true
                })
            );
        }
        else
        {
            $('#select_correos_miembros').append(
                $('<option>', {
                    value: datosFila.correo_sistema_id,
                    text: datosFila.correo_sistema_id,
                    disabled: true,
                    selected: true
                })
            );
        }

            
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].text === datosFila.sexo) {
                select.selectedIndex = i;
                break;
            }
        }

        /* ********** SELECT CORREOS ********** */
        $.ajax({
            url: 'Http/Controlllers/MiembroController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 3 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                
                    response.forEach(function(correos) {
                
                        $('#select_correos_miembros').append(
                            $('<option>', {
                                value: correos.correo,
                                text: correos.correo
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
        /* ************************************* */

        /* ********** SELECT PROCEDENCIA ********** */
        $.ajax({
            url: 'Http/Controlllers/AcademiaController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 2 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_ciudad_miembros').empty();

                    $('#select_ciudad_miembros').append(
                        $('<option>', {
                            value: '',
                            text: 'Seleccione una ciudad...',
                            disabled: true,
                            selected: true
                        })
                    );
                
                    response.forEach(function(ciudad) {
                
                        $('#select_ciudad_miembros').append(
                            $('<option>', {
                                value: ciudad.id_ciudad,
                                text: ciudad.opc
                            })
                        );
                    });

                    const select = document.getElementById('select_ciudad_miembros');
            
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].text === datosFila.ciudad_id) {
                            select.selectedIndex = i;
                            break;
                        }
                    }

                    prev_ciudad = $.trim($('#select_ciudad_miembros option:selected').text());
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });
        /* ************************************* */

        /* ********** SELECT ACADEMIAS ********** */
        $.ajax({
            url: 'Http/Controlllers/MiembroController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 2 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_academia_miembros').empty();

                    $('#select_academia_miembros').append(
                        $('<option>', {
                            value: '',
                            text: 'Seleccione una academia...',
                            disabled: true,
                            selected: true
                        })
                    );
                
                    response.forEach(function(academia) {
                
                        $('#select_academia_miembros').append(
                            $('<option>', {
                                value: academia.nombre_academia,
                                text: academia.nombre_academia
                            })
                        );
                    });

                    const select = document.getElementById('select_academia_miembros');
            
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].text === datosFila.academia_id) {
                            select.selectedIndex = i;
                            break;
                        }
                    }

                    prev_academia = $.trim($("#select_academia_miembros").val());
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });
        /* ************************************* */

        const switchElement = $('#switch_estado_miembro')[0];
        const label = $('#switchLabel_miembro');
            
        if (datosFila.estado_miembro === "Activo") {
            switchElement.checked = true;
            switchElement.style.backgroundColor = '#28a745';
            switchElement.style.borderColor = '#28a745';
            label.text("Activo");
        } else if (datosFila.estado_miembro === "Inactivo") {
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

        prev_identificacion = $.trim($("#input_identificacion_miembro").val());
        prev_nombre = $.trim($('#input_nombre_miembros').val());
        prev_apellido = $.trim($('#input_apellidos_miembros').val());
        prev_sexo = $.trim($('#select_sexo_miembros option:selected').text());
        prev_fnacimiento = $('#input_fechanacimiento_miembros').val();
        prev_telefono = $('#input_phone_miembros').val();
        prev_finscripcion = $('#input_fechainscrip_miembros').val();
        prev_club = $('#input_club_miembros').val();

        var estado = $('#switch_estado_miembro').prop('checked') ? 1 : 0;

        if(estado == 0)
        {
            prev_estado = 'Inactivo';
        }
        else if(estado == 1)
        {
            prev_estado = 'Activo';
        }

        opcion = 7;

        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_miembros").text("Editar miembro");
        $("#modal_miembros").modal("show");
    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN EDITAR PARA: ACADEMIAS ******************************* */
    $(document).on("click", ".btnEliminar_miembro", function(){
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_miembros.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.cedula;

        prev_identificacion = datosFila.cedula;
        prev_nombre = datosFila.nombres;
        prev_apellido = datosFila.apellidos;
        prev_correo = datosFila.correo_sistema_id;
        prev_academia = datosFila.academia_id;
        prev_estado = datosFila.estado_miembro;
        prev_sexo = datosFila.sexo;
        prev_fnacimiento = datosFila.fecha_nacimiento;
        prev_ciudad = datosFila.ciudad_id;
        prev_telefono = datosFila.telefono;
        prev_finscripcion = datosFila.fecha_inscripcion;
        prev_club = datosFila.club; 
        
       Swal.fire({
            title: '¿Desea eliminar al miembro <b>'+prev_nombre+' '+prev_apellido+'</b>?',
            icon: 'warning',
            showDenyButton: true,
            confirmButtonText: "Eliminar",
            denyButtonText: 'Cancelar',
            html: `<p>Una vez eliminado, no podrá revertir los cambios</p>`,
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: 'Http/Controlllers/MiembroController.php',
                    type: 'POST',
                    data: {
                        search: search,
                        opcion: 9
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'El miembro ha sido eliminado con éxito.'
                        });

                        window.tabla_miembros.ajax.reload();

                        $.ajax({
                           // url: 'http://192.168.100.100:3001/refresh/miembros', 
                            type: 'GET',
                            success: function() {
                                //console.log('Evento de actualización enviado para academias');
                            },
                            error: function(xhr, status, error) {
                                //console.log('Error al emitir evento: ' + error);
                            }
                        });

                        $.ajax({
                            url: 'Http/Controlllers/MiembroController.php',
                            type: 'POST',
                            data: {
                                opcion: 10,
                                mail_log: mail_log,
                                prev_identificacion: prev_identificacion,
                                prev_nombre: prev_nombre,
                                prev_apellido: prev_apellido,
                                prev_sexo: prev_sexo,
                                prev_correo: prev_correo,
                                prev_academia: prev_academia,
                                prev_fnacimiento: prev_fnacimiento,
                                prev_ciudad: prev_ciudad,
                                prev_telefono: prev_telefono,
                                prev_finscripcion: prev_finscripcion,
                                prev_club: prev_club,
                                prev_estado: prev_estado
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
    })
    /* ****************************************************************************************** */

    /* ********************* ENVÍO DE FORMULARIO: ACADEMIAS ********************* */
    $("#form_miembros").submit(function(e) {
        e.preventDefault();
    
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
    
        var identificacion = $.trim($("#input_identificacion_miembro").val());
        var nombre = $.trim($('#input_nombre_miembros').val());
        var apellido = $.trim($('#input_apellidos_miembros').val());

        var sexo_char = $.trim($("#select_sexo_miembros").val());
        var sexo_text = $.trim($('#select_sexo_miembros option:selected').text());

        var correo = $.trim($('#select_correos_miembros option:selected').text());

        var academia = $.trim($("#select_academia_miembros").val());
        var fnacimiento = $('#input_fechanacimiento_miembros').val();

        var ciudad_val = $.trim($("#select_ciudad_miembros").val());
        var ciudad_text = $.trim($('#select_ciudad_miembros option:selected').text());

        var telefono = $('#input_phone_miembros').val();
        var finscripcion = $('#input_fechainscrip_miembros').val();
        var club = $('#input_club_miembros').val();

        var estado = $('#switch_estado_miembro').prop('checked') ? 1 : 0;
        var estado_text;

        if(estado == 0)
        {
            estado_text = 'Inactivo';
        }
        else if(estado == 1)
        {
            estado_text = 'Activo';
        }
        
        if(correo == 'Seleccione un correo...')
        {
            correo = null;
        }

        console.log({
            identificacion: identificacion,
            nombre: nombre,
            apellido: apellido,
            sexo_char: sexo_char,
            correo: correo,
            academia: academia,
            fnacimiento: fnacimiento,
            ciudad_val: ciudad_val,
            telefono: telefono,
            finscripcion: finscripcion,
            club: club,
            estado: estado,
            opcion: opcion,
            search: search
        });
        
       $.ajax({
            url: 'Http/Controlllers/MiembroController.php',
            type: 'POST',
            data: {
                identificacion: identificacion,
                nombre: nombre,
                apellido: apellido,
                sexo_char: sexo_char,
                correo: correo,
                academia: academia,
                fnacimiento: fnacimiento,
                ciudad_val: ciudad_val,
                telefono: telefono,
                finscripcion: finscripcion,
                club: club,
                estado: estado,
                opcion: opcion,
                search: search
            },
            dataType: 'json',
            success: function(response) {

                $.ajax({
                //    url: 'http://192.168.100.100:3001/refresh/miembros', 
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
                        html: 'El miembro <strong>' + nombre + ' ' + apellido + '</strong> ha sido agregado con éxito.'
                    });

                    window.tabla_miembros.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/MiembroController.php',
                        type: 'POST',
                        data: {
                            identificacion: identificacion,
                            nombre: nombre,
                            apellido: apellido,
                            sexo_text: sexo_text,
                            correo: correo,
                            academia: academia,
                            fnacimiento: fnacimiento,
                            ciudad_text: ciudad_text,
                            telefono: telefono,
                            finscripcion: finscripcion,
                            club: club,
                            estado_text: estado_text,
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

                    $("#modal_miembros").modal("hide");
                }
                else if(opcion == 7)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'El miembro <strong>' + nombre + ' ' + apellido + '</strong> ha sido editado con éxito.'
                    });
                    
                    window.tabla_miembros.ajax.reload();

                    $.ajax({
                        url: 'Http/Controlllers/MiembroController.php',
                        type: 'POST',
                        data: {
                            identificacion: identificacion,
                            nombre: nombre,
                            apellido: apellido,
                            sexo_text: sexo_text,
                            correo: correo,
                            academia: academia,
                            fnacimiento: fnacimiento,
                            ciudad_text: ciudad_text,
                            telefono: telefono,
                            finscripcion: finscripcion,
                            club: club,
                            estado_text: estado_text,
                            opcion: 8,
                            mail_log: mail_log,
                            prev_identificacion: prev_identificacion,
                            prev_nombre: prev_nombre,
                            prev_apellido: prev_apellido,
                            prev_sexo: prev_sexo,
                            prev_correo: prev_correo,
                            prev_academia: prev_academia,
                            prev_fnacimiento: prev_fnacimiento,
                            prev_ciudad: prev_ciudad,
                            prev_telefono: prev_telefono,
                            prev_finscripcion: prev_finscripcion,
                            prev_club: prev_club,
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
                    $("#modal_miembros").modal("hide");
                }

            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al agregar el registro. Verifique los datos ingresados y si la academia ya existe.'
                });
            }
        });

    });
    /* ************************************************************************ */

});