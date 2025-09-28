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
            <button id="btnNuevoUsuario" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors duration-200 shadow mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
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
    <div id="panelBusquedaUsuarios" class="bg-white shadow-md rounded-lg p-4 mb-4 {{ ($search || $filtroCorreo || $filtroRol || $filtroEstado) ? '' : 'hidden' }}">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Búsqueda de Usuarios</h3>
            <button id="btnCancelarBusquedaUsuarios" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                ✕
            </button>
        </div>
        
        <form method="GET" action="{{ route('usuarios.index') }}" id="formBusquedaUsuarios">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                    <input type="text" id="searchInput" name="search" value="{{ $search }}" placeholder="Buscar por correo, rol..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" id="btnBuscarAvanzadaUsuarios" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                        <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                        <i class="fas fa-brush mr-2"></i>Limpiar
                    </a>
                </div>
            </div>
            
            <!-- Panel de búsqueda avanzada -->
            <div id="panelBusquedaAvanzadaUsuarios" class="mt-4 p-4 bg-gray-50 rounded-md {{ ($filtroCorreo || $filtroRol || $filtroEstado) ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo:</label>
                        <input type="text" id="filtroCorreo" name="filtro_correo" value="{{ $filtroCorreo }}" placeholder="Filtrar por correo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol:</label>
                        <select id="filtroRol" name="filtro_rol" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                            <option value="">Todos los roles</option>
                            <option value="Administrador" {{ $filtroRol == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="Evaluador" {{ $filtroRol == 'Evaluador' ? 'selected' : '' }}>Evaluador</option>
                            <option value="Estudiante" {{ $filtroRol == 'Estudiante' ? 'selected' : '' }}>Estudiante</option>
                            <option value="Gestor" {{ $filtroRol == 'Gestor' ? 'selected' : '' }}>Gestor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                        <select id="filtroEstado" name="filtro_estado" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                            <option value="">Todos los estados</option>
                            <option value="1" {{ $filtroEstado === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ $filtroEstado === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Campo oculto para mantener per_page -->
            <input type="hidden" name="per_page" value="{{ $perPage }}">
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden" id="tablaUsuariosContainer">
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
                @if($users->count() > 0)
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
                @else
                    <tr>
                        <td colspan="{{ PermissionHelper::hasAnyActionPermission('usuarios') ? '4' : '3' }}" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-search text-4xl text-gray-300 mb-2"></i>
                                <p class="text-lg font-medium">No se encontraron resultados</p>
                                <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <!-- Paginación de Laravel -->
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <label class="text-sm text-gray-700 mr-2">Mostrar:</label>
                    <select id="perPageSelectUsuarios" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white" onchange="changePerPageUsuarios(this.value)">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-sm text-gray-700 ml-2">registros por página</span>
                </div>
                <div class="flex-1 flex items-center justify-center">
                    {{ $users->links('pagination.custom') }}
                </div>
                <div class="text-sm text-gray-700">
                    Mostrando {{ $users->firstItem() ?? 0 }} a {{ $users->lastItem() ?? 0 }} registros de {{ $users->total() }} resultados
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
        <div id="panelBusquedaPermisos" class="bg-white shadow-md rounded-lg p-4 mb-4 {{ ($searchPermisos || $filtroRolPermisos || $filtroGrupoPermisos || $filtroPermisos) ? '' : 'hidden' }}">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Búsqueda de Permisos</h3>
                <button id="btnCancelarBusquedaPermisos" class="text-gray-500 hover:text-gray-700 text-xl font-bold">
                    ✕
                </button>
            </div>
            
            <form method="GET" action="{{ route('usuarios.index') }}" id="formBusquedaPermisos">
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-64">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar:</label>
                        <input type="text" id="searchInputPermisos" name="search_permisos" value="{{ $searchPermisos }}" placeholder="Buscar por rol, grupo..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="btnBuscarAvanzadaPermisos" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                            <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                        </button>
                        <a href="{{ route('usuarios.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium">
                            <i class="fas fa-brush mr-2"></i>Limpiar
                        </a>
                    </div>
                </div>
                
                <!-- Panel de búsqueda avanzada -->
                <div id="panelBusquedaAvanzadaPermisos" class="mt-4 p-4 bg-gray-50 rounded-md {{ ($filtroRolPermisos || $filtroGrupoPermisos || $filtroPermisos) ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rol:</label>
                            <input type="text" id="filtroRolPermisos" name="filtro_rol_permisos" value="{{ $filtroRolPermisos }}" placeholder="Filtrar por rol" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grupo:</label>
                            <input type="text" id="filtroGrupoPermisos" name="filtro_grupo_permisos" value="{{ $filtroGrupoPermisos }}" placeholder="Filtrar por grupo" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Permisos:</label>
                            <input type="text" id="filtroPermisos" name="filtro_permisos" value="{{ $filtroPermisos }}" placeholder="Filtrar por permisos" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                
                <!-- Campos ocultos para mantener parámetros de usuarios -->
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="filtro_correo" value="{{ $filtroCorreo }}">
                <input type="hidden" name="filtro_rol" value="{{ $filtroRol }}">
                <input type="hidden" name="filtro_estado" value="{{ $filtroEstado }}">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
            </form>
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
                    @if($rolesDataPaginated->count() > 0)
                        @foreach($rolesDataPaginated as $roleData)
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
                    @else
                        <tr>
                            <td colspan="{{ $canUpdateAsignaciones ? '5' : '4' }}" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-search text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-lg font-medium">No se encontraron resultados</p>
                                    <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            
            <!-- Paginación de Laravel para permisos -->
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <label class="text-sm text-gray-700 mr-2">Mostrar:</label>
                        <select id="perPageSelectPermisos" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white" onchange="changePerPagePermisos(this.value)">
                            <option value="5" {{ request('per_page_permisos', 10) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page_permisos', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page_permisos', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page_permisos', 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page_permisos', 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-700 ml-2">registros por página</span>
                    </div>
                    <div class="flex-1 flex items-center justify-center">
                        {{ $rolesDataPaginated->links('pagination.custom') }}
                    </div>
                    <div class="text-sm text-gray-700">
                        Mostrando {{ $rolesDataPaginated->firstItem() ?? 0 }} a {{ $rolesDataPaginated->lastItem() ?? 0 }} registros de {{ $rolesDataPaginated->total() }} resultados
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
    // Variables globales para búsqueda en tiempo real
    let searchTimeout;
    let isLoading = false;

    // Función para realizar búsqueda en tiempo real
    function performSearchUsuarios() {
        if (isLoading) return;
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchInput = document.getElementById('searchInput');
            const filtroCorreo = document.getElementById('filtroCorreo');
            const filtroRol = document.getElementById('filtroRol');
            const filtroEstado = document.getElementById('filtroEstado');
            const perPageSelect = document.getElementById('perPageSelectUsuarios');
            
            const params = new URLSearchParams();
            
            if (searchInput && searchInput.value) params.append('search', searchInput.value);
            if (filtroCorreo && filtroCorreo.value) params.append('filtro_correo', filtroCorreo.value);
            if (filtroRol && filtroRol.value) params.append('filtro_rol', filtroRol.value);
            if (filtroEstado && filtroEstado.value) params.append('filtro_estado', filtroEstado.value);
            if (perPageSelect && perPageSelect.value) params.append('per_page', perPageSelect.value);
            
            toggleLoadingUsuarios(true);
            
            fetch(`{{ route('usuarios.index') }}?${params.toString()}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Actualizar tabla y paginación
                    const newTable = doc.querySelector('#tablaUsuariosContainer');
                    if (newTable) {
                        document.getElementById('tablaUsuariosContainer').innerHTML = newTable.innerHTML;
                    }
                    
                    // Actualizar URL sin recargar la página
                    const newUrl = `{{ route('usuarios.index') }}?${params.toString()}`;
                    window.history.pushState({}, '', newUrl);
                    
                    toggleLoadingUsuarios(false);
                })
                .catch(error => {
                    console.error('Error en búsqueda:', error);
                    toggleLoadingUsuarios(false);
                });
        }, 500);
    }

    // Función para cambiar registros por página de usuarios
    function changePerPageUsuarios(value) {
        if (isLoading) return;
        
        const searchInput = document.getElementById('searchInput');
        const filtroCorreo = document.getElementById('filtroCorreo');
        const filtroRol = document.getElementById('filtroRol');
        const filtroEstado = document.getElementById('filtroEstado');
        
        const params = new URLSearchParams();
        
        if (searchInput && searchInput.value) params.append('search', searchInput.value);
        if (filtroCorreo && filtroCorreo.value) params.append('filtro_correo', filtroCorreo.value);
        if (filtroRol && filtroRol.value) params.append('filtro_rol', filtroRol.value);
        if (filtroEstado && filtroEstado.value) params.append('filtro_estado', filtroEstado.value);
        params.append('per_page', value);
        
        toggleLoadingUsuarios(true);
        
        fetch(`{{ route('usuarios.index') }}?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Actualizar tabla y paginación de usuarios
                const newTable = doc.querySelector('#tablaUsuariosContainer');
                if (newTable) {
                    document.getElementById('tablaUsuariosContainer').innerHTML = newTable.innerHTML;
                }
                
                // Actualizar tabla de permisos
                const newPermisosTable = doc.querySelector('.mt-8 .bg-white.shadow-md.rounded-lg.overflow-hidden table');
                if (newPermisosTable) {
                    const currentPermisosTable = document.querySelector('.mt-8 .bg-white.shadow-md.rounded-lg.overflow-hidden table');
                    if (currentPermisosTable) {
                        currentPermisosTable.outerHTML = newPermisosTable.outerHTML;
                    }
                }
                
                // Actualizar URL sin recargar la página
                const newUrl = `{{ route('usuarios.index') }}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
                
                toggleLoadingUsuarios(false);
            })
            .catch(error => {
                console.error('Error al cambiar página:', error);
                toggleLoadingUsuarios(false);
            });
    }

    // Función para cambiar registros por página de permisos
    function changePerPagePermisos(value) {
        if (isLoading) return;
        
        const searchInput = document.getElementById('searchInput');
        const filtroCorreo = document.getElementById('filtroCorreo');
        const filtroRol = document.getElementById('filtroRol');
        const filtroEstado = document.getElementById('filtroEstado');
        const perPageSelect = document.getElementById('perPageSelectUsuarios');
        
        const searchInputPermisos = document.getElementById('searchInputPermisos');
        const filtroRolPermisos = document.getElementById('filtroRolPermisos');
        const filtroGrupoPermisos = document.getElementById('filtroGrupoPermisos');
        const filtroPermisos = document.getElementById('filtroPermisos');
        
        const params = new URLSearchParams();
        
        // Parámetros de usuarios
        if (searchInput && searchInput.value) params.append('search', searchInput.value);
        if (filtroCorreo && filtroCorreo.value) params.append('filtro_correo', filtroCorreo.value);
        if (filtroRol && filtroRol.value) params.append('filtro_rol', filtroRol.value);
        if (filtroEstado && filtroEstado.value) params.append('filtro_estado', filtroEstado.value);
        if (perPageSelect && perPageSelect.value) params.append('per_page', perPageSelect.value);
        
        // Parámetros de permisos
        if (searchInputPermisos && searchInputPermisos.value) params.append('search_permisos', searchInputPermisos.value);
        if (filtroRolPermisos && filtroRolPermisos.value) params.append('filtro_rol_permisos', filtroRolPermisos.value);
        if (filtroGrupoPermisos && filtroGrupoPermisos.value) params.append('filtro_grupo_permisos', filtroGrupoPermisos.value);
        if (filtroPermisos && filtroPermisos.value) params.append('filtro_permisos', filtroPermisos.value);
        params.append('per_page_permisos', value);
        
        toggleLoadingUsuarios(true);
        
        fetch(`{{ route('usuarios.index') }}?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Actualizar tabla y paginación de usuarios
                const newTable = doc.querySelector('#tablaUsuariosContainer');
                if (newTable) {
                    document.getElementById('tablaUsuariosContainer').innerHTML = newTable.innerHTML;
                }
                
                // Actualizar tabla de permisos completa (tabla + paginación)
                const newPermisosContainer = doc.querySelector('.mt-8 .bg-white.shadow-md.rounded-lg.overflow-hidden');
                if (newPermisosContainer) {
                    const currentPermisosContainer = document.querySelector('.mt-8 .bg-white.shadow-md.rounded-lg.overflow-hidden');
                    if (currentPermisosContainer) {
                        currentPermisosContainer.outerHTML = newPermisosContainer.outerHTML;
                    }
                }
                
                // Actualizar URL sin recargar la página
                const newUrl = `{{ route('usuarios.index') }}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
                
                toggleLoadingUsuarios(false);
            })
            .catch(error => {
                console.error('Error al cambiar página:', error);
                toggleLoadingUsuarios(false);
            });
    }

    // Función para mostrar/ocultar loading
    function toggleLoadingUsuarios(show) {
        isLoading = show;
        const container = document.getElementById('tablaUsuariosContainer');
        if (container) {
            container.style.opacity = show ? '0.6' : '1';
            container.style.pointerEvents = show ? 'none' : 'auto';
        }
    }

    // Función para realizar búsqueda en tiempo real de permisos
    function performSearchPermisos() {
        if (isLoading) return;
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchInput = document.getElementById('searchInput');
            const filtroCorreo = document.getElementById('filtroCorreo');
            const filtroRol = document.getElementById('filtroRol');
            const filtroEstado = document.getElementById('filtroEstado');
            const perPageSelect = document.getElementById('perPageSelectUsuarios');
            
            const searchInputPermisos = document.getElementById('searchInputPermisos');
            const filtroRolPermisos = document.getElementById('filtroRolPermisos');
            const filtroGrupoPermisos = document.getElementById('filtroGrupoPermisos');
            const filtroPermisos = document.getElementById('filtroPermisos');
            const perPageSelectPermisos = document.getElementById('perPageSelectPermisos');
            
            const params = new URLSearchParams();
            
            // Parámetros de usuarios
            if (searchInput && searchInput.value) params.append('search', searchInput.value);
            if (filtroCorreo && filtroCorreo.value) params.append('filtro_correo', filtroCorreo.value);
            if (filtroRol && filtroRol.value) params.append('filtro_rol', filtroRol.value);
            if (filtroEstado && filtroEstado.value) params.append('filtro_estado', filtroEstado.value);
            if (perPageSelect && perPageSelect.value) params.append('per_page', perPageSelect.value);
            
            // Parámetros de permisos
            if (searchInputPermisos && searchInputPermisos.value) params.append('search_permisos', searchInputPermisos.value);
            if (filtroRolPermisos && filtroRolPermisos.value) params.append('filtro_rol_permisos', filtroRolPermisos.value);
            if (filtroGrupoPermisos && filtroGrupoPermisos.value) params.append('filtro_grupo_permisos', filtroGrupoPermisos.value);
            if (filtroPermisos && filtroPermisos.value) params.append('filtro_permisos', filtroPermisos.value);
            if (perPageSelectPermisos && perPageSelectPermisos.value) params.append('per_page_permisos', perPageSelectPermisos.value);
            
            toggleLoadingUsuarios(true);
            
            fetch(`{{ route('usuarios.index') }}?${params.toString()}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Actualizar tabla y paginación de usuarios
                    const newTable = doc.querySelector('#tablaUsuariosContainer');
                    if (newTable) {
                        document.getElementById('tablaUsuariosContainer').innerHTML = newTable.innerHTML;
                    }
                    
                    // Actualizar tabla de permisos
                    const newPermisosTable = doc.querySelector('.mt-8 .bg-white.shadow-md.rounded-lg.overflow-hidden table');
                    if (newPermisosTable) {
                        const currentPermisosTable = document.querySelector('.mt-8 .bg-white.shadow-md.rounded-lg.overflow-hidden table');
                        if (currentPermisosTable) {
                            currentPermisosTable.outerHTML = newPermisosTable.outerHTML;
                        }
                    }
                    
                    // Actualizar URL sin recargar la página
                    const newUrl = `{{ route('usuarios.index') }}?${params.toString()}`;
                    window.history.pushState({}, '', newUrl);
                    
                    toggleLoadingUsuarios(false);
                })
                .catch(error => {
                    console.error('Error en búsqueda:', error);
                    toggleLoadingUsuarios(false);
                });
        }, 500);
    }

    // Event listeners para búsqueda en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filtroCorreo = document.getElementById('filtroCorreo');
        const filtroRol = document.getElementById('filtroRol');
        const filtroEstado = document.getElementById('filtroEstado');
        
        if (searchInput) {
            searchInput.addEventListener('input', performSearchUsuarios);
        }
        
        if (filtroCorreo) {
            filtroCorreo.addEventListener('input', performSearchUsuarios);
        }
        
        if (filtroRol) {
            filtroRol.addEventListener('change', performSearchUsuarios);
        }
        
        if (filtroEstado) {
            filtroEstado.addEventListener('change', performSearchUsuarios);
        }
        
        // Event listeners para búsqueda de permisos
        const searchInputPermisos = document.getElementById('searchInputPermisos');
        const filtroRolPermisos = document.getElementById('filtroRolPermisos');
        const filtroGrupoPermisos = document.getElementById('filtroGrupoPermisos');
        const filtroPermisos = document.getElementById('filtroPermisos');
        
        if (searchInputPermisos) {
            searchInputPermisos.addEventListener('input', performSearchPermisos);
        }
        
        if (filtroRolPermisos) {
            filtroRolPermisos.addEventListener('input', performSearchPermisos);
        }
        
        if (filtroGrupoPermisos) {
            filtroGrupoPermisos.addEventListener('input', performSearchPermisos);
        }
        
        if (filtroPermisos) {
            filtroPermisos.addEventListener('input', performSearchPermisos);
        }
        
        // Botón para mostrar/ocultar búsqueda avanzada de usuarios
        const btnBuscarAvanzada = document.getElementById('btnBuscarAvanzadaUsuarios');
        const panelBusquedaAvanzada = document.getElementById('panelBusquedaAvanzadaUsuarios');
        
        if (btnBuscarAvanzada && panelBusquedaAvanzada) {
            btnBuscarAvanzada.addEventListener('click', function() {
                panelBusquedaAvanzada.classList.toggle('hidden');
            });
        }
        
        // Botón para mostrar/ocultar búsqueda avanzada de permisos
        const btnBuscarAvanzadaPermisos = document.getElementById('btnBuscarAvanzadaPermisos');
        const panelBusquedaAvanzadaPermisos = document.getElementById('panelBusquedaAvanzadaPermisos');
        
        if (btnBuscarAvanzadaPermisos && panelBusquedaAvanzadaPermisos) {
            btnBuscarAvanzadaPermisos.addEventListener('click', function() {
                panelBusquedaAvanzadaPermisos.classList.toggle('hidden');
            });
        }
        
        // Botón para cancelar búsqueda de usuarios
        const btnCancelarBusqueda = document.getElementById('btnCancelarBusquedaUsuarios');
        const panelBusqueda = document.getElementById('panelBusquedaUsuarios');
        
        if (btnCancelarBusqueda && panelBusqueda) {
            btnCancelarBusqueda.addEventListener('click', function() {
                panelBusqueda.classList.add('hidden');
            });
        }
        
        // Botón para cancelar búsqueda de permisos
        const btnCancelarBusquedaPermisos = document.getElementById('btnCancelarBusquedaPermisos');
        const panelBusquedaPermisos = document.getElementById('panelBusquedaPermisos');
        
        if (btnCancelarBusquedaPermisos && panelBusquedaPermisos) {
            btnCancelarBusquedaPermisos.addEventListener('click', function() {
                panelBusquedaPermisos.classList.add('hidden');
            });
        }
    });

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
        
        // Validación de Bootstrap primero
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }
        
        const isEdit = document.getElementById('modal_user_title').textContent === 'Editar usuario';
        const correo = document.getElementById('input_correo_add_user').value;
        const rol_id = document.getElementById('select_rol_add_user').value;
        const usuario_estado = document.getElementById('switch_add_user').checked ? 1 : 0;
        let contrasena = document.getElementById('input_pass_add_user').value;
        let contrasena_confirmation = document.getElementById('input_passconfirm_add_user').value;
        
        // Validar que las contraseñas coincidan si están visibles
        if (document.getElementById('div_pass_add_user').style.display !== 'none') {
            if (contrasena !== contrasena_confirmation) {
                // Bootstrap se encarga de mostrar el error en el campo
                document.getElementById('input_passconfirm_add_user').setCustomValidity('Las contraseñas no coinciden');
                document.getElementById('form_add_users').reportValidity();
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
                    // Bootstrap se encarga de mostrar el error en los campos
                    document.getElementById('form_add_users').reportValidity();
                    return;
                }
                if (contrasena !== contrasena_confirmation) {
                    // Bootstrap se encarga de mostrar el error en el campo
                    document.getElementById('input_passconfirm_add_user').setCustomValidity('Las contraseñas no coinciden');
                    document.getElementById('form_add_users').reportValidity();
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

    // Event listeners para mostrar búsqueda
    document.addEventListener('DOMContentLoaded', function() {
        // Botón para mostrar búsqueda de usuarios
        const btnMostrarBusquedaUsuarios = document.getElementById('btnMostrarBusquedaUsuarios');
        const panelBusquedaUsuarios = document.getElementById('panelBusquedaUsuarios');
        
        if (btnMostrarBusquedaUsuarios && panelBusquedaUsuarios) {
            btnMostrarBusquedaUsuarios.addEventListener('click', function() {
                panelBusquedaUsuarios.classList.remove('hidden');
            });
        }
        
        // Botón para mostrar búsqueda de permisos
        const btnMostrarBusquedaPermisos = document.getElementById('btnMostrarBusquedaPermisos');
        const panelBusquedaPermisos = document.getElementById('panelBusquedaPermisos');
        
        if (btnMostrarBusquedaPermisos && panelBusquedaPermisos) {
            btnMostrarBusquedaPermisos.addEventListener('click', function() {
                panelBusquedaPermisos.classList.remove('hidden');
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