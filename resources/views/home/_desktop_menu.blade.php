<div class="hidden md:block">
    <div class="ml-10 flex items-baseline space-x-4">
        <!-- Home -->
        <a href="{{ route('home') }}" 
           :class="activeMenuItem === 'Home' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
           @click="activeMenuItem = 'Home'"
           class="rounded-md px-3 py-2 text-sm font-medium">
            Home
        </a>

        <!-- Menú Usuarios -->
        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
            <button type="button" 
                    @click="open = !open; activeMenuItem = 'Usuarios'"
                    :class="activeMenuItem === 'Usuarios' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                    class="rounded-md px-3 py-2 text-sm font-medium">
                Usuarios
            </button>
            
            <div x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg">
                <div class="py-1">
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'Usuarios'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="usuarios_opcion">
                        Usuarios
                    </a>
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'Usuarios'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="asigpermis_opcion">
                        Asignación de permisos
                    </a>
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'Usuarios'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="historial_opcion">
                        Historial de cambios
                    </a>
                </div>
            </div>
        </div>

        <!-- Menú Miembros -->
        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
            <button type="button" 
                    @click="open = !open; activeMenuItem = 'Miembros'"
                    :class="activeMenuItem === 'Miembros' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                    class="rounded-md px-3 py-2 text-sm font-medium">
                Miembros
            </button>
            
            <div x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg">
                <div class="py-1">
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'Miembros'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="miembros_opcion">
                        Miembros
                    </a>
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'Miembros'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="academias_opcion">
                        Academias
                    </a>
                </div>
            </div>
        </div>

        <!-- Menú FIDES -->
        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
            <button type="button" 
                    @click="open = !open; activeMenuItem = 'FIDES'"
                    :class="activeMenuItem === 'FIDES' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                    class="rounded-md px-3 py-2 text-sm font-medium">
                FIDES
            </button>
            
            <div x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg">
                <div class="py-1">
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'FIDES'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="fide_opcion">
                        FIDES
                    </a>
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'FIDES'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="federaciones_opcion">
                        Federaciones
                    </a>
                </div>
            </div>
        </div>

        <!-- Menú Torneos -->
        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
            <button type="button" 
                    @click="open = !open; activeMenuItem = 'Torneos'"
                    :class="activeMenuItem === 'Torneos' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                    class="rounded-md px-3 py-2 text-sm font-medium">
                Torneos
            </button>
            
            <div x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg">
                <div class="py-1">
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'Torneos'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="torneos_y_partidas_opcion">
                        Torneos
                    </a>
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'Torneos'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="partidas_opcion">
                        Partidas
                    </a>
                    <a href="#" 
                       @click="open = false; activeMenuItem = 'Torneos'"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                       id="inscripciones_opcion">
                        Inscripciones
                    </a>
                </div>
            </div>
        </div>

        <!-- Menú Ciudades -->
        <a href="#" 
           :class="activeMenuItem === 'Ciudades' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
           @click="activeMenuItem = 'Ciudades'"
           class="rounded-md px-3 py-2 text-sm font-medium"
           id="ciudades_opcion">
            Ciudades
        </a>
    </div>
</div>