$(document).ready(function() {
    /* ***** VARIABLES PARA EL CRUD ***** */
    var opcion = 1;
    var search;

    var load_table = 0;
    /* ********************************** */

    /* ***** VARIABLES PARA AUDITAR ***** */
    var mail_log = userData.correo;

    /* ********************* CARGA E INICIALIZACIÓN DE LA TABLA: ACADEMIAS ********************* */

    $('#academias_opcion, #academias_opcion_movil').click(function() {

        load_table++;

        if(load_table === 1)
        {
            window.tabla_academias = $('#tabla-academias').DataTable({
                responsive: true,
                "ajax": {
                    "url": "Http/Controllers/AcademiaController.php",
                    "method": "POST",
                    "data": { opcion: 1 },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "nombre_academia" },
                    { "data": "correo_academia" },
                    { "data": "telefono_academia" },
                    { "data": "representante_academia" },
                    { "data": "direccion_academia" },
                    { "data": "ciudad_id" },
                    { "data": "estado_academia" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button 
                                    class="btn btn-danger btn-sm btnEditar_academia"
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
                                    class="btn btn-danger btn-sm btnEliminar_academia"
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
                            $("#form_academias").trigger("reset");
                            $("#form_academias").removeClass("was-validated");
                            $(".modal-header").css("background-color", "#1e2936");
                            $(".modal-header").css("color", "#ffffff");
                            $("#title_academias").text("Agregar academia");
        
                            $.ajax({
                                url: 'Http/Controllers/AcademiaController.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { opcion: 2 },
                                success: function(response) {
                                    if (response && Array.isArray(response)) {
                                        $('#select_ciudades_academia').empty();
        
                                        $('#select_ciudades_academia').append(
                                            $('<option>', {
                                                value: '',
                                                text: 'Seleccione una ciudad...',
                                                disabled: true,
                                                selected: true
                                            })
                                        );
                        
                                        response.forEach(function(ciudad) {
                        
                                            $('#select_ciudades_academia').append(
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
        
                            const switchElement = $('#switch_estado_academia')[0];
                            const label = $('#switchLabel_academia');
                                
                            switchElement.checked = true;
                            switchElement.style.backgroundColor = '#28a745';
                            switchElement.style.borderColor = '#28a745';
                            label.text("Activo");
        
                            opcion = 3;
        
                            $('#modal_academias').modal('show');
                        }
                    },
                    {
                        text: 'Importar academias',
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
                                        url: 'Http/Controllers/ImportarAcademiasController.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        success: function(response) {
                                            
                                            var result = JSON.parse(response);

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

                                            window.tabla_academias.ajax.reload();
                                            $.ajax({
                                               // url: 'http://192.168.100.100:3001/refresh/academias', 
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
                        title: 'Lista de academias',
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

    var prev_academia;
    var prev_correo;
    var prev_telefono;
    var prev_director;
    var prev_direccion;
    var prev_ciudadText;
    var prev_estado;

    /* ******************************* BOTÓN EDITAR PARA: ACADEMIAS ******************************* */
    $(document).on("click", ".btnEditar_academia", function(){
        $("#form_academias").trigger("reset");
        $("#form_academias").removeClass("was-validated");

        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_academias.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.nombre_academia;

        $('#input_nombre_academia').val(datosFila.nombre_academia);
        $('#input_correo_academia').val(datosFila.correo_academia);
        $('#input_phone_academia').val(datosFila.telefono_academia);
        $('#input_director_academia').val(datosFila.representante_academia);
        $('#input_direccion_academia').val(datosFila.direccion_academia);

        const switchElement = $('#switch_estado_academia')[0];
        const label = $('#switchLabel_academia');
            
        if (datosFila.estado_academia === 'Activo') {
            switchElement.checked = true;
            switchElement.style.backgroundColor = '#28a745';
            switchElement.style.borderColor = '#28a745';
            label.text("Activo");
        } else if (datosFila.estado_academia === 'Inactivo') {
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

        //CARGA DE CIUDADES EN EL SELECT
        $.ajax({
            url: 'Http/Controllers/AcademiaController.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: 2 },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    $('#select_ciudades_academia').empty();

                    $('#select_ciudades_academia').append(
                        $('<option>', {
                            value: '',
                            text: 'Seleccione una ciudad...',
                            disabled: true,
                            selected: true
                        })
                    );
    
                    response.forEach(function(ciudad) {
    
                        $('#select_ciudades_academia').append(
                            $('<option>', {
                                value: ciudad.id_ciudad,
                                text: ciudad.opc
                            })
                        );
                    });

                    const select = document.getElementById('select_ciudades_academia');
                        
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].text === datosFila.ciudad_id) {
                            select.selectedIndex = i;
                            break;
                        }
                    }
                } else {
                    console.error('No se recibieron datos válidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la llamada AJAX: ' + error);
            }
        });

        prev_academia = $.trim($("#input_nombre_academia").val());
        prev_correo = $.trim($('#input_correo_academia').val());
        prev_telefono = $.trim($('#input_phone_academia').val());
        prev_director = $.trim($("#input_director_academia").val());
        prev_direccion = $.trim($('#input_direccion_academia').val());

        prev_ciudadText = $('#select_ciudades_academia option:selected').text();
        var estado = $('#switch_estado_academia').prop('checked') ? 1 : 0;

        if(estado == 0)
        {
            prev_estado = 'Inactivo';
        }
        else if(estado == 1)
        {
            prev_estado = 'Activo';
        }

        opcion = 6;

        $(".modal-header").css("background-color", "#1e2936");
        $(".modal-header").css("color", "#ffffff");
        $("#title_academias").text("Editar academia");
        $("#modal_academias").modal("show");
    })
    /* ****************************************************************************************** */

    /* ******************************* BOTÓN EDITAR PARA: ACADEMIAS ******************************* */
    $(document).on("click", ".btnEliminar_academia", function(){
        let fila = $(this).closest("tr");

        if (fila.hasClass("child")) {
            fila = fila.prev("tr");
        }

        let filaDataTable = window.tabla_academias.row(fila);

        if (!filaDataTable || !filaDataTable.data) {
            console.error("No se pudo obtener la fila de DataTables.");
            return;
        }

        let datosFila = filaDataTable.data();

        if (!datosFila) {
            console.error("No se pudieron obtener los datos de la fila.");
            return;
        }

        search = datosFila.nombre_academia;

        prev_academia = fila.find('td:eq(0)').text();
        prev_correo = fila.find('td:eq(1)').text();
        prev_telefono = fila.find('td:eq(2)').text();
        prev_director = fila.find('td:eq(3)').text();
        prev_direccion = fila.find('td:eq(4)').text();
        prev_ciudadText = fila.find('td:eq(5)').text();
        prev_estado = fila.find('td:eq(6)').text();

       Swal.fire({
            title: `¿Desea eliminar la academia <b>${search}</b>?`,
            icon: 'warning',
            showDenyButton: true,
            confirmButtonText: "Eliminar",
            denyButtonText: 'Cancelar',
            html: `<p>Una vez eliminado, no podrá revertir los cambios</p>`,
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: 'Http/Controllers/AcademiaController.php',
                    type: 'POST',
                    data: {
                        search: search,
                        opcion: 8
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            html: 'La academia ha sido eliminada con éxito.'
                        });

                        window.tabla_academias.ajax.reload();

                        $.ajax({
                           // url: 'http://192.168.100.100:3001/refresh/academias', 
                            type: 'GET',
                            success: function() {
                                //console.log('Evento de actualización enviado para academias');
                            },
                            error: function(xhr, status, error) {
                                //console.log('Error al emitir evento: ' + error);
                            }
                        });

                        $.ajax({
                            url: 'Http/Controllers/AcademiaController.php',
                            type: 'POST',
                            data: {
                                opcion: 9,
                                mail_log: mail_log,
                                prev_academia: prev_academia,
                                prev_correo: prev_correo,
                                prev_telefono: prev_telefono,
                                prev_director: prev_director,
                                prev_direccion: prev_direccion,
                                prev_ciudadText: prev_ciudadText,
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
    $("#form_academias").submit(function(e) {
        e.preventDefault();
    
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
    
        var academia = $.trim($("#input_nombre_academia").val());
        var correo = $.trim($('#input_correo_academia').val());
        var telefono = $.trim($('#input_phone_academia').val());
        var director = $.trim($("#input_director_academia").val());
        var direccion = $.trim($('#input_direccion_academia').val());

        var ciudadValue = $('#select_ciudades_academia').val();
        var ciudadText = $('#select_ciudades_academia option:selected').text();

        var estado = $('#switch_estado_academia').prop('checked') ? 1 : 0;
        var estado_text;

        if(estado == 0)
        {
            estado_text = 'Inactivo';
        }
        else if(estado == 1)
        {
            estado_text = 'Activo';
        }

        $.ajax({
            url: 'Http/Controllers/AcademiaController.php',
            type: 'POST',
            data: {
                academia: academia,
                correo: correo,
                telefono: telefono,
                director: director,
                direccion: direccion,
                estado: estado,
                ciudadValue: ciudadValue,
                opcion: opcion,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                $.ajax({
                   // url: 'http://127.0.0.1:8000//refresh/academias', 
                    type: 'GET',
                    success: function() {
                        //console.log('Evento de actualización enviado para academias');
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error al emitir evento: ' + error);
                    }
                });

                if(opcion == 3)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'La academia <strong>' + academia + '</strong> ha sido agregada con éxito.'
                    });

                    window.tabla_academias.ajax.reload();

                    $.ajax({
                        url: 'Http/Controllers/AcademiaController.php',
                        type: 'POST',
                        data: {
                            academia: academia,
                            correo: correo,
                            telefono: telefono,
                            director: director,
                            direccion: direccion,
                            ciudadText: ciudadText,
                            estado_text: estado_text,
                            opcion: 4,
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

                    $("#modal_academias").modal("hide");
                }
                else if(opcion == 6)
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        html: 'La academia <strong>' + academia + '</strong> ha sido actualizada con éxito.'
                    });
                    
                    window.tabla_academias.ajax.reload();

                    $.ajax({
                        url: 'Http/Controllers/AcademiaController.php',
                        type: 'POST',
                        data: {
                            academia: academia,
                            correo: correo,
                            telefono: telefono,
                            director: director,
                            direccion: direccion,
                            ciudadText: ciudadText,
                            estado_text: estado_text,
                            opcion: 7,
                            mail_log: mail_log,
                            prev_academia: prev_academia,
                            prev_correo: prev_correo,
                            prev_telefono: prev_telefono,
                            prev_director: prev_director,
                            prev_direccion: prev_direccion,
                            prev_ciudadText: prev_ciudadText,
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
                    $("#modal_academias").modal("hide");
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