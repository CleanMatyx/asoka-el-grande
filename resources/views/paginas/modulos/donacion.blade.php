@php
    use App\Support\ColorModulo;
    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
@endphp

<section class="py-12 sm:py-16" style="background-color: {{ $colorFondo }}">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h2 class="font-display text-3xl font-bold text-asoka-900">
            {{ $bloque['titulo'] ?? 'Ayúdanos a seguir cuidando' }}
        </h2>
        <div class="prose mx-auto mt-4 max-w-2xl">
            {!! $bloque['contenido'] ?? '<p>Tu colaboración se transforma en alimento, cuidados y oportunidades.</p>' !!}
        </div>
        <div class="mt-7 flex flex-wrap justify-center gap-3">
            <a
                href="{{ route('donaciones.donar') }}"
                class="rounded-xl bg-asoka-600 px-5 py-3 font-bold text-white"
            >Hacer una donación</a
            ><a
                href="{{ route('apadrinamientos.crear') }}"
                class="rounded-xl border-2 border-asoka-600 px-5 py-3 font-bold text-asoka-800"
            >Apadrinar</a
            >
        </div>
    </div>
</section>
