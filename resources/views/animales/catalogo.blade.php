@extends ('layouts.app')

@section ('title', 'Animales en adopción')
@section ('meta_description', 'Conoce a los animales de Asoka el Grande que buscan una familia en Alicante. Filtra por especie, tamaño, sexo y edad.')

@section ('content')
    <section
        class="paw-pattern bg-gradient-to-br from-asoka-100 to-asoka-50 py-14"
    >
        <div class="container mx-auto px-4">
            <nav class="mb-3 text-sm text-slate-600" aria-label="Migas de pan">
                <a href="{{ route('inicio') }}" class="hover:text-asoka-600"
                    >Inicio</a
                ><span class="mx-2">/</span
                ><span class="font-semibold text-asoka-700">Adopciones</span>
            </nav>
            <h1
                class="font-display text-4xl font-bold text-asoka-800 md:text-5xl"
            >
                Asoketes en adopción
            </h1>
            <p class="mt-2 max-w-2xl text-slate-700">Estos son nuestros peludos que buscan una familia. Filtra para encontrar a tu compañero ideal.</p>
        </div>
    </section>
    <section class="container mx-auto px-4 py-10">
        <form
            method="GET"
            action="{{ route('animales.catalogo') }}"
            class="mb-8 grid grid-cols-1 gap-3 rounded-2xl bg-white p-4 shadow-sm sm:grid-cols-2 md:grid-cols-5 md:p-6"
        >
            <label class="sr-only" for="especie">Especie</label
            ><select
                id="especie"
                name="especie"
                class="rounded-full border border-asoka-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-asoka-400"
            >
                <option value="">Todas las especies</option>
                @foreach (['perro'=>'Perro','gato'=>'Gato','otro'=>'Otro'] as $valor=>$texto)
                    <option
                        value="{{ $valor }}"
                        @selected (($filtros['especie'] ?? '') === $valor)
                    >
                        {{ $texto }}
                    </option>
                @endforeach</select
            ><label class="sr-only" for="sexo">Sexo</label
            ><select
                id="sexo"
                name="sexo"
                class="rounded-full border border-asoka-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-asoka-400"
            >
                <option value="">Cualquier sexo</option>
                <option
                    value="macho"
                    @selected (($filtros['sexo'] ?? '') === 'macho')
                >
                    Macho
                </option>
                <option
                    value="hembra"
                    @selected (($filtros['sexo'] ?? '') === 'hembra')
                >
                    Hembra
                </option></select
            ><label class="sr-only" for="edad">Edad</label
            ><select
                id="edad"
                name="edad"
                class="rounded-full border border-asoka-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-asoka-400"
            >
                <option value="">Cualquier edad</option>
                @foreach (['cachorro'=>'Cachorro','joven'=>'Joven','adulto'=>'Adulto','senior'=>'Senior'] as $valor=>$texto)
                    <option
                        value="{{ $valor }}"
                        @selected (($filtros['edad'] ?? '') === $valor)
                    >
                        {{ $texto }}
                    </option>
                @endforeach</select
            ><label class="sr-only" for="tamano">Tamaño</label
            ><select
                id="tamano"
                name="tamano"
                class="rounded-full border border-asoka-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-asoka-400"
            >
                <option value="">Cualquier tamaño</option>
                @foreach (['pequeno'=>'Pequeño','mediano'=>'Mediano','grande'=>'Grande','gigante'=>'Gigante'] as $valor=>$texto)
                    <option
                        value="{{ $valor }}"
                        @selected (($filtros['tamano'] ?? '') === $valor)
                    >
                        {{ $texto }}
                    </option>
                @endforeach</select
            ><button
                type="submit"
                class="rounded-full bg-asoka-500 px-6 py-2 font-bold text-white transition hover:bg-asoka-600 focus:outline-none focus:ring-4 focus:ring-asoka-300"
            >
                <i class="fa fa-search mr-1" aria-hidden="true"></i> Filtrar
            </button>
        </form>
        @if ($animales->isNotEmpty())
            <p class="mb-5 text-sm text-slate-600">{{ $animales->total() }} animales encontrados.</p>
            <div
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
            >
                @foreach ($animales as $animal) @include ('partials.tarjeta-animal', ['animal'=>$animal]) @endforeach
            </div>
            <div class="mt-10">{{ $animales->links() }}</div>
        @else
            <div class="rounded-2xl bg-asoka-50 p-8 text-center">
                <i
                    class="fa fa-paw text-4xl text-asoka-500"
                    aria-hidden="true"
                ></i>
                <h2 class="mt-4 font-display text-2xl font-bold text-asoka-800">
                    No hemos encontrado ningún animal
                </h2>
                <p class="mt-2 text-slate-700">Prueba a quitar algún filtro o vuelve a revisar pronto.</p>
                <a
                    href="{{ route('animales.catalogo') }}"
                    class="mt-5 inline-block rounded-full bg-asoka-500 px-5 py-2 font-bold text-white"
                    >Limpiar filtros</a
                >
            </div>
        @endif
    </section>
@endsection
