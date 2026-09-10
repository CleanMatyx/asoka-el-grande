@extends ('layouts.app')

@section ('title', 'Donación cancelada')

@section ('content')
    <section class="bg-asoka-50 py-16">
        <div class="container mx-auto max-w-2xl px-4">
            <div class="rounded-3xl bg-white p-8 text-center shadow-xl sm:p-12">
                <i
                    class="fas fa-circle-xmark text-5xl text-asoka-600"
                    aria-hidden="true"
                ></i>
                <h1 class="mt-5 font-display text-4xl font-bold text-asoka-900">
                    El pago no se ha completado
                </h1>
                <p class="mt-3 text-lg text-slate-600">No se ha realizado ningún cargo. Puedes volver a intentarlo cuando quieras.</p>
                <a
                    href="{{ route('donaciones.donar') }}"
                    class="mt-8 inline-flex rounded-xl bg-asoka-600 px-6 py-3 font-bold text-white hover:bg-asoka-700"
                    >Volver a donar</a
                >
            </div>
        </div>
    </section>
@endsection
