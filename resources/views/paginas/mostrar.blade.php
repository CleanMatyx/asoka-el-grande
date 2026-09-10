@extends ('layouts.app')

@php
    $versionPagina = $pagina->versionParaMostrar($previsualizacion ?? false);
@endphp

@section ('title', $versionPagina['meta_titulo'] ?: $versionPagina['titulo'])
@section ('meta_description', $versionPagina['meta_descripcion'] ?: ($versionPagina['subtitulo'] ?: 'Información de Asoka el Grande, protectora de animales en Alicante.'))

@push ('meta')
    @php
        $descripcion = $versionPagina['meta_descripcion'] ?: ($versionPagina['subtitulo'] ?: strip_tags($versionPagina['contenido'] ?: ''));
        $schemaPagina = ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => $versionPagina['titulo'], 'description' => $descripcion, 'url' => ($esInicio ?? false) ? url('/') : url('/' . $versionPagina['clave'])];
    @endphp
    <meta property="og:type" content="website" />
    <meta
        property="og:title"
        content="{{ $versionPagina['meta_titulo'] ?: $versionPagina['titulo'] }}"
    />
    <meta property="og:description" content="{{ $descripcion }}" />
    <script type="application/ld+json">
        @json($schemaPagina, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    </script>
@endpush

@section ('content')
    @php ($bloques = $pagina->bloquesParaMostrar($previsualizacion ?? false))

    @if (count($bloques))
        @foreach ($bloques as $bloque)
            @includeIf ('paginas.modulos.' . ($bloque['tipo'] ?? ''), ['bloque' => $bloque, 'modoPrevisualizacion' => $previsualizacion ?? false])
        @endforeach
    @else
        <section class="bg-asoka-100 py-12 sm:py-16">
            <div class="container mx-auto max-w-4xl px-4 text-center">
                <h1
                    class="font-display text-4xl font-bold text-asoka-900 sm:text-5xl"
                >
                    {{ $versionPagina['titulo'] }}
                </h1>
                @if ($versionPagina['subtitulo'])
                    <p class="mx-auto mt-4 max-w-3xl text-lg leading-8 text-slate-700">{{ $versionPagina['subtitulo'] }}</p>
                @endif
            </div>
        </section>
        <article class="container mx-auto max-w-4xl px-4 py-12 sm:py-16">
            <div
                class="prose prose-lg max-w-none prose-headings:font-display prose-headings:text-asoka-900 prose-a:text-asoka-700 hover:prose-a:text-asoka-900"
            >
                {!! $versionPagina['contenido'] !!}
            </div>
        </article>
    @endif
@endsection
