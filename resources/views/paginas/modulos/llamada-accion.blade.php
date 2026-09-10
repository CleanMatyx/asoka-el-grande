<section class="bg-asoka-800 py-12 text-white sm:py-16">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h2 class="font-display text-3xl font-bold sm:text-4xl">
            {{ $bloque['titulo'] ?? 'Tu ayuda cuenta' }}
        </h2>
        @if (filled($bloque['contenido'] ?? null))
            <div class="prose prose-lg mx-auto mt-4 max-w-2xl prose-invert">
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
