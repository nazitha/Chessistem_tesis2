@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Navegación superior -->
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0">
                        <img class="h-16 w-auto" src="{{ asset('img/estrellas_del_ajedrez_logo.png') }}" alt="Escuela Estrellas del Ajedrez">
                    </div>
                    <div class="flex space-x-8">
                        <a href="{{ route('home') }}" class="border-b-2 border-indigo-500 text-gray-900 px-1 pt-1 text-sm font-medium">Home</a>
                        <a href="{{ route('usuarios.index') }}" class="text-gray-500 hover:text-gray-700 px-1 pt-1 text-sm font-medium">Usuarios</a>
                        <a href="{{ route('miembros.index') }}" class="text-gray-500 hover:text-gray-700 px-1 pt-1 text-sm font-medium">Miembros</a>
                        <a href="{{ route('fides.index') }}" class="text-gray-500 hover:text-gray-700 px-1 pt-1 text-sm font-medium">FIDES</a>
                        <a href="{{ route('torneos.index') }}" class="text-gray-500 hover:text-gray-700 px-1 pt-1 text-sm font-medium">Torneos</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-500 text-sm mr-4">Bienvenido, {{ Auth::user()->correo }}</span>
                    <a href="{{ route('logout') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Cerrar Sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido del perfil -->
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-6">Mi Perfil</h2>

                    <!-- Información Personal -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Rol</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->rol_id == 1 ? 'Administrador' : 'Usuario' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Cambiar Contraseña -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Contraseña</h3>
                        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña Actual</label>
                                <input type="password" name="current_password" id="current_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 