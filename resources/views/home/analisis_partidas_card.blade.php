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
                    @foreach($partidasAnalisis->take(2) as $analisis)
                    <tr>
                        <td>{{ $analisis->created_at->format('Y-m-d') }}</td>
                        <td>{{ $analisis->jugador_blancas_nombre }}</td>
                        <td>{{ $analisis->jugador_negras_nombre }}</td>
                        <td>
                            <span class="badge bg-danger me-1">B: {{ $analisis->errores_blancas }}</span>
                            <span class="badge bg-info">N: {{ $analisis->errores_negras }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success me-1">B: {{ $analisis->brillantes_blancas }}</span>
                            <span class="badge bg-success">N: {{ $analisis->brillantes_negras }}</span>
                        </td>
                        <td>
                            <span class="badge bg-warning me-1">B: {{ $analisis->blunders_blancas }}</span>
                            <span class="badge bg-warning">N: {{ $analisis->blunders_negras }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($analisis->evaluacion_general, 50) }}</td>
                        <td>
                            <a href="{{ route('analisis.show', $analisis->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-1"></i>Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nuevo Análisis -->
<div class="modal fade" id="nuevoAnalisisModal" tabindex="-1" aria-labelledby="nuevoAnalisisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevoAnalisisModalLabel">Nuevo Análisis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="analisisTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="existente-tab" data-bs-toggle="tab" data-bs-target="#existente" type="button" role="tab" aria-controls="existente" aria-selected="true">Partida Existente</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="agregar-movimientos-tab" data-bs-toggle="tab" data-bs-target="#agregar-movimientos" type="button" role="tab" aria-controls="agregar-movimientos" aria-selected="false">Agregar Movimientos</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="false">Pegar/Cargar PGN</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="analisisTabContent">
                    <!-- Tab Partida Existente -->
                    <div class="tab-pane fade show active" id="existente" role="tabpanel" aria-labelledby="existente-tab">
                        <div class="mt-3">
                            <label for="partidaSelect" class="form-label">Selecciona una partida con movimientos</label>
                            <select class="form-select" id="partidaSelect">
                                <option value="">-- Selecciona una partida --</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Tab Agregar Movimientos -->
                    <div class="tab-pane fade" id="agregar-movimientos" role="tabpanel" aria-labelledby="agregar-movimientos-tab">
                        <div class="mt-3">
                            <label for="partidaSinMovimientosSelect" class="form-label">Selecciona una partida sin movimientos</label>
                            <select class="form-select" id="partidaSinMovimientosSelect">
                                <option value="">-- Selecciona una partida --</option>
                            </select>
                            <div class="mt-3">
                                <label for="movimientosPartida" class="form-label">Agrega los movimientos (PGN)</label>
                                <textarea class="form-control" id="movimientosPartida" rows="5" placeholder="1.e4 e5 2.Nf3 Nc6 3.Bb5 a6 4.Ba4 Nf6 5.O-O Be7 6.Re1 b5 7.Bb3 d6 8.c3 O-O 9.h3 Nb8 10.d4 Nbd7 1/2-1/2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab PGN Manual -->
                    <div class="tab-pane fade" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                        <div class="mt-3">
                            <label for="pgnManual" class="form-label">Pega tu PGN aquí</label>
                            <textarea class="form-control" id="pgnManual" rows="8" placeholder="1.e4 e5 2.Nf3 Nc6 3.Bb5 a6 4.Ba4 Nf6 5.O-O Be7 6.Re1 b5 7.Bb3 d6 8.c3 O-O 9.h3 Nb8 10.d4 Nbd7 1/2-1/2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAnalizar">Analizar</button>
            </div>
        </div>
    </div>
</div> 