@php
    $ajustes = $ajustesSitio ?? \App\Models\AjusteSitio::actual();
    $etiqueta = $bloque['etiqueta'] ?? $ajustes->etiqueta_hero ?? 'Protectora de animales · Alicante';
    $titulo = $bloque['titulo'] ?? $ajustes->titulo_hero ?? 'Cada mirada merece un hogar. La tuya puede cambiarlo todo.';
    $texto = $bloque['contenido'] ?? $ajustes->subtitulo_hero ?? 'Conoce a los animales que esperan una segunda oportunidad, ofrece acogida temporal o ayuda a que nunca les falte cuidado.';
@endphp

<section class="bg-slate-950">
    <div
        class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-[1.15fr_.85fr] lg:px-8 lg:py-24"
    >
        <div>
            @if (filled($etiqueta))
                <p class="font-bold uppercase tracking-[.18em] text-amber-300">{{ $etiqueta }}</p>
            @endif
            <h1
                class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl"
            >
                {{ $titulo }}
            </h1>
            @if (filled($texto))
                <div class="mt-5 max-w-2xl text-lg leading-8 text-slate-200">
                    {!! $texto !!}
                </div>
            @endif
        </div>
        @include ('paginas.modulos.parciales.formulario-buscador-animales', ['bloque' => $bloque])
    </div>
</section>
