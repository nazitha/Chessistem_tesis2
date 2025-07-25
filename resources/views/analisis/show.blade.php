@extends('layouts.app')
@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Análisis de Partida #{{ $analisis->partida_id }}</h5>
            <a href="{{ route('analisis.index') }}" class="btn btn-light btn-sm">Volver a análisis</a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Blancas:</strong> {{ $analisis->jugadorBlancas->nombre ?? $analisis->jugador_blancas_id }}<br>
                    <strong>Negras:</strong> {{ $analisis->jugadorNegras->nombre ?? $analisis->jugador_negras_id }}<br>
                </div>
                <div class="col-md-6">
                    <strong>Evaluación general:</strong> {{ $analisis->evaluacion_general }}
                </div>
            </div>
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>Errores</th>
                        <th>Brillantes</th>
                        <th>Blunders</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="bg-light">Blancas</th>
                        <td class="bg-warning">{{ $analisis->errores_blancas }}</td>
                        <td class="bg-success text-white">{{ $analisis->brillantes_blancas }}</td>
                        <td class="bg-danger text-white">{{ $analisis->blunders_blancas }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Negras</th>
                        <td class="bg-warning">{{ $analisis->errores_negras }}</td>
                        <td class="bg-success text-white">{{ $analisis->brillantes_negras }}</td>
                        <td class="bg-danger text-white">{{ $analisis->blunders_negras }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4">
                <h6>Movimientos (PGN/FEN):</h6>
                <pre class="bg-light p-2">{{ $analisis->movimientos }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection 