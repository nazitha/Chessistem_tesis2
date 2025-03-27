<header class="bg-white shadow mb-6">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Bienvenido, {{ Auth::user()->correo }}
            <span class="text-sm text-gray-500">
                (Rol: {{ Auth::user()->rol->nombre ?? 'Sin rol' }})
            </span>
        </h1>
    </div>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Tarjetas dinámicas -->
    @foreach($dashboardCards as $card)
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold">{{ $card['title'] }}</h3>
            <p class="text-gray-600">{{ $card['content'] }}</p>
        </div>
    @endforeach
</div>

<!-- Tablas -->
<div class="mt-8">
    @include('tables.tabla_usuarios')
    @include('tables.tabla_federaciones')
    @include('tables.tabla_historial')
    @include('tables.tabla_ciudades')
    @include('tables.tabla_academias')
    @include('tables.tabla_miembros')
    @include('tables.tabla_fides')
    @include('tables.tabla_torneos')
    @include('tables.tabla_inscripciones')
    @include('tables.tabla_partidas')    
</div>