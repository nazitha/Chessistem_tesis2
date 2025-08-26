@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('miembros.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-sm font-medium rounded-md text-gray-700 border border-gray-300 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
        <a href="{{ route('miembros.edit', $miembro) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar Miembro
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Detalle del Miembro</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Cédula</span>
                <span class="text-gray-900">{{ $miembro->cedula }}</span>
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Nombres</span>
                <span class="text-gray-900">{{ $miembro->nombres }}</span>
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Apellidos</span>
                <span class="text-gray-900">{{ $miembro->apellidos }}</span>
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Sexo</span>
                <span class="text-gray-900">{{ $miembro->sexo == 'M' ? 'Masculino' : 'Femenino' }}</span>
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Fecha de nacimiento</span>
                <span class="text-gray-900">{{ \Carbon\Carbon::parse($miembro->fecha_nacimiento)->format('d-m-Y') }}</span>
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Fecha de inscripción</span>
                <span class="text-gray-900">{{ \Carbon\Carbon::parse($miembro->fecha_inscripcion)->format('d-m-Y') }}</span>
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Estado</span>
                @if($miembro->estado_miembro)
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Activo</span>
                @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Inactivo</span>
                @endif
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Academia</span>
                <span class="text-gray-900">{{ $miembro->academia->nombre_academia ?? '-' }}</span>
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">ELO</span>
                <span class="text-gray-900">{{ $miembro->elo ?? '-' }}</span>
            </div>
            <div>
                <span class="block font-semibold text-gray-700 mb-1">Correo</span>
                <span class="text-gray-900">{{ $miembro->correo_sistema_id ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection 