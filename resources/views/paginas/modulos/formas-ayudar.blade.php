@php
    use App\Support\ColorModulo;

    $tarjetas = collect($bloque['tarjetas'] ?? []);

    if ($tarjetas->isEmpty()) {
        $tarjetas = collect([
            ['titulo' => 'Adopta o acoge', 'texto' => 'Abre tu hogar para siempre o durante el tiempo que más lo necesitan.', 'texto_boton' => 'Conocer animales', 'url_boton' => '/animales', 'estilo' => 'claro'],
            ['titulo' => 'Hazte padrino o socio', 'texto' => 'Tu aportación mensual da estabilidad, tratamientos y alimento.', 'texto_boton' => 'Quiero colaborar', 'url_boton' => '/apadrinar', 'estilo' => 'claro'],
            ['titulo' => 'Donación rápida', 'texto' => 'Colabora mediante Bizum, tarjeta con Stripe o Teaming por 1 € al mes.', 'texto_boton' => 'Hacer una donación', 'url_boton' => '/donar', 'estilo' => 'destacado'],
        ]);
    }
    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
@endphp

<section id="ayudar" class="py-14" style="background-color: {{ $colorFondo }}">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (filled($bloque['etiqueta'] ?? null))
            <p class="font-bold uppercase tracking-wider text-amber-900">{{ $bloque['etiqueta'] }}</p>
        @endif
        <h2 class="mt-2 text-3xl font-extrabold">
            {{ $bloque['titulo'] ?? 'Hay muchas formas de estar a su lado' }}
        </h2>
        <div class="mt-7 grid gap-5 md:grid-cols-3">
            @foreach ($tarjetas as $tarjeta)
                @php ($destacada = ($tarjeta['estilo'] ?? 'claro') === 'destacado')
                <article
                    class="rounded-2xl p-6 shadow-sm {{ $destacada ? 'bg-slate-900 text-white' : 'bg-white' }}"
                >
                    <h3 class="text-xl font-extrabold">
                        {{ $tarjeta['titulo'] ?? '' }}
                    </h3>
                    <p
                        class="mt-3 leading-7 {{ $destacada ? 'text-slate-200' : 'text-slate-700' }}"
                    >{{ $tarjeta['texto'] ?? '' }}</p>
                    @if (filled($tarjeta['texto_boton'] ?? null))
                        <a
                            href="{{ $modoPrevisualizacion ? '#' : ($tarjeta['url_boton'] ?? '#') }}"
                            class="mt-5 inline-block font-bold underline {{ $destacada ? 'text-amber-300' : 'text-amber-800' }}"
                        >{{ $tarjeta['texto_boton'] }}</a
                        >
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
