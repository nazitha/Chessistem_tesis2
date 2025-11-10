@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
    use App\Services\PermissionService;
    use Illuminate\Support\Facades\Log;
    
    $canViewTorneos = PermissionHelper::canViewModule('torneos');
    $canViewMiembros = PermissionHelper::canViewModule('miembros');
    $canViewAcademias = PermissionService::hasPermission('academias.read');
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

<style>
    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: all 0.3s ease;
    }
    
    .card-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .card-link:hover .text-blue-600 {
        color: #2563eb !important;
    }
    
    /* Asegurar que los cards tengan layout horizontal */
    .card-link .flex {
        display: flex !important;
        align-items: center !important;
        flex-direction: row !important;
    }
    
    .card-link .flex-shrink-0 {
        flex-shrink: 0 !important;
    }
    
    .card-link .flex-1 {
        flex: 1 !important;
        min-width: 0 !important;
    }
</style>

<div class="min-h-screen bg-gray-50">
    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Bienvenido al sistema de gestión de torneos de ajedrez</p>

        <!-- Tarjetas principales -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card Mi Perfil -->
            <a href="{{ route('profile') }}" class="card-link bg-white rounded-lg shadow p-6" style="display: flex; align-items: flex-start;">
                <div class="p-2 bg-blue-100 rounded-lg mr-4" style="flex-shrink: 0;">
                    <i class="fas fa-user-circle text-blue-600 text-2xl"></i>
                </div>
                <div style="flex: 1;">
                    <h3 class="text-lg font-medium text-gray-900">Mi Perfil</h3>
                    <p class="text-gray-600 mt-1">Edita tu perfil</p>
                </div>
            </a>

            <!-- Card Usuarios -->
            @if(PermissionHelper::canViewModule('usuarios'))
            <a href="{{ route('usuarios.index') }}" class="card-link bg-white rounded-lg shadow p-6" style="display: flex; align-items: flex-start;">
                <div class="p-2 bg-blue-100 rounded-lg mr-4" style="flex-shrink: 0;">
                    <i class="fas fa-users-cog text-blue-600 text-2xl"></i>
                </div>
                <div style="flex: 1;">
                    <h3 class="text-lg font-medium text-gray-900">Usuarios</h3>
                    <p class="text-gray-600 mt-1">Gestión de usuarios del sistema</p>
                </div>
            </a>
            @endif

            <!-- Card Academias -->
            @if($canViewAcademias)
            <a href="{{ route('academias.index') }}" class="card-link bg-white rounded-lg shadow p-6" style="display: flex; align-items: flex-start;">
                <div class="p-2 bg-blue-100 rounded-lg mr-4" style="flex-shrink: 0;">
                    <i class="fas fa-graduation-cap text-blue-600 text-2xl"></i>
                </div>
                <div style="flex: 1;">
                    <h3 class="text-lg font-medium text-gray-900">Academias</h3>
                    <p class="text-gray-600 mt-1">Ver y editar academias</p>
                </div>
            </a>
            @endif

            <!-- Card Torneos -->
            @if($canViewTorneos)
            <a href="{{ route('torneos.index') }}" class="card-link bg-white rounded-lg shadow p-6" style="display: flex; align-items: flex-start;">
                <div class="p-2 bg-blue-100 rounded-lg mr-4" style="flex-shrink: 0;">
                    <i class="fas fa-chess text-blue-600 text-2xl"></i>
                </div>
                <div style="flex: 1;">
                    <h3 class="text-lg font-medium text-gray-900">Torneos</h3>
                    <p class="text-gray-600 mt-1">Administración de torneos</p>
                </div>
            </a>
            @endif
        </div>

        <!-- Acciones Rápidas -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-medium text-gray-900 mb-4">Acciones Rápidas</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <a href="{{ route('torneos.create') }}" class="block w-full text-center rounded-lg p-3 min-h-[56px] bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-trophy text-base"></i>
                            <span class="text-sm sm:text-base leading-tight">Nuevo Torneo</span>
                        </div>
                    </a>
                    @if($canViewAcademias)
                        <a href="{{ route('academias.index') }}" class="block w-full text-center rounded-lg p-3 min-h-[56px] bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-school text-base"></i>
                                <span class="text-sm sm:text-base leading-tight">Gestionar Academias</span>
                            </div>
                        </a>
                    @endif
                    <a href="{{ route('miembros.index') }}" class="block w-full text-center rounded-lg p-3 min-h-[56px] bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-users text-base"></i>
                            <span class="text-sm sm:text-base leading-tight">Gestionar Miembros</span>
                        </div>
                    </a>
                    @if($canViewAuditorias)
                    <a href="{{ route('auditoria.index') }}" class="block w-full text-center rounded-lg p-3 min-h-[56px] bg-gray-600 text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-grenn-400 transition-colors">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-clipboard-list text-base"></i>
                            <span class="text-sm sm:text-base leading-tight">Auditoría</span>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        @if($canViewMisEstadisticas || $canViewEstadisticasAdmin)
        <div class="mt-8 grid grid-cols-1 {{ ($canViewMisEstadisticas && $canViewEstadisticasAdmin) ? 'lg:grid-cols-2' : '' }} gap-6">
            @if($canViewMisEstadisticas)
            <!-- Desempeño -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Desempeño</h2>
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

        <!-- Tarjeta de Análisis de Partidas -->
        <div class="mt-8">
            @include('home.analisis_partidas_card', ['partidasAnalisis' => $partidasAnalisis])
        </div>
    </div>
</div>

<div id="modalEditarUsuario" class="fixed inset-0 bg-gray-200/80 backdrop-blur-md flex items-center justify-center z-50 hidden">
@endsection