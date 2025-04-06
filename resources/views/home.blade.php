@extends('layouts.app')

@section('content')
<div class="min-h-full">
    @include('home._navbar')
    
    <header class="bg-white shadow" style="background-color: #f3f5f7;">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 titulo_dashboard">Dashboard</h1>
        </div>
    </header>
    
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @include('home._content')
        </div>
    </main>
</div>

@include('home._modals')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/boots_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/other_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('tailwind_datatables.css') }}">
@endpush

@push('scripts')
    <!-- Scripts específicos de la página -->
    <script src="{{ asset('js/actions/usuarios_load_v2.js') }}"></script>
    <!-- Incluir otros scripts según sea necesario -->
    
    <script>
        // User data is passed via data attributes
        var userData = {
            correo: '{{ Auth::user()->correo }}',
            rol: '{{ Auth::user()->rol_id }}'
        };
        /* eslint-enable */

        // Socket.io
        var socket = io('http://192.168.100.100:3001', {
            transports: ['websocket', 'polling'],
            reconnectionAttempts: 5,
            reconnectionDelay: 1000
        });

        socket.on('connect', function() {
            console.log('Conectado al servidor de Socket.io');
        });

        function suscribirseATabla(tabla) {
            socket.on('refresh_' + tabla, function() {
                console.log('Recibido evento refresh_' + tabla);
                if (window['tabla_' + tabla]) {
                    window['tabla_' + tabla].ajax.reload();
                }
            });
        }
        
        // Suscribirse a múltiples tablas
        suscribirseATabla('torneos');
        suscribirseATabla('usuarios');
        suscribirseATabla('asigpermis');
        suscribirseATabla('academias');
        suscribirseATabla('ciudades');
        suscribirseATabla('federaciones');
        suscribirseATabla('fide');
        suscribirseATabla('miembros');
        suscribirseATabla('inscripciones');

        socket.on('disconnect', function() {
            console.log('Desconectado del servidor de Socket.io');
        });
    </script>
@endpush
@endsection