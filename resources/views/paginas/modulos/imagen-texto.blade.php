@php ($imagen = filled($bloque['imagen'] ?? null) ? Storage::disk('public')->url($bloque['imagen']) : ($bloque['imagen_url'] ?? asset('images/animal-sin-foto.png')))
<section class="bg-asoka-50 py-12 sm:py-16">
    <div
        class="container mx-auto grid max-w-6xl items-center gap-8 px-4 md:grid-cols-2"
    >
        <img
            src="{{ $imagen }}"
            alt=""
            class="aspect-[4/3] w-full rounded-2xl object-cover shadow-lg"
        />
        <div>
            <h2 class="font-display text-3xl font-bold text-asoka-900">
                {{ $bloque['titulo'] ?? 'Nuestro compromiso' }}
            </h2>
            <div class="prose mt-4 max-w-none">
                {!! $bloque['contenido'] ?? '' !!}
            </div>
            @if (filled($bloque['texto_boton'] ?? null))
                <a
                    href="{{ $bloque['url_boton'] ?? '#' }}"
                    class="mt-6 inline-flex rounded-xl bg-asoka-600 px-5 py-3 font-bold text-white"
                    >{{ $bloque['texto_boton'] }}</a
                >
            @endif
        </div>
    </div>
</section>
