<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        @yield ('title', 'Asoka el Grande')
        - Protectora de Animales Alicante
    </title>
    <meta
        name="description"
        content="@yield('meta_description', 'Asoka el Grande es una asociación sin ánimo de lucro dedicada a la defensa de los derechos de los animales y a la gestión de su adopción en Alicante.')"
    />
    <link
        rel="icon"
        href="{{ asset($ajustesSitio->favicon ?: 'favicon.ico') }}"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka:wght@500;600;700&display=swap"
        rel="stylesheet"
    />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    @vite (['resources/css/app.css', 'resources/js/app.js'])
    <script
        defer
        src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"
    ></script>
    @stack ('meta')
    @stack ('styles')
</head>
<body class="bg-asoka-50 text-slate-800 antialiased">
    <a
        href="#contenido"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-slate-950 focus:px-4 focus:py-3 focus:font-bold focus:text-white"
        >Saltar al contenido</a
    >
    @include ('partials.header')
    <main id="contenido" class="min-h-screen">@yield ('content')</main>
    @include ('partials.footer')
    <button
        id="btnSubir"
        type="button"
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-asoka-600 text-white shadow-lg hover:bg-asoka-700 focus:outline-none focus:ring-4 focus:ring-asoka-300"
        aria-label="Volver al inicio"
    >
        <i class="fa fa-arrow-up" aria-hidden="true"></i>
    </button>
    @stack ('scripts')
</body>
</html>
