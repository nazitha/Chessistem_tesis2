@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use App\Services\PermissionService;
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="max-w-full mx-auto px-4">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-semibold">Miembros</h1>
        @if(PermissionHelper::canCreate('miembros'))
            <a href="{{ route('miembros.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Miembro
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mt-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700" id="success-alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700" id="error-alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cédula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellidos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sexo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de nacimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de inscripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                        @if(PermissionHelper::hasAnyMiembroActionPermission())
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($miembros as $miembro)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $miembro->cedula }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $miembro->nombres }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $miembro->apellidos }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->sexo == 'M' ? 'Masculino' : 'Femenino' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->fecha_nacimiento ? \Carbon\Carbon::parse($miembro->fecha_nacimiento)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->fecha_inscripcion ? \Carbon\Carbon::parse($miembro->fecha_inscripcion)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($miembro->estado_miembro)
                                        bg-green-100 text-green-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ $miembro->estado_miembro ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->academia->nombre_academia ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $miembro->correo_sistema_id ?? '-' }}</td>
                            @if(PermissionHelper::hasAnyMiembroActionPermission())
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        @if(PermissionService::hasPermission('miembros.details'))
                                            <a href="{{ route('miembros.show', $miembro) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100 transition-colors duration-200"
                                               data-tooltip="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        @if(PermissionHelper::canUpdate('miembros'))
                                            <a href="{{ route('miembros.edit', $miembro) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-100 transition-colors duration-200"
                                               data-tooltip="Editar miembro">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(PermissionHelper::canDelete('miembros'))
                                            <button type="button"
                                                    onclick="confirmarEliminacion('{{ $miembro->cedula }}')"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 transition-colors duration-200"
                                                    data-tooltip="Eliminar miembro">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                                No hay miembros registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($miembros instanceof \Illuminate\Pagination\LengthAwarePaginator && $miembros->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $miembros->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmación -->
<div id="modal-confirmacion" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar eliminación</h3>
        <p class="text-sm text-gray-500 mb-4">
            ¿Estás seguro de que deseas eliminar este miembro? Esta acción no se puede deshacer.
        </p>
        <div class="flex justify-end space-x-3">
            <button type="button" 
                    onclick="cerrarModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancelar
            </button>
            <button type="button"
                    id="btn-confirmar-eliminacion"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Eliminar
            </button>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ocultar alertas después de 3 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('#success-alert, #error-alert');
        alerts.forEach(alert => {
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 3000);
});

function confirmarEliminacion(miembroId) {
    const modal = document.getElementById('modal-confirmacion');
    const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    btnConfirmar.onclick = function() {
        const form = document.getElementById('form-eliminar-' + miembroId);
        if (form) {
            form.submit();
        }
    };
}

function cerrarModal() {
    const modal = document.getElementById('modal-confirmacion');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush

@endsection 