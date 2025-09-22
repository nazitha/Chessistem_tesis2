@php
    use App\Helpers\PermissionHelper;
    use App\Services\PermissionService;
    $canViewTorneos = PermissionHelper::canViewModule('torneos');
    $canViewMiembros = PermissionHelper::canViewModule('miembros');
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
    </style>
    
    @stack('styles')
    <style>
    html, body {
        opacity: 1 !important;
        filter: none !important;
        background: #f9fafb !important;
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
        position: relative !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1000 !important;
        order: -1 !important;
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
    <nav class="shadow fixed top-0 left-0 right-0 z-50" style="background-color: #282c34;">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0">
                        <img class="h-16 w-auto" src="{{ asset('img/estrellas_del_ajedrez_logo.png') }}" alt="Escuela Estrellas del Ajedrez">
                    </div>
                    <div class="flex space-x-8">
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium no-underline">Home</a>
                        
                        @if(Auth::check() && Auth::user()->rol_id == 1)
                            <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium">Usuarios</a>
                        @endif

                        <a href="{{ route('miembros.index') }}" class="{{ request()->routeIs('miembros.*') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium">Miembros</a>
                        <a href="{{ route('academias.index') }}" class="{{ request()->routeIs('academias.*') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium">Academias</a>
                        <a href="{{ route('torneos.index') }}" class="{{ request()->routeIs('torneos.*') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium">Torneos</a>
                        @if(PermissionHelper::canViewModule('auditorias'))
                            <a href="{{ route('auditoria.index') }}" class="{{ request()->routeIs('auditoria.index') ? 'border-b-2 border-indigo-400 text-white' : 'text-gray-300 hover:text-white' }} px-1 pt-1 text-sm font-medium">Auditoría</a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-300 text-sm mr-4">Bienvenido, {{ Auth::user()->correo }}</span>
                    <a href="{{ route('logout') }}" class="text-gray-300 hover:text-white text-sm font-medium" 
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

    <!-- Contenido principal -->
    <main class="flex-grow py-6" style="margin-top: 64px;">
        @yield('content')
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