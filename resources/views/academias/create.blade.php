@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('academias.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-sm font-medium rounded-md text-gray-700 border border-gray-300 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Nueva Academia</h2>

        @if($errors->any())
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('academias.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre_academia" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="nombre_academia" id="nombre_academia" value="{{ old('nombre_academia') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                           required>
                </div>

                <div>
                    <label for="correo_academia" class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                    <input type="email" name="correo_academia" id="correo_academia" value="{{ old('correo_academia') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                           required>
                </div>

                <div>
                    <label for="telefono_academia" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono_academia" id="telefono_academia" value="{{ old('telefono_academia') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                           required>
                </div>

                <div>
                    <label for="representante_academia" class="block text-sm font-medium text-gray-700 mb-1">Representante</label>
                    <input type="text" name="representante_academia" id="representante_academia" value="{{ old('representante_academia') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                           required>
                </div>

                <div>
                    <label for="direccion_academia" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion_academia" id="direccion_academia" value="{{ old('direccion_academia') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                           required>
                </div>

                <div>
                    <label for="ciudad_id" class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <select name="ciudad_id" id="ciudad_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                            required>
                        <option value="">Seleccione una ciudad</option>
                        @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad->id_ciudad }}" {{ old('ciudad_id') == $ciudad->id_ciudad ? 'selected' : '' }}>
                                {{ $ciudad->nombre_ciudad }}, {{ $ciudad->departamento->nombre_depto ?? '-' }} ({{ $ciudad->departamento->pais->nombre_pais ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="estado_academia" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado_academia" id="estado_academia"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                            required>
                        <option value="1" {{ old('estado_academia', '1') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estado_academia') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Academia
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 