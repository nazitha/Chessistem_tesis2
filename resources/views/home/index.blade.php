@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Navegación superior -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <img class="h-16 w-auto" src="{{ asset('img/estrellas_del_ajedrez_logo.png') }}" alt="Escuela Estrellas del Ajedrez">
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('home') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        @if(Auth::user()->rol_id == 1)
                        <a href="{{ route('torneos.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Torneos
                        </a>
                        <a href="{{ route('usuarios.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Usuarios
                        </a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="py-10">
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold leading-tight text-gray-900">Dashboard</h1>
                <p class="mt-2 text-gray-600">Bienvenido al sistema de gestión de torneos de ajedrez</p>
            </div>
        </header>
        <main>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Grid de tarjetas -->
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($dashboardCards as $card)
                    <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300">
                        <a href="{{ route($card['route']) }}" class="block p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                    <i class="fas fa-{{ $card['icon'] }} text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $card['title'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ $card['description'] }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

                @if(Auth::user()->rol_id == 1)
                <!-- Sección de acciones rápidas para administradores -->
                <div class="mt-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <a href="{{ route('torneos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>
                            Nuevo Torneo
                        </a>
                        <a href="{{ route('academias.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-school mr-2"></i>
                            Gestionar Academias
                        </a>
                        <a href="{{ route('miembros.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-users mr-2"></i>
                            Gestionar Miembros
                        </a>
                        <a href="{{ route('inscripciones.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Ver Inscripciones
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>
</div>

@include('modals.miembros_modal')
@include('modals.pais_modal')
@include('modals.partidasbusqueda_modal')
@include('modals.torneo_modal')
@endsection