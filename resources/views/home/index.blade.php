@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
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

            @if(PermissionHelper::canViewModule('usuarios'))
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
            @endif

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
                    <a href="{{ route('inscripciones.index') }}" class="inline-block text-center py-2 px-4 bg-gray-500 text-white rounded hover:bg-gray-600">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Ver Inscripciones
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Rendimiento en Torneos -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Rendimiento en Torneos</h2>
                    <button class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-8">
                    <div class="text-center">
                        <p style="color: #FF6B00;" class="text-2xl font-semibold">0</p>
                        <p class="text-gray-600">Partidas Jugadas</p>
                    </div>
                    <div class="text-center">
                        <p style="color: #00A651;" class="text-2xl font-semibold">0</p>
                        <p class="text-gray-600">Victorias</p>
                    </div>
                    <div class="text-center">
                        <p style="color: #0D6EFD;" class="text-2xl font-semibold">1200</p>
                        <p class="text-gray-600">Rating ELO</p>
                    </div>
                </div>
            </div>

            <!-- Análisis de Partidas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Análisis de Partidas</h2>
                    <a href="#" class="text-blue-600 hover:text-blue-700">Ver todas las partidas</a>
                </div>
                <p class="text-gray-600">No hay partidas analizadas recientemente</p>
            </div>
        </div>
        
        <!-- Entrenamiento y Progreso -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Entrenamiento y Progreso</h2>
                    <div class="flex space-x-2">
                        <button class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-compress"></i>
                        </button>
                        <button class="text-blue-500 hover:text-blue-600">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>

                <div class="flex justify-between gap-4">
                    <!-- Ejercicios Tácticos -->
                    <div class="w-1/3 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-base font-medium text-gray-900">Ejercicios Tácticos</h3>
                        <p class="text-sm text-gray-500 mb-2">Completados</p>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-blue-500">0/100</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1">
                            <div class="bg-blue-500 h-1 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Aperturas -->
                    <div class="w-1/3 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-base font-medium text-gray-900">Aperturas</h3>
                        <p class="text-sm text-gray-500 mb-2">En progreso</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-500">0</span>
                            <a href="#" class="text-sm text-blue-500 hover:text-blue-600">Practicar apertura</a>
                        </div>
                    </div>

                    <!-- Finales -->
                    <div class="w-1/3 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-base font-medium text-gray-900">Finales</h3>
                        <p class="text-sm text-gray-500 mb-2">Temas dominados</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-blue-300">0/10</span>
                            <a href="#" class="text-sm text-blue-500 hover:text-blue-600">Practicar finales</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('modals.miembros_modal')
@include('modals.pais_modal')
@include('modals.partidasbusqueda_modal')
@include('modals.torneo_modal')
@endsection