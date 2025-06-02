<nav class="bg-gray-800" x-data="{ openMobileMenu: false, activeMenuItem: 'Home' }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="shrink-0">
                    <img class="h-16 w-16" 
                         src="{{ asset('img/estrellas_del_ajedrez_logo.png') }}" 
                         alt="Logo">
                </div>
                @include('home._desktop_menu')
            </div>

            @include('home._user_menu')

            <!-- Mobile menu button -->
            <div class="-mr-2 flex md:hidden">
                <button @click="openMobileMenu = !openMobileMenu" type="button" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:bg-gray-700 hover:text-white">
                    <!-- Iconos del menú móvil -->
                </button>
            </div>
        </div>

        @include('home._mobile_menu')
    </div>
</nav>