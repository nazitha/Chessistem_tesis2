$(document).ready(function() {
    console.log('Script de análisis cargado');
    
    // Poblar los selects cuando se abre el modal
    $('#nuevoAnalisisModal').on('show.bs.modal', function() {
        console.log('Modal abriéndose, cargando datos...');
        
        // Cargar análisis recientes
        $.get('/api/analisis-recientes', function(data) {
            var list = $('#analisisRecientesList');
            list.empty();
            
            if (data.length === 0) {
                list.append('<div class="text-center text-muted p-3">No hay análisis recientes disponibles</div>');
            } else {
                data.forEach(function(analisis) {
                    let item = `
                        <div class="list-group-item list-group-item-action" data-analisis-id="${analisis.id}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${analisis.jugador_blancas_nombre} vs ${analisis.jugador_negras_nombre}</h6>
                                <small class="text-muted">${analisis.created_at}</small>
                            </div>
                            <p class="mb-1">Errores: B:${analisis.errores_blancas} N:${analisis.errores_negras} | Brillantes: B:${analisis.brillantes_blancas} N:${analisis.brillantes_negras}</p>
                            <small class="text-muted">${analisis.evaluacion_general.substring(0, 100)}...</small>
                        </div>
                    `;
                    list.append(item);
                });
            }
            console.log('Análisis recientes cargados:', data.length);
        }).fail(function(xhr, status, error) {
            console.error('Error al cargar análisis recientes:', error);
            var list = $('#analisisRecientesList');
            list.empty();
            list.append('<div class="text-center text-danger p-3">Error al cargar análisis recientes</div>');
        });

        // Cargar partidas CON movimientos
        $.get('/api/partidas-con-movimientos', function(data) {
            var select = $('#partidaExistenteSelect');
            select.empty();
            select.append('<option value="">-- Selecciona una partida --</option>');
            
            if (data.length === 0) {
                select.append('<option value="" disabled>No hay partidas con movimientos disponibles</option>');
            } else {
                data.forEach(function(partida) {
                    let desc = `Partida #${partida.no_partida} (Torneo: ${partida.torneo_id})`;
                    select.append(`<option value="${partida.no_partida}">${desc}</option>`);
                });
            }
            console.log('Partidas con movimientos cargadas:', data.length);
        }).fail(function(xhr, status, error) {
            console.error('Error al cargar partidas con movimientos:', error);
            var select = $('#partidaExistenteSelect');
            select.empty();
            select.append('<option value="">Error al cargar partidas</option>');
        });

        // Cargar partidas SIN movimientos
        $.get('/api/partidas-sin-movimientos', function(data) {
            var select = $('#partidaSinMovimientosSelect');
            select.empty();
            select.append('<option value="">-- Selecciona una partida --</option>');
            
            if (data.length === 0) {
                select.append('<option value="" disabled>No hay partidas sin movimientos disponibles</option>');
            } else {
                data.forEach(function(partida) {
                    let desc = `Partida #${partida.no_partida} (Torneo: ${partida.torneo_id})`;
                    select.append(`<option value="${partida.no_partida}">${desc}</option>`);
                });
            }
            console.log('Partidas sin movimientos cargadas:', data.length);
        }).fail(function(xhr, status, error) {
            console.error('Error al cargar partidas sin movimientos:', error);
            var select = $('#partidaSinMovimientosSelect');
            select.empty();
            select.append('<option value="">Error al cargar partidas</option>');
        });
    });

    // Hacer clic en un análisis reciente
    $(document).on('click', '#analisisRecientesList .list-group-item', function() {
        var analisisId = $(this).data('analisis-id');
        console.log('Análisis seleccionado:', analisisId);
        
        // Redirigir a la vista de detalles del análisis
        window.location.href = '/analisis-partidas/' + analisisId;
    });

    // Enviar análisis de partida existente
    $('#formAnalizarExistente').submit(function(e) {
        e.preventDefault();
        console.log('Enviando análisis de partida existente...');
        
        var partidaId = $('#partidaExistenteSelect').val();
        
        if (!partidaId) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor selecciona una partida',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        var btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).text('Analizando...');
        
        $.ajax({
            url: '/analisis-partidas',
            method: 'POST',
            data: {
                partida_id: partidaId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(resp) {
                console.log('Análisis exitoso:', resp);
                btn.prop('disabled', false).text('Analizar');
                
                // Cerrar modal usando Bootstrap 5
                var modal = bootstrap.Modal.getInstance(document.getElementById('nuevoAnalisisModal'));
                if (modal) {
                    modal.hide();
                }
                
                Swal.fire({
                    title: '¡Análisis completado!',
                    text: 'La partida ha sido analizada exitosamente',
                    icon: 'success',
                    confirmButtonText: 'Ver Análisis',
                    cancelButtonText: 'Cerrar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/analisis-partidas/' + resp.analisis_id;
                    } else {
                        // Recargar la página para mostrar el nuevo análisis
                        window.location.reload();
                    }
                });
            },
            error: function(xhr) {
                console.error('Error en análisis:', xhr);
                btn.prop('disabled', false).text('Analizar');
                let msg = 'Error al analizar la partida';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                
                Swal.fire({
                    title: 'Error',
                    text: msg,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Agregar movimientos con información de jugadores
    $('#formAgregarMovimientos').submit(function(e) {
        e.preventDefault();
        console.log('Enviando análisis con información de jugadores...');
        
        var jugadorBlancas = $('#jugadorBlancas').val();
        var jugadorNegras = $('#jugadorNegras').val();
        var fechaPartida = $('#fechaPartida').val();
        var movimientos = $('#movimientosPartida').val();
        
        if (!jugadorBlancas || !jugadorNegras) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor ingresa los nombres de ambos jugadores',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        if (!fechaPartida) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor selecciona la fecha de la partida',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        if (!movimientos || movimientos.trim().length < 10) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor ingresa los movimientos en formato PGN (mínimo 10 caracteres)',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        var btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).text('Guardando y Analizando...');
        
        $.ajax({
            url: '/analisis-partidas',
            method: 'POST',
            data: {
                jugador_blancas: jugadorBlancas,
                jugador_negras: jugadorNegras,
                fecha_partida: fechaPartida,
                pgn_manual: movimientos,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(resp) {
                console.log('Análisis creado exitosamente:', resp);
                btn.prop('disabled', false).text('Guardar y Analizar');
                
                // Cerrar modal usando Bootstrap 5
                var modal = bootstrap.Modal.getInstance(document.getElementById('nuevoAnalisisModal'));
                if (modal) {
                    modal.hide();
                }
                
                Swal.fire({
                    title: '¡Análisis completado!',
                    text: 'La partida ha sido analizada exitosamente',
                    icon: 'success',
                    confirmButtonText: 'Ver Análisis',
                    cancelButtonText: 'Cerrar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/analisis-partidas/' + resp.analisis_id;
                    } else {
                        // Recargar la página para mostrar el nuevo análisis
                        location.reload();
                    }
                });
            },
            error: function(xhr) {
                console.error('Error al crear análisis:', xhr);
                btn.prop('disabled', false).text('Guardar y Analizar');
                
                var errorMsg = 'Error al crear el análisis';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Enviar análisis de PGN manual
    $('#formAnalizarManual').submit(function(e) {
        e.preventDefault();
        console.log('Enviando análisis de PGN manual...');
        
        var pgn = $('#pgnManual').val();
        
        if (!pgn.trim()) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor ingresa un PGN válido',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        var btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).text('Analizando...');
        
        $.ajax({
            url: '/analisis-partidas',
            method: 'POST',
            data: {
                pgn_manual: pgn,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(resp) {
                console.log('Análisis PGN exitoso:', resp);
                btn.prop('disabled', false).text('Analizar PGN');
                
                // Cerrar modal usando Bootstrap 5
                var modal = bootstrap.Modal.getInstance(document.getElementById('nuevoAnalisisModal'));
                if (modal) {
                    modal.hide();
                }
                
                Swal.fire({
                    title: '¡Análisis completado!',
                    text: 'El PGN ha sido analizado exitosamente',
                    icon: 'success',
                    confirmButtonText: 'Ver Análisis',
                    cancelButtonText: 'Cerrar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/analisis-partidas/' + resp.analisis_id;
                    } else {
                        // Recargar la página para mostrar el nuevo análisis
                        window.location.reload();
                    }
                });
            },
            error: function(xhr) {
                console.error('Error en análisis PGN:', xhr);
                btn.prop('disabled', false).text('Analizar PGN');
                let msg = 'Error al analizar el PGN';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                
                Swal.fire({
                    title: 'Error',
                    text: msg,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Limpiar formularios cuando se cierra el modal
    $('#nuevoAnalisisModal').on('hidden.bs.modal', function() {
        console.log('Modal cerrado, limpiando formularios...');
        $('#formAnalizarExistente')[0].reset();
        $('#formAnalizarManual')[0].reset();
        $('#partidaExistenteSelect').empty();
    });

    // Verificar que todo esté funcionando
    console.log('Verificando elementos...');
    if ($('button[data-bs-target="#nuevoAnalisisModal"]').length > 0) {
        console.log('✅ Botón Nuevo Análisis encontrado');
    } else {
        console.log('❌ Botón Nuevo Análisis NO encontrado');
    }
    
    if ($('#nuevoAnalisisModal').length > 0) {
        console.log('✅ Modal encontrado');
    } else {
        console.log('❌ Modal NO encontrado');
    }
}); 