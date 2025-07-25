<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Análisis de Partidas</h5>
            <a href="{{ route('analisis.index') }}" class="btn btn-outline-primary btn-sm">Ver más análisis</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Blancas</th>
                        <th>Negras</th>
                        <th>Resultado</th>
                        <th>Análisis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partidasAnalisis as $partida)
                    <tr>
                        <td>{{ $partida['fecha'] }}</td>
                        <td>{{ $partida['blancas'] }}</td>
                        <td>{{ $partida['negras'] }}</td>
                        <td>{{ $partida['resultado'] }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalAnalisis{{ $loop->index }}">Ver análisis</button>
                            <!-- Modal -->
                            <div class="modal fade" id="modalAnalisis{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalAnalisisLabel{{ $loop->index }}" aria-hidden="true">
                              <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="modalAnalisisLabel{{ $loop->index }}">Análisis de la partida</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Resumen</h6>
                                            <ul>
                                                <li><b>Sugerencia de apertura:</b> {{ $partida['apertura'] }}</li>
                                                <li><b>Errores críticos:</b> {{ $partida['errores'] }}</li>
                                                <li><b>Jugadas clave:</b> {{ $partida['jugadas_clave'] }}</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Análisis textual</h6>
                                            <p>{{ $partida['analisis'] }}</p>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 