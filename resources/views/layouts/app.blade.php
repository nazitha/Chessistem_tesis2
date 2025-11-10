@php
    use App\Helpers\PermissionHelper;
    use App\Services\PermissionService;
    $canViewTorneos = PermissionHelper::canViewModule('torneos');
    $canViewMiembros = PermissionHelper::canViewModule('miembros');
    $canViewAcademias = PermissionService::hasPermission('academias.read');
@endphp

<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ChessSystem')</title>
    
    <!-- Fuentes -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    <!-- Tailwind CSS -->
    <link href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.21/dist/sweetalert2.min.css">
    
    <!-- Box Icons -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/tailwind_datatables.css') }}">
    
    <style>
        /* Estilo global para eliminar subrayados en todos los enlaces */
        a {
            text-decoration: none !important;
        }
        
        a:hover {
            text-decoration: none !important;
        }
        
        a:focus {
            text-decoration: none !important;
        }
        
        a:visited {
            text-decoration: none !important;
        }
        
        /* Asegurar que no haya subrayados en los enlaces del navbar */
        nav a {
            text-decoration: none !important;
        }
        
        /* Asegurar que no haya subrayados en hover */
        nav a:hover {
            text-decoration: none !important;
        }
        
        /* Asegurar que no haya subrayados en enlaces activos */
        nav a.active {
            text-decoration: none !important;
        }
        
        /* Asegurar que no haya subrayados en focus */
        nav a:focus {
            text-decoration: none !important;
        }
        
        /* Asegurar que no haya subrayados en visited */
        nav a:visited {
            text-decoration: none !important;
        }
        
        /* Estilos específicos para el navbar principal */
        .flex.space-x-8 a {
            text-decoration: none !important;
            border-bottom: none !important;
        }
        
        .flex.space-x-8 a:hover {
            text-decoration: none !important;
            border-bottom: none !important;
        }
        
        .flex.space-x-8 a:focus {
            text-decoration: none !important;
            border-bottom: none !important;
        }
        
        /* Estilos específicos para el navbar usando el selector de clase */
        .bg-white.shadow nav a,
        .bg-white.shadow .flex.space-x-8 a {
            text-decoration: none !important;
            border-bottom: none !important;
        }
        
        .bg-white.shadow nav a:hover,
        .bg-white.shadow .flex.space-x-8 a:hover {
            text-decoration: none !important;
            border-bottom: none !important;
        }
        
        /* Asegurar que los modales estén por encima del navbar fijo */
        .modal {
            z-index: 3000 !important;
        }
        .modal-backdrop {
            z-index: 2990 !important;
        }
    </style>
    <style>
    [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
    <style>
    html, body {
        opacity: 1 !important;
        filter: none !important;
        background: #f9fafb !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important; /* Evitar scroll horizontal global */
    }
    
    /* Asegurar que el navbar esté en la parte superior */
    body {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
    }
    
    /* Navbar fijo en la parte superior */
    nav.shadow {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 2000 !important;
        order: -1 !important;
        pointer-events: auto !important;
    }
    
    /* Contenido principal */
    main {
        flex: 1 !important;
        order: 1 !important;
    }
    
    /* Footer al final */
    footer {
        order: 2 !important;
    }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <!-- Navegación superior -->
    <nav x-data="{ open: false }" @scroll.window="open = false" class="shadow fixed top-0 left-0 right-0 z-50" style="background-color: #282c34;">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center h-16 px-4">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}">
                        <img class="h-12 w-auto sm:h-16" src="{{ asset('img/estrellas_del_ajedrez_logo.png') }}" alt="Escuela Estrellas del Ajedrez">
                    </a>
                </div>

                <!-- Menú desktop -->
                <div class="hidden md:flex items-center space-x-6 lg:space-x-8">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium no-underline transition-colors">Inicio</a>
                    
                    @if(Auth::check() && Auth::user()->rol_id == 1)
                        <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium transition-colors">Usuarios</a>
                    @endif

                    <a href="{{ route('miembros.index') }}" class="{{ request()->routeIs('miembros.*') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium transition-colors">Miembros</a>
                    @if($canViewAcademias)
                        <a href="{{ route('academias.index') }}" class="{{ request()->routeIs('academias.*') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium transition-colors">Academias</a>
                    @endif
                    <a href="{{ route('torneos.index') }}" class="{{ request()->routeIs('torneos.*') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium transition-colors">Torneos</a>
                    @if(PermissionHelper::canViewModule('auditorias'))
                        <a href="{{ route('auditoria.index') }}" class="{{ request()->routeIs('auditoria.index') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium transition-colors">Auditoría</a>
                    @endif
                </div>

                <!-- Usuario y logout desktop -->
                <div class="hidden md:flex items-center space-x-4">
                    <span class="text-gray-300 text-sm">Bienvenido, {{ Auth::user()->correo }}</span>
                    <a href="{{ route('logout') }}" class="text-gray-300 hover:text-white text-sm font-medium transition-colors" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Cerrar Sesión
                    </a>
                    <!-- Formulario de logout oculto -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>

                <!-- Botón hamburguesa móvil -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-300 hover:text-white focus:outline-none focus:text-white transition-colors" id="mobile-menu-button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Menú móvil -->
            <div x-cloak x-show="open" x-transition.opacity @click.outside="open=false" class="md:hidden absolute inset-x-0 top-16 bg-gray-800 border-t border-gray-700 shadow-lg z-[1001] max-h-[calc(100vh-64px)] overflow-y-auto" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 pointer-events-auto">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }} block px-3 py-2 text-base font-medium transition-colors">Inicio</a>
                    
                    @if(Auth::check() && Auth::user()->rol_id == 1)
                        <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }} block px-3 py-2 text-base font-medium transition-colors">Usuarios</a>
                    @endif

                    <a href="{{ route('miembros.index') }}" class="{{ request()->routeIs('miembros.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }} block px-3 py-2 text-base font-medium transition-colors">Miembros</a>
                    @if($canViewAcademias)
                        <a href="{{ route('academias.index') }}" class="{{ request()->routeIs('academias.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }} block px-3 py-2 text-base font-medium transition-colors">Academias</a>
                    @endif
                    <a href="{{ route('torneos.index') }}" class="{{ request()->routeIs('torneos.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }} block px-3 py-2 text-base font-medium transition-colors">Torneos</a>
                    @if(PermissionHelper::canViewModule('auditorias'))
                        <a href="{{ route('auditoria.index') }}" class="{{ request()->routeIs('auditoria.index') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }} block px-3 py-2 text-base font-medium transition-colors">Auditoría</a>
                    @endif
                    
                    <!-- Usuario móvil -->
                    <div class="border-t border-gray-700 pt-4">
                        <div class="px-3 py-2">
                            <p class="text-gray-300 text-sm">Bienvenido, {{ Auth::user()->correo }}</p>
                        </div>
                        <a href="{{ route('logout') }}" class="text-gray-300 hover:text-white hover:bg-gray-700 block px-3 py-2 text-base font-medium transition-colors" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="flex-grow py-4 sm:py-6" style="margin-top: 64px;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.21/dist/sweetalert2.all.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

    <!-- Script de estadísticas -->
    <script src="{{ asset('js/actions/estadisticas_charts.js') }}"></script>
    
    <!-- Script responsive -->
    <script src="{{ asset('js/responsive.js') }}"></script>
    
    <!-- Debug script for mobile menu (temporal) -->
    {{-- <script src="{{ asset('js/mobile-menu-debug.js') }}"></script> --}}

    @stack('scripts')


    <!-- Modal para Detalle de Miembro -->
    <div class="modal fade" id="modalDetalleMiembro" tabindex="-1" role="dialog" aria-labelledby="detalleMiembroLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detalleMiembroLabel">Detalle del Miembro</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Aquí se cargará el contenido de la vista show.blade.php -->
          </div>
        </div>
      </div>
    </div>
</body>
</html>