@php
    use App\Support\ColorModulo;

    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
    $textoClaro = ColorModulo::requiereTextoClaro($colorFondo);
@endphp

<section
    class="py-12 sm:py-16"
    style="background-color: {{ $colorFondo }}; color: {{ $textoClaro ? '#ffffff' : '#1e293b' }}"
>
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h2 class="font-display text-3xl font-bold sm:text-4xl">
            {{ $bloque['titulo'] ?? 'Tu ayuda cuenta' }}
        </h2>
        @if (filled($bloque['contenido'] ?? null))
            <div
                class="prose prose-lg mx-auto mt-4 max-w-2xl {{ $textoClaro ? 'prose-invert' : '' }}"
            >
                {!! $bloque['contenido'] !!}
            </div>
        @endif
        @if (filled($bloque['texto_boton'] ?? null))
            <a
                href="{{ $bloque['url_boton'] ?? '#' }}"
                class="mt-7 inline-flex rounded-xl bg-white px-6 py-3 font-bold text-asoka-800"
            >{{ $bloque['texto_boton'] }}</a
            >
        @endif
    </div>
</section>
