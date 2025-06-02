@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Evaluaciones</h1>
        <p class="mt-2 text-gray-600">Gestión de evaluaciones de torneos</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center py-8">
            <i class="fas fa-clipboard-check text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">No hay evaluaciones pendientes en este momento.</p>
        </div>
    </div>
</div>
@endsection 