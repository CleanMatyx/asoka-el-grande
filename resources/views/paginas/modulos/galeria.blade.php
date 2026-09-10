@php ($imagenes = collect($bloque['imagenes'] ?? [])->filter()->values())
<section class="bg-white py-12 sm:py-16">
    <div class="container mx-auto max-w-6xl px-4">
        @if (filled($bloque['titulo'] ?? null))
            <h2 class="font-display text-3xl font-bold text-asoka-900">
                {{ $bloque['titulo'] }}
            </h2>
        @endif
        @if (filled($bloque['contenido'] ?? null))
            <div class="prose mt-3 max-w-none">
                {!! $bloque['contenido'] !!}
            </div>
        @endif
        <div
            class="mt-6 @if(($bloque['estilo_galeria'] ?? 'cuadricula') === 'cuadricula' || $modoPrevisualizacion) grid grid-cols-2 gap-4 md:grid-cols-3 @else flex snap-x gap-4 overflow-x-auto pb-3 @endif"
        >
            @forelse ($imagenes as $imagen)
                <img
                    src="{{ Storage::disk('public')->url($imagen) }}"
                    alt=""
                    class="@if(($bloque['estilo_galeria'] ?? 'cuadricula') === 'carrusel' && !$modoPrevisualizacion) w-80 shrink-0 snap-center @else w-full @endif aspect-square rounded-xl object-cover shadow-sm"
                />
            @empty
                <p class="rounded-xl bg-asoka-50 p-5 text-slate-600">Añade imágenes a esta galería.</p>
            @endforelse
        </div>
    </div>
</section>
