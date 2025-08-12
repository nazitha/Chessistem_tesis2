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
            <button id="btnNuevoUsuario" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow mb-4">
                Nuevo usuario
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
                                        <button title="Eliminar" class="text-red-600 hover:text-red-900 btnEliminar_usuario" data-id="{{ $user->id_email }}">
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

@include('modals.add_users')

@push('scripts')
<script>
    function validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function setSwitchState(isActive) {
        const checkbox = document.getElementById('switch_add_user');
        const switchButton = document.getElementById('switch_button');
        const thumb = document.getElementById('switch_thumb');
        const label = document.getElementById('switchLabel');
        
        checkbox.checked = isActive;
        
        if (isActive) {
            // Estado Activo
            switchButton.classList.remove('bg-red-500');
            switchButton.classList.add('bg-green-500');
            thumb.classList.remove('translate-x-0');
            thumb.classList.add('translate-x-4');
            label.textContent = 'Activo';
            label.classList.remove('text-red-600');
            label.classList.add('text-green-600');
        } else {
            // Estado Inactivo
            switchButton.classList.remove('bg-green-500');
            switchButton.classList.add('bg-red-500');
            thumb.classList.remove('translate-x-4');
            thumb.classList.add('translate-x-0');
            label.textContent = 'Inactivo';
            label.classList.remove('text-green-600');
            label.classList.add('text-red-600');
        }
    }

    function editarUsuario(id) {
        document.getElementById('modal_user_title').textContent = 'Editar usuario';
        document.getElementById('btn_submit_user').textContent = 'Guardar cambios';
        document.getElementById('modal_add_users').classList.remove('hidden');
        document.getElementById('form_add_users').reset();
        limpiarErroresEditarUsuario();
        
        // Establecer el ID del usuario en edición
        if (!document.getElementById('edit_user_id')) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'edit_user_id';
            document.getElementById('form_add_users').appendChild(hiddenInput);
        }
        document.getElementById('edit_user_id').value = id;
        
        fetch(`/usuarios/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('input_correo_add_user').value = data.correo;
                document.getElementById('select_rol_add_user').value = data.rol_id;
                if (data.usuario_estado) {
                    setSwitchState(true);
                } else {
                    setSwitchState(false);
                }
                
                // Mostrar campos de contraseña para UPDATE con ********
                document.getElementById('div_pass_add_user').style.display = 'block';
                document.getElementById('div_passconfirm_add_user').style.display = 'block';
                document.getElementById('input_pass_add_user').required = false;
                document.getElementById('input_passconfirm_add_user').required = false;
                document.getElementById('input_pass_add_user').value = '********';
                document.getElementById('input_passconfirm_add_user').value = '********';
            });
    }

    function limpiarErroresEditarUsuario() {
        document.querySelectorAll('#form_add_users input, #form_add_users select').forEach(function(el) {
            el.classList.remove('border-red-500');
            let feedback = el.parentElement.querySelector('.invalid-feedback');
            if (feedback) feedback.style.display = 'none';
        });
    }

    // Validación en tiempo real
    document.getElementById('input_correo_add_user').addEventListener('input', function() {
        if (this.value && !validarEmail(this.value)) {
            this.classList.add('border-red-500');
            this.nextElementSibling.textContent = 'Ingrese un correo electrónico válido';
            this.nextElementSibling.style.display = 'block';
        } else {
            this.classList.remove('border-red-500');
            this.nextElementSibling.style.display = 'none';
        }
    });

    document.getElementById('form_add_users').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const isEdit = document.getElementById('modal_user_title').textContent === 'Editar usuario';
        const correo = document.getElementById('input_correo_add_user').value;
        const rol_id = document.getElementById('select_rol_add_user').value;
        const usuario_estado = document.getElementById('switch_add_user').checked ? 1 : 0;
        let contrasena = document.getElementById('input_pass_add_user').value;
        let contrasena_confirmation = document.getElementById('input_passconfirm_add_user').value;
        
        // Validaciones básicas
        if (!correo || !rol_id) {
            Swal.fire('Error', 'Por favor, complete todos los campos.', 'error');
            return;
        }
        
        // Validar que las contraseñas coincidan si están visibles
        if (document.getElementById('div_pass_add_user').style.display !== 'none') {
            if (contrasena !== contrasena_confirmation) {
                Swal.fire('Error', 'Las contraseñas no coinciden.', 'error');
                return;
            }
        }
        
        const payload = {
            correo,
            rol_id,
            usuario_estado,
        };
        
        if (isEdit) {
            // En UPDATE, solo enviar contraseña si se cambió de ********
            if (contrasena !== '********' && contrasena_confirmation !== '********') {
                if (!contrasena || !contrasena_confirmation) {
                    Swal.fire('Error', 'Por favor, complete ambos campos de contraseña.', 'error');
                    return;
                }
                if (contrasena !== contrasena_confirmation) {
                    Swal.fire('Error', 'Las contraseñas no coinciden.', 'error');
                    return;
                }
                payload.contrasena = contrasena;
                payload.contrasena_confirmation = contrasena_confirmation;
            }
        }
        
        const url = isEdit ? `/usuarios/${document.getElementById('edit_user_id').value}` : '/usuarios';
        const method = isEdit ? 'PUT' : 'POST';
        
        console.log('Enviando payload:', payload);
        console.log('URL:', url);
        console.log('Method:', method);
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: isEdit ? 'Usuario actualizado' : 'Usuario creado',
                    html: `El usuario <strong>${correo}</strong> ha sido ${isEdit ? 'actualizado' : 'creado'} con éxito.`,
                    confirmButtonColor: '#282c34'
                });
                document.getElementById('modal_add_users').classList.add('hidden');
                document.getElementById('form_add_users').reset();
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Ocurrió un error.',
                    confirmButtonColor: '#282c34'
                });
            }
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar la solicitud.',
                confirmButtonColor: '#282c34'
            });
        });
    });

    document.getElementById('btnNuevoUsuario').addEventListener('click', function() {
        document.getElementById('modal_user_title').textContent = 'Nuevo usuario';
        document.getElementById('btn_submit_user').textContent = 'Crear usuario';
        document.getElementById('form_add_users').reset();
        setSwitchState(true); // Por defecto activo
        document.getElementById('modal_add_users').classList.remove('hidden');
        
        // Mostrar campos de contraseña para CREATE
        document.getElementById('div_pass_add_user').style.display = 'block';
        document.getElementById('div_passconfirm_add_user').style.display = 'block';
        document.getElementById('input_pass_add_user').required = true;
        document.getElementById('input_passconfirm_add_user').required = true;
        document.getElementById('input_pass_add_user').value = '';
        document.getElementById('input_passconfirm_add_user').value = '';
    });

    // Eliminar usuario
    $(document).on('click', '.btnEliminar_usuario', function() {
        const id = $(this).data('id');
        const correo = $(this).closest('tr').find('td:first').text();
        
        Swal.fire({
            icon: 'warning',
            title: '¿Desea eliminar al usuario?',
            html: `¿Desea eliminar al usuario <strong>${correo}</strong>?`,
            showCancelButton: true,
            confirmButtonColor: '#282c34',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/usuarios/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Usuario eliminado',
                            html: `El usuario <strong>${correo}</strong> ha sido eliminado con éxito.`,
                            confirmButtonColor: '#282c34'
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo eliminar el usuario.',
                            confirmButtonColor: '#282c34'
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al eliminar el usuario.',
                        confirmButtonColor: '#282c34'
                    });
                });
            }
        });
    });
</script>
@endpush
@endsection 