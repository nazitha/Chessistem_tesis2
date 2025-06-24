@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Auditoría del Sistema</h1>
    <form method="GET" class="mb-4 flex flex-col md:flex-row md:items-center md:space-x-2 space-y-2 md:space-y-0">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="form-input rounded border-gray-300 text-center w-32 md:w-32" />
        <select name="usuario" class="form-select rounded border-gray-300 w-36 md:w-36">
            <option value="">Todos los usuarios</option>
            @foreach($usuarios as $usuario)
                <option value="{{ $usuario }}" @if(request('usuario') == $usuario) selected @endif>{{ $usuario }}</option>
            @endforeach
        </select>
        <select name="accion" class="form-select rounded border-gray-300 w-36 md:w-36">
            <option value="">Todas las acciones</option>
            @foreach($acciones as $accion)
                <option value="{{ $accion }}" @if(request('accion') == $accion) selected @endif>{{ $accion }}</option>
            @endforeach
        </select>
        <select name="tabla" class="form-select rounded border-gray-300 w-36 md:w-36">
            <option value="">Todas las tablas</option>
            @foreach($tablas as $tabla)
                <option value="{{ $tabla }}" @if(request('tabla') == $tabla) selected @endif>{{ $tabla }}</option>
            @endforeach
        </select>
        <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-input rounded border-gray-300 text-center w-36 md:w-36" placeholder="Fecha" />
        <button type="submit" class="btn btn-primary h-10 px-4 whitespace-nowrap">Filtrar</button>
    </form>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">Fecha</th>
                    <th class="px-4 py-2">Hora</th>
                    <th class="px-4 py-2">Usuario</th>
                    <th class="px-4 py-2">Acción</th>
                    <th class="px-4 py-2">Tabla</th>
                    <th class="px-4 py-2">Equipo/IP</th>
                    <th class="px-4 py-2">Detalles</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($auditorias as $auditoria)
                <tr>
                    <td class="px-4 py-2">{{ $auditoria->fecha }}</td>
                    <td class="px-4 py-2">{{ $auditoria->hora }}</td>
                    <td class="px-4 py-2">{{ $auditoria->correo_id }}</td>
                    <td class="px-4 py-2">{{ $auditoria->accion }}</td>
                    <td class="px-4 py-2">{{ $auditoria->tabla_afectada }}</td>
                    <td class="px-4 py-2">{{ $auditoria->equipo }}</td>
                    <td class="px-4 py-2">
                        <button type="button" onclick="toggleDetalle('detalle-{{ $auditoria->id }}')" class="text-blue-600 hover:underline">Ver</button>
                    </td>
                </tr>
                <tr id="detalle-{{ $auditoria->id }}" style="display:none; background:#f9fafb;">
                    <td colspan="7" class="px-4 py-2">
                        <b>Valor previo:</b> <pre class="whitespace-pre-wrap">{{ $auditoria->valor_previo }}</pre>
                        <b>Valor posterior:</b> <pre class="whitespace-pre-wrap">{{ $auditoria->valor_posterior }}</pre>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4">No hay registros de auditoría.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $auditorias->links() }}</div>
    </div>
</div>
<script>
function toggleDetalle(id) {
    var row = document.getElementById(id);
    if (row.style.display === 'none') {
        row.style.display = '';
    } else {
        row.style.display = 'none';
    }
}
</script>
@endsection 