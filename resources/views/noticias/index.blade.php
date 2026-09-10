@extends ('layouts.app')

@section ('title', 'Noticias')
@section ('meta_description', 'Noticias, actividades y actualidad de Asoka el Grande.')

@section ('content')
    <section class="bg-asoka-100 py-12 sm:py-16">
        <div class="container mx-auto max-w-6xl px-4">
            <h1
                class="font-display text-4xl font-bold text-asoka-900 sm:text-5xl"
            >
                Noticias
            </h1>
            <p class="mt-3 max-w-2xl text-lg text-slate-700">Conoce la actualidad, campañas y actividades de Asoka el Grande.</p>
        </div>
    </section>

    <section class="container mx-auto max-w-6xl px-4 py-12 sm:py-16">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($noticias as $noticia)
                @php
                    $imagen = filled($noticia->imagen_principal)
                        ? Storage::disk('public')->url($noticia->imagen_principal)
                        : ($noticia->imagen_principal_url ?: asset('images/animal-sin-foto.png'));
                @endphp
                <article
                    class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <a href="{{ $noticia->urlPublica() }}" class="block">
                        <img
                            src="{{ $imagen }}"
                            alt="{{ $noticia->titulo }}"
                            class="aspect-[16/10] w-full object-cover"
                        />
                    </a>
                    <div class="p-5">
                        @if ($noticia->fecha_publicacion)
                            <time
                                datetime="{{ $noticia->fecha_publicacion->toDateString() }}"
                                class="text-sm font-bold text-asoka-700"
                            >
                                {{ $noticia->fecha_publicacion->translatedFormat('d \d\e F \d\e Y') }}
                            </time>
                        @endif
                        <h2
                            class="mt-2 font-display text-2xl font-bold text-asoka-900"
                        >
                            <a
                                href="{{ $noticia->urlPublica() }}"
                                class="hover:text-asoka-700"
                                >{{ $noticia->titulo }}</a
                            >
                        </h2>
                        @if ($noticia->subtitulo)
                            <p class="mt-2 text-slate-700">{{ $noticia->subtitulo }}</p>
                        @endif
                        <a
                            href="{{ $noticia->urlPublica() }}"
                            class="mt-4 inline-flex font-bold text-asoka-700 underline underline-offset-4 hover:text-asoka-900"
                            >Leer noticia</a
                        >
                    </div>
                </article>
            @empty
                <p class="text-slate-700">Todavía no hay noticias publicadas.</p>
            @endforelse
        </div>
        <div class="mt-10">{{ $noticias->links() }}</div>
    </section>
@endsection
