@php
    $camposBuscador = collect($bloque['campos_buscador'] ?? [
        ['nombre' => 'especie', 'etiqueta' => 'Especie', 'texto_vacio' => 'Todas', 'visible' => true, 'opciones' => [['valor' => 'perro', 'etiqueta' => 'Perro'], ['valor' => 'gato', 'etiqueta' => 'Gato'], ['valor' => 'otro', 'etiqueta' => 'Otro']]],
        ['nombre' => 'tamano', 'etiqueta' => 'Tamaño', 'texto_vacio' => 'Cualquiera', 'visible' => true, 'opciones' => [['valor' => 'pequeno', 'etiqueta' => 'Pequeño'], ['valor' => 'mediano', 'etiqueta' => 'Mediano'], ['valor' => 'grande', 'etiqueta' => 'Grande'], ['valor' => 'gigante', 'etiqueta' => 'Gigante']]],
        ['nombre' => 'sexo', 'etiqueta' => 'Sexo', 'texto_vacio' => 'Cualquiera', 'visible' => true, 'opciones' => [['valor' => 'macho', 'etiqueta' => 'Macho'], ['valor' => 'hembra', 'etiqueta' => 'Hembra']]],
    ])->filter(fn (array $campo): bool => $campo['visible'] ?? true);
@endphp

<form
    action="{{ route('animales.catalogo') }}"
    method="GET"
    class="rounded-2xl bg-white p-5 shadow-xl"
    aria-label="Buscar un animal"
>
    <h2 class="text-xl font-extrabold">
        {{ $bloque['titulo_buscador'] ?? 'Busca a tu compañero' }}
    </h2>
    <div class="mt-4 grid gap-3 sm:grid-cols-2">
        @foreach ($camposBuscador as $campo)
            @if (($campo['nombre'] ?? '') === 'buscar')
                <label class="text-sm font-bold sm:col-span-2">
                    {{ $campo['etiqueta'] ?? '' }}
                    <input
                        type="search"
                        name="buscar"
                        placeholder="{{ $campo['texto_vacio'] ?? '' }}"
                        class="mt-1 w-full rounded-lg border-slate-300"
                    />
                </label>
            @else
                <label
                    class="text-sm font-bold {{ $loop->last && $camposBuscador->count() % 2 === 1 ? 'sm:col-span-2' : '' }}"
                >
                    {{ $campo['etiqueta'] ?? '' }}
                    <select
                        name="{{ $campo['nombre'] ?? '' }}"
                        class="mt-1 w-full rounded-lg border-slate-300"
                    >
                        <option value="">
                            {{ $campo['texto_vacio'] ?? 'Cualquiera' }}
                        </option>
                        @foreach ($campo['opciones'] ?? [] as $opcion)
                            <option value="{{ $opcion['valor'] ?? '' }}">
                                {{ $opcion['etiqueta'] ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endif
        @endforeach
    </div>
    <button
        class="mt-5 w-full rounded-xl bg-amber-700 px-5 py-3 font-extrabold text-white hover:bg-amber-800 focus:outline-none focus:ring-4 focus:ring-amber-400"
    >
        {{ $bloque['texto_boton'] ?? 'Buscar mi compañero' }}
    </button>
</form>
