@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use Illuminate\Support\Facades\Log;
    
    // Verificar primero si tiene permiso de lectura
    if (!PermissionHelper::canViewModule('usuarios')) {
        // Si no tiene permiso de lectura, redirigir al home
        header('Location: ' . route('home'));
        exit;
    }
    
    // Debug de permisos
    Log::info('Vista usuarios: Verificando permisos', [
        'can_create' => PermissionHelper::canCreate('usuarios'),
        'can_update' => PermissionHelper::canUpdate('usuarios'),
        'can_delete' => PermissionHelper::canDelete('usuarios'),
        'has_any_action' => PermissionHelper::hasAnyActionPermission('usuarios')
    ]);
@endphp

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
        @if(PermissionHelper::canCreate('usuarios'))
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" id="btnNuevoUsuario">
                Nuevo Usuario
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    @if(PermissionHelper::hasAnyActionPermission('usuarios'))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->correo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->rol->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->usuario_estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->usuario_estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        @if(PermissionHelper::hasAnyActionPermission('usuarios'))
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if(PermissionHelper::canUpdate('usuarios'))
                                    <button class="text-indigo-600 hover:text-indigo-900 mr-3" onclick="editarUsuario({{ $user->id_email }})">
                                        Editar
                                    </button>
                                @endif
                                @if(PermissionHelper::canDelete('usuarios'))
                                    <button class="text-red-600 hover:text-red-900" onclick="eliminarUsuario({{ $user->id_email }})">
                                        Eliminar
                                    </button>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    function editarUsuario(id) {
        // Implementar lógica de edición
    }

    function eliminarUsuario(id) {
        if (confirm('¿Está seguro de eliminar este usuario?')) {
            // Implementar lógica de eliminación
        }
    }

    document.getElementById('btnNuevoUsuario')?.addEventListener('click', function() {
        // Implementar lógica para nuevo usuario
    });
</script>
@endpush
@endsection 