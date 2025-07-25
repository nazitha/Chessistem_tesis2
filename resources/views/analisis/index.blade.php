@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">Análisis de Partidas</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="analisis-table">
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
                @foreach($analisis as $a)
                <tr>
                    <td>{{ $a->created_at->format('Y-m-d') }}</td>
                    <td>{{ $a->jugadorBlancas->nombre ?? '-' }}</td>
                    <td>{{ $a->jugadorNegras->nombre ?? '-' }}</td>
                    <td><span class="badge bg-danger">B: {{ $a->errores_blancas }}</span> <span class="badge bg-warning text-dark">N: {{ $a->errores_negras }}</span></td>
                    <td><span class="badge bg-success">B: {{ $a->brillantes_blancas }}</span> <span class="badge bg-info text-dark">N: {{ $a->brillantes_negras }}</span></td>
                    <td><span class="badge bg-dark">B: {{ $a->blunders_blancas }}</span> <span class="badge bg-secondary">N: {{ $a->blunders_negras }}</span></td>
                    <td>{{ Str::limit($a->evaluacion_general, 40) }}</td>
                    <td><a href="{{ route('analisis.show', $a->id) }}" class="btn btn-sm btn-outline-primary">Ver</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $analisis->links() }}</div>
</div>
@endsection
@push('scripts')
<script>
$(function() {
    $('#analisis-table').DataTable({
        paging: false,
        searching: true,
        info: false,
        ordering: true
    });
});
</script>
@endpush 