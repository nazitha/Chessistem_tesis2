<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Estrellas del Ajedrez</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
                :root{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-serif:ui-serif,Georgia,Cambria,"Times New Roman",Times,serif;--font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--color-black:#000;--color-white:#fff;--spacing:.25rem;--text-sm:.875rem;--text-base:1rem;--text-xl:1.25rem;--text-3xl:1.875rem;--text-5xl:3rem}
                *,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}
                img{display:block;max-width:100%;height:auto}
                a{color:inherit;text-decoration:inherit}
                body{line-height:1.5;font-family:var(--font-sans)}
                .min-h-screen{min-height:100vh}
                .flex{display:flex}
                .items-center{align-items:center}
                .justify-between{justify-content:space-between}
                .justify-center{justify-content:center}
                .w-full{width:100%}
                .max-w-4xl{max-width:56rem}
                .p-6{padding:1.5rem}
                .p-8{padding:2rem}
                .p-14{padding:3.5rem}
                .mb-6{margin-bottom:1.5rem}
                .space-y-10>:not([hidden])~:not([hidden]){--tw-space-y-reverse:0;margin-top:calc(2.5rem*(1 - var(--tw-space-y-reverse)));margin-bottom:calc(2.5rem*var(--tw-space-y-reverse))}
                .rounded-lg{border-radius:.5rem}
                .rounded-sm{border-radius:.125rem}
                .bg-white{background-color:var(--color-white)}
                .bg-black{background-color:var(--color-black)}
                .text-white{color:var(--color-white)}
                .text-black{color:var(--color-black)}
                .text-sm{font-size:var(--text-sm)}
                .text-xl{font-size:var(--text-xl)}
                .text-3xl{font-size:var(--text-3xl)}
                .text-5xl{font-size:var(--text-5xl)}
                .font-semibold{font-weight:600}
                .font-medium{font-weight:500}
                .leading-tight{line-height:1.25}
                .shadow-inset{box-shadow:inset 0 0 0 1px rgba(26,26,0,.16)}
                .border{border:1px solid}
                .border-black{border-color:#000}
                .gap-3{gap:.75rem}
                .grid{display:grid}
                .grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
                .gap-6{gap:1.5rem}
                .hidden{display:none}
                .md\:p-14{padding:3.5rem}
                @media (min-width:768px){.md\:h-96{height:24rem}.md\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}.md\:text-5xl{font-size:var(--text-5xl)}}
                .h-64{height:16rem}
                .object-cover{object-fit:cover}
                .relative{position:relative}
                .absolute{position:absolute}
                .inset-0{top:0;right:0;bottom:0;left:0}
                .opacity-80{opacity:.8}
                .underline{text-decoration-line:underline}
                .underline-offset-4{text-underline-offset:4px}
                /* Fondo igual al login */
                .bg-login-grad{background-color:#c9d6ff;background:linear-gradient(to right,#e2e2e2,#c9d6ff)}
                /* Hero helpers */
                .hero-overlay{position:absolute;inset:0;background:linear-gradient(0deg,rgba(0,0,0,.45),rgba(0,0,0,.25));}
            </style>
        @endif
    </head>
    <body class="flex items-center justify-center min-h-screen p-6 bg-login-grad">
        <div class="w-full max-w-4xl">
            <header class="mb-6">
                <div class="flex items-center justify-between text-sm">
                    <div class="font-medium">Estrellas del Ajedrez</div>
                    <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 border rounded-sm">Ingresar</a>
                </div>
            </header>

            <main class="space-y-10">
                @php
                    $heroCandidates = [
                        'img/landing_hero.jpg','img/landing_hero.png',
                        'img/chess_hero.jpg','img/chess_hero.png',
                        'img/descarga.jpg','img/descarga.png',
                        'img/estrellas_del_ajedrez_logo.png'
                    ];
                    $heroImage = null;
                    foreach ($heroCandidates as $c) {
                        if (file_exists(public_path($c))) { $heroImage = $c; break; }
                    }
                @endphp
                <section class="relative overflow-hidden rounded-lg">
                    <img src="{{ asset($heroImage) }}" alt="Estrellas del Ajedrez" class="w-full h-64 md:h-96 object-cover">
                    <div class="hero-overlay"></div>
                    <div class="absolute inset-0 flex items-center">
                        <div class="p-8 md:p-14 max-w-4xl">
                            <h1 class="text-3xl md:text-5xl font-semibold leading-tight text-white">Academia de Ajedrez Online Profesional</h1>
                            <p class="mt-3 text-white">Lleva tu ajedrez al siguiente nivel.</p>
                            <div class="mt-6 flex gap-3">
                                <a href="{{ route('login') }}" class="px-5 py-2 bg-black text-white rounded-sm border border-black">Inscríbete</a>
                                <a href="#contacto" class="px-5 py-2 bg-white rounded-sm border">Contáctanos</a>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-lg p-8 shadow-inset">
                    <h2 class="text-xl font-medium mb-3">Nuestra misión</h2>
                    <p class="text-sm">En Estrellas del Ajedrez acercamos a niños, jóvenes y adultos al mundo de los juegos de mesa y del ajedrez, fomentando el aprendizaje, la concentración y la sana competencia mediante clases, ligas internas y torneos abiertos.</p>
                </section>

                <section id="contacto" class="bg-white rounded-lg p-8 shadow-inset">
                    <h2 class="text-xl font-medium mb-4">Contáctanos</h2>
                    <div class="grid md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <div>Correo</div>
                            <a href="mailto:info@estrellasajedrez.com" class="underline underline-offset-4">info@estrellasajedrez.com</a>
                        </div>
                        <div>
                            <div>Teléfono</div>
                            <a href="tel:+573000000000" class="underline underline-offset-4">+57 300 000 0000</a>
                        </div>
                        <div>
                            <div>Dirección</div>
                            <div>Calle 123 #45-67, Ciudad</div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>


