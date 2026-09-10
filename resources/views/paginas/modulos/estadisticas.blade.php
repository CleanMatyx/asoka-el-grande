@php
    $ajustes = $ajustesSitio ?? \App\Models\AjusteSitio::actual();
    $estadisticas = $modoPrevisualizacion
        ? ['adoptados' => 124, 'acogida' => 18, 'anos' => 25]
        : [
            'adoptados' => $ajustes->contador_adoptados ?? \App\Models\Animal::query()->where('estado', 'adoptado')->whereYear('updated_at', now()->year)->count(),
            'acogida' => $ajustes->contador_acogidas ?? \App\Models\Animal::query()->where('estado', 'en_acogida')->count(),
            'anos' => $ajustes->contador_anos_cuidando ?? max(1, now()->year - 2001),
        ];
@endphp

<section id="labor" class="bg-slate-900 py-14 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (filled($bloque['etiqueta'] ?? null))
            <p class="font-bold uppercase tracking-wider text-amber-300">{{ $bloque['etiqueta'] }}</p>
        @endif
        <h2 class="mt-1 text-3xl font-extrabold">
            {{ $bloque['titulo'] ?? 'Nuestra labor en cifras' }}
        </h2>
        <dl class="mt-8 grid gap-5 sm:grid-cols-3">
            <div class="rounded-2xl bg-white/10 p-6">
                <dt class="text-slate-200">Adoptados este año</dt>
                <dd class="mt-2 text-4xl font-extrabold text-amber-300">
                    {{ $estadisticas['adoptados'] }}
                </dd>
            </div>
            <div class="rounded-2xl bg-white/10 p-6">
                <dt class="text-slate-200">En acogida</dt>
                <dd class="mt-2 text-4xl font-extrabold text-amber-300">
                    {{ $estadisticas['acogida'] }}
                </dd>
            </div>
            <div class="rounded-2xl bg-white/10 p-6">
                <dt class="text-slate-200">Años cuidando</dt>
                <dd class="mt-2 text-4xl font-extrabold text-amber-300">
                    {{ $estadisticas['anos'] }}
                </dd>
            </div>
        </dl>
    </div>
</section>
