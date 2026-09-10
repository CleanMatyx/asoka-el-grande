@extends ('layouts.app')

@section ('title', 'Adopta, acoge y ayuda | Asoka el Grande · Alicante')
@section ('meta_description', 'Encuentra a tu compañero de vida, ofrece acogida o colabora con Asoka el Grande, protectora de animales en Alicante.')

@push ('meta')
    @php
        $descripcion = $ajustesSitio->subtitulo_hero ?: 'Encuentra a tu compañero de vida, ofrece acogida o colabora con Asoka el Grande, protectora de animales en Alicante.';
        $foto = fn ($animal) => filled($animal->galeria[0] ?? null)
            ? (filter_var($animal->galeria[0], FILTER_VALIDATE_URL) ? $animal->galeria[0] : \Illuminate\Support\Facades\Storage::disk('public')->url($animal->galeria[0]))
            : asset('images/animal-sin-foto.png');
        $edad = fn ($animal) => $animal->fecha_nacimiento ? $animal->fecha_nacimiento->age . ' ' . ($animal->fecha_nacimiento->age === 1 ? 'año' : 'años') : 'Edad por estimar';
        $schemaOrganizacion = [
            '@context' => 'https://schema.org',
            '@type' => ['NGO', 'AnimalShelter'],
            'name' => 'Asoka el Grande',
            'description' => $descripcion,
            'url' => url('/'),
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Alicante', 'addressRegion' => 'Alicante', 'addressCountry' => 'ES'],
            'areaServed' => ['Alicante', 'Orihuela'],
        ];
    @endphp
    <meta property="og:type" content="website"
    /><meta property="og:title" content="Asoka el Grande · Alicante"
    /><meta property="og:description" content="{{ $descripcion }}"
    /><meta property="og:locale" content="es_ES"
    /><meta name="twitter:card" content="summary_large_image" />
    <script type="application/ld+json">
        @json($schemaOrganizacion, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    </script>
@endpush

@section ('content')
    <div id="contenido">
        <section class="bg-slate-950">
            <div
                class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-[1.15fr_.85fr] lg:px-8 lg:py-24"
            >
                <div>
                    <p class="font-bold uppercase tracking-[.18em] text-amber-300">{{ $ajustesSitio->etiqueta_hero ?: 'Protectora de animales · Alicante' }}</p>
                    <h1
                        class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl"
                    >
                        {{ $ajustesSitio->titulo_hero ?: 'Cada mirada merece un hogar. La tuya puede cambiarlo todo.' }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-200">{{ $ajustesSitio->subtitulo_hero ?: 'Conoce a los animales que esperan una segunda oportunidad, ofrece acogida temporal o ayuda a que nunca les falte cuidado.' }}</p>
                </div>
                <form
                    action="{{ route('animales.catalogo') }}"
                    method="GET"
                    class="rounded-2xl bg-white p-5 shadow-xl"
                    aria-label="Buscar un animal"
                >
                    <h2 class="text-xl font-extrabold">Busca a tu compañero</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <label class="text-sm font-bold"
                            >Especie<select
                                name="especie"
                                class="mt-1 w-full rounded-lg border-slate-300"
                            >
                                <option value="">Todas</option>
                                <option value="perro">Perro</option>
                                <option value="gato">Gato</option>
                                <option value="otro">Otro</option>
                            </select></label
                        ><label class="text-sm font-bold"
                            >Tamaño<select
                                name="tamano"
                                class="mt-1 w-full rounded-lg border-slate-300"
                            >
                                <option value="">Cualquiera</option>
                                <option value="pequeno">Pequeño</option>
                                <option value="mediano">Mediano</option>
                                <option value="grande">Grande</option>
                                <option value="gigante">Gigante</option>
                            </select></label
                        ><label class="text-sm font-bold sm:col-span-2"
                            >Sexo<select
                                name="sexo"
                                class="mt-1 w-full rounded-lg border-slate-300"
                            >
                                <option value="">Cualquiera</option>
                                <option value="macho">Macho</option>
                                <option value="hembra">Hembra</option>
                            </select></label
                        >
                    </div>
                    <button
                        class="mt-5 w-full rounded-xl bg-amber-700 px-5 py-3 font-extrabold text-white hover:bg-amber-800 focus:outline-none focus:ring-4 focus:ring-amber-400"
                    >
                        {{ $ajustesSitio->texto_boton_hero ?: 'Buscar mi compañero' }}
                    </button>
                </form>
            </div>
        </section>
        <section
            class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8"
            aria-labelledby="urgentes"
        >
            <p class="font-bold uppercase tracking-wider text-rose-700">Prioridad</p>
            <h2 id="urgentes" class="mt-1 text-3xl font-extrabold">
                Necesitan tu ayuda hoy
            </h2>
            <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($animalesUrgentes as $animal)
                    <article
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200"
                    >
                        <img
                            src="{{ $foto($animal) }}"
                            alt="{{ $animal->nombre }}"
                            class="aspect-[4/3] w-full object-cover"
                        />
                        <div class="p-5">
                            <span
                                class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-900"
                                >{{ $animal->estado === 'caso_especial' ? 'Caso especial' : 'Ayuda urgente' }}</span
                            >
                            <h3 class="mt-3 text-2xl font-extrabold">
                                {{ $animal->nombre }}
                            </h3>
                            <p class="mt-1 text-slate-600">{{ $edad($animal) }} · {{ $animal->raza ?: 'Mestizo/a' }}</p>
                            <div class="mt-4 flex gap-3 text-sm font-semibold">
                                <span aria-label="Compatible con perros"
                                    >🐕 {{ $animal->compatible_perros ? 'Sí' : '—' }}</span
                                ><span aria-label="Compatible con gatos"
                                    >🐈 {{ $animal->compatible_gatos ? 'Sí' : '—' }}</span
                                ><span aria-label="Compatible con niños"
                                    >👧 {{ $animal->compatible_ninos ? 'Sí' : '—' }}</span
                                >
                            </div>
                            <a
                                href="{{ route('animales.mostrar',$animal->slug) }}"
                                class="mt-5 inline-block rounded-lg bg-slate-900 px-4 py-2.5 font-bold text-white hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-amber-400"
                                >Ver ficha</a
                            >
                        </div>
                    </article>
                @empty
                    <p class="rounded-xl bg-amber-50 p-5 text-slate-700 sm:col-span-2 lg:col-span-3">Ahora mismo no hay casos destacados.</p>
                @endforelse
            </div>
        </section>
        <section id="ayudar" class="bg-amber-100 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="font-bold uppercase tracking-wider text-amber-900">Tu ayuda transforma vidas</p>
                <h2 class="mt-2 text-3xl font-extrabold">
                    Hay muchas formas de estar a su lado
                </h2>
                <div class="mt-7 grid gap-5 md:grid-cols-3">
                    <article class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-xl font-extrabold">Adopta o acoge</h3>
                        <p class="mt-3 leading-7 text-slate-700">Abre tu hogar para siempre o durante el tiempo que más lo necesitan.</p>
                        <a
                            href="#asoketes"
                            class="mt-5 inline-block font-bold text-amber-800 underline"
                            >Conocer animales</a
                        >
                    </article>
                    <article class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-xl font-extrabold">
                            Hazte padrino o socio
                        </h3>
                        <p class="mt-3 leading-7 text-slate-700">Tu aportación mensual da estabilidad, tratamientos y alimento.</p>
                        <a
                            href="{{ route('apadrinamientos.crear') }}"
                            class="mt-5 inline-block font-bold text-amber-800 underline"
                            >Quiero colaborar</a
                        >
                    </article>
                    <article
                        class="rounded-2xl bg-slate-900 p-6 text-white shadow-sm"
                    >
                        <h3 class="text-xl font-extrabold">Donación rápida</h3>
                        <p class="mt-3 leading-7 text-slate-200">Colabora mediante Bizum, tarjeta con Stripe o Teaming por 1 € al mes.</p>
                        <a
                            href="{{ route('donaciones.donar') }}"
                            class="mt-5 inline-block font-bold text-amber-300 underline"
                            >Hacer una donación</a
                        >
                    </article>
                </div>
            </div>
        </section>
        <section
            id="asoketes"
            class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8"
            aria-labelledby="ultimos"
        >
            <p class="font-bold uppercase tracking-wider text-amber-800">Recién llegados</p>
            <h2 id="ultimos" class="mt-1 text-3xl font-extrabold">
                Últimos Asoketes en adopción
            </h2>
            <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($ultimosIngresos as $animal)
                    <article
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200"
                    >
                        <img
                            src="{{ $foto($animal) }}"
                            alt="{{ $animal->nombre }}"
                            class="aspect-square w-full object-cover"
                        />
                        <div class="p-4">
                            <h3 class="text-xl font-extrabold">
                                {{ $animal->nombre }}
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">{{ ucfirst($animal->especie) }} · {{ $edad($animal) }}</p>
                            <a
                                href="{{ route('animales.mostrar',$animal->slug) }}"
                                class="mt-4 inline-block font-bold text-amber-800 underline"
                                >Conocer a {{ $animal->nombre }}</a
                            >
                        </div>
                    </article>
                @empty
                    <p class="rounded-xl bg-slate-100 p-5 text-slate-700 sm:col-span-2 lg:col-span-4">Muy pronto presentaremos a los animales que buscan familia.</p>
                @endforelse
            </div>
        </section>
        <section id="labor" class="bg-slate-900 py-14 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="font-bold uppercase tracking-wider text-amber-300">Transparencia</p>
                <h2 class="mt-1 text-3xl font-extrabold">
                    Nuestra labor en cifras
                </h2>
                <dl class="mt-8 grid gap-5 sm:grid-cols-3">
                    <div class="rounded-2xl bg-white/10 p-6">
                        <dt class="text-slate-200">Adoptados este año</dt>
                        <dd class="mt-2 text-4xl font-extrabold text-amber-300">
                            {{ $estadisticas['adoptados_este_ano'] }}
                        </dd>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-6">
                        <dt class="text-slate-200">En acogida</dt>
                        <dd class="mt-2 text-4xl font-extrabold text-amber-300">
                            {{ $estadisticas['en_acogida'] }}
                        </dd>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-6">
                        <dt class="text-slate-200">Años cuidando</dt>
                        <dd class="mt-2 text-4xl font-extrabold text-amber-300">
                            {{ $estadisticas['anos_cuidando'] }}
                        </dd>
                    </div>
                </dl>
            </div>
        </section>
    </div>
@endsection
