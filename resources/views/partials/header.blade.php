@php
    $resolverUrl = function (array $enlace): string {
        if (filled($enlace['url'] ?? null)) {
            return $enlace['url'];
        }

        return filled($enlace['pagina_clave'] ?? null) ? url('/' . $enlace['pagina_clave']) : '#';
    };
    $seccionesCabecera = collect($menusCabecera ?? [])
        ->flatMap(fn ($menu) => $menu->elementos ?? [])
        ->filter(fn (array $seccion): bool => $seccion['visible'] ?? true);
@endphp

<div class="asoka-franja border-b border-asoka-300 text-xs text-asoka-800">
    <div
        class="container mx-auto flex min-h-10 items-center justify-end gap-4 px-4"
    >
        @if ($ajustesSitio->facebook_url)
            <a
                href="{{ $ajustesSitio->facebook_url }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Facebook"
                ><i class="fab fa-facebook-f" aria-hidden="true"></i
            ></a>
        @endif
        @if ($ajustesSitio->twitter_url)
            <a
                href="{{ $ajustesSitio->twitter_url }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="X/Twitter"
                ><i class="fab fa-twitter" aria-hidden="true"></i
            ></a>
        @endif
        @if ($ajustesSitio->teaming_url)
            <a
                href="{{ $ajustesSitio->teaming_url }}"
                target="_blank"
                rel="noopener noreferrer"
                ><i class="fas fa-heart mr-1" aria-hidden="true"></i>Teaming</a
            >
        @endif
        @if ($ajustesSitio->instagram_url)
            <a
                href="{{ $ajustesSitio->instagram_url }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Instagram"
                ><i class="fab fa-instagram" aria-hidden="true"></i
            ></a>
        @endif
        <a href="/admin"
            ><i class="fas fa-sign-in-alt mr-1" aria-hidden="true"></i>Acceso</a
        >
        @if ($ajustesSitio->email_contacto)
            <a href="mailto:{{ $ajustesSitio->email_contacto }}"
                ><i class="fas fa-envelope mr-1" aria-hidden="true"></i
                >Contactar</a
            >
        @endif
    </div>
</div>

<header
    class="sticky top-0 z-30 transition-all duration-300"
    x-data="{ movil: false, compacto: false, sobreCabecera: false }"
    @scroll.window="
        compacto = window.scrollY > 80;
        if (compacto) movil = false;
    "
    @mouseenter="sobreCabecera = true"
    @mouseleave="sobreCabecera = false"
    :class="compacto && !sobreCabecera
        ? 'bg-transparent shadow-none'
        : 'bg-white/95 shadow-sm backdrop-blur'"
>
    <div
        class="container relative mx-auto flex items-center justify-end px-4 transition-all duration-300"
        :class="compacto ? 'min-h-[106px]' : 'min-h-[130px]'"
    >
        <a
            href="{{ route('inicio') }}"
            aria-label="Asoka el Grande, inicio"
            class="logo-asoka absolute left-4 -top-[13px] z-10 block shrink-0"
            ><img
                src="{{ asset('images/' . ($ajustesSitio->logo_sin_fondo ?: 'asoka_logo_completo_sin_fondo.png')) }}"
                alt="Asoka el Grande"
                class="logo-asoka__imagen logo-asoka__imagen--sin-fondo"
        /></a>
        <nav
            x-show="!compacto || sobreCabecera"
            x-transition:enter="transition duration-400 ease-out"
            x-transition:enter-start="-translate-y-5 scale-95 opacity-0"
            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
            x-transition:leave="transition duration-250 ease-in"
            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
            x-transition:leave-end="-translate-y-3 scale-95 opacity-0"
            class="ml-auto hidden origin-top-right items-center lg:flex"
            aria-label="Navegación principal"
        >
            @foreach ($seccionesCabecera as $seccion)
                @php ($subenlaces = collect($seccion['subenlaces'] ?? [])->filter(fn (array $subenlace): bool => $subenlace['visible'] ?? true))
                <div
                    class="relative"
                    x-data="{ abierto: false }"
                    @mouseenter="abierto = true"
                    @mouseleave="abierto = false"
                    @keydown.escape.window="abierto = false"
                >
                    @if ($subenlaces->isEmpty())
                        <a
                            href="{{ $resolverUrl($seccion) }}"
                            class="rounded px-5 py-4 text-base font-bold text-asoka-400 transition-colors duration-200 hover:bg-asoka-50 hover:text-asoka-900"
                            >{{ $seccion['etiqueta'] ?? '' }}</a
                        >
                    @else
                        <button
                            type="button"
                            @click="abierto = !abierto"
                            :aria-expanded="abierto"
                            class="rounded px-5 py-4 text-base font-bold text-asoka-400 transition-colors duration-200 hover:bg-asoka-50 hover:text-asoka-900"
                        >
                            {{ $seccion['etiqueta'] ?? '' }}<i
                                class="fas fa-chevron-down ml-1 text-[10px] transition-transform duration-200"
                                :class="abierto && 'rotate-180'"
                                aria-hidden="true"
                            ></i>
                        </button>
                    @endif
                    @if ($subenlaces->isNotEmpty())
                        <div
                            x-cloak
                            x-show="abierto"
                            x-transition
                            class="absolute left-0 top-full min-w-60 origin-top-left border border-asoka-300 bg-asoka-100 py-2 shadow-xl"
                        >
                            @foreach ($subenlaces as $subenlace)
                                <a
                                    href="{{ $resolverUrl($subenlace) }}"
                                    class="block px-4 py-2 text-sm font-semibold text-asoka-700 transition-colors hover:bg-white hover:text-asoka-400"
                                    >{{ $subenlace['etiqueta'] ?? '' }}</a
                                >
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
        <button
            x-show="!compacto || sobreCabecera"
            x-transition.opacity
            type="button"
            @click="movil = !movil"
            :aria-expanded="movil"
            aria-controls="menu-movil"
            class="ml-auto p-2 text-2xl text-asoka-600 lg:hidden"
        >
            <span class="sr-only">Abrir navegación</span
            ><i class="fas fa-bars" aria-hidden="true"></i>
        </button>
    </div>
    <nav
        id="menu-movil"
        x-cloak
        x-show="movil && (!compacto || sobreCabecera)"
        x-transition
        class="border-t border-asoka-200 bg-white lg:hidden"
    >
        @foreach ($seccionesCabecera as $seccion)
            <div class="container mx-auto px-4 py-3">
                <p class="font-display font-bold text-asoka-900">{{ $seccion['etiqueta'] ?? '' }}</p>
                @foreach (collect($seccion['subenlaces'] ?? [])->filter(fn (array $subenlace): bool => $subenlace['visible'] ?? true) as $subenlace)
                    <a
                        @click="movil = false"
                        href="{{ $resolverUrl($subenlace) }}"
                        class="block py-1 text-sm text-asoka-700"
                        >{{ $subenlace['etiqueta'] ?? '' }}</a
                    >
                @endforeach
            </div>
        @endforeach
    </nav>
</header>
