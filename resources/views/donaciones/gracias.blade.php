@extends ('layouts.app')

@section ('title', 'Gracias por colaborar')

@section ('content')
    <section class="bg-asoka-50 py-16">
        <div class="container mx-auto max-w-2xl px-4">
            <div class="rounded-3xl bg-white p-8 text-center shadow-xl sm:p-12">
                <i
                    class="fas fa-heart text-5xl text-asoka-600"
                    aria-hidden="true"
                ></i>
                <h1 class="mt-5 font-display text-4xl font-bold text-asoka-900">
                    ¡Gracias de corazón!
                </h1>
                <p class="mt-3 text-lg text-slate-600">Tu colaboración ya está en camino y nos ayuda a seguir cuidando de ellos.</p>
                <a
                    href="{{ route('inicio') }}"
                    class="mt-8 inline-flex rounded-xl bg-asoka-600 px-6 py-3 font-bold text-white hover:bg-asoka-700"
                    >Volver al inicio</a
                >
            </div>
        </div>
    </section>
@endsection
