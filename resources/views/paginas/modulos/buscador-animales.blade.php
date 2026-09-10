@php
    use App\Support\ColorModulo;
    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
@endphp

<section class="py-12 sm:py-16" style="background-color: {{ $colorFondo }}">
    <div class="container mx-auto max-w-5xl px-4">
        @if (filled($bloque['titulo'] ?? null))
            <h2 class="font-display text-3xl font-bold text-asoka-900">
                {{ $bloque['titulo'] }}
            </h2>
        @endif
        @if (filled($bloque['contenido'] ?? null))
            <div class="mt-3 max-w-3xl text-slate-700">
                {!! $bloque['contenido'] !!}
            </div>
        @endif
        <div class="mt-7">
            @include ('paginas.modulos.parciales.formulario-buscador-animales', ['bloque' => $bloque])
        </div>
    </div>
</section>
