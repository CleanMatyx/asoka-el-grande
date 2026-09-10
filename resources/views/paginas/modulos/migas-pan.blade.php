@php
    use App\Models\Pagina;
    use App\Support\ColorModulo;

    $colorFondo = ColorModulo::fondo($bloque['color_fondo'] ?? null);
    $modoPrevisualizacion = $modoPrevisualizacion ?? false;
    $versionActual = isset($pagina) ? $pagina->versionParaMostrar($modoPrevisualizacion) : [];
    $textoInicio = 'Inicio';
    $separador = match ($bloque['separador_migas'] ?? 'chevron') {
        'barra' => '/',
        'punto' => '·',
        default => '›',
    };
    $claseTamano = match ($bloque['tamano_migas'] ?? 'normal') {
        'compacto' => 'text-xs sm:text-sm',
        'grande' => 'text-base sm:text-lg',
        default => 'text-sm sm:text-base',
    };
    $nivelesBase = [];
    $paginaBaseId = $versionActual['pagina_base_id'] ?? null;
    $visitadas = [];

    if (($bloque['incluir_paginas_base'] ?? true) && filled($paginaBaseId)) {
        while ($paginaBaseId && ! in_array($paginaBaseId, $visitadas, true)) {
            $visitadas[] = $paginaBaseId;
            $paginaBase = Pagina::query()->find($paginaBaseId);

            if (! $paginaBase) {
                break;
            }

            $versionBase = $paginaBase->versionParaMostrar($modoPrevisualizacion);
            $nivelesBase[] = [
                'titulo' => $versionBase['titulo'] ?? $paginaBase->titulo,
                'url' => ($versionBase['clave'] ?? $paginaBase->clave) === 'inicio'
                    ? route('inicio')
                    : url('/' . ($versionBase['clave'] ?? $paginaBase->clave)),
            ];
            $paginaBaseId = $versionBase['pagina_base_id'] ?? null;
        }

        $nivelesBase = array_reverse($nivelesBase);
    }

    $tituloActual = $versionActual['titulo'] ?? $bloque['titulo'] ?? 'Página actual';
@endphp

<nav
    aria-label="Migas de pan"
    class="py-2"
    style="background-color: {{ $colorFondo }}"
>
    <div class="container mx-auto max-w-6xl px-4">
        <ol
            class="{{ $claseTamano }} flex flex-wrap items-center gap-x-1.5 gap-y-0.5 font-semibold text-asoka-700"
        >
            <li>
                <a
                    href="{{ route('inicio') }}"
                    class="rounded underline-offset-4 hover:text-asoka-900 hover:underline focus:outline-none focus:ring-2 focus:ring-asoka-500"
                >
                    {{ $textoInicio }}
                </a>
            </li>

            @foreach ($nivelesBase as $nivel)
                <li aria-hidden="true" class="text-asoka-400">
                    {{ $separador }}
                </li>
                <li>
                    <a
                        href="{{ $nivel['url'] }}"
                        class="rounded underline-offset-4 hover:text-asoka-900 hover:underline focus:outline-none focus:ring-2 focus:ring-asoka-500"
                    >
                        {{ $nivel['titulo'] }}
                    </a>
                </li>
            @endforeach

            @if ($bloque['mostrar_pagina_actual'] ?? true)
                <li aria-hidden="true" class="text-asoka-400">
                    {{ $separador }}
                </li>
                <li aria-current="page" class="text-slate-700">
                    {{ $tituloActual }}
                </li>
            @endif
        </ol>
    </div>
</nav>
