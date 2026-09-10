<section class="bg-white py-12 sm:py-16">
    <div class="container mx-auto max-w-4xl px-4">
        @if (filled($bloque['titulo'] ?? null))
            <h2 class="font-display text-3xl font-bold text-asoka-900">
                {{ $bloque['titulo'] }}
            </h2>
        @endif
        <div
            class="prose prose-lg mt-5 max-w-none prose-headings:text-asoka-900 prose-a:text-asoka-700"
        >
            {!! $bloque['contenido'] ?? '' !!}
        </div>
    </div>
</section>
