<div class="hidden md:block">
    <div class="ml-4 flex items-center md:ml-6">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="flex items-center max-w-xs rounded-full bg-gray-800 text-sm focus:outline-none">
                <img class="h-8 w-8 rounded-full" 
                     src="{{ asset('img/user-circle-solid-24.png') }}" 
                     alt="Avatar">
            </button>
            
            <div x-show="open" @click.away="open = false" 
                 class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Mi cuenta
                </a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Configuraciones
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>