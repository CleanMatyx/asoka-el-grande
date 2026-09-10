@php
    use App\Support\ColorModulo;
    use Illuminate\Support\Str;

    $imagen = filled($bloque['imagen'] ?? null)
        ? Storage::disk('public')->url($bloque['imagen'])
        : ($bloque['imagen_url'] ?? asset('images/animal-sin-foto.png'));
    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
    $posicionImagen = $bloque['posicion_imagen'] ?? 'arriba_izquierda';
    $posicionesValidas = [
        'arriba_izquierda',
        'arriba_centro',
        'arriba_derecha',
        'abajo_izquierda',
        'abajo_centro',
        'abajo_derecha',
    ];
    $posicionImagen = in_array($posicionImagen, $posicionesValidas, true) ? $posicionImagen : 'arriba_izquierda';
    $imagenCentrada = in_array($posicionImagen, ['arriba_centro', 'abajo_centro'], true);
    $imagenAlFinal = str_starts_with($posicionImagen, 'abajo');
    $imagenFlotante = ! $imagenCentrada && ! $imagenAlFinal;
    $ajusteImagen = ($bloque['ajuste_imagen'] ?? 'completa') === 'recortar' ? 'recortar' : 'completa';
    $claseAjusteImagen = $ajusteImagen === 'recortar' ? 'aspect-[4/3] object-cover' : 'h-auto object-contain';
    $tamanoImagen = $bloque['tamano_imagen'] ?? 'mediano';
    $tamanoImagen = in_array($tamanoImagen, ['pequeno', 'mediano', 'grande', 'completo'], true) ? $tamanoImagen : 'mediano';
    $claseTamanoImagen = $imagenCentrada
        ? match ($tamanoImagen) {
            'pequeno' => 'md:max-w-xl',
            'grande' => 'md:max-w-5xl',
            'completo' => 'md:max-w-none',
            default => 'md:max-w-3xl',
        }
        : match ($tamanoImagen) {
            'pequeno' => 'md:w-[28%]',
            'grande' => 'md:w-[58%]',
            'completo' => 'md:w-full',
            default => 'md:w-[42%]',
        };
    $contenidoGuardado = (string) ($bloque['contenido'] ?? '');
    $contenidoPlano = trim(strip_tags($contenidoGuardado));
    $pareceMarkdown = preg_match('/(^|\n)\s{0,3}#{1,6}(?:\s|(?=\S))/', $contenidoPlano) === 1;
    $formatoMarkdown = ($bloque['formato_contenido'] ?? 'editor') === 'markdown' || $pareceMarkdown;
    $contenido = $contenidoGuardado;

    if ($formatoMarkdown) {
        $contenidoMarkdown = (string) ($bloque['contenido_markdown'] ?? '');
        $archivoMarkdown = $bloque['archivo_markdown'] ?? null;

        if (blank($contenidoMarkdown) && $pareceMarkdown) {
            $contenidoMarkdown = $contenidoPlano;
        }

        if (blank($contenidoMarkdown) && is_string($archivoMarkdown) && Storage::disk('public')->exists($archivoMarkdown)) {
            $contenidoMarkdown = Storage::disk('public')->get($archivoMarkdown);
        }

        // CommonMark exige un espacio tras #; el CMS admite también ##Título por comodidad.
        $contenidoMarkdown = preg_replace('/^(#{1,6})(?=\S)/m', '$1 ', $contenidoMarkdown) ?? $contenidoMarkdown;
        $contenido = Str::markdown($contenidoMarkdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    $clasesImagen = match ($posicionImagen) {
        'arriba_izquierda' => 'mb-6 w-full rounded-2xl shadow-lg md:float-left md:mr-8 md:mb-4',
        'arriba_derecha' => 'mb-6 w-full rounded-2xl shadow-lg md:float-right md:ml-8 md:mb-4',
        'abajo_izquierda' => 'mt-8 w-full rounded-2xl shadow-lg',
        'abajo_derecha' => 'mt-8 w-full rounded-2xl shadow-lg md:ml-auto',
        default => 'my-6 w-full rounded-2xl shadow-lg md:mx-auto',
    };
    $enlaces = collect($bloque['enlaces'] ?? [])->filter(
        fn (array $enlace): bool => ($enlace['visible'] ?? true)
            && filled($enlace['texto'] ?? null)
            && filled($enlace['url'] ?? null),
    );
@endphp

<section class="py-12 sm:py-16" style="background-color: {{ $colorFondo }}">
    <div class="container mx-auto max-w-6xl px-4">
        <div class="contenido-imagen-texto">
            <h2 class="font-display text-3xl font-bold text-asoka-900">
                {{ $bloque['titulo'] ?? 'Nuestro compromiso' }}
            </h2>

            @if ($imagenFlotante)
                @if (filled($bloque['url_imagen'] ?? null))
                    <a
                        href="{{ $bloque['url_imagen'] }}"
                        class="block"
                        aria-label="Abrir enlace de la imagen"
                    >


                @endif
                <img
                    src="{{ $imagen }}"
                    alt="{{ $bloque['titulo'] ?? '' }}"
                    class="{{ $clasesImagen }} {{ $claseAjusteImagen }} {{ $claseTamanoImagen }}"
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
                    alt="{{ $bloque['titulo'] ?? '' }}"
                    class="{{ $clasesImagen }} {{ $claseAjusteImagen }} {{ $claseTamanoImagen }}"
                />
                @if (filled($bloque['url_imagen'] ?? null))
                    </a>
                @endif
            @endif

            <div class="mt-4 max-w-none">{!! $contenido !!}</div>

            @if (filled($bloque['texto_boton'] ?? null))
                <a
                    href="{{ $bloque['url_boton'] ?? '#' }}"
                    class="mt-6 inline-flex rounded-xl bg-asoka-600 px-5 py-3 font-bold text-white"
                >
                    {{ $bloque['texto_boton'] }}
                </a>
            @endif
        </div>

        @if ($imagenAlFinal && ! $imagenCentrada)
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
                    alt="{{ $bloque['titulo'] ?? '' }}"
                    class="{{ $clasesImagen }} {{ $claseAjusteImagen }} {{ $claseTamanoImagen }}"
                />
                @if (filled($bloque['url_imagen'] ?? null))
                    </a>
                @endif
            </div>
        @endif

        @if ($enlaces->isNotEmpty())
            <div class="clear-both mt-8 space-y-3">
                @foreach ($enlaces as $enlace)
                    <a
                        href="{{ $enlace['url'] }}"
                        class="group flex rounded-xl border border-asoka-300 bg-white px-5 py-4 text-asoka-900 shadow-sm transition hover:border-asoka-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-asoka-500 focus:ring-offset-2"
                    >
                        <span class="min-w-0 flex-1">
                            <span
                                class="block font-bold group-hover:text-asoka-700"
                            >
                                {{ $enlace['texto'] }}
                            </span>
                            @if (filled($enlace['descripcion'] ?? null))
                                <span class="mt-1 block text-sm text-slate-600">
                                    {{ $enlace['descripcion'] }}
                                </span>
                            @endif
                        </span>
                        <span
                            class="ml-4 self-center text-asoka-600"
                            aria-hidden="true"
                            >→</span
                        >
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
