@php
    use App\Helpers\PermissionHelper;
@endphp

<header class="bg-white shadow mb-4 sm:mb-6">
    <div class="px-4 py-4 sm:px-6 sm:py-6">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">
            Bienvenido, {{ Auth::user()->correo }}
        </h1>
        <p class="text-sm sm:text-base text-gray-500 mt-1">
            Rol: {{ Auth::user()->rol->nombre ?? 'Sin rol' }}
        </p>
    </div>
</header>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
    <!-- Tarjetas dinámicas -->
    @foreach($dashboardCards as $card)
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900">{{ $card['title'] }}</h3>
                <i class="bx bx-{{ $card['icon'] }} text-xl sm:text-2xl text-gray-600"></i>
            </div>
            <p class="text-sm sm:text-base text-gray-600 mb-4">{{ $card['description'] }}</p>
            <a href="{{ route($card['route']) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm sm:text-base font-medium transition-colors">
                Acceder <i class="bx bx-right-arrow-alt ml-1"></i>
            </a>
        </div>
    @endforeach
</div>

<!-- Acciones rápidas -->
<div class="mt-6 sm:mt-8">
    <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Acciones Rápidas</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mb-2">
        <a href="{{ route('torneos.create') }}" class="min-h-[44px] flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm sm:text-base font-medium shadow-sm ring-1 ring-black/5 bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-colors whitespace-nowrap">
            <i class="fa fa-trophy"></i>
            <span class="hidden xs:inline">Nuevo Torneo</span>
            <span class="xs:hidden">Torneo</span>
        </a>
        <a href="{{ route('academias.index') }}" class="min-h-[44px] flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm sm:text-base font-medium shadow-sm ring-1 ring-black/5 bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors whitespace-nowrap">
            <i class="fa fa-school"></i>
            <span class="hidden xs:inline">Gestionar Academias</span>
            <span class="xs:hidden">Academias</span>
        </a>
        <a href="{{ route('miembros.index') }}" class="min-h-[44px] flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm sm:text-base font-medium shadow-sm ring-1 ring-black/5 bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors whitespace-nowrap">
            <i class="fa fa-users"></i>
            <span class="hidden xs:inline">Gestionar Miembros</span>
            <span class="xs:hidden">Miembros</span>
        </a>
        @if(PermissionHelper::canViewModule('participantes'))
        <a href="{{ route('participantes.index') }}" class="min-h-[44px] flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm sm:text-base font-medium shadow-sm ring-1 ring-black/5 bg-amber-500 text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-300 transition-colors whitespace-nowrap">
            <i class="fa fa-user-plus"></i>
            <span class="hidden xs:inline">Gestionar Participantes</span>
            <span class="xs:hidden">Participantes</span>
        </a>
        @endif
        @if(Auth::user()->rol_id == 1)
        <a href="{{ route('usuarios.index') }}" class="min-h-[44px] flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm sm:text-base font-medium shadow-sm ring-1 ring-black/5 bg-slate-600 text-white hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors whitespace-nowrap">
            <i class="fa fa-user-shield"></i>
            <span class="hidden xs:inline">Gestionar Usuarios</span>
            <span class="xs:hidden">Usuarios</span>
        </a>
        @endif
    </div>
    
    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mt-6">
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Historial de Partidas</h3>
            <div id="grafico-historial-partidas" style="height: 300px; min-height: 300px;"></div>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Estadísticas de Torneos</h3>
            <div id="grafico-estadisticas-torneos" style="height: 300px; min-height: 300px;"></div>
        </div>
    </div>
    @if(Auth::user()->rol_id == 1)
        @include('tables.tabla_usuarios')
        @include('tables.tabla_federaciones')
        @include('tables.tabla_historial')
        @include('tables.tabla_ciudades')
        @if(PermissionHelper::canViewModule('academias'))
        @include('tables.tabla_academias')
        @endif
        @include('tables.tabla_miembros')
        @include('tables.tabla_fides')
        @include('tables.tabla_torneos')
        @include('tables.tabla_inscripciones')
        @include('tables.tabla_partidas')
    @elseif(Auth::user()->rol_id == 2)
        @include('tables.tabla_torneos')
        @include('tables.tabla_partidas')
    @elseif(Auth::user()->rol_id == 3)
        @include('tables.tabla_torneos')
        @include('tables.tabla_inscripciones')
    @elseif(Auth::user()->rol_id == 4)
        @include('tables.tabla_torneos')
        @include('tables.tabla_inscripciones')
        @include('tables.tabla_partidas')
    @endif
</div>