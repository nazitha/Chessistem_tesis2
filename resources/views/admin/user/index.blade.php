@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Gestión de Usuarios</h1>
    
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-200">Nombre</th>
                    <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-200">Correo</th>
                    <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-200">Rol</th>
                    <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-200">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $user->rol->nombre }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.users.permissions', $user) }}" 
                           class="text-blue-600 hover:text-blue-400 dark:text-blue-400 dark:hover:text-blue-300">
                            Editar permisos
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection