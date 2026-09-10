@php ($contacto = $ajustesSitio ?? AppModelsAjusteSitio::actual())
<section class="bg-white py-12 sm:py-16">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h2 class="font-display text-3xl font-bold text-asoka-900">
            {{ $bloque['titulo'] ?? 'Contacta con Asoka' }}
        </h2>
        <div class="prose mx-auto mt-4 max-w-2xl">
            {!! $bloque['contenido'] ?? '' !!}
        </div>
        <div
            class="mx-auto mt-7 max-w-xl rounded-2xl bg-asoka-50 p-6 text-left text-slate-700"
        >
            @if ($contacto->email_contacto)
                <p><strong>Correo:</strong> <a class="text-asoka-700 underline" href="mailto:{{ $contacto->email_contacto }}">{{ $contacto->email_contacto }}</a></p>
            @endif
            @if ($contacto->telefono_alicante)
                <p class="mt-2"><strong>Teléfono:</strong> {{ $contacto->telefono_alicante }}</p>
            @endif
            @if ($contacto->direccion_albergue)
                <p class="mt-2 whitespace-pre-line"><strong>Dirección:</strong> {{ $contacto->direccion_albergue }}</p>
            @endif
        </div>
    </div>
</section>
