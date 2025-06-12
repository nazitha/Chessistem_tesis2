@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-semibold">Torneos</h1>
        @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 4)
            <a href="{{ route('torneos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Nuevo Torneo
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lugar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($torneos as $torneo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $torneo->nombre_torneo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $torneo->fecha_inicio->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $torneo->lugar }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $torneo->categoria->categoria_torneo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($torneo->torneo_cancelado)
                                        bg-red-100 text-red-800
                                    @elseif($torneo->fecha_inicio && $torneo->fecha_inicio->isPast())
                                        bg-gray-100 text-gray-800
                                    @elseif(!$torneo->estado_torneo)
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-green-100 text-green-800
                                    @endif
                                ">
                                    @if($torneo->torneo_cancelado)
                                        Cancelado
                                    @elseif($torneo->fecha_inicio && $torneo->fecha_inicio->isPast())
                                        Finalizado
                                    @elseif(!$torneo->estado_torneo)
                                        Borrador
                                    @else
                                        Activo
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('torneos.show', $torneo) }}" 
                                       class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100 transition-colors duration-200"
                                       data-tooltip="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 4)
                                        <a href="{{ route('torneos.edit', $torneo) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-100 transition-colors duration-200"
                                           data-tooltip="Editar torneo">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if(!$torneo->torneo_cancelado && !$torneo->fecha_inicio->isPast())
                                            <button type="button"
                                                    onclick="confirmarCancelacion('{{ $torneo->id }}')"
                                                    class="text-orange-600 hover:text-orange-900 p-1 rounded-full hover:bg-orange-100 transition-colors duration-200"
                                                    data-tooltip="Cancelar torneo">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                        
                                        <button type="button"
                                                onclick="confirmarEliminacion('{{ $torneo->id }}')"
                                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 transition-colors duration-200"
                                                data-tooltip="Eliminar torneo">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <form id="form-eliminar-{{ $torneo->id }}" 
                                              action="{{ route('torneos.destroy', $torneo) }}" 
                                              method="POST" 
                                              class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <form id="form-cancelar-{{ $torneo->id }}" 
                                              action="{{ route('torneos.cancelar', $torneo) }}" 
                                              method="POST" 
                                              class="hidden">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No hay torneos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($torneos->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $torneos->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmación -->
<div id="modal-confirmacion" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar eliminación</h3>
        <p class="text-sm text-gray-500 mb-4">
            ¿Estás seguro de que deseas eliminar este torneo? Esta acción no se puede deshacer.
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

<!-- Modal de cancelación -->
<div id="modal-cancelacion" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Cancelar Torneo</h3>
        <form id="form-cancelar-torneo" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700">Motivo de la cancelación</label>
                <textarea id="motivo_cancelacion" name="motivo_cancelacion" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
            </div>
            <div class="flex flex-row gap-4 mt-2">
                <button type="button" 
                        onclick="cerrarModalCancelacion()"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    [data-tooltip] {
        position: relative;
    }

    [data-tooltip]:hover:after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 10;
    }
</style>
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

function confirmarEliminacion(torneoId) {
    const modal = document.getElementById('modal-confirmacion');
    const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    btnConfirmar.onclick = function() {
        document.getElementById('form-eliminar-' + torneoId).submit();
    };
}

function cerrarModal() {
    const modal = document.getElementById('modal-confirmacion');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('modal-confirmacion').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

function confirmarCancelacion(torneoId) {
    const modal = document.getElementById('modal-cancelacion');
    const form = document.getElementById('form-cancelar-torneo');
    const motivoInput = document.getElementById('motivo_cancelacion');
    // Cambia la acción del formulario al torneo correcto
    form.action = `/torneos/${torneoId}/cancelar`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    motivoInput.value = '';
}

function cerrarModalCancelacion() {
    const modal = document.getElementById('modal-cancelacion');
    const motivoInput = document.getElementById('motivo_cancelacion');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    motivoInput.value = '';
}

// Cerrar modal de cancelación al hacer clic fuera
document.getElementById('modal-cancelacion').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalCancelacion();
    }
});
</script>
@endpush 