@php
    use App\Models\Noticia;
    use App\Support\ColorModulo;

    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
    $fuente = $bloque['fuente_noticias'] ?? 'ultimas';
    $ids = collect($bloque['noticias_seleccionadas'] ?? [])->filter()->values();
    $noticias = Noticia::query()
        ->publicadas()
        ->when($fuente === 'seleccionadas' && $ids->isNotEmpty(), fn ($consulta) => $consulta->whereIn('id', $ids))
        ->latest('fecha_publicacion')
        ->latest('id')
        ->limit((int) ($bloque['limite_noticias'] ?? 3))
        ->get();
    $imagen = filled($bloque['imagen'] ?? null)
        ? Storage::disk('public')->url($bloque['imagen'])
        : ($bloque['imagen_url'] ?? null);
    $posicion = $bloque['posicion_imagen'] ?? 'arriba_izquierda';
    $posicionesLaterales = ['arriba_izquierda', 'arriba_derecha'];
    $imagenLateral = filled($imagen) && in_array($posicion, $posicionesLaterales, true);
    $imagenAlFinal = filled($imagen) && str_starts_with($posicion, 'abajo');
    $imagenCentrada = filled($imagen) && ! $imagenLateral && ! $imagenAlFinal;
    $ajuste = ($bloque['ajuste_imagen'] ?? 'completa') === 'recortar' ? 'aspect-[4/3] object-cover' : 'h-auto object-contain';
    $tamano = $bloque['tamano_imagen'] ?? 'mediano';
    $claseTamano = $imagenCentrada
        ? match ($tamano) {
            'pequeno' => 'md:max-w-xl',
            'grande' => 'md:max-w-5xl',
            'completo' => 'md:max-w-none',
            default => 'md:max-w-3xl',
        }
        : match ($tamano) {
            'pequeno' => 'md:w-[28%]',
            'grande' => 'md:w-[58%]',
            'completo' => 'md:w-full',
            default => 'md:w-[42%]',
        };
    $claseImagen = match ($posicion) {
        'arriba_derecha' => 'mb-6 w-full rounded-2xl shadow-lg md:float-right md:mb-4 md:ml-8',
        'abajo_izquierda' => 'mt-8 w-full rounded-2xl shadow-lg',
        'abajo_derecha' => 'mt-8 w-full rounded-2xl shadow-lg md:ml-auto',
        'arriba_centro', 'abajo_centro' => 'my-6 w-full rounded-2xl shadow-lg md:mx-auto',
        default => 'mb-6 w-full rounded-2xl shadow-lg md:float-left md:mr-8 md:mb-4',
    };
    $alineacionBoton = match ($bloque['posicion_boton'] ?? 'izquierda') {
        'centro' => 'text-center',
        'derecha' => 'text-right',
        default => 'text-left',
    };
@endphp

<section class="py-12 sm:py-16" style="background-color: {{ $colorFondo }}">
    <div class="container mx-auto max-w-6xl px-4">
        @if (filled($bloque['titulo'] ?? null))
            <h2 class="font-display text-3xl font-bold text-asoka-900">
                {{ $bloque['titulo'] }}
            </h2>
        @endif

        @if ($imagenLateral)
            @if (filled($bloque['url_imagen'] ?? null))
                <a
                    href="{{ $bloque['url_imagen'] }}"
                    class="block"
                    aria-label="Abrir enlace de la imagen"
                >

            @endif
            <img
                src="{{ $imagen }}"
                alt=""
                class="{{ $claseImagen }} {{ $ajuste }} {{ $claseTamano }}"
            />
            @if (filled($bloque['url_imagen'] ?? null))
                </a>
            @endif
        @endif

        @if ($imagenCentrada)
            @if (filled($bloque['url_imagen'] ?? null))
                <a
                    href="{{ $bloque['url_imagen'] }}"
                    class="block"
                    aria-label="Abrir enlace de la imagen"
                >

            @endif
            <img
                src="{{ $imagen }}"
                alt=""
                class="{{ $claseImagen }} {{ $ajuste }} {{ $claseTamano }}"
            />
            @if (filled($bloque['url_imagen'] ?? null))
                </a>
            @endif
        @endif

        @if (filled($bloque['contenido'] ?? null))
            <div class="contenido-enriquecido mt-4 max-w-none">
                {!! $bloque['contenido'] !!}
            </div>
        @endif

        @if ($imagenAlFinal)
            <div class="clear-both">
                @if (filled($bloque['url_imagen'] ?? null))
                    <a
                        href="{{ $bloque['url_imagen'] }}"
                        class="block"
                        aria-label="Abrir enlace de la imagen"
                    >

                @endif
                <img
                    src="{{ $imagen }}"
                    alt=""
                    class="{{ $claseImagen }} {{ $ajuste }} {{ $claseTamano }}"
                />
                @if (filled($bloque['url_imagen'] ?? null))
                    </a>
                @endif
            </div>
        @endif

        <div class="clear-both mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($noticias as $noticia)
                @php
                    $foto = filled($noticia->imagen_principal)
                        ? Storage::disk('public')->url($noticia->imagen_principal)
                        : ($noticia->imagen_principal_url ?: asset('images/animal-sin-foto.png'));
                @endphp
                <article
                    class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <a href="{{ $noticia->urlPublica() }}" class="block">
                        <img
                            src="{{ $foto }}"
                            alt="{{ $noticia->titulo }}"
                            class="aspect-[16/10] w-full object-cover"
                        />
                    </a>
                    <div class="p-5">
                        @if ($noticia->fecha_publicacion)
                            <time
                                datetime="{{ $noticia->fecha_publicacion->toDateString() }}"
                                class="text-sm font-bold text-asoka-700"
                            >
                                {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                            </time>
                        @endif
                        <h3
                            class="mt-2 font-display text-2xl font-bold text-asoka-900"
                        >
                            <a
                                href="{{ $noticia->urlPublica() }}"
                                class="hover:text-asoka-700"
                                >{{ $noticia->titulo }}</a
                            >
                        </h3>
                        @if ($noticia->subtitulo)
                            <p class="mt-2 text-slate-700">{{ $noticia->subtitulo }}</p>
                        @endif
                        <a
                            href="{{ $noticia->urlPublica() }}"
                            class="mt-4 inline-flex font-bold text-asoka-700 underline underline-offset-4 hover:text-asoka-900"
                            >Leer noticia</a
                        >
                    </div>
                </article>
            @empty
                @for ($indice = 1; $indice <= 3; $indice++)
                    <article
                        class="overflow-hidden rounded-2xl bg-white shadow-sm"
                    >
                        <img
                            src="{{ asset('images/animal-sin-foto.png') }}"
                            alt=""
                            class="aspect-[16/10] w-full object-cover"
                        />
                        <div class="p-5">
                            <p class="text-sm font-bold text-asoka-700">Próximamente</p>
                            <h3
                                class="mt-2 font-display text-2xl font-bold text-asoka-900"
                            >
                                Título de noticia
                            </h3>
                            <p class="mt-2 text-slate-700">La vista previa mostrará aquí las noticias publicadas.</p>
                        </div>
                    </article>
                @endfor
            @endforelse
        </div>

        @if (filled($bloque['texto_boton'] ?? null))
            <div class="{{ $alineacionBoton }} clear-both mt-8">
                <a
                    href="{{ $bloque['url_boton'] ?? route('noticias.index') }}"
                    class="inline-flex rounded-xl bg-asoka-600 px-5 py-3 font-bold text-white transition hover:bg-asoka-700"
                >
                    {{ $bloque['texto_boton'] }}
                </a>
            </div>
        @endif
    </div>
</section>
