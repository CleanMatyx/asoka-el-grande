@php
    use App\Support\ColorModulo;

    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
    $campos = collect($bloque['campos_tarjeta'] ?? [])
        ->filter(fn (array $campo): bool => ($campo['visible'] ?? true) && filled($campo['etiqueta'] ?? null) && filled($campo['valor'] ?? null));
    $direccionConMapa = $campos->first(fn (array $campo): bool => ($campo['tipo'] ?? '') === 'direccion' && ($campo['mostrar_mapa'] ?? false));
@endphp

<section class="py-12 sm:py-16" style="background-color: {{ $colorFondo }}">
    <div class="container mx-auto max-w-4xl px-4">
        @if (filled($bloque['titulo'] ?? null))
            <h2
                class="font-display text-center text-3xl font-bold text-asoka-900"
            >
                {{ $bloque['titulo'] }}
            </h2>
        @endif

        @if (filled($bloque['contenido'] ?? null))
            <div
                class="contenido-enriquecido mx-auto mt-4 max-w-2xl text-center"
            >
                {!! $bloque['contenido'] !!}
            </div>
        @endif

        <div
            class="mx-auto mt-7 max-w-2xl overflow-hidden rounded-2xl border border-asoka-200 bg-white shadow-sm"
        >
            <dl class="divide-y divide-asoka-100">
                @forelse ($campos as $campo)
                    @php
                        $tipo = $campo['tipo'] ?? 'texto';
                        $valor = $campo['valor'];
                        $enlace = match ($tipo) {
                            'correo' => 'mailto:' . $valor,
                            'telefono' => 'tel:' . preg_replace('/[^+0-9]/', '', $valor),
                            'direccion' => $campo['url_mapa'] ?? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($valor),
                            'enlace' => $campo['url'] ?? $valor,
                            default => null,
                        };
                        $icono = match ($tipo) {
                            'correo' => '✉',
                            'telefono' => '☎',
                            'direccion' => '⌖',
                            'enlace' => '↗',
                            default => '•',
                        };
                    @endphp
                    <div
                        class="grid gap-0.5 px-4 py-2.5 sm:grid-cols-[9rem_1fr] sm:gap-3"
                    >
                        <dt class="font-bold text-asoka-800">
                            <span
                                class="mr-2"
                                aria-hidden="true"
                                >{{ $icono }}</span
                            >{{ $campo['etiqueta'] }}
                        </dt>
                        <dd class="whitespace-pre-line text-slate-700">
                            @if ($enlace)
                                <a
                                    href="{{ $enlace }}"
                                    @if ($tipo === 'enlace') target="_blank" rel="noopener noreferrer" @endif
                                    class="font-semibold text-asoka-700 underline underline-offset-4 hover:text-asoka-900"
                                >
                                    {{ $valor }}
                                </a>
                            @else
                                {{ $valor }}
                            @endif
                        </dd>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-slate-600">
                        Añade los datos que quieras mostrar en esta tarjeta.
                    </div>
                @endforelse
            </dl>

            @if ($direccionConMapa)
                <iframe
                    title="Mapa de {{ $direccionConMapa['valor'] }}"
                    src="https://www.google.com/maps?q={{ rawurlencode($direccionConMapa['valor']) }}&output=embed"
                    class="h-72 w-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            @endif
        </div>
    </div>
</section>
