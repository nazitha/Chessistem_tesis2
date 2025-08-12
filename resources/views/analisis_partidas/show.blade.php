@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Análisis de la Partida</h2>
        
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Jugadores</div>
                <div class="card-body">
                    <strong>Blancas:</strong> {{ $analisis->jugador_blancas_nombre }}<br>
                    <strong>Negras:</strong> {{ $analisis->jugador_negras_nombre }}<br>
                    <strong>Fecha:</strong> {{ $analisis->created_at->format('Y-m-d') }}
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Evaluación General</div>
                <div class="card-body">
                    {{ $analisis->evaluacion_general }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Errores y Aciertos</div>
                <div class="card-body">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Blancas</th>
                                <th>Negras</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Errores</th>
                                <td class="bg-danger text-white">{{ $analisis->errores_blancas }}</td>
                                <td class="bg-primary text-white">{{ $analisis->errores_negras }}</td>
                            </tr>
                            <tr>
                                <th>Brillantes</th>
                                <td class="bg-success text-white">{{ $analisis->brillantes_blancas }}</td>
                                <td class="bg-success text-white">{{ $analisis->brillantes_negras }}</td>
                            </tr>
                            <tr>
                                <th>Blunders</th>
                                <td class="bg-warning text-dark">{{ $analisis->blunders_blancas }}</td>
                                <td class="bg-warning text-dark">{{ $analisis->blunders_negras }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('analisis.index') }}" class="btn btn-secondary">Volver al listado</a>

    <!-- Modal Nuevo Análisis -->
    <div class="modal fade" id="nuevoAnalisisModal" tabindex="-1" role="dialog" aria-labelledby="nuevoAnalisisModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="nuevoAnalisisModalLabel">Nuevo Análisis</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs" id="analisisTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="existente-tab" data-toggle="tab" href="#existente" role="tab" aria-controls="existente" aria-selected="true">Partida Existente</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="manual-tab" data-toggle="tab" href="#manual" role="tab" aria-controls="manual" aria-selected="false">Pegar/Cargar PGN</a>
              </li>
            </ul>
            <div class="tab-content mt-3" id="analisisTabContent">
              <!-- Opción 1: Seleccionar partida existente -->
              <div class="tab-pane fade show active" id="existente" role="tabpanel" aria-labelledby="existente-tab">
                <form id="formAnalizarExistente">
                  <div class="form-group">
                    <label for="partidaExistenteSelect">Selecciona una partida</label>
                    <select class="form-control" id="partidaExistenteSelect" name="partida_id" required>
                      <option value="">-- Selecciona --</option>
                      <!-- Opciones se llenarán por JS -->
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary">Analizar</button>
                </form>
              </div>
              <!-- Opción 2: Pegar/Cargar PGN -->
              <div class="tab-pane fade" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                <form id="formAnalizarManual">
                  <div class="form-group">
                    <label for="pgnManual">Pega el PGN aquí</label>
                    <textarea class="form-control" id="pgnManual" name="pgn" rows="5" required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Analizar PGN</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    // Poblar el select de partidas existentes
    $('#nuevoAnalisisModal').on('show.bs.modal', function() {
        $.get('/api/partidas-con-movimientos', function(data) {
            var select = $('#partidaExistenteSelect');
            select.empty();
            select.append('<option value="">-- Selecciona --</option>');
            data.forEach(function(p) {
                let desc = `Partida #${p.no_partida} (Participante: ${p.participante_id}, Torneo: ${p.torneo_id})`;
                select.append(`<option value="${p.no_partida}">${desc}</option>`);
            });
        });
    });

    // Enviar análisis de partida existente
    $('#formAnalizarExistente').submit(function(e) {
        e.preventDefault();
        var partidaId = $('#partidaExistenteSelect').val();
        if(!partidaId) return;
        var btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).text('Analizando...');
        $.post('/analisis-partida', {
            partida_id: partidaId,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(resp) {
            btn.text('Ver Análisis').removeClass('btn-primary').addClass('btn-success');
            btn.off('click').on('click', function() {
                window.location.href = '/analisis-partidas/' + resp.analisis_id;
            });
        }).fail(function(xhr) {
            btn.prop('disabled', false).text('Analizar');
            let msg = 'Error al analizar la partida';
            if(xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
            alert(msg);
        });
    });

    // Enviar análisis de PGN manual
    $('#formAnalizarManual').submit(function(e) {
        e.preventDefault();
        var pgn = $('#pgnManual').val();
        if(!pgn) return;
        var btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).text('Analizando...');
        $.post('/analisis-partidas', {
            movimientos: pgn,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(resp) {
            btn.text('Ver Análisis').removeClass('btn-primary').addClass('btn-success');
            btn.off('click').on('click', function() {
                window.location.href = '/analisis-partidas/' + resp.analisis_id;
            });
        }).fail(function(xhr) {
            btn.prop('disabled', false).text('Analizar PGN');
            let msg = 'Error al analizar el PGN';
            if(xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
            alert(msg);
        });
    });
});
</script>
@endpush 