@php
    use App\Support\ColorModulo;
    use Illuminate\Support\Str;

    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
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

        $contenidoMarkdown = preg_replace('/^(#{1,6})(?=\S)/m', '$1 ', $contenidoMarkdown) ?? $contenidoMarkdown;
        $contenido = Str::markdown($contenidoMarkdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
@endphp

<section class="py-12 sm:py-16" style="background-color: {{ $colorFondo }}">
    <div class="container mx-auto max-w-4xl px-4">
        @if (filled($bloque['titulo'] ?? null))
            <h2 class="font-display text-3xl font-bold text-asoka-900">
                {{ $bloque['titulo'] }}
            </h2>
        @endif
        <div class="contenido-enriquecido mt-5 max-w-none">
            {!! $contenido !!}
        </div>
    </div>
</section>
