<!-- Modal de Búsqueda de Partidas -->
<div class="modal fade" id="busquedaPartidasModal" tabindex="-1" role="dialog" aria-labelledby="busquedaPartidasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="busquedaPartidasModalLabel">Búsqueda de Partidas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formBusquedaPartidas">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="torneo_id">Torneo</label>
                            <select class="form-control" id="torneo_id" name="torneo_id">
                                <option value="">Todos los torneos</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ronda">Ronda</label>
                            <input type="number" class="form-control" id="ronda" name="ronda" min="1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="jugador">Jugador</label>
                            <input type="text" class="form-control" id="jugador" name="jugador" placeholder="Nombre del jugador">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="resultado">Resultado</label>
                            <select class="form-control" id="resultado" name="resultado">
                                <option value="">Todos los resultados</option>
                                <option value="1-0">Victoria Blancas (1-0)</option>
                                <option value="0-1">Victoria Negras (0-1)</option>
                                <option value="1/2-1/2">Tablas (½-½)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fecha_desde">Fecha Desde</label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="fecha_hasta">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="buscarPartidas()">Buscar</button>
            </div>
        </div>
    </div>
</div>

<script>
function buscarPartidas() {
    const formData = new FormData(document.getElementById('formBusquedaPartidas'));
    
    fetch('/partidas/buscar', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la tabla de resultados
            actualizarTablaPartidas(data.partidas);
            $('#busquedaPartidasModal').modal('hide');
        } else {
            alert('Error en la búsqueda: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la búsqueda');
    });
}

function actualizarTablaPartidas(partidas) {
    // Implementar la actualización de la tabla con los resultados
    const tabla = document.querySelector('#tablaPartidas tbody');
    tabla.innerHTML = '';
    
    partidas.forEach(partida => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${partida.torneo}</td>
            <td>${partida.ronda}</td>
            <td>${partida.jugador_blancas}</td>
            <td>${partida.jugador_negras}</td>
            <td>${partida.resultado}</td>
            <td>${partida.fecha}</td>
        `;
        tabla.appendChild(fila);
    });
}
</script>