@extends('layouts.app')

@section('content')
<div class="min-h-full">
    @include('home._navbar')
    
    <header class="bg-white shadow" style="background-color: #f3f5f7;">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>
        </div>
    </header>
    
    <main>
        @include('home._content')
    </main>
</div>

@include('home._modals')

@push('styles')
    <link rel="stylesheet" href="{{ asset('styles/boots_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/other_styles.css') }}">
@endpush

@push('scripts')
    <!-- Scripts específicos -->
    @foreach(['usuarios', 'academias', 'miembros', 'fides', 'torneos', 'historial', 'federaciones', 'ciudades', 'inscripciones', 'partidas'] as $script)
        <script src="{{ asset('js/actions/{$script}_load.js') }}"></script>
    @endforeach
    
    <script src="{{ asset('js/actions/show_and_hide_v2.js') }}"></script>
    <script src="{{ asset('js/actions/recover_password.js') }}"></script>
    <script src="{{ asset('js/actions/generate_bak.js') }}"></script>

    <!-- Socket.io -->
    <script src="https://cdn.socket.io/4.3.2/socket.io.min.js"></script>
    <script>
        const userData = {
            correo: '{{ Auth::user()->correo }}',
            rol: '{{ Auth::user()->rol_id }}'
        };
        /* eslint-enable */

        // Configuración de Socket.io
        const socket = io('http://192.168.100.100:3001', {
            transports: ['websocket', 'polling'],
            reconnectionAttempts: 5,
            reconnectionDelay: 1000
        });

        // Suscripciones a tablas
        const tablas = ['torneos', 'usuarios', 'asigpermis', 'academias', 'ciudades', 'federaciones', 'fide', 'miembros', 'inscripciones'];
        
        tablas.forEach(tabla => {
            socket.on(`refresh_${tabla}`, () => {
                if (window[`tabla_${tabla}`]) {
                    window[`tabla_${tabla}`].ajax.reload();
                }
            });
        });
    </script>
@endpush
@endsection