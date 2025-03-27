@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Gestión de Usuarios</h1>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Nombre</th>
                    <th class="px-6 py-3 text-left">Correo</th>
                    <th class="px-6 py-3 text-left">Rol</th>
                    <th class="px-6 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ $user->rol->nombre }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.users.permissions', $user) }}" 
                           class="text-blue-600 hover:text-blue-900">
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