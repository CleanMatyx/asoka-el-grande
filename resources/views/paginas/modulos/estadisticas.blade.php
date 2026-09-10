@php
    use App\Models\AjusteSitio;
    use App\Models\Animal;
    use App\Support\ColorModulo;

    $ajustes = $ajustesSitio ?? AjusteSitio::actual();
    $estadisticas = $modoPrevisualizacion
        ? ['adoptados' => 124, 'acogida' => 18, 'anos' => 25]
        : [
            'adoptados' => $ajustes->contador_adoptados ?? Animal::query()->where('estado', 'adoptado')->whereYear('updated_at', now()->year)->count(),
            'acogida' => $ajustes->contador_acogidas ?? Animal::query()->where('estado', 'en_acogida')->count(),
            'anos' => $ajustes->contador_anos_cuidando ?? max(1, now()->year - 2001),
        ];
    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
    $textoClaro = ColorModulo::requiereTextoClaro($colorFondo);
@endphp

<section
    id="labor"
    class="py-14"
    style="background-color: {{ $colorFondo }}; color: {{ $textoClaro ? '#ffffff' : '#1e293b' }}"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (filled($bloque['etiqueta'] ?? null))
            <p
                class="font-bold uppercase tracking-wider {{ $textoClaro ? 'text-amber-300' : 'text-asoka-700' }}"
            >{{ $bloque['etiqueta'] }}</p>
        @endif
        <h2 class="mt-1 text-3xl font-extrabold">
            {{ $bloque['titulo'] ?? 'Nuestra labor en cifras' }}
        </h2>
        <dl class="mt-8 grid gap-5 sm:grid-cols-3">
            <div
                class="rounded-2xl p-6 {{ $textoClaro ? 'bg-white/10' : 'bg-slate-900/5' }}"
            >
                <dt
                    class="{{ $textoClaro ? 'text-slate-200' : 'text-slate-700' }}"
                >
                    Adoptados este año
                </dt>
                <dd
                    class="mt-2 text-4xl font-extrabold {{ $textoClaro ? 'text-amber-300' : 'text-asoka-700' }}"
                >
                    {{ $estadisticas['adoptados'] }}
                </dd>
            </div>
            <div
                class="rounded-2xl p-6 {{ $textoClaro ? 'bg-white/10' : 'bg-slate-900/5' }}"
            >
                <dt
                    class="{{ $textoClaro ? 'text-slate-200' : 'text-slate-700' }}"
                >
                    En acogida
                </dt>
                <dd
                    class="mt-2 text-4xl font-extrabold {{ $textoClaro ? 'text-amber-300' : 'text-asoka-700' }}"
                >
                    {{ $estadisticas['acogida'] }}
                </dd>
            </div>
            <div
                class="rounded-2xl p-6 {{ $textoClaro ? 'bg-white/10' : 'bg-slate-900/5' }}"
            >
                <dt
                    class="{{ $textoClaro ? 'text-slate-200' : 'text-slate-700' }}"
                >
                    Años cuidando
                </dt>
                <dd
                    class="mt-2 text-4xl font-extrabold {{ $textoClaro ? 'text-amber-300' : 'text-asoka-700' }}"
                >
                    {{ $estadisticas['anos'] }}
                </dd>
            </div>
        </dl>
    </div>
</section>
