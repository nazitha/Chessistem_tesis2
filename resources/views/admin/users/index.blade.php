@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use Illuminate\Support\Facades\Log;
    use App\Services\PermissionService;
    
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

    // Verificar permisos de asignaciones de manera más robusta
    $canViewAsignaciones = false;
    $canUpdateAsignaciones = false;

    try {
        $canViewAsignaciones = PermissionService::hasPermission('asignaciones.read');
        $canUpdateAsignaciones = PermissionService::hasPermission('asignaciones.update');
    } catch (\Exception $e) {
        // Si hay error, asumir que no tiene permisos
        $canViewAsignaciones = false;
        $canUpdateAsignaciones = false;
    }
@endphp

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Gestión de usuarios</h1>
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

    <!-- Botón para mostrar controles de búsqueda para tabla de usuarios -->
    <div class="mb-4">
        <div class="flex gap-2">
            <button id="btnMostrarBusquedaUsuarios" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
            <button id="btnExportarUsuarios" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
    </div>

    <!-- Controles de búsqueda para tabla de usuarios -->
    <div id="panelBusquedaUsuarios" class="bg-white shadow-md rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Usuarios</h3>
            <button id="btnCancelarBusquedaUsuarios" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                <input type="text" id="buscarUsuarios" placeholder="Buscar por correo, rol o estado..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button id="btnBuscarAvanzadaUsuarios" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                    Búsqueda Avanzada
                </button>
                <button id="btnLimpiarBusquedaUsuarios" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                    Limpiar
                </button>
            </div>
        </div>
        
        <!-- Panel de búsqueda avanzada -->
        <div id="panelBusquedaAvanzadaUsuarios" class="mt-4 p-4 bg-gray-50 rounded-md hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo:</label>
                    <input type="text" id="filtroCorreo" placeholder="Filtrar por correo" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol:</label>
                    <select id="filtroRol" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todos los roles</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Evaluador">Evaluador</option>
                        <option value="Estudiante">Estudiante</option>
                        <option value="Gestor">Gestor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                    <select id="filtroEstado" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

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
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
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
        
        <!-- Controles de paginación para tabla de usuarios -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <label class="text-sm text-gray-700 mr-2">Mostrar:</label>
                    <select id="registrosPorPaginaUsuarios" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-700 ml-2">registros por página</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="btnAnteriorUsuarios" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Anterior
                    </button>
                    <span id="infoPaginacionUsuarios" class="text-sm text-gray-700">
                        Página <span id="paginaActualUsuarios">1</span> de <span id="totalPaginasUsuarios">1</span>
                    </span>
                    <button id="btnSiguienteUsuarios" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Nueva tabla de Gestión de Roles -->
    <!-- NOTA: Los permisos para esta tabla se implementarán cuando se ejecute el PermissionSeeder -->
    <!-- Permisos requeridos: asignaciones.read (ver tabla) y asignaciones.update (ver columna acciones) -->
    <div class="mt-8">
        @if($canViewAsignaciones)
        <h2 class="text-xl font-bold text-gray-900 mb-4">Gestión de permisos</h2>
        
        <!-- Botón para mostrar controles de búsqueda para tabla de gestión de permisos -->
        <div class="mb-4">
            <div class="flex gap-2">
                <button id="btnMostrarBusquedaPermisos" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
                <button id="btnExportarPermisos" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    <i class="fas fa-download mr-2"></i>Exportar
                </button>
            </div>
        </div>
        
        <!-- Controles de búsqueda para tabla de gestión de permisos -->
        <div id="panelBusquedaPermisos" class="bg-white shadow-md rounded-lg p-4 mb-4 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Búsqueda de Permisos</h3>
                <button id="btnCancelarBusquedaPermisos" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                    ✕
                </button>
            </div>
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                    <input type="text" id="buscarPermisos" placeholder="Buscar por rol, grupo o permisos..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button id="btnBuscarAvanzadaPermisos" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        Búsqueda Avanzada
                    </button>
                    <button id="btnLimpiarBusquedaPermisos" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                        Limpiar
                    </button>
                </div>
            </div>
            
            <!-- Panel de búsqueda avanzada -->
            <div id="panelBusquedaAvanzadaPermisos" class="mt-4 p-4 bg-gray-50 rounded-md hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol:</label>
                        <input type="text" id="filtroRolPermisos" placeholder="Filtrar por rol" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grupo:</label>
                        <input type="text" id="filtroGrupoPermisos" placeholder="Filtrar por grupo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Permisos:</label>
                        <input type="text" id="filtroPermisos" placeholder="Filtrar por permisos" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permisos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        @if($canUpdateAsignaciones)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rolesData as $roleData)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $roleData->rol }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $roleData->grupo }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $roleData->permisos }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $roleData->descripciones }}</td>
                            @if($canUpdateAsignaciones)
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button title="Asignar Permisos" class="text-green-600 hover:text-green-900" onclick="asignarPermisosRol('{{ $roleData->rol }}', '{{ $roleData->grupo }}')">
                                    <i class="fas fa-user-shield"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Controles de paginación para tabla de gestión de permisos -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <label class="text-sm text-gray-700 mr-2">Mostrar:</label>
                        <select id="registrosPorPaginaPermisos" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="text-sm text-gray-700 ml-2">registros por página</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="btnAnteriorPermisos" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Anterior
                        </button>
                        <span id="infoPaginacionPermisos" class="text-sm text-gray-700">
                            Página <span id="paginaActualPermisos">1</span> de <span id="totalPaginasPermisos">1</span>
                        </span>
                        <button id="btnSiguientePermisos" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
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

