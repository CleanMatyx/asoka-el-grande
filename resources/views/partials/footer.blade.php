@php
    $resolverUrlPie = function (array $enlace): string {
        if (filled($enlace['url'] ?? null)) {
            return $enlace['url'];
        }

        return filled($enlace['pagina_clave'] ?? null) ? url('/' . $enlace['pagina_clave']) : '#';
    };
    $menuPiePrincipal = collect($menusPie ?? [])->first();
    $opcionesPie = $menuPiePrincipal?->opciones ?? [];
    $imagenIdentidadPie = $opcionesPie['imagen_identidad'] ?? null;
    $imagenIdentidadPie = is_array($imagenIdentidadPie) ? reset($imagenIdentidadPie) : $imagenIdentidadPie;
    $imagenIdentidadPie = is_string($imagenIdentidadPie) && $imagenIdentidadPie !== '[]' ? $imagenIdentidadPie : null;
    $textoIdentidadPie = $opcionesPie['texto_identidad'] ?? 'Asociación para la defensa y protección de los animales.';
    $seccionesPie = collect($menusPie ?? [])
        ->flatMap(fn ($menu) => $menu->elementos ?? [])
        ->filter(fn (array $seccion): bool => $seccion['visible'] ?? true);
@endphp

<footer class="mt-20 bg-asoka-400 text-white">
    <div
        class="container mx-auto grid grid-cols-1 gap-8 px-4 py-10 md:grid-cols-4"
    >
        <div>
            <img
                src="{{ $imagenIdentidadPie ? Storage::disk('public')->url($imagenIdentidadPie) : asset('images/animal-sin-foto.png') }}"
                alt="Asoka el Grande"
                class="h-20 w-auto max-w-full rounded bg-white p-1 object-contain"
            />
            @if (filled($textoIdentidadPie))
                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed">{{ $textoIdentidadPie }}</p>
            @endif
        </div>
        @foreach ($seccionesPie as $seccion)
            @php ($tipo = $seccion['tipo_bloque'] ?? 'enlaces')
            <div>
                @if ($seccion['etiqueta'] ?? null)
                    <h2 class="font-display text-lg">
                        {{ $seccion['etiqueta'] }}
                    </h2>
                @endif
                @if ($tipo === 'contacto')
                    @if ($ajustesSitio->direccion_albergue)
                        <p class="mt-3 whitespace-pre-line text-sm leading-6"><i class="fas fa-map-marker-alt mr-2" aria-hidden="true"></i>{{ $ajustesSitio->direccion_albergue }}</p>
                    @endif
                    @if ($ajustesSitio->email_contacto)
                        <p class="mt-2 text-sm"><a href="mailto:{{ $ajustesSitio->email_contacto }}">{{ $ajustesSitio->email_contacto }}</a></p>
                    @endif
                    @if ($ajustesSitio->telefono_alicante)
                        <p class="mt-2 text-sm"><a href="tel:{{ preg_replace('/\s+/', '', $ajustesSitio->telefono_alicante) }}">{{ $ajustesSitio->telefono_alicante }}</a></p>
                    @endif
                    @if ($ajustesSitio->horarios_visita)
                        <p class="mt-3 whitespace-pre-line text-sm leading-6"><strong>Visitas:</strong> {{ $ajustesSitio->horarios_visita }}</p>
                    @endif
                @elseif ($tipo === 'texto')
                    <p class="mt-3 whitespace-pre-line text-sm leading-6">{{ $seccion['texto'] ?? '' }}</p>
                @else
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach (collect($seccion['subenlaces'] ?? [])->filter(fn (array $enlace): bool => $enlace['visible'] ?? true) as $enlace)
                            <li>
                                <a
                                    href="{{ $resolverUrlPie($enlace) }}"
                                    class="hover:text-asoka-100"
                                    >{{ $enlace['etiqueta'] ?? '' }}</a
                                >
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
    <div class="border-t border-white/40 py-4 text-center text-xs">
        © {{ date('Y') }} Asoka el Grande. Todos los derechos reservados.
    </div>
</footer>
