@extends ('layouts.app')

@section ('title', 'Instrucciones de donación')

@section ('content')
    <section class="bg-asoka-50 py-16">
        <div class="container mx-auto max-w-2xl px-4">
            <div class="rounded-3xl bg-white p-8 text-center shadow-xl sm:p-12">
                <i
                    class="fas fa-heart-circle-check text-5xl text-asoka-600"
                    aria-hidden="true"
                ></i>
                <h1 class="mt-5 font-display text-4xl font-bold text-asoka-900">
                    Gracias por tu ayuda
                </h1>
                <p class="mt-3 text-slate-600">Hemos reservado tu aportación. Usa este concepto para que el equipo pueda identificarla.</p>
                <p class="my-6 rounded-xl bg-asoka-50 p-4 font-mono text-xl font-bold text-asoka-900">{{ $donacion->id_transaccion }}</p>

                @if ($donacion->metodo_pago === 'bizum')
                    <h2 class="text-xl font-bold text-slate-900">
                        Donación por Bizum
                    </h2>
                    <p class="mt-2 text-slate-600">
                        Envía
                        <strong
                            >{{ number_format($donacion->importe, 2, ',', '.') }} €</strong
                        >
                        a
                        <strong>{{ config('services.bizum.titular') }}</strong>
                        @if (config('services.bizum.telefono'))
                            al número
                            <strong
                                >{{ config('services.bizum.telefono') }}</strong
                            >
                        @endif
                        e indica el concepto anterior.
                    </p>
                @else
                    <h2 class="text-xl font-bold text-slate-900">
                        Donación por transferencia
                    </h2>
                    <p class="mt-2 text-slate-600">Titular: <strong>{{ config('services.transferencia.titular') }}</strong></p>
                    <p class="mt-1 text-slate-600">IBAN: <strong>{{ config('services.transferencia.iban') ?: 'Pendiente de configurar por la protectora' }}</strong></p>
                    <p class="mt-2 text-slate-600">Importe: <strong>{{ number_format($donacion->importe, 2, ',', '.') }} €</strong>. Incluye el concepto anterior.</p>
                @endif

                <a
                    href="{{ route('inicio') }}"
                    class="mt-8 inline-flex rounded-xl bg-asoka-600 px-6 py-3 font-bold text-white hover:bg-asoka-700"
                    >Volver al inicio</a
                >
            </div>
        </div>
    </section>
@endsection
