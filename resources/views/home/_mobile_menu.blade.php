@php
    use App\Helpers\PermissionHelper;
@endphp

<div x-show="openMobileMenu" class="md:hidden">
    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <!-- Home -->
        <a href="{{ route('home') }}" 
           :class="activeMenuItem === 'Home' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
           @click="activeMenuItem = 'Home'; openMobileMenu = false"
           class="block rounded-md px-3 py-2 text-base font-medium"
           id="home_opcion_movil">
            Home
        </a>

        <!-- Menú Usuarios -->
        @if(PermissionHelper::canViewModule('usuarios'))
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open; activeMenuItem = 'Usuarios'" 
                        class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                    Usuarios
                    <svg class="h-5 w-5 transform transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                
                <div x-show="open" x-collapse class="ml-4 space-y-1">
                    <a href="{{ route('usuarios.index') }}"
                       @click="activeMenuItem = 'Usuarios'; openMobileMenu = false"
                       class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white">
                        Gestión de Usuarios
                    </a>
                    <a href="{{ route('roles.index') }}"
                       @click="activeMenuItem = 'Usuarios'; openMobileMenu = false"
                       class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white">
                        Gestión de Roles
                    </a>
                </div>
            </div>
        @endif

        <!-- Menú Miembros -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open; activeMenuItem = 'Miembros'" 
                    class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                Miembros
                <svg class="h-5 w-5 transform transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
            
            <div x-show="open" x-collapse class="ml-4 space-y-1">
                <a href="#"
                   @click="activeMenuItem = 'Miembros'; openMobileMenu = false"
                   class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
                   id="miembros_opcion_movil">
                    Miembros
                </a>
                <a href="#"
                   @click="activeMenuItem = 'Miembros'; openMobileMenu = false"
                   class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
                   id="academias_opcion_movil">
                    Academias
                </a>
            </div>
        </div>

        <!-- Menú FIDES -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open; activeMenuItem = 'FIDES'" 
                    class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                FIDES
                <svg class="h-5 w-5 transform transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
            
            <div x-show="open" x-collapse class="ml-4 space-y-1">
                <a href="#"
                   @click="activeMenuItem = 'FIDES'; openMobileMenu = false"
                   class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
                   id="fide_opcion_movil">
                    FIDES
                </a>
                <a href="#"
                   @click="activeMenuItem = 'FIDES'; openMobileMenu = false"
                   class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
                   id="federaciones_opcion_movil">
                    Federaciones
                </a>
            </div>
        </div>

        <!-- Menú Torneos -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open; activeMenuItem = 'Torneos'" 
                    class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                Torneos
                <svg class="h-5 w-5 transform transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
            
            <div x-show="open" x-collapse class="ml-4 space-y-1">
                <a href="#"
                   @click="activeMenuItem = 'Torneos'; openMobileMenu = false"
                   class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
                   id="torneos_y_partidas_opcion_movil">
                    Torneos
                </a>
                <a href="#"
                   @click="activeMenuItem = 'Torneos'; openMobileMenu = false"
                   class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
                   id="partidas_opcion_movil">
                    Partidas
                </a>
                <a href="#"
                   @click="activeMenuItem = 'Torneos'; openMobileMenu = false"
                   class="block px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
                   id="inscripciones_opcion_movil">
                    Inscripciones
                </a>
            </div>
        </div>

        <!-- Otros elementos del menú -->
        <a href="#" 
           :class="activeMenuItem === 'Ciudades' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
           @click="activeMenuItem = 'Ciudades'; openMobileMenu = false"
           class="block rounded-md px-3 py-2 text-base font-medium"
           id="ciudades_opcion_movil">
            Ciudades
        </a>
        
        <a href="#" 
           :class="activeMenuItem === 'FIDE' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
           @click="activeMenuItem = 'FIDE'; openMobileMenu = false"
           class="block rounded-md px-3 py-2 text-base font-medium"
           id="fide_opcion_movil">
            FIDE
        </a>
    </div>

    <!-- Sección del perfil del usuario -->
    <div class="border-t border-gray-700 pt-4 pb-3">
        <div class="flex items-center px-5">
            <div class="shrink-0">
                <img class="h-10 w-10 rounded-full" 
                     src="{{ asset(Auth::user()->avatar ?? 'img/user-circle-solid-24.png') }}" 
                     alt="Avatar de usuario">
            </div>
            <div class="ml-3">
                <div class="text-base font-medium text-white">{{ Auth::user()->nombre }}</div>
                <div class="text-sm font-medium text-gray-400">{{ Auth::user()->correo }}</div>
            </div>
        </div>
        <div class="mt-3 space-y-1 px-2">
            <a href="#" 
               class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
               id="user-menu-item-0-movil">
                Mi cuenta
            </a>
            <a href="#" 
               class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
               id="user-menu-item-1-movil">
                Configuraciones
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
                        id="user-menu-item-2-movil">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</div>