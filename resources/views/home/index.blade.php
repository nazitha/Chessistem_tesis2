@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use Illuminate\Support\Facades\Log;
    
    $canViewTorneos = PermissionHelper::canViewModule('torneos');
    $canViewMiembros = PermissionHelper::canViewModule('miembros');
    $canViewAcademias = PermissionHelper::canViewModule('academias');
    $canViewAuditorias = PermissionHelper::canViewModule('auditorias');
    $canViewMisEstadisticas = PermissionHelper::canViewMisEstadisticas();
    $canViewEstadisticasAdmin = PermissionHelper::canViewEstadisticasAdmin();
    
    // Debug de permisos
    Log::info('Vista home: Verificando permisos', [
        'can_view_torneos' => $canViewTorneos,
        'can_create_torneos' => PermissionHelper::canCreate('torneos'),
        'can_view_miembros' => $canViewMiembros,
        'can_view_academias' => $canViewAcademias,
        'can_view_auditorias' => $canViewAuditorias,
        'can_view_mis_estadisticas' => $canViewMisEstadisticas,
        'can_view_estadisticas_admin' => $canViewEstadisticasAdmin
    ]);
@endphp

<div class="min-h-screen bg-gray-50">
    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Bienvenido al sistema de gestión de torneos de ajedrez</p>

        <!-- Tarjetas principales -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('profile') }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Mi Perfil</h3>
                        <p class="text-gray-600 mt-1">Edita tu perfil</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('usuarios.index') }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-users-cog text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Usuarios</h3>
                        <p class="text-gray-600 mt-1">Gestión de usuarios del sistema</p>
                    </div>
                </div>
            </a>

            @if($canViewAcademias)
            <a href="{{ route('academias.index') }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Academias</h3>
                        <p class="text-gray-600 mt-1">Ver y editar academias</p>
                    </div>
                </div>
            </a>
            @endif

            @if($canViewTorneos)
            <a href="{{ route('torneos.index') }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-chess text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Torneos</h3>
                        <p class="text-gray-600 mt-1">Administración de torneos</p>
                    </div>
                </div>
            </a>
            @endif
        </div>

        <!-- Acciones Rápidas -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-medium text-gray-900 mb-4">Acciones Rápidas</h2>
                <div class="grid grid-cols-4 gap-4">
                    <a href="{{ route('torneos.create') }}" class="inline-block text-center py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <i class="fas fa-trophy mr-2"></i>
                        Nuevo Torneo
                    </a>
                    <a href="{{ route('academias.index') }}" class="inline-block text-center py-2 px-4 bg-green-500 text-white rounded hover:bg-green-600">
                        <i class="fas fa-school mr-2"></i>
                        Gestionar Academias
                    </a>
                    <a href="{{ route('miembros.index') }}" class="inline-block text-center py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <i class="fas fa-users mr-2"></i>
                        Gestionar Miembros
                    </a>
                    @auth
                    <a href="{{ route('auditoria.index') }}" class="inline-block text-center py-2 px-4 bg-gray-500 text-white rounded hover:bg-gray-600">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Auditoría
                    </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        @if($canViewMisEstadisticas || $canViewEstadisticasAdmin)
        <div class="mt-8 grid grid-cols-1 {{ ($canViewMisEstadisticas && $canViewEstadisticasAdmin) ? 'lg:grid-cols-2' : '' }} gap-6">
            @if($canViewMisEstadisticas)
            <!-- Historial de Partidas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Historial de Partidas</h2>
                </div>
                <!-- Contenedor para el gráfico -->
                <div id="grafico-historial-partidas" class="w-full"></div>
            </div>
            @endif

            @if($canViewEstadisticasAdmin)
            <!-- Estadísticas de Torneos -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Estadísticas de Torneos</h2>
                </div>
                <!-- Contenedor para el gráfico -->
                <div id="grafico-estadisticas-torneos" class="w-full"></div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<div id="modalEditarUsuario" class="fixed inset-0 bg-gray-200/80 backdrop-blur-md flex items-center justify-center z-50 hidden">
@endsection