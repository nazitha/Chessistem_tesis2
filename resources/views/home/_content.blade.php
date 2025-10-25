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
    <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 mb-6">
        <a href="{{ route('torneos.create') }}" class="btn btn-primary flex items-center justify-center sm:justify-start text-sm sm:text-base px-4 py-2">
            <i class="fa fa-trophy mr-2"></i> 
            <span class="hidden sm:inline">Nuevo Torneo</span>
            <span class="sm:hidden">Torneo</span>
        </a>
        <a href="{{ route('academias.index') }}" class="btn btn-success flex items-center justify-center sm:justify-start text-sm sm:text-base px-4 py-2">
            <i class="fa fa-school mr-2"></i> 
            <span class="hidden sm:inline">Gestionar Academias</span>
            <span class="sm:hidden">Academias</span>
        </a>
        <a href="{{ route('miembros.index') }}" class="btn btn-primary flex items-center justify-center sm:justify-start text-sm sm:text-base px-4 py-2">
            <i class="fa fa-users mr-2"></i> 
            <span class="hidden sm:inline">Gestionar Miembros</span>
            <span class="sm:hidden">Miembros</span>
        </a>
        @if(PermissionHelper::canViewModule('participantes'))
        <a href="{{ route('participantes.index') }}" class="btn btn-warning flex items-center justify-center sm:justify-start text-sm sm:text-base px-4 py-2">
            <i class="fa fa-user-plus mr-2"></i> 
            <span class="hidden sm:inline">Gestionar Participantes</span>
            <span class="sm:hidden">Participantes</span>
        </a>
        @endif
        @if(Auth::user()->rol_id == 1)
        <a href="{{ route('usuarios.index') }}" class="btn btn-info flex items-center justify-center sm:justify-start text-sm sm:text-base px-4 py-2">
            <i class="fa fa-user-shield mr-2"></i> 
            <span class="hidden sm:inline">Gestionar Usuarios</span>
            <span class="sm:hidden">Usuarios</span>
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