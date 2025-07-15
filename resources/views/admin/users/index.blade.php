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
                                <div class="flex space-x-4">
                                    @if(PermissionHelper::canUpdate('usuarios'))
                                        <button title="Editar" class="text-blue-600 hover:text-blue-900" onclick="editarUsuario({{ $user->id_email }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    <button title="Asignar Permisos" class="text-green-600 hover:text-green-900" onclick="asignarPermisos({{ $user->id_email }}, '{{ $user->correo }}')">
                                        <i class="fas fa-user-shield"></i>
                                    </button>
                                    @if(PermissionHelper::canDelete('usuarios'))
                                        <button title="Eliminar" class="text-red-600 hover:text-red-900" onclick="eliminarUsuario({{ $user->id_email }})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Asignar Permisos -->
<div id="modalPermisos" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white relative">
        <!-- Botón de cierre -->
        <button type="button" onclick="cerrarModalPermisos()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold focus:outline-none">&times;</button>
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
                    <div id="permisosContainer" class="space-y-2 max-h-64 overflow-y-auto border rounded p-2 bg-gray-50">
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

<!-- Modal de Edición de Usuario -->
<div id="modalEditarUsuario" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-8 w-full max-w-2xl mx-4">
        <h2 class="text-2xl font-bold text-center mb-6">Editar Usuario</h2>
        <form id="formEditarUsuario" novalidate>
            <input type="hidden" id="edit_user_id" name="user_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Correo:</label>
                    <input type="email" id="edit_correo" name="correo" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <div class="invalid-feedback text-red-500 text-xs">Por favor, ingrese un correo electrónico válido</div>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Rol:</label>
                    <select id="edit_rol" name="rol_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="1">Administrador</option>
                        <option value="2">Usuario</option>
                        <option value="3">Arbitro</option>
                        <option value="4">Organizador</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Estado:</label>
                    <select id="edit_estado" name="usuario_estado" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Nombre(s):</label>
                    <input type="text" id="edit_nombres" name="nombres" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Apellido(s):</label>
                    <input type="text" id="edit_apellidos" name="apellidos" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Cédula:</label>
                    <input type="text" id="edit_cedula" name="cedula" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Sexo:</label>
                    <select id="edit_sexo" name="sexo" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Fecha de nacimiento:</label>
                    <input type="date" id="edit_fecha_nacimiento" name="fecha_nacimiento" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Teléfono:</label>
                    <input type="text" id="edit_telefono" name="telefono" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Academia:</label>
                    <input type="text" id="edit_academia" name="academia" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-1">Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" id="edit_contrasena" name="contrasena" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>
            <div class="flex justify-end mt-8 space-x-3">
                <button type="button" onclick="cerrarModalEditarUsuario()" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function editarUsuario(id) {
        document.getElementById('modalEditarUsuario').classList.remove('hidden');
        document.getElementById('formEditarUsuario').reset();
        limpiarErroresEditarUsuario();
        fetch(`/usuarios/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_user_id').value = data.id;
                document.getElementById('edit_correo').value = data.correo;
                document.getElementById('edit_rol').value = data.rol_id;
                document.getElementById('edit_estado').value = data.usuario_estado;
                if (data.miembro) {
                    document.getElementById('edit_nombres').value = data.miembro.nombres || '';
                    document.getElementById('edit_apellidos').value = data.miembro.apellidos || '';
                    document.getElementById('edit_cedula').value = data.miembro.cedula || '';
                    document.getElementById('edit_sexo').value = data.miembro.sexo || '';
                    document.getElementById('edit_fecha_nacimiento').value = data.miembro.fecha_nacimiento || '';
                    document.getElementById('edit_telefono').value = data.miembro.telefono || '';
                    document.getElementById('edit_academia').value = data.miembro.academia || '';
                }
            });
    }

    function limpiarErroresEditarUsuario() {
        document.querySelectorAll('#formEditarUsuario input, #formEditarUsuario select').forEach(function(el) {
            el.classList.remove('border-red-500');
            let feedback = el.parentElement.querySelector('.invalid-feedback');
            if (feedback) feedback.style.display = 'none';
        });
    }

    // Validación en tiempo real
    document.querySelectorAll('#formEditarUsuario input[required], #formEditarUsuario select[required]').forEach(function(el) {
        el.addEventListener('input', function() {
            let feedback = el.parentElement.querySelector('.invalid-feedback');
            if (el.id === 'edit_correo') {
                if (!validarEmail(el.value)) {
                    el.classList.add('border-red-500');
                    if (feedback) feedback.textContent = 'Ingrese un correo electrónico válido';
                    if (feedback) feedback.style.display = 'block';
                } else {
                    el.classList.remove('border-red-500');
                    if (feedback) feedback.style.display = 'none';
                }
            } else if (el.id === 'edit_contrasena' && el.value.length > 0 && el.value.length < 6) {
                el.classList.add('border-red-500');
                if (feedback) feedback.textContent = 'La contraseña debe tener al menos 6 caracteres';
                if (feedback) feedback.style.display = 'block';
            } else if (el.value.trim() === '') {
                el.classList.add('border-red-500');
                if (feedback) feedback.textContent = 'Este campo es obligatorio';
                if (feedback) feedback.style.display = 'block';
            } else {
                el.classList.remove('border-red-500');
                if (feedback) feedback.style.display = 'none';
            }
        });
    });

    function cerrarModalEditarUsuario() {
        document.getElementById('modalEditarUsuario').classList.add('hidden');
    }

    function eliminarUsuario(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el usuario de forma permanente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch(`/usuarios/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Eliminado', 'El usuario ha sido eliminado.', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire('Error', data.error || 'No se pudo eliminar el usuario.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'No se pudo eliminar el usuario.', 'error');
                });
            }
        });
    }

    function asignarPermisos(userId, userName) {
        document.getElementById('usuarioNombre').textContent = userName;
        document.getElementById('userId').value = userId;
        document.getElementById('modalPermisos').classList.remove('hidden');
        cargarPermisos(userId);
    }

    function cerrarModalPermisos() {
        document.getElementById('modalPermisos').classList.add('hidden');
    }

    function cargarPermisos(userId) {
        // Obtener todos los permisos y los permisos actuales del usuario
        fetch(`/api/permisos-usuario/${userId}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('permisosContainer');
                container.innerHTML = '';
                data.todos.forEach(permiso => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center';
                    div.innerHTML = `
                        <input type="checkbox" id="permiso_${permiso.id}" name="permisos[]" value="${permiso.id}" class="mr-2" ${data.asignados.includes(permiso.id) ? 'checked' : ''}>
                        <label for="permiso_${permiso.id}" class="text-sm text-gray-700">${permiso.descripcion}</label>
                    `;
                    container.appendChild(div);
                });
                document.getElementById('rolSelect').value = data.rol_id;
            });
    }

    document.getElementById('formPermisos').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/usuarios/asignar-permisos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
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
                Swal.fire('Permisos actualizados', 'Los permisos han sido asignados correctamente.', 'success');
                cerrarModalPermisos();
                setTimeout(() => location.reload(), 1000);
            } else {
                Swal.fire('Error', data.message || 'No se pudieron asignar los permisos.', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'No se pudieron asignar los permisos.', 'error');
        });
    });

    document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
        e.preventDefault();
        let valido = true;
        document.querySelectorAll('#formEditarUsuario input[required], #formEditarUsuario select[required]').forEach(function(el) {
            let feedback = el.parentElement.querySelector('.invalid-feedback');
            if (el.id === 'edit_correo') {
                if (!validarEmail(el.value)) {
                    el.classList.add('border-red-500');
                    if (feedback) feedback.textContent = 'Ingrese un correo electrónico válido';
                    if (feedback) feedback.style.display = 'block';
                    valido = false;
                } else {
                    el.classList.remove('border-red-500');
                    if (feedback) feedback.style.display = 'none';
                }
            } else if (el.id === 'edit_contrasena' && el.value.length > 0 && el.value.length < 6) {
                el.classList.add('border-red-500');
                if (feedback) feedback.textContent = 'La contraseña debe tener al menos 6 caracteres';
                if (feedback) feedback.style.display = 'block';
                valido = false;
            } else if (el.value.trim() === '') {
                el.classList.add('border-red-500');
                if (feedback) feedback.textContent = 'Este campo es obligatorio';
                if (feedback) feedback.style.display = 'block';
                valido = false;
            } else {
                el.classList.remove('border-red-500');
                if (feedback) feedback.style.display = 'none';
            }
        });
        if (!valido) return;
        const id = document.getElementById('edit_user_id').value;
        const formData = new FormData(this);
        fetch(`/usuarios/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Actualizado', 'El usuario ha sido actualizado.', 'success');
                cerrarModalEditarUsuario();
                setTimeout(() => location.reload(), 1000);
            } else {
                Swal.fire('Error', data.error || 'No se pudo actualizar el usuario.', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'No se pudo actualizar el usuario.', 'error');
        });
    });

    document.getElementById('btnNuevoUsuario')?.addEventListener('click', function() {
        // Implementar lógica para nuevo usuario
        console.log('Nuevo usuario');
    });
</script>
@endpush
@endsection 