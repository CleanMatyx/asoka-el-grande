@php
    $imagen = $animal->galeria[0] ?? null;
    $imagen = $imagen
        ? (filter_var($imagen, FILTER_VALIDATE_URL) ? $imagen : \Illuminate\Support\Facades\Storage::disk('public')->url($imagen))
        : asset('images/animal-sin-foto.png');
    $edad = $animal->fecha_nacimiento
        ? $animal->fecha_nacimiento->age . ' ' . ($animal->fecha_nacimiento->age === 1 ? 'año' : 'años')
        : 'Edad por estimar';
@endphp
<article
    class="card-hover flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-asoka-100"
>
    <a
        href="{{ route('animales.mostrar', $animal->slug) }}"
        class="relative block aspect-square overflow-hidden bg-asoka-100"
    >
        <img
            src="{{ $imagen }}"
            alt="{{ $animal->nombre }}"
            class="h-full w-full object-cover"
        />
        @if ($animal->estado === 'caso_especial')
            <span
                class="absolute right-3 top-3 rounded-full bg-amber-300 px-3 py-1 text-xs font-extrabold text-amber-950"
                ><i class="fa fa-heartbeat mr-1" aria-hidden="true"></i>Caso
                especial</span
            >
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between gap-2">
            <h2 class="font-display text-2xl font-bold text-asoka-800">
                {{ $animal->nombre }}
            </h2>
            <span
                class="rounded-full bg-asoka-100 px-2 py-1 text-xs font-bold text-asoka-700"
                >{{ $animal->estado === 'en_acogida' ? 'En acogida' : 'En adopción' }}</span
            >
        </div>
        <p class="mt-2 text-sm text-slate-600">{{ ucfirst($animal->especie) }} · {{ $animal->sexo ? ucfirst($animal->sexo) . ' · ' : '' }}{{ $edad }}</p>
        <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($animal->descripcion), 130) }}</p>
        <a
            href="{{ route('animales.mostrar', $animal->slug) }}"
            class="mt-5 block rounded-full border-2 border-asoka-500 px-4 py-2 text-center font-bold text-asoka-700 transition hover:bg-asoka-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-asoka-300"
            >Conocer a {{ $animal->nombre }}</a
        >
    </div>
</article>
