@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Análisis de Partidas</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoAnalisisModal">
            <i class="fas fa-plus me-2"></i>Nuevo Análisis
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200" id="analisisTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blancas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Negras</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Errores</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brillantes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blunders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($analisis as $a)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $a->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $a->jugador_blancas_nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $a->jugador_negras_nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 mr-1">B: {{ $a->errores_blancas }}</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">N: {{ $a->errores_negras }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 mr-1">B: {{ $a->brillantes_blancas }}</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">N: {{ $a->brillantes_negras }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-1">B: {{ $a->blunders_blancas }}</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">N: {{ $a->blunders_negras }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ \Illuminate\Support\Str::limit($a->evaluacion_general, 40) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('analisis.show', $a->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye mr-1"></i>Ver
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($analisis->hasPages())
        <div class="mt-4 px-6 py-3 bg-white border-t border-gray-200">
            {{ $analisis->links() }}
        </div>
    @endif
</div>

<!-- Modal Nuevo Análisis -->
<div class="modal fade" id="nuevoAnalisisModal" tabindex="-1" aria-labelledby="nuevoAnalisisModalLabel" aria-hidden="true">
  <div class="modal-dialog">
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
              <div class="list-group" id="analisisRecientesList">
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
    console.log('Script de análisis cargado');
    
    // Verificar que el botón existe
    if ($('button[data-target="#nuevoAnalisisModal"]').length > 0) {
        console.log('Botón Nuevo Análisis encontrado');
    } else {
        console.log('Botón Nuevo Análisis NO encontrado');
    }
    
    // Verificar que el modal existe
    if ($('#nuevoAnalisisModal').length > 0) {
        console.log('Modal encontrado');
    } else {
        console.log('Modal NO encontrado');
    }
    
    // Agregar evento de clic manual si es necesario
    $('button[data-target="#nuevoAnalisisModal"]').on('click', function() {
        console.log('Botón Nuevo Análisis clickeado');
        $('#nuevoAnalisisModal').modal('show');
    });
});
</script>
@endpush 