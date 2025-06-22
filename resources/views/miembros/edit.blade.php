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
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Editar Miembro</h2>
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="form-editar-miembro" method="POST" action="{{ route('miembros.update', $miembro) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-1">Cédula</label>
                    <input type="text" name="cedula" value="{{ old('cedula', $miembro->cedula) }}" class="form-input w-full rounded border-gray-300" required readonly>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Nombres</label>
                    <input type="text" name="nombres" value="{{ old('nombres', $miembro->nombres) }}" class="form-input w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Apellidos</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $miembro->apellidos) }}" class="form-input w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Sexo</label>
                    <select name="sexo" class="form-input w-full rounded border-gray-300" required>
                        <option value="M" @if(old('sexo', $miembro->sexo) == 'M') selected @endif>Masculino</option>
                        <option value="F" @if(old('sexo', $miembro->sexo) == 'F') selected @endif>Femenino</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $miembro->fecha_nacimiento ? \Carbon\Carbon::parse($miembro->fecha_nacimiento)->format('Y-m-d') : '') }}" class="form-input w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Fecha de inscripción</label>
                    <input type="date" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', $miembro->fecha_inscripcion ? \Carbon\Carbon::parse($miembro->fecha_inscripcion)->format('Y-m-d') : '') }}" class="form-input w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Estado</label>
                    <select name="estado_miembro" class="form-input w-full rounded border-gray-300" required>
                        <option value="1" @if(old('estado_miembro', $miembro->estado_miembro)) selected @endif>Activo</option>
                        <option value="0" @if(!old('estado_miembro', $miembro->estado_miembro)) selected @endif>Inactivo</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Academia</label>
                    <select name="academia_id" class="form-input w-full rounded border-gray-300" required>
                        <option value="">-</option>
                        @foreach($academias as $academia)
                            <option value="{{ $academia->id_academia }}" @if(old('academia_id', $miembro->academia_id) == $academia->id_academia) selected @endif>{{ $academia->nombre_academia }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Correo</label>
                    <select name="correo_sistema_id" class="form-input w-full rounded border-gray-300">
                        <option value="">Sin correo asignado</option>
                        @foreach(App\Models\User::active()->get() as $user)
                            <option value="{{ $user->correo }}" @if(old('correo_sistema_id', $miembro->correo_sistema_id) == $user->correo) selected @endif>
                                {{ $user->correo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <a href="{{ route('miembros.show', $miembro) }}" class="mr-4 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection 