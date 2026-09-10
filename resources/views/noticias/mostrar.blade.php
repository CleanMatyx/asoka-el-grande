@extends ('layouts.app')

@php
    use Illuminate\Support\Str;

    $contenido = (string) ($noticia->contenido ?? '');

    if ($noticia->formato_contenido === 'markdown') {
        $contenido = (string) ($noticia->contenido_markdown ?? '');

        if (blank($contenido) && filled($noticia->contenido_markdown_path) && Storage::disk('public')->exists($noticia->contenido_markdown_path)) {
            $contenido = Storage::disk('public')->get($noticia->contenido_markdown_path);
        }

        $contenido = preg_replace('/^(#{1,6})(?=\S)/m', '$1 ', $contenido) ?? $contenido;
        $contenido = Str::markdown($contenido, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }

    $imagenPrincipal = filled($noticia->imagen_principal)
        ? Storage::disk('public')->url($noticia->imagen_principal)
        : ($noticia->imagen_principal_url ?: asset('images/animal-sin-foto.png'));
    $galeria = collect($noticia->galeria ?? [])->filter();
@endphp

@section ('title', $noticia->meta_titulo ?: $noticia->titulo)
@section ('meta_description', $noticia->meta_descripcion ?: ($noticia->subtitulo ?: Str::limit(strip_tags($contenido), 155)))

@push ('meta')
    <meta property="og:type" content="article" />
    <meta
        property="og:title"
        content="{{ $noticia->meta_titulo ?: $noticia->titulo }}"
    />
    <meta
        property="og:description"
        content="{{ $noticia->meta_descripcion ?: ($noticia->subtitulo ?: Str::limit(strip_tags($contenido), 155)) }}"
    />
    <meta property="og:image" content="{{ $imagenPrincipal }}" />
@endpush

@section ('content')
    <article class="pb-16">
        <header class="bg-asoka-100 py-12 sm:py-16">
            <div class="container mx-auto max-w-4xl px-4">
                <a
                    href="{{ route('noticias.index') }}"
                    class="text-sm font-bold text-asoka-700 underline underline-offset-4 hover:text-asoka-900"
                    >← Todas las noticias</a
                >
                <h1
                    class="mt-4 font-display text-4xl font-bold text-asoka-900 sm:text-5xl"
                >
                    {{ $noticia->titulo }}
                </h1>
                @if ($noticia->subtitulo)
                    <p class="mt-4 text-xl leading-8 text-slate-700">{{ $noticia->subtitulo }}</p>
                @endif
                @if ($noticia->fecha_publicacion)
                    <time
                        datetime="{{ $noticia->fecha_publicacion->toDateString() }}"
                        class="mt-5 block font-bold text-asoka-700"
                        >{{ $noticia->fecha_publicacion->translatedFormat('d \d\e F \d\e Y') }}</time
                    >
                @endif
            </div>
        </header>

        <div class="container mx-auto max-w-4xl px-4 py-10 sm:py-14">
            <img
                src="{{ $imagenPrincipal }}"
                alt="{{ $noticia->titulo }}"
                class="h-auto w-full rounded-2xl shadow-lg"
            />
            <div class="contenido-enriquecido mt-8 max-w-none">
                {!! $contenido !!}
            </div>

            @if ($noticia->estilo_imagenes === 'cuadricula' && $galeria->isNotEmpty())
                <div class="mt-10 grid gap-4 sm:grid-cols-2">
                    @foreach ($galeria as $imagen)
                        <img
                            src="{{ Storage::disk('public')->url($imagen) }}"
                            alt="Imagen de {{ $noticia->titulo }}"
                            class="h-auto w-full rounded-xl"
                        />
                    @endforeach
                </div>
            @elseif ($noticia->estilo_imagenes === 'carrusel' && $galeria->isNotEmpty())
                <div
                    x-data="{ indice: 0, total: {{ $galeria->count() }} }"
                    class="mt-10"
                >
                    <div
                        class="relative overflow-hidden rounded-2xl bg-slate-100"
                    >
                        @foreach ($galeria as $indice => $imagen)
                            <img
                                x-show="indice === {{ $indice }}"
                                x-transition.opacity
                                src="{{ Storage::disk('public')->url($imagen) }}"
                                alt="Imagen {{ $indice + 1 }} de {{ $noticia->titulo }}"
                                class="h-auto w-full"
                            />
                        @endforeach
                        <button
                            type="button"
                            @click="indice = (indice - 1 + total) % total"
                            class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 px-3 py-2 font-bold text-asoka-900"
                            aria-label="Imagen anterior"
                        >
                            ‹
                        </button>
                        <button
                            type="button"
                            @click="indice = (indice + 1) % total"
                            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 px-3 py-2 font-bold text-asoka-900"
                            aria-label="Imagen siguiente"
                        >
                            ›
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </article>
@endsection
