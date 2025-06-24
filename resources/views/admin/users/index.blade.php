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
                                <button class="text-green-600 hover:text-green-900 mr-3" onclick="asignarPermisos({{ $user->id_email }}, '{{ $user->correo }}')">
                                    Asignar Permisos
                                </button>
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

<!-- Modal para Asignar Permisos -->
<div id="modalPermisos" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Asignar Permisos a: <span id="usuarioNombre"></span></h3>
            <form id="formPermisos">
                <input type="hidden" id="userId" name="user_id">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rol:</label>
                    <select id="rolSelect" name="rol_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">Administrador</option>
                        <option value="2">Usuario</option>
                        <option value="3">Arbitro</option>
                        <option value="4">Organizador</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Permisos Específicos:</label>
                    <div id="permisosContainer" class="space-y-2">
                        <!-- Los permisos se cargarán dinámicamente -->
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalPermisos()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editarUsuario(id) {
        // Implementar lógica de edición
        console.log('Editar usuario:', id);
    }

    function eliminarUsuario(id) {
        if (confirm('¿Está seguro de eliminar este usuario?')) {
            // Implementar lógica de eliminación
            console.log('Eliminar usuario:', id);
        }
    }

    function asignarPermisos(userId, userName) {
        document.getElementById('usuarioNombre').textContent = userName;
        document.getElementById('userId').value = userId;
        document.getElementById('modalPermisos').classList.remove('hidden');
        
        // Cargar permisos disponibles
        cargarPermisos();
    }

    function cerrarModalPermisos() {
        document.getElementById('modalPermisos').classList.add('hidden');
    }

    function cargarPermisos() {
        // Aquí puedes cargar los permisos disponibles desde el servidor
        const permisos = [
            { id: 1, nombre: 'usuarios.read', descripcion: 'Ver usuarios' },
            { id: 2, nombre: 'usuarios.create', descripcion: 'Crear usuarios' },
            { id: 3, nombre: 'usuarios.update', descripcion: 'Editar usuarios' },
            { id: 4, nombre: 'usuarios.delete', descripcion: 'Eliminar usuarios' },
            { id: 5, nombre: 'torneos.read', descripcion: 'Ver torneos' },
            { id: 6, nombre: 'torneos.create', descripcion: 'Crear torneos' },
            { id: 7, nombre: 'miembros.read', descripcion: 'Ver miembros' },
            { id: 8, nombre: 'miembros.create', descripcion: 'Crear miembros' }
        ];

        const container = document.getElementById('permisosContainer');
        container.innerHTML = '';

        permisos.forEach(permiso => {
            const div = document.createElement('div');
            div.className = 'flex items-center';
            div.innerHTML = `
                <input type="checkbox" id="permiso_${permiso.id}" name="permisos[]" value="${permiso.id}" class="mr-2">
                <label for="permiso_${permiso.id}" class="text-sm text-gray-700">${permiso.descripcion}</label>
            `;
            container.appendChild(div);
        });
    }

    document.getElementById('formPermisos').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/usuarios/asignar-permisos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: formData.get('user_id'),
                rol_id: formData.get('rol_id'),
                permisos: formData.getAll('permisos[]')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Permisos asignados correctamente');
                cerrarModalPermisos();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al asignar permisos');
        });
    });

    document.getElementById('btnNuevoUsuario')?.addEventListener('click', function() {
        // Implementar lógica para nuevo usuario
        console.log('Nuevo usuario');
    });
</script>
@endpush
@endsection 