@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="flex justify-start mb-4">
        <a href="{{ route('miembros.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-sm font-medium rounded-md text-gray-700 border border-gray-300 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Nuevo Miembro</h2>
        
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('miembros.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-1">Cédula *</label>
                    <input type="text" name="cedula" value="{{ old('cedula') }}" 
                           class="form-input w-full rounded border-gray-300" required>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Nombres *</label>
                    <input type="text" name="nombres" value="{{ old('nombres') }}" 
                           class="form-input w-full rounded border-gray-300" required>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Apellidos *</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos') }}" 
                           class="form-input w-full rounded border-gray-300" required>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Sexo *</label>
                    <select name="sexo" class="form-select w-full rounded border-gray-300" required>
                        <option value="">Seleccione...</option>
                        <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Fecha de nacimiento *</label>
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" 
                           class="form-input w-full rounded border-gray-300" required>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Fecha de inscripción *</label>
                    <input type="date" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', date('Y-m-d')) }}" 
                           class="form-input w-full rounded border-gray-300" required>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Estado *</label>
                    <select name="estado_miembro" class="form-select w-full rounded border-gray-300" required>
                        <option value="1" {{ old('estado_miembro', '1') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estado_miembro') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Academia *</label>
                    <select name="academia_id" class="form-select w-full rounded border-gray-300" required>
                        <option value="">Seleccione...</option>
                        @foreach($academias as $academia)
                            <option value="{{ $academia->id_academia }}" {{ old('academia_id') == $academia->id_academia ? 'selected' : '' }}>
                                {{ $academia->nombre_academia }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">ELO</label>
                    <input type="number" name="elo" value="{{ old('elo') }}" 
                           class="form-input w-full rounded border-gray-300" 
                           placeholder="Ej: 1500" min="0" max="3000">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Correo del sistema</label>
                    <select name="correo_sistema_id" class="form-select w-full rounded border-gray-300">
                        <option value="">Sin correo asignado</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->correo }}" {{ old('correo_sistema_id') == $usuario->correo ? 'selected' : '' }}>
                                {{ $usuario->correo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('miembros.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 