<!-- Modal para Gestionar Permisos de Roles -->
<div id="modalGestionarPermisos" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl" style="max-height: 90vh;">
            <!-- Header -->
            <div class="flex justify-between items-center p-4 border-b">
                <div>
                    <h3 class="text-lg font-bold">Gestionar permisos</h3>
                    <p class="text-sm text-gray-600">Rol: <span id="rolNombre" class="text-blue-600 font-medium"></span></p>
                </div>
                <button type="button" onclick="cerrarModalGestionarPermisos()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>
            </div>
            
            <!-- Contenido con scroll -->
            <div class="p-4" style="max-height: calc(90vh - 140px); overflow-y: auto;">
                <form id="formGestionarPermisos">
                    <input type="hidden" id="rolId" name="rol_id">
                    <div id="permisosContainerGestion" class="space-y-4">
                        <!-- Los permisos se cargarán dinámicamente -->
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end space-x-3 p-4 border-t bg-gray-50">
                <button type="button" onclick="cerrarModalGestionarPermisos()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit" form="formGestionarPermisos" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Guardar Cambios
                </button>
            </div>
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

    // Función para asignar permisos a roles
    function asignarPermisosRol(nombreRol, grupo) {
        // Si los roles no están cargados, cargarlos y esperar
        if (!rolesCargados && !rolesCargando) {
            cargarRoles();
            
            // Esperar un momento y reintentar una sola vez
            setTimeout(() => {
                if (rolesCargados) {
                    asignarPermisosRol(nombreRol, grupo);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los roles. Por favor, recarga la página.',
                        confirmButtonColor: '#282c34'
                    });
                }
            }, 1000);
            return;
        }
        
        // Si aún están cargando, esperar
        if (rolesCargando) {
            setTimeout(() => {
                asignarPermisosRol(nombreRol, grupo);
            }, 500);
            return;
        }
        
        // Obtener el rol_id del nombre del rol usando mapeo dinámico
        const rolId = obtenerRolIdPorNombre(nombreRol);
        
        if (rolId) {
            // Abrir modal y cargar permisos
            abrirModalGestionarPermisos(nombreRol, rolId, grupo);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `No se pudo identificar el ID del rol: "${nombreRol}"`,
                confirmButtonColor: '#282c34'
            });
        }
    }

    // Variable global para almacenar los roles
    let rolesMapeo = {};
    let rolesCargando = false;
    let rolesCargados = false;

    // Función para cargar los roles desde la base de datos
    function cargarRoles() {
        if (rolesCargando || rolesCargados) {
            return;
        }
        
        rolesCargando = true;
        
        fetch('/usuarios/roles')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Crear el mapeo dinámico
                    data.data.forEach(rol => {
                        rolesMapeo[rol.nombre] = rol.id;
                    });
                    rolesCargados = true;
                } else {
                    console.error('Error en respuesta de roles:', data.error);
                }
            })
            .catch(error => {
                console.error('Error al cargar roles:', error);
            })
            .finally(() => {
                rolesCargando = false;
            });
    }

    // Función para obtener el ID del rol por nombre
    function obtenerRolIdPorNombre(nombreRol) {
        return rolesMapeo[nombreRol] || null;
    }

    // Función para abrir el modal de gestión de permisos
    function abrirModalGestionarPermisos(nombreRol, rolId, grupo) {
        document.getElementById('rolNombre').textContent = nombreRol;
        document.getElementById('rolNombre').setAttribute('data-grupo', grupo); // Almacenar el grupo en el atributo data
        document.getElementById('rolId').value = rolId;
        document.getElementById('modalGestionarPermisos').classList.remove('hidden');
        
        // Cargar permisos del rol
        cargarPermisosDelRol(rolId, grupo);
    }

    // Función para cerrar el modal de gestión de permisos
    function cerrarModalGestionarPermisos() {
        document.getElementById('modalGestionarPermisos').classList.add('hidden');
        document.getElementById('permisosContainerGestion').innerHTML = '';
        document.getElementById('rolNombre').textContent = '';
        document.getElementById('rolId').value = '';
    }

    // Función para cargar los permisos del rol
    function cargarPermisosDelRol(rolId, grupo) {
        const url = grupo ? `/usuarios/rol/${rolId}/permisos/${encodeURIComponent(grupo)}` : `/usuarios/rol/${rolId}/permisos`;
        
        fetch(url)
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    renderizarPermisos(data.data, grupo);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los permisos del rol',
                        confirmButtonColor: '#282c34'
                    });
                }
            })
            .catch(error => {
                console.error('Error al cargar permisos:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los permisos del rol',
                    confirmButtonColor: '#282c34'
                });
            });
    }

    // Función para renderizar los permisos agrupados
    function renderizarPermisos(permisos, grupo) {
        const container = document.getElementById('permisosContainerGestion');
        container.innerHTML = '';
        
        // Agrupar permisos por grupo
        const grupos = {};
        permisos.forEach(permiso => {
            const permisoGrupo = permiso.grupo || 'Sin grupo';
            if (!grupos[permisoGrupo]) {
                grupos[permisoGrupo] = [];
            }
            grupos[permisoGrupo].push(permiso);
        });
        
        // Renderizar cada grupo
        Object.keys(grupos).forEach(permisoGrupo => {
            const permisosGrupo = grupos[permisoGrupo];
            
            // Crear contenedor del grupo
            const grupoDiv = document.createElement('div');
            grupoDiv.className = 'border rounded-lg p-4 bg-gray-50';
            
            // Título del grupo (nodo padre)
            const tituloGrupo = document.createElement('h5');
            tituloGrupo.className = 'text-lg font-semibold text-gray-800 mb-3';
            tituloGrupo.textContent = permisoGrupo;
            grupoDiv.appendChild(tituloGrupo);
            
            // Checkbox para seleccionar/deseleccionar todo el grupo
            const selectAllDiv = document.createElement('div');
            selectAllDiv.className = 'mb-3';
            const selectAllCheckbox = document.createElement('input');
            selectAllCheckbox.type = 'checkbox';
            selectAllCheckbox.className = 'mr-2';
            selectAllCheckbox.id = `select-all-${permisoGrupo.replace(/\s+/g, '-').replace(/[^a-zA-Z0-9-]/g, '')}`;
            
            // Función para actualizar el estado del checkbox padre
            function actualizarCheckboxPadre() {
                const checkboxes = grupoDiv.querySelectorAll('input[name="permisos[]"]');
                const checkboxesMarcados = grupoDiv.querySelectorAll('input[name="permisos[]"]:checked');
                selectAllCheckbox.checked = checkboxes.length > 0 && checkboxes.length === checkboxesMarcados.length;
                selectAllCheckbox.indeterminate = checkboxesMarcados.length > 0 && checkboxesMarcados.length < checkboxes.length;
            }
            
            selectAllCheckbox.onchange = function() {
                const checkboxes = grupoDiv.querySelectorAll('input[name="permisos[]"]');
                checkboxes.forEach(cb => cb.checked = this.checked);
            };
            
            const selectAllLabel = document.createElement('label');
            selectAllLabel.textContent = 'Seleccionar todos';
            selectAllLabel.className = 'text-sm font-medium text-gray-700';
            selectAllLabel.htmlFor = selectAllCheckbox.id;
            selectAllDiv.appendChild(selectAllCheckbox);
            selectAllDiv.appendChild(selectAllLabel);
            grupoDiv.appendChild(selectAllDiv);
            
            // Permisos del grupo (nodos hijos)
            permisosGrupo.forEach(permiso => {
                const permisoDiv = document.createElement('div');
                permisoDiv.className = 'ml-6 mb-2 flex items-center';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'permisos[]';
                checkbox.value = permiso.permiso_id;
                checkbox.id = `permiso-${permiso.permiso_id}`;
                checkbox.checked = permiso.asignado === 1;
                checkbox.className = 'mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded';
                
                // Evento para actualizar el checkbox padre cuando cambie un hijo
                checkbox.onchange = function() {
                    actualizarCheckboxPadre();
                };
                
                const label = document.createElement('label');
                label.textContent = permiso.descripcion || `Permiso ${permiso.permiso}`;
                label.className = 'text-sm text-gray-600 cursor-pointer';
                label.htmlFor = checkbox.id;
                
                permisoDiv.appendChild(checkbox);
                permisoDiv.appendChild(label);
                grupoDiv.appendChild(permisoDiv);
            });
            
            // Actualizar el estado inicial del checkbox padre
            actualizarCheckboxPadre();
            
            container.appendChild(grupoDiv);
        });
        
        // Si no hay permisos, mostrar mensaje
        if (Object.keys(grupos).length === 0) {
            const noDataDiv = document.createElement('div');
            noDataDiv.className = 'text-center py-8 text-gray-500';
            noDataDiv.textContent = 'No se encontraron permisos para este rol';
            container.appendChild(noDataDiv);
        }
    }

    // Event listener para el formulario de gestión de permisos
    document.addEventListener('DOMContentLoaded', function() {
        const formGestionarPermisos = document.getElementById('formGestionarPermisos');
        if (formGestionarPermisos) {
            formGestionarPermisos.addEventListener('submit', function(e) {
                e.preventDefault();
                guardarPermisosDelRol();
            });
        }
    });

    // Función para guardar los permisos del rol
    function guardarPermisosDelRol() {
        const rolId = document.getElementById('rolId').value;
        const grupo = document.getElementById('rolNombre').getAttribute('data-grupo');
        const checkboxes = document.querySelectorAll('#permisosContainerGestion input[name="permisos[]"]:checked');
        
        const permisosSeleccionados = Array.from(checkboxes).map(cb => cb.value);
        
        // Mostrar loading
        Swal.fire({
            title: 'Guardando cambios...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Enviar datos al servidor
        fetch('/usuarios/rol/permisos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                rol_id: rolId,
                permisos: permisosSeleccionados,
                grupo: grupo
            })
        })
        .then(response => {
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message || 'Permisos actualizados correctamente',
                    confirmButtonColor: '#282c34'
                }).then(() => {
                    cerrarModalGestionarPermisos();
                    // Opcional: recargar la página para ver los cambios
                    // location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al actualizar los permisos',
                    confirmButtonColor: '#282c34'
                });
            }
        })
        .catch(error => {
            console.error('Error al guardar permisos:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión al guardar los permisos',
                confirmButtonColor: '#282c34'
            });
        });
    }

    // Sistema de búsqueda y paginación personalizado
    class TablaPersonalizada {
        constructor(tablaElement, config) {
            this.tabla = tablaElement; // Ahora recibe el elemento directamente
            this.tbody = this.tabla.querySelector('tbody');
            this.filasOriginales = Array.from(this.tbody.querySelectorAll('tr'));
            this.filasFiltradas = [...this.filasOriginales];
            this.paginaActual = 1;
            this.registrosPorPagina = 10;
            this.config = config;
            
            this.inicializar();
        }
        
        inicializar() {
            this.configurarEventos();
            this.verificarEstadoExportar();
            this.aplicarPaginacion();
        }
        
        configurarEventos() {
            // Registros por página - PRIORIDAD ALTA
            const selectRegistros = document.getElementById(this.config.selectRegistros);
            if (selectRegistros) {
                selectRegistros.addEventListener('change', (e) => {
                    this.registrosPorPagina = parseInt(e.target.value);
                    this.paginaActual = 1;
                    this.aplicarPaginacion();
                });
            }
            
            // Botón de exportación
            const btnExportar = document.getElementById(this.config.btnExportar);
            if (btnExportar) {
                btnExportar.addEventListener('click', () => {
                    this.exportarDatos();
                });
            }
            
            // Botones de paginación
            const btnAnterior = document.getElementById(this.config.btnAnterior);
            const btnSiguiente = document.getElementById(this.config.btnSiguiente);
            
            if (btnAnterior) {
                btnAnterior.addEventListener('click', () => {
                    if (this.paginaActual > 1) {
                        this.paginaActual--;
                        this.aplicarPaginacion();
                    }
                });
            }
            
            if (btnSiguiente) {
                btnSiguiente.addEventListener('click', () => {
                    const totalPaginas = Math.ceil(this.filasFiltradas.length / this.registrosPorPagina);
                    if (this.paginaActual < totalPaginas) {
                        this.paginaActual++;
                        this.aplicarPaginacion();
                    }
                });
            }
            
            // Otros eventos...
            const btnMostrarBusqueda = document.getElementById(this.config.btnMostrarBusqueda);
            if (btnMostrarBusqueda) {
                btnMostrarBusqueda.addEventListener('click', () => {
                    this.mostrarPanelBusqueda();
                });
            }
            
            const btnCancelarBusqueda = document.getElementById(this.config.btnCancelarBusqueda);
            if (btnCancelarBusqueda) {
                btnCancelarBusqueda.addEventListener('click', () => {
                    this.cancelarBusqueda();
                });
            }
            
            const inputBusqueda = document.getElementById(this.config.inputBusqueda);
            if (inputBusqueda) {
                inputBusqueda.addEventListener('input', (e) => {
                    this.filtrar(e.target.value);
                });
            }
            
            const btnBuscarAvanzada = document.getElementById(this.config.btnBuscarAvanzada);
            if (btnBuscarAvanzada) {
                btnBuscarAvanzada.addEventListener('click', () => {
                    this.toggleBusquedaAvanzada();
                });
            }
            
            const btnLimpiar = document.getElementById(this.config.btnLimpiar);
            if (btnLimpiar) {
                btnLimpiar.addEventListener('click', () => {
                    this.limpiarFiltros();
                });
            }
            
            if (this.config.filtrosAvanzados) {
                this.config.filtrosAvanzados.forEach(filtro => {
                    const elemento = document.getElementById(filtro.id);
                    if (elemento) {
                        elemento.addEventListener('input', () => {
                            this.aplicarFiltrosAvanzados();
                        });
                    }
                });
            }
        }
        
        exportarDatos() {
            // Obtener las filas filtradas (visibles)
            const filasAExportar = this.filasFiltradas;
            
            // Obtener los encabezados de la tabla (excluyendo la columna de acciones)
            const encabezados = [];
            const filasEncabezado = this.tabla.querySelectorAll('thead th');
            filasEncabezado.forEach((th, index) => {
                // Excluir la última columna si es "Acciones"
                if (index < filasEncabezado.length - 1 || !th.textContent.trim().includes('Acciones')) {
                    encabezados.push(th.textContent.trim());
                }
            });
            
            // Preparar los datos para exportar
            const datos = [];
            filasAExportar.forEach(fila => {
                const filaDatos = [];
                const celdas = fila.querySelectorAll('td');
                
                // Excluir la última celda si es la columna de acciones
                const celdasAExportar = celdas.length > 0 && celdas[celdas.length - 1].querySelector('button') ? 
                    Array.from(celdas).slice(0, -1) : Array.from(celdas);
                
                celdasAExportar.forEach(celda => {
                    filaDatos.push(celda.textContent.trim());
                });
                
                datos.push(filaDatos);
            });
            
            // Crear el contenido CSV con BOM para UTF-8
            const BOM = '\uFEFF'; // Byte Order Mark para UTF-8
            let csvContent = BOM + encabezados.join(',') + '\n';
            datos.forEach(fila => {
                csvContent += fila.join(',') + '\n';
            });
            
            // Determinar el nombre del archivo según la tabla
            let nombreArchivo = 'datos_exportados.csv';
            if (this.config.btnExportar === 'btnExportarUsuarios') {
                nombreArchivo = 'usuarios_exportados.csv';
            } else if (this.config.btnExportar === 'btnExportarPermisos') {
                nombreArchivo = 'permisos_exportados.csv';
            }
            
            // Crear y descargar el archivo con codificación UTF-8
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', nombreArchivo);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        aplicarPaginacion() {
            // Ocultar todas las filas primero
            this.filasOriginales.forEach(fila => {
                fila.style.display = 'none';
            });
            
            // Calcular qué filas mostrar
            const inicio = (this.paginaActual - 1) * this.registrosPorPagina;
            const fin = inicio + this.registrosPorPagina;
            const filasAMostrar = this.filasFiltradas.slice(inicio, fin);
            
            // Mostrar solo las filas de la página actual
            filasAMostrar.forEach(fila => {
                fila.style.display = '';
            });
            
            // Actualizar información de paginación
            const totalPaginas = Math.ceil(this.filasFiltradas.length / this.registrosPorPagina);
            
            const paginaActual = document.getElementById(this.config.paginaActual);
            const totalPaginasElement = document.getElementById(this.config.totalPaginas);
            const btnAnterior = document.getElementById(this.config.btnAnterior);
            const btnSiguiente = document.getElementById(this.config.btnSiguiente);
            
            if (paginaActual) paginaActual.textContent = this.paginaActual;
            if (totalPaginasElement) totalPaginasElement.textContent = totalPaginas;
            
            if (btnAnterior) {
                const puedeAnterior = this.paginaActual > 1;
                btnAnterior.disabled = !puedeAnterior;
                if (!puedeAnterior) {
                    btnAnterior.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    btnAnterior.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
            
            if (btnSiguiente) {
                const puedeSiguiente = this.paginaActual < totalPaginas;
                btnSiguiente.disabled = !puedeSiguiente;
                if (!puedeSiguiente) {
                    btnSiguiente.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    btnSiguiente.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
            
            // Verificar estado del botón exportar
            this.verificarEstadoExportar();
        }
        
        verificarEstadoExportar() {
            const btnExportar = document.getElementById(this.config.btnExportar);
            if (btnExportar) {
                let tieneRegistros = false;
                
                // Si hay filtros aplicados, verificar las filas filtradas
                if (this.filasFiltradas.length !== this.filasOriginales.length) {
                    tieneRegistros = this.filasFiltradas.length > 0;
                } else {
                    // Si no hay filtros, verificar las filas originales en el DOM
                    const filasEnTabla = this.tabla.querySelectorAll('tbody tr');
                    tieneRegistros = Array.from(filasEnTabla).some(fila => {
                        // Excluir filas que contengan mensajes como "No hay usuarios registrados"
                        const textoFila = fila.textContent.toLowerCase();
                        return !textoFila.includes('no hay') && !textoFila.includes('registrados') && !textoFila.includes('registradas');
                    });
                }
                
                btnExportar.disabled = !tieneRegistros;
                
                if (!tieneRegistros) {
                    btnExportar.classList.add('opacity-50', 'cursor-not-allowed');
                    btnExportar.title = 'No hay registros para exportar';
                } else {
                    btnExportar.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnExportar.title = 'Exportar registros';
                }
            }
        }
        
        mostrarPanelBusqueda() {
            const panel = document.getElementById(this.config.panelBusqueda);
            if (panel) {
                panel.classList.remove('hidden');
            }
        }
        
        cancelarBusqueda() {
            this.limpiarFiltros();
            const panel = document.getElementById(this.config.panelBusqueda);
            if (panel) {
                panel.classList.add('hidden');
            }
        }
        
        filtrar(texto) {
            this.filasFiltradas = this.filasOriginales.filter(fila => {
                const textoFila = fila.textContent.toLowerCase();
                return textoFila.includes(texto.toLowerCase());
            });
            
            this.paginaActual = 1;
            this.aplicarPaginacion();
            this.verificarEstadoExportar();
        }
        
        aplicarFiltrosAvanzados() {
            const filtros = {};
            
            if (this.config.filtrosAvanzados) {
                this.config.filtrosAvanzados.forEach(filtro => {
                    const elemento = document.getElementById(filtro.id);
                    if (elemento && elemento.value) {
                        filtros[filtro.campo] = elemento.value.toLowerCase();
                    }
                });
            }
            
            this.filasFiltradas = this.filasOriginales.filter(fila => {
                const celdas = fila.querySelectorAll('td');
                
                for (const [campo, valor] of Object.entries(filtros)) {
                    const indice = this.config.filtrosAvanzados.find(f => f.campo === campo)?.indice;
                    if (indice !== undefined && celdas[indice]) {
                        const textoCelda = celdas[indice].textContent.toLowerCase();
                        if (!textoCelda.includes(valor)) {
                            return false;
                        }
                    }
                }
                
                return true;
            });
            
            this.paginaActual = 1;
            this.aplicarPaginacion();
            this.verificarEstadoExportar();
        }
        
        toggleBusquedaAvanzada() {
            const panel = document.getElementById(this.config.panelBusquedaAvanzada);
            if (panel) {
                panel.classList.toggle('hidden');
            }
        }
        
        limpiarFiltros() {
            // Limpiar búsqueda general
            const inputBusqueda = document.getElementById(this.config.inputBusqueda);
            if (inputBusqueda) {
                inputBusqueda.value = '';
            }
            
            // Limpiar filtros avanzados
            if (this.config.filtrosAvanzados) {
                this.config.filtrosAvanzados.forEach(filtro => {
                    const elemento = document.getElementById(filtro.id);
                    if (elemento) {
                        elemento.value = '';
                    }
                });
            }
            
            // Ocultar panel de búsqueda avanzada
            const panelAvanzado = document.getElementById(this.config.panelBusquedaAvanzada);
            if (panelAvanzado) {
                panelAvanzado.classList.add('hidden');
            }
            
            // Restaurar todas las filas y reinicializar paginación
            this.filasFiltradas = [...this.filasOriginales];
            this.paginaActual = 1;
            this.aplicarPaginacion();
            this.verificarEstadoExportar();
        }
    }
    
    // Inicializar tablas cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        
        // Tabla de usuarios - usar un selector más específico
        const tablaUsuarios = document.querySelector('.bg-white.shadow-md.rounded-lg.overflow-hidden table');
        if (tablaUsuarios) {
            new TablaPersonalizada(tablaUsuarios, {
                inputBusqueda: 'buscarUsuarios',
                btnMostrarBusqueda: 'btnMostrarBusquedaUsuarios',
                btnCancelarBusqueda: 'btnCancelarBusquedaUsuarios',
                btnBuscarAvanzada: 'btnBuscarAvanzadaUsuarios',
                btnLimpiar: 'btnLimpiarBusquedaUsuarios',
                btnExportar: 'btnExportarUsuarios', // Agregar el botón de exportación
                panelBusqueda: 'panelBusquedaUsuarios',
                panelBusquedaAvanzada: 'panelBusquedaAvanzadaUsuarios',
                selectRegistros: 'registrosPorPaginaUsuarios',
                btnAnterior: 'btnAnteriorUsuarios',
                btnSiguiente: 'btnSiguienteUsuarios',
                paginaActual: 'paginaActualUsuarios',
                totalPaginas: 'totalPaginasUsuarios',
                filtrosAvanzados: [
                    { id: 'filtroCorreo', campo: 'correo', indice: 0 },
                    { id: 'filtroRol', campo: 'rol', indice: 1 },
                    { id: 'filtroEstado', campo: 'estado', indice: 2 }
                ]
            });
        }
        
        // Tabla de gestión de permisos - usar un selector más específico
        const tablaPermisos = document.querySelectorAll('.bg-white.shadow-md.rounded-lg.overflow-hidden table')[1];
        if (tablaPermisos) {
            new TablaPersonalizada(tablaPermisos, {
                inputBusqueda: 'buscarPermisos',
                btnMostrarBusqueda: 'btnMostrarBusquedaPermisos',
                btnCancelarBusqueda: 'btnCancelarBusquedaPermisos',
                btnBuscarAvanzada: 'btnBuscarAvanzadaPermisos',
                btnLimpiar: 'btnLimpiarBusquedaPermisos',
                btnExportar: 'btnExportarPermisos', // Agregar el botón de exportación
                panelBusqueda: 'panelBusquedaPermisos',
                panelBusquedaAvanzada: 'panelBusquedaAvanzadaPermisos',
                selectRegistros: 'registrosPorPaginaPermisos',
                btnAnterior: 'btnAnteriorPermisos',
                btnSiguiente: 'btnSiguientePermisos',
                paginaActual: 'paginaActualPermisos',
                totalPaginas: 'totalPaginasPermisos',
                filtrosAvanzados: [
                    { id: 'filtroRolPermisos', campo: 'rol', indice: 0 },
                    { id: 'filtroGrupoPermisos', campo: 'grupo', indice: 1 },
                    { id: 'filtroPermisos', campo: 'permisos', indice: 2 }
                ]
            });
        }
    });

    // Event listener para el formulario de gestión de permisos
    
    // Función para exportar usuarios
    document.getElementById('btnExportarUsuarios').addEventListener('click', function() {
        const link = document.createElement('a');
        link.href = '{{ route("usuarios.export") }}';
        link.download = 'usuarios_export.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
    
    // Función para exportar permisos
    document.getElementById('btnExportarPermisos').addEventListener('click', function() {
        const link = document.createElement('a');
        link.href = '{{ route("permisos.export") }}';
        link.download = 'permisos_export.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
@endpush
@endsection 