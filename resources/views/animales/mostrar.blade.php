<!doctype html>
<html lang="es">
<head>
    @php
        $tituloSeo = $animal->meta_titulo ?: "Adopta a {$animal->nombre} | Asoka el Grande";
        $descripcionSeo = $animal->meta_descripcion ?: "Conoce a {$animal->nombre}, {$animal->especie} que busca una familia en Asoka el Grande.";
        $imagenPrincipal = $imagenes[0];
        $edad = $animal->fecha_nacimiento?->age;
        $schemaAnimal = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $animal->nombre,
            'description' => strip_tags($animal->descripcion ?: $descripcionSeo),
            'image' => $imagenes,
            'category' => ucfirst($animal->especie),
            'brand' => ['@type' => 'Organization', 'name' => 'Asoka el Grande'],
        ];
    @endphp
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $tituloSeo }}</title>
    <meta name="description" content="{{ $descripcionSeo }}" />
    <meta property="og:title" content="{{ $tituloSeo }}" />
    <meta property="og:description" content="{{ $descripcionSeo }}" />
    <meta property="og:image" content="{{ $imagenPrincipal }}" />
    <meta property="og:type" content="article" />
    <meta property="og:locale" content="es_ES" />
    <meta name="twitter:card" content="summary_large_image" />
    @vite (['resources/css/app.css'])
    <script
        defer
        src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"
    ></script>
    <script type="application/ld+json">
        @json($schemaAnimal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    </script>
</head>
<body class="bg-amber-50 text-slate-900 antialiased">
    <a
        href="#contenido"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-slate-950 focus:px-4 focus:py-3 focus:text-white"
        >Saltar al contenido</a
    >

    <header class="border-b border-amber-100 bg-white">
        <div
            class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
        >
            <a
                href="/"
                class="text-lg font-extrabold tracking-tight text-amber-800"
                >Asoka el Grande</a
            >
            <a
                href="/"
                class="text-sm font-semibold text-slate-700 underline-offset-4 hover:text-amber-800 hover:underline"
                >Inicio</a
            >
        </div>
    </header>

    <main
        id="contenido"
        class="mx-auto max-w-7xl px-4 py-8 pb-40 sm:px-6 lg:px-8"
        x-data="{ imagenActual: 0, imagenes: @js($imagenes), modalAbierto: false, tipo: 'adopcion', paso: 1, abrirSolicitud(tipo) { this.tipo = tipo; this.paso = 1; this.modalAbierto = true; this.$nextTick(() => this.$refs.cerrarModal.focus()) }, anterior() { this.imagenActual = (this.imagenActual - 1 + this.imagenes.length) % this.imagenes.length }, siguiente() { this.imagenActual = (this.imagenActual + 1) % this.imagenes.length } }"
    >
        @if (session('exito'))
            <div
                role="status"
                class="mb-6 rounded-xl border border-emerald-300 bg-emerald-50 p-4 font-medium text-emerald-900"
            >
                {{ session('exito') }}
            </div>
        @endif

        <div class="mb-6 flex flex-wrap items-center gap-2 text-sm font-bold">
            <span
                class="rounded-full bg-emerald-700 px-3 py-1 text-white"
                >{{ match($animal->estado) { 'adoptable' => 'En adopción', 'caso_especial' => 'Caso especial', 'en_acogida' => 'En acogida', 'adoptado' => 'Adoptado', 'invisible' => 'Invisible', default => 'Santuario' } }}</span
            >
            <span
                class="rounded-full bg-slate-200 px-3 py-1 text-slate-900"
                >{{ ucfirst($animal->especie) }}</span
            >
            @if ($animal->sexo)
                <span
                    class="rounded-full bg-sky-100 px-3 py-1 text-sky-950"
                    >{{ ucfirst($animal->sexo) }}</span
                >
            @endif
        </div>

        <div
            class="grid gap-8 lg:grid-cols-[minmax(0,1.45fr)_minmax(19rem,.8fr)]"
        >
            <section aria-label="Galería de {{ $animal->nombre }}">
                <div
                    class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-slate-200 shadow-sm"
                >
                    <img
                        :src="imagenes[imagenActual]"
                        :alt="`Foto ${imagenActual + 1} de {{ e($animal->nombre) }}`"
                        class="h-full w-full object-cover"
                    />
                    <template x-if="imagenes.length > 1">
                        <div>
                            <button
                                type="button"
                                @click="anterior"
                                class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/95 p-3 text-slate-900 shadow hover:bg-white focus:outline-none focus:ring-4 focus:ring-amber-400"
                                aria-label="Foto anterior"
                            >
                                ‹
                            </button>
                            <button
                                type="button"
                                @click="siguiente"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/95 p-3 text-slate-900 shadow hover:bg-white focus:outline-none focus:ring-4 focus:ring-amber-400"
                                aria-label="Foto siguiente"
                            >
                                ›
                            </button>
                        </div>
                    </template>
                </div>
                <div
                    class="mt-3 flex gap-3 overflow-x-auto pb-2"
                    aria-label="Seleccionar imagen"
                >
                    <template
                        x-for="(imagen, indice) in imagenes"
                        :key="imagen"
                    >
                        <button
                            type="button"
                            @click="imagenActual = indice"
                            class="h-20 w-24 shrink-0 overflow-hidden rounded-lg border-4 focus:outline-none focus:ring-4 focus:ring-amber-400"
                            :class="imagenActual === indice
                                ? 'border-amber-600'
                                : 'border-transparent'"
                            :aria-label="`Mostrar foto ${indice + 1}`"
                            :aria-current="imagenActual === indice"
                        >
                            <img
                                :src="imagen"
                                alt=""
                                class="h-full w-full object-cover"
                            />
                        </button>
                    </template>
                </div>
            </section>

            <aside
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <p class="text-sm font-bold uppercase tracking-wider text-amber-800">Busca una familia</p>
                <h1
                    class="mt-1 text-4xl font-extrabold tracking-tight text-slate-950"
                >
                    {{ $animal->nombre }}
                </h1>
                <dl class="mt-6 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-amber-50 p-3">
                        <dt class="text-xs font-bold uppercase text-slate-600">
                            Edad
                        </dt>
                        <dd class="mt-1 font-bold">
                            {{ $edad ? $edad . ' ' . ($edad === 1 ? 'año' : 'años') : 'Por estimar' }}
                            @if ($animal->fecha_estimada)
                                <span class="text-xs font-normal"
                                    >(estimada)</span
                                >
                            @endif
                        </dd>
                    </div>
                    <div class="rounded-xl bg-amber-50 p-3">
                        <dt class="text-xs font-bold uppercase text-slate-600">
                            Raza
                        </dt>
                        <dd class="mt-1 font-bold">
                            {{ $animal->raza ?: 'Mestizo/a' }}
                        </dd>
                    </div>
                    <div class="rounded-xl bg-amber-50 p-3">
                        <dt class="text-xs font-bold uppercase text-slate-600">
                            Tamaño
                        </dt>
                        <dd class="mt-1 font-bold">
                            {{ $animal->tamano ? ucfirst($animal->tamano) : 'Por estimar' }}
                        </dd>
                    </div>
                    <div class="rounded-xl bg-amber-50 p-3">
                        <dt class="text-xs font-bold uppercase text-slate-600">
                            Salud
                        </dt>
                        <dd class="mt-1 text-sm font-bold">
                            {{ collect([$animal->vacunado ? 'Vacunado' : null, $animal->con_chip ? 'Con chip' : null, $animal->esterilizado ? 'Esterilizado' : null])->filter()->implode(' · ') ?: 'Consultar' }}
                        </dd>
                    </div>
                </dl>
            </aside>
        </div>

        <div
            class="mt-10 grid gap-8 lg:grid-cols-[minmax(0,1.45fr)_minmax(19rem,.8fr)]"
        >
            <article
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8"
            >
                <h2 class="text-2xl font-extrabold">
                    La historia de {{ $animal->nombre }}
                </h2>
                <div class="prose prose-slate mt-5 max-w-none leading-8">
                    {!! nl2br(e(strip_tags($animal->descripcion ?: 'Estamos preparando la historia de este animal.'))) !!}
                </div>
                @if ($animal->necesidades_especiales)
                    <section
                        class="mt-7 rounded-xl border-l-4 border-amber-600 bg-amber-50 p-5"
                        aria-labelledby="necesidades-especiales"
                    >
                        <h3
                            id="necesidades-especiales"
                            class="font-extrabold text-amber-950"
                        >
                            Necesidades especiales
                        </h3>
                        <p class="mt-2 leading-7 text-amber-950">{{ $animal->descripcion_necesidades_especiales ?: 'Consulta al equipo para conocer sus cuidados específicos.' }}</p>
                    </section>
                @endif
            </article>

            <section
                class="rounded-2xl bg-slate-900 p-6 text-white shadow-sm"
                aria-labelledby="compatibilidad"
            >
                <h2 id="compatibilidad" class="text-2xl font-extrabold">
                    Compatibilidad
                </h2>
                <ul class="mt-5 space-y-4">
                    @foreach (['perros' => ['Perros', $animal->compatible_perros], 'gatos' => ['Gatos', $animal->compatible_gatos], 'ninos' => ['Niños', $animal->compatible_ninos]] as [$etiqueta, $apto])
                        <li
                            class="flex items-center justify-between rounded-lg bg-white/10 px-4 py-3"
                        >
                            <span>Apto con {{ strtolower($etiqueta) }}</span
                            ><span
                                class="font-bold {{ $apto === true ? 'text-emerald-300' : ($apto === false ? 'text-rose-300' : 'text-amber-200') }}"
                                aria-label="{{ $apto === true ? 'Sí' : ($apto === false ? 'No' : 'Por valorar') }}"
                                >{{ $apto === true ? '✓ Sí' : ($apto === false ? '✕ No' : '— Por valorar') }}</span
                            >
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        <div
            class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:p-4"
        >
            <div class="mx-auto flex max-w-4xl flex-col gap-3 sm:flex-row">
                <button
                    type="button"
                    @click="abrirSolicitud('adopcion')"
                    class="flex-1 rounded-xl bg-amber-700 px-5 py-3 text-center font-extrabold text-white hover:bg-amber-800 focus:outline-none focus:ring-4 focus:ring-amber-400"
                >
                    Adoptar a {{ $animal->nombre }}
                </button>
                <button
                    type="button"
                    @click="abrirSolicitud('acogida')"
                    class="flex-1 rounded-xl border-2 border-amber-700 px-5 py-3 text-center font-extrabold text-amber-900 hover:bg-amber-50 focus:outline-none focus:ring-4 focus:ring-amber-400"
                >
                    Solicitar acogida
                </button>
            </div>
        </div>

        <div
            x-cloak
            x-show="modalAbierto"
            x-transition.opacity
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-solicitud"
            @keydown.escape.window="modalAbierto = false"
        >
            <div
                class="mx-auto my-6 max-w-2xl rounded-2xl bg-white p-6 shadow-2xl sm:p-8"
                @click.outside="modalAbierto = false"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2
                            id="titulo-solicitud"
                            class="text-2xl font-extrabold"
                            x-text="tipo === 'adopcion' ? 'Adoptar a {{ e($animal->nombre) }}' : 'Solicitar acogida para {{ e($animal->nombre) }}'"
                        ></h2>
                        <p class="mt-1 text-slate-600">Paso <span x-text="paso"></span> de 3</p>
                    </div>
                    <button
                        type="button"
                        x-ref="cerrarModal"
                        @click="modalAbierto = false"
                        class="rounded-lg p-2 text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-amber-400"
                        aria-label="Cerrar formulario"
                    >
                        ✕
                    </button>
                </div>
                <form
                    class="mt-6"
                    method="POST"
                    action="{{ route('solicitudes-adopcion.almacenar', $animal) }}"
                >
                    @csrf
                    <input type="hidden" name="tipo" :value="tipo" />
                    <fieldset x-show="paso === 1">
                        <legend class="text-lg font-bold">Tus datos</legend>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="font-semibold"
                                >Nombre y apellidos<input
                                    required
                                    name="nombre_solicitante"
                                    value="{{ old('nombre_solicitante') }}"
                                    class="mt-1 w-full rounded-lg border-slate-300"
                                    autocomplete="name" /></label
                            ><label class="font-semibold"
                                >Correo electrónico<input
                                    required
                                    type="email"
                                    name="email_solicitante"
                                    value="{{ old('email_solicitante') }}"
                                    class="mt-1 w-full rounded-lg border-slate-300"
                                    autocomplete="email" /></label
                            ><label class="font-semibold"
                                >Teléfono<input
                                    required
                                    name="telefono_solicitante"
                                    value="{{ old('telefono_solicitante') }}"
                                    class="mt-1 w-full rounded-lg border-slate-300"
                                    autocomplete="tel" /></label
                            ><label class="font-semibold"
                                >Ciudad<input
                                    required
                                    name="ciudad_solicitante"
                                    value="{{ old('ciudad_solicitante') }}"
                                    class="mt-1 w-full rounded-lg border-slate-300"
                                    autocomplete="address-level2"
                            /></label>
                        </div>
                    </fieldset>
                    <fieldset x-show="paso === 2" x-cloak>
                        <legend class="text-lg font-bold">Tu hogar</legend>
                        <div class="mt-4 space-y-4">
                            <label class="block font-semibold"
                                >Tipo de vivienda<select
                                    required
                                    name="tipo_vivienda"
                                    class="mt-1 w-full rounded-lg border-slate-300"
                                >
                                    <option value="">
                                        Selecciona una opción
                                    </option>
                                    <option value="piso">Piso</option>
                                    <option value="casa_con_jardin">
                                        Casa con jardín
                                    </option>
                                    <option value="casa_de_campo">
                                        Casa de campo
                                    </option>
                                    <option value="piso_compartido">
                                        Piso compartido
                                    </option>
                                    <option value="otro">Otro</option>
                                </select></label
                            ><label
                                class="flex items-center gap-3 font-semibold"
                                ><input
                                    type="hidden"
                                    name="tiene_otras_mascotas"
                                    value="0"
                                /><input
                                    type="checkbox"
                                    name="tiene_otras_mascotas"
                                    value="1"
                                    class="rounded border-slate-400 text-amber-700 focus:ring-amber-500"
                                />
                                Convivo con otras mascotas</label
                            ><label class="block font-semibold"
                                >Cuéntanos sobre tu vivienda<textarea
                                    required
                                    name="cuestionario[detalles_vivienda]"
                                    rows="4"
                                    class="mt-1 w-full rounded-lg border-slate-300"
                                ></textarea>
                            </label>
                        </div>
                    </fieldset>
                    <fieldset x-show="paso === 3" x-cloak>
                        <legend class="text-lg font-bold">Compromiso</legend>
                        <div class="mt-4 space-y-4">
                            <label class="block font-semibold"
                                >¿Qué experiencia tienes con animales?<textarea
                                    required
                                    name="cuestionario[experiencia_animales]"
                                    rows="4"
                                    class="mt-1 w-full rounded-lg border-slate-300"
                                ></textarea></label
                            ><label class="flex gap-3 text-sm leading-6"
                                ><input
                                    required
                                    type="checkbox"
                                    name="cuestionario[compromiso]"
                                    value="1"
                                    class="mt-1 rounded border-slate-400 text-amber-700 focus:ring-amber-500"
                                />
                                Entiendo que adoptar o acoger es un compromiso
                                responsable y acepto que Asoka el Grande
                                contacte conmigo para valorar la
                                solicitud.</label
                            >
                        </div>
                    </fieldset>
                    @if ($errors->any())
                        <div
                            role="alert"
                            class="mt-4 rounded-lg bg-rose-50 p-4 text-sm text-rose-900"
                        >
                            Revisa los campos obligatorios antes de enviar la
                            solicitud.
                        </div>
                    @endif
                    <div class="mt-7 flex justify-between gap-3">
                        <button
                            x-show="paso > 1"
                            type="button"
                            @click="paso--"
                            class="rounded-lg px-4 py-2 font-bold text-slate-700 hover:bg-slate-100"
                        >
                            Atrás</button
                        ><span x-show="paso === 1"></span
                        ><button
                            x-show="paso < 3"
                            type="button"
                            @click="paso++"
                            class="rounded-lg bg-slate-900 px-5 py-3 font-bold text-white focus:outline-none focus:ring-4 focus:ring-amber-400"
                        >
                            Continuar</button
                        ><button
                            x-show="paso === 3"
                            type="submit"
                            class="rounded-lg bg-amber-700 px-5 py-3 font-bold text-white focus:outline-none focus:ring-4 focus:ring-amber-400"
                        >
                            Enviar solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
