@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Equipos del Torneo</h1>
        <a href="{{ route('torneos.show', $torneo->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Torneo
        </a>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 w-12">No.</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Nombre del Equipo</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Capitán</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Federación</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Jugadores</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipos as $index => $equipo)
                <tr>
                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                    <td class="px-3 py-2">{{ $equipo->nombre }}</td>
                    <td class="px-3 py-2">{{ $equipo->capitan ? $equipo->capitan->nombres . ' ' . $equipo->capitan->apellidos : '-' }}</td>
                    <td class="px-3 py-2">{{ $equipo->federacion ?? '-' }}</td>
                    <td class="px-3 py-2">
                        @if($equipo->jugadores && $equipo->jugadores->count())
                            <ul class="list-disc ml-4">
                                @foreach($equipo->jugadores as $jugador)
                                    <li>{{ $jugador->miembro->nombres }} {{ $jugador->miembro->apellidos }} (Tablero {{ $jugador->tablero }})</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-400">Sin jugadores</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 space-x-2">
                        <a href="{{ route('equipos.show', [$torneo->id, $equipo->id]) }}" title="Ver" class="inline-block text-blue-600 hover:text-blue-800 text-xl align-middle">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('equipos.edit', [$torneo->id, $equipo->id]) }}" title="Editar" class="inline-block text-yellow-600 hover:text-yellow-800 text-xl align-middle">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button title="Bloquear" class="inline-block text-black hover:text-gray-700 text-xl align-middle cursor-not-allowed" disabled>
                            <i class="fas fa-ban"></i>
                        </button>
                        <form action="#" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Eliminar" class="inline-block text-red-600 hover:text-red-800 text-xl align-middle" onclick="return confirm('¿Eliminar equipo?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-3 py-2 text-center text-gray-400">No hay equipos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 