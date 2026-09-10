@php
    use App\Models\Animal;
    use App\Support\ColorModulo;

    $fuente = $bloque['fuente_animales'] ?? 'ultimos';
    $animales = $modoPrevisualizacion
        ? collect()
        : Animal::query()
            ->when(
                $fuente === 'urgentes',
                fn ($consulta) => $consulta->whereIn('estado', ['caso_especial', 'invisible']),
                fn ($consulta) => $consulta->whereIn('estado', ['adoptable', 'en_acogida']),
            )
            ->latest('fecha_llegada')
            ->limit((int) ($bloque['limite'] ?? 4))
            ->get();
    $esUrgente = $fuente === 'urgentes';
    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
@endphp
<section class="py-12 sm:py-16" style="background-color: {{ $colorFondo }}">
    <div class="container mx-auto max-w-6xl px-4">
        @if (filled($bloque['etiqueta'] ?? null))
            <p
                class="font-bold uppercase tracking-wider {{ $esUrgente ? 'text-rose-700' : 'text-asoka-700' }}"
            >{{ $bloque['etiqueta'] }}</p>
        @endif
        <h2 class="font-display text-3xl font-bold text-asoka-900">
            {{ $bloque['titulo'] ?? 'Conoce a nuestros Asoketes' }}
        </h2>
        <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($animales as $animal)
                <a
                    href="{{ route('animales.mostrar', $animal->slug) }}"
                    class="overflow-hidden rounded-2xl bg-white shadow-sm"
                    ><img
                        src="{{ filled($animal->galeria[0] ?? null) ? Storage::disk('public')->url($animal->galeria[0]) : asset('images/animal-sin-foto.png') }}"
                        alt="{{ $animal->nombre }}"
                        class="aspect-square w-full object-cover"
                    /><span
                        class="block p-4 text-xl font-bold text-asoka-900"
                        >{{ $animal->nombre }}</span
                    >
                    @if ($esUrgente)
                        <span
                            class="mx-4 mb-4 inline-block rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-900"
                            >{{ $animal->estado === 'caso_especial' ? 'Caso especial' : 'Ayuda urgente' }}</span
                        >
                    @endif
                </a>
            @empty
                @for ($i = 0; $i < 4; $i++)
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <div
                            class="aspect-square rounded-xl bg-asoka-100"
                        ></div>
                        <span class="mt-3 block font-bold text-asoka-900"
                            >Animal destacado</span
                        >
                    </div>
                @endfor
            @endforelse
        </div>
    </div>
</section>
