@php
    use App\Support\ColorModulo;

    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
    $diapositivas = collect($bloque['diapositivas'] ?? [])
        ->filter(fn (array $diapositiva): bool => ($diapositiva['visible'] ?? true) && (filled($diapositiva['imagen'] ?? null) || filled($diapositiva['imagen_url'] ?? null)))
        ->values();
    $automatico = ($bloque['modo_carrusel'] ?? 'automatico') === 'automatico';
    $intervalo = min(max((int) ($bloque['intervalo_carrusel'] ?? 5), 2), 20) * 1000;
@endphp

<section class="py-12 sm:py-16" style="background-color: {{ $colorFondo }}">
    <div class="container mx-auto max-w-6xl px-4">
        @if (filled($bloque['titulo'] ?? null))
            <h2 class="mb-6 font-display text-3xl font-bold text-asoka-900">
                {{ $bloque['titulo'] }}
            </h2>
        @endif

        @if ($diapositivas->isNotEmpty())
            <div
                x-data="{
                    indice: 0,
                    total: {{ $diapositivas->count() }},
                    temporizador: null,
                    siguiente() { this.indice = (this.indice + 1) % this.total },
                    anterior() { this.indice = (this.indice - 1 + this.total) % this.total },
                    iniciar() {
                        if ({{ $automatico ? 'true' : 'false' }} && this.total > 1) {
                            this.temporizador = setInterval(() => this.siguiente(), {{ $intervalo }})
                        }
                    },
                    detener() { clearInterval(this.temporizador); this.temporizador = null },
                    reanudar() { if (!this.temporizador) this.iniciar() }
                }"
                x-init="iniciar()"
                @mouseenter="detener()"
                @mouseleave="reanudar()"
                class="relative overflow-hidden rounded-2xl bg-slate-900 shadow-lg"
                aria-roledescription="carrusel"
                aria-label="Galería de imágenes"
            >
                @foreach ($diapositivas as $indice => $diapositiva)
                    @php
                        $imagen = filled($diapositiva['imagen'] ?? null)
                            ? Storage::disk('public')->url($diapositiva['imagen'])
                            : $diapositiva['imagen_url'];
                        $enlace = $diapositiva['enlace'] ?? null;
                    @endphp
                    <div
                        x-show="indice === {{ $indice }}"
                        x-transition.opacity
                        @if ($indice > 0) x-cloak @endif
                    >
                        @if (filled($enlace))
                            <a
                                href="{{ $enlace }}"
                                class="block focus:outline-none focus:ring-4 focus:ring-asoka-400"
                            >

                        @endif
                        <img
                            src="{{ $imagen }}"
                            alt="{{ $diapositiva['titulo'] ?? '' }}"
                            class="aspect-[16/8] w-full object-cover"
                        />
                        @if (filled($enlace))
                            </a>
                        @endif
                        @if (filled($diapositiva['titulo'] ?? null) || filled($diapositiva['texto'] ?? null))
                            <div
                                class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 to-transparent px-6 pb-6 pt-20 text-center text-white sm:px-10 sm:pb-10"
                            >
                                @if (filled($diapositiva['titulo'] ?? null))
                                    <h3
                                        class="font-display text-2xl font-bold sm:text-4xl"
                                    >
                                        {{ $diapositiva['titulo'] }}
                                    </h3>
                                @endif
                                @if (filled($diapositiva['texto'] ?? null))
                                    <p class="mx-auto mt-2 max-w-3xl text-sm sm:text-lg">{{ $diapositiva['texto'] }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach

                @if (($bloque['mostrar_flechas_carrusel'] ?? true) && $diapositivas->count() > 1)
                    <button
                        type="button"
                        @click="anterior()"
                        class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 px-3 py-2 text-xl font-bold text-asoka-900 shadow transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-asoka-300"
                        aria-label="Diapositiva anterior"
                    >
                        ‹
                    </button>
                    <button
                        type="button"
                        @click="siguiente()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 px-3 py-2 text-xl font-bold text-asoka-900 shadow transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-asoka-300"
                        aria-label="Diapositiva siguiente"
                    >
                        ›
                    </button>
                @endif

                @if ($diapositivas->count() > 1)
                    <div
                        class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-2"
                        aria-label="Seleccionar diapositiva"
                    >
                        @foreach ($diapositivas as $indice => $diapositiva)
                            <button
                                type="button"
                                @click="indice = {{ $indice }}"
                                :aria-current="indice === {{ $indice }}"
                                class="h-2.5 w-2.5 rounded-full bg-white/60 transition"
                                :class="indice === {{ $indice }} && 'scale-125 bg-white'"
                                aria-label="Ir a la diapositiva {{ $indice + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div
                class="flex aspect-[16/8] items-center justify-center rounded-2xl bg-asoka-100 text-slate-600"
            >
                Añade al menos una diapositiva con imagen para ver el carrusel.
            </div>
        @endif
    </div>
</section>
