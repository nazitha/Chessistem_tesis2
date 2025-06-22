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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/tailwind_datatables.css') }}">
    
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <!-- Navegación superior -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0">
                        <img class="h-16 w-auto" src="{{ asset('img/estrellas_del_ajedrez_logo.png') }}" alt="Escuela Estrellas del Ajedrez">
                    </div>
                    <div class="flex space-x-8">
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'border-b-2 border-indigo-500 text-gray-900' : 'text-gray-500 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">Home</a>
                        
                        @if(PermissionHelper::canViewModule('usuarios'))
                            <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'border-b-2 border-indigo-500 text-gray-900' : 'text-gray-500 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">Usuarios</a>
                        @endif
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

    <!-- Contenido principal -->
    <main class="flex-grow py-6">
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