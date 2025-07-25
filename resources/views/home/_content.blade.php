@php
    use App\Helpers\PermissionHelper;
@endphp

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
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">{{ $card['title'] }}</h3>
                <i class="bx bx-{{ $card['icon'] }} text-2xl text-gray-600"></i>
            </div>
            <p class="text-gray-600 mt-2">{{ $card['description'] }}</p>
            <a href="{{ route($card['route']) }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                Acceder <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>
    @endforeach
</div>

<!-- Tablas -->
<div class="mt-8">
    <div class="flex flex-wrap gap-4 mb-6">
        <a href="{{ route('torneos.create') }}" class="btn btn-primary flex items-center"><i class="fa fa-trophy mr-2"></i> Nuevo Torneo</a>
        <a href="{{ route('academias.index') }}" class="btn btn-success flex items-center"><i class="fa fa-school mr-2"></i> Gestionar Academias</a>
        <a href="{{ route('miembros.index') }}" class="btn btn-primary flex items-center"><i class="fa fa-users mr-2"></i> Gestionar Miembros</a>
        @if(Auth::user()->rol_id == 1)
        <a href="{{ route('usuarios.index') }}" class="btn btn-info flex items-center"><i class="fa fa-user-shield mr-2"></i> Gestionar Usuarios</a>
        @endif
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <div id="grafico-historial-partidas" style="height: 400px;"></div>
        </div>
        <div class="col-md-6">
            <div id="grafico-estadisticas-torneos" style="height: 400px;"></div>
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