@php
    use App\Support\ColorModulo;

    $imagen = filled($bloque['imagen'] ?? null) ? Storage::disk('public')->url($bloque['imagen']) : ($bloque['imagen_url'] ?? null);
    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
    $textoClaro = $imagen || ColorModulo::requiereTextoClaro($colorFondo);
@endphp
<section
    class="bg-cover bg-center py-16 sm:py-24"
    style="background-color: {{ $colorFondo }}; color: {{ $textoClaro ? '#ffffff' : '#1e293b' }}; @if ($imagen) background-image: linear-gradient(rgb(15 115 205 / .82), rgb(64 124 166 / .82)), url('{{ $imagen }}'); @endif"
>
    <div class="container mx-auto max-w-5xl px-4 text-center">
        <h1 class="font-display text-4xl font-bold sm:text-6xl">
            {{ $bloque['titulo'] ?? 'Un nuevo comienzo' }}
        </h1>
        @if (filled($bloque['contenido'] ?? null))
            <div
                class="prose prose-lg mx-auto mt-5 max-w-3xl {{ $textoClaro ? 'prose-invert' : '' }}"
            >
                {!! $bloque['contenido'] !!}
            </div>
        @endif
        @if (filled($bloque['texto_boton'] ?? null))
            <a
                href="{{ $bloque['url_boton'] ?? '#' }}"
                class="mt-8 inline-flex rounded-xl bg-white px-6 py-3 font-bold text-asoka-800"
            >{{ $bloque['texto_boton'] }}</a
            >
        @endif
    </div>
</section>
