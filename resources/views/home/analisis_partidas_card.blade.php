<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Análisis de Partidas</h5>

            <div>
                <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#nuevoAnalisisModal">
                    <i class="fas fa-plus me-1"></i>Nuevo Análisis
                </button>
                <a href="{{ route('analisis.index') }}" class="btn btn-outline-primary btn-sm">Ver más análisis</a>
            </div>

        </div>
<div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Blancas</th>
                        <th>Negras</th>

                        <th>Errores</th>
                        <th>Brillantes</th>
                        <th>Blunders</th>
                        <th>Evaluación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partidasAnalisis->take(2) as $analisis)
                    <tr>
                        <td>{{ $analisis->created_at ? $analisis->created_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $analisis->jugador_blancas_nombre ?? 'N/A' }}</td>
                        <td>{{ $analisis->jugador_negras_nombre ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-danger me-1">B: {{ $analisis->errores_blancas ?? 0 }}</span>
                            <span class="badge bg-info">N: {{ $analisis->errores_negras ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success me-1">B: {{ $analisis->brillantes_blancas ?? 0 }}</span>
                            <span class="badge bg-success">N: {{ $analisis->brillantes_negras ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="badge bg-warning me-1">B: {{ $analisis->blunders_blancas ?? 0 }}</span>
                            <span class="badge bg-warning">N: {{ $analisis->blunders_negras ?? 0 }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($analisis->evaluacion_general ?? 'Sin evaluación', 50) }}</td>
                        <td>
                            <a href="{{ route('analisis.show', $analisis->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-1"></i>Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay análisis de partidas disponibles
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Nuevo Análisis -->
<div class="modal fade" id="nuevoAnalisisModal" tabindex="-1" aria-labelledby="nuevoAnalisisModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="nuevoAnalisisModalLabel">Nuevo Análisis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="analisisTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="analisis-recientes-tab" data-bs-toggle="tab" data-bs-target="#analisis-recientes" type="button" role="tab" aria-controls="analisis-recientes" aria-selected="true">Análisis Recientes</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="agregar-movimientos-tab" data-bs-toggle="tab" data-bs-target="#agregar-movimientos" type="button" role="tab" aria-controls="agregar-movimientos" aria-selected="false">Agregar Movimientos</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="false">Pegar/Cargar PGN</button>
          </li>
        </ul>
        <div class="tab-content mt-3" id="analisisTabContent">
          <!-- Tab Análisis Recientes -->
          <div class="tab-pane fade show active" id="analisis-recientes" role="tabpanel" aria-labelledby="analisis-recientes-tab">
            <div class="mb-3">
              <label class="form-label">Selecciona un análisis reciente para ver detalles:</label>
              <div class="list-group" id="analisisRecientesList" style="max-height: 60vh; overflow:auto;">
                <!-- Se llenará dinámicamente -->
              </div>
            </div>
          </div>
          
          <!-- Opción 2: Agregar movimientos con información de jugadores -->
          <div class="tab-pane fade" id="agregar-movimientos" role="tabpanel" aria-labelledby="agregar-movimientos-tab">
            <form id="formAgregarMovimientos">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="jugadorBlancas" class="form-label">Jugador con Piezas Blancas</label>
                    <input type="text" class="form-control" id="jugadorBlancas" name="jugador_blancas" placeholder="Nombre del jugador" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="jugadorNegras" class="form-label">Jugador con Piezas Negras</label>
                    <input type="text" class="form-control" id="jugadorNegras" name="jugador_negras" placeholder="Nombre del jugador" required>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label for="fechaPartida" class="form-label">Fecha de la Partida</label>
                <input type="date" class="form-control" id="fechaPartida" name="fecha_partida" required>
              </div>
              <div class="mb-3">
                <label for="movimientosPartida" class="form-label">Agrega los movimientos (PGN)</label>
                <textarea class="form-control" id="movimientosPartida" name="movimientos" rows="5" required placeholder="Ejemplo: 1.e4 e5 2.Nf3 Nc6 3.Bb5 a6 4.Ba4 Nf6 5.O-O Be7 6.Re1 b5 7.Bb3 d6 8.c3 O-O 9.h3 Nb8 10.d4 Nbd7 1/2-1/2"></textarea>
              </div>
              <button type="submit" class="btn btn-success">Guardar y Analizar</button>
            </form>
          </div>
          
          <!-- Opción 3: Pegar/Cargar PGN -->
          <div class="tab-pane fade" id="manual" role="tabpanel" aria-labelledby="manual-tab">
            <form id="formAnalizarManual">
              <div class="mb-3">
                <label for="pgnManual" class="form-label">Pega el PGN aquí</label>
                <textarea class="form-control" id="pgnManual" name="pgn" rows="5" required placeholder="Ejemplo: 1.e4 e5 2.Nf3 Nc6 3.Bb5 a6 4.Ba4 Nf6 5.O-O Be7 6.Re1 b5 7.Bb3 d6 8.c3 O-O 9.h3 Nb8 10.d4 Nbd7 1/2-1/2"></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Analizar PGN</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('js/actions/analisis_load.js') }}"></script>
<script>
// Script de prueba para verificar que el modal funcione
$(document).ready(function() {
    console.log('Script de análisis cargado en home');
    
    // Verificar que el botón existe
    if ($('button[data-bs-target="#nuevoAnalisisModal"]').length > 0) {
        console.log('Botón Nuevo Análisis encontrado en home');
    } else {
        console.log('Botón Nuevo Análisis NO encontrado en home');
    }
    
    // Verificar que el modal existe
    if ($('#nuevoAnalisisModal').length > 0) {
        console.log('Modal encontrado en home');
    } else {
        console.log('Modal NO encontrado en home');
    }
    
    // Agregar evento de clic manual si es necesario
    $('button[data-bs-target="#nuevoAnalisisModal"]').on('click', function() {
        console.log('Botón Nuevo Análisis clickeado en home');
        $('#nuevoAnalisisModal').modal('show');
    });
});
</script>
@endpush 