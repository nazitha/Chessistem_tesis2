@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Lista de Torneos</h2>
    <table id="torneosTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/tables/torneos.js') }}"></script>
@endpush
