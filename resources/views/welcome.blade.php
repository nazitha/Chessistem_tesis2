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
        @endif
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
                :root{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-serif:ui-serif,Georgia,Cambria,"Times New Roman",Times,serif;--font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--color-black:#000;--color-white:#fff;--spacing:.25rem;--text-sm:.875rem;--text-base:1rem;--text-xl:1.25rem;--text-3xl:1.875rem;--text-5xl:3rem;--accent-blue:#3d5afe}
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
                .space-y-10>:not([hidden])~:not([hidden]){--tw-space-y-reverse:0;margin-top:calc(1rem*(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1rem*var(--tw-space-y-reverse))}
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
                .text-7xl{font-size:3.75rem}
                .text-8xl{font-size:4.5rem}
                .text-9xl{font-size:5.25rem}
                .font-semibold{font-weight:600}
                .font-medium{font-weight:500}
                .font-bold{font-weight:700}
                .leading-tight{line-height:1.25}
                .shadow-inset{box-shadow:inset 0 0 0 1px rgba(26,26,0,.16)}
                .border{border:1px solid}
                .border-black{border-color:#000}
                .border-white{border-color:#fff}
                .gap-3{gap:.75rem}
                .grid{display:grid}
                .grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
                .gap-6{gap:1.5rem}
                .hidden{display:none}
                .md\:p-14{padding:3.5rem}
                @media (min-width:768px){.md\:h-96{height:24rem}.md\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}.md\:text-5xl{font-size:var(--text-5xl)}}
                .h-64{height:16rem}
                .object-cover{object-fit:cover}
                .object-contain{object-fit:contain}
                .relative{position:relative}
                .absolute{position:absolute}
                .inset-0{top:0;right:0;bottom:0;left:0}
                .opacity-80{opacity:.8}
                .underline{text-decoration-line:underline}
                .underline-offset-4{text-underline-offset:4px}
                /* Fondo igual al login */
                .bg-login-grad{background-color:#c9d6ff;background:linear-gradient(to right,#e2e2e2,#c9d6ff)}
                /* Hero helpers */
                .hero-overlay{position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,0,0,.95) 0%, rgba(0,0,0,.75) 40%, rgba(0,0,0,.35) 70%, rgba(0,0,0,0) 100%);}                
                .container{max-width:56rem;margin:0 auto}
                .text-center{text-align:center}
                .text-left{text-align:left}
                .justify-end{justify-content:flex-end}
                .btn{display:inline-block;padding:12px 20px;border-radius:6px;font-weight:600;text-decoration:none;border:1px solid transparent}
                .btn-pill{border-radius:9999px}
                .btn-primary{background-color:#3a4852;color:#fff;border-color:#3a4852}
                .btn-light{background-color:transparent;color:#fff;border-color:#fff}
                .btn-primary:hover{background-color:#202c34}
                .btn-light:hover{background-color:#f5f5f5}
                /* Hero size */
                .h-hero{height:100vh;min-height:520px}
                .hero-wrap{position:relative;overflow:hidden}
                .hero-image{width:100%;height:100%;object-fit:cover;object-position:right center}
                .hero-content{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center}
                .content-inner{max-width:48rem;padding:2rem 1.5rem}
                @media (min-width:768px){.content-inner{padding:3rem 3.5rem}}
                .accent-blue{color:var(--accent-blue)}
            </style>
    </head>
    <body class="flex items-center justify-center min-h-screen p-6 bg-login-grad">
        <div class="w-full max-w-4xl">

            <main class="space-y-4">
                @php
                    // Preferimos landing_hero.jpg; si no existe, caemos a otras opciones
                    $preferred = 'img/landing_hero.jpg';
                    $heroImage = file_exists(public_path($preferred)) ? $preferred : null;
                    if (!$heroImage) {
                        $heroCandidates = [
                            'img/landing_hero.png',
                            'img/chess_hero.jpg','img/chess_hero.png',
                            'img/descarga.jpg','img/descarga.png',
                            'img/estrellas_del_ajedrez_logo.png'
                        ];
                        foreach ($heroCandidates as $c) {
                            if (file_exists(public_path($c))) { $heroImage = $c; break; }
                        }
                    }
                @endphp
                <section class="hero-wrap h-hero">
                    <img src="{{ asset($heroImage) }}" alt="Estrellas del Ajedrez" class="hero-image" />
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <div class="content-inner">
                            <h1 class="font-bold text-9xl leading-tight text-left" style="letter-spacing:.5px; text-transform:uppercase;">
                                <span class="accent-blue">Estrellas</span> del Ajedrez
                            </h1>
                            <p class="text-white text-left" style="margin-top:16px;font-size:1.125rem">Lleva tu ajedrez al siguiente nivel.</p>
                            <div class="flex gap-3" style="margin-top:28px">
                                <a href="{{ route('login') }}" class="btn btn-primary btn-pill">Ingresar</a>
                                <a href="https://wa.me/50584403892" target="_blank" rel="noopener" class="btn btn-light btn-pill border-white">Contactar por WhatsApp</a>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-lg p-8 shadow-inset">
                    <h2 class="text-xl font-medium mb-3">Nuestra misión</h2>
                    <p class="text-sm">En Estrellas del Ajedrez acercamos a niños, jóvenes y adultos al mundo del ajedrez, fomentando el aprendizaje, la concentración y la sana competencia mediante clases, ligas internas y torneos abiertos.</p>
                </section>

                
            </main>
        </div>
    </body>
</html>


