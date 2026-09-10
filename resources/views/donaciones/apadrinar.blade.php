@extends ('layouts.app')

@section ('title', 'Apadrina a un Asokete')

@section ('content')
    <section class="bg-asoka-50 py-12 sm:py-16">
        <div class="container mx-auto max-w-3xl px-4">
            <div class="mb-8 text-center">
                <p class="font-bold uppercase tracking-[.2em] text-asoka-700">Un vínculo que cambia vidas</p>
                <h1
                    class="mt-2 font-display text-4xl font-bold text-asoka-900 sm:text-5xl"
                >
                    Apadrina a un Asokete
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-slate-600">Tu aportación mensual ayuda especialmente a quienes necesitan cuidados continuos.</p>
            </div>

            <form
                action="{{ route('apadrinamientos.iniciar') }}"
                method="POST"
                class="rounded-3xl bg-white p-6 shadow-xl ring-1 ring-asoka-100 sm:p-10"
            >
                @csrf
                @if ($errors->any())
                    <div
                        class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"
                        role="alert"
                    >
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block sm:col-span-2"
                        ><span class="mb-1 block font-bold text-slate-700"
                            >Animal a apadrinar</span
                        ><select
                            name="animal_id"
                            required
                            class="w-full rounded-lg border-slate-300 focus:border-asoka-500 focus:ring-asoka-500"
                        >
                            <option value="">Selecciona un Asokete</option>
                            @foreach ($animales as $animal)
                                <option
                                    value="{{ $animal->id }}"
                                    @selected (old('animal_id') == $animal->id)
                                >
                                    {{ $animal->nombre }} · {{ ucfirst($animal->especie) }}
                                </option>
                            @endforeach</select
                    ></label>
                    <label class="block"
                        ><span class="mb-1 block font-bold text-slate-700"
                            >Tu nombre</span
                        ><input
                            name="nombre_padrino"
                            required
                            value="{{ old('nombre_padrino') }}"
                            autocomplete="name"
                            class="w-full rounded-lg border-slate-300 focus:border-asoka-500 focus:ring-asoka-500"
                    /></label>
                    <label class="block"
                        ><span class="mb-1 block font-bold text-slate-700"
                            >Correo electrónico</span
                        ><input
                            name="email_padrino"
                            type="email"
                            required
                            value="{{ old('email_padrino') }}"
                            autocomplete="email"
                            class="w-full rounded-lg border-slate-300 focus:border-asoka-500 focus:ring-asoka-500"
                    /></label>
                    <label class="block sm:col-span-2"
                        ><span class="mb-1 block font-bold text-slate-700"
                            >Aportación mensual (€)</span
                        ><input
                            name="importe_mensual"
                            type="number"
                            min="1"
                            step="0.01"
                            required
                            value="{{ old('importe_mensual', 10) }}"
                            class="w-full rounded-lg border-slate-300 focus:border-asoka-500 focus:ring-asoka-500"
                    /></label>
                </div>
                <button
                    type="submit"
                    class="mt-8 flex w-full items-center justify-center gap-2 rounded-xl bg-asoka-600 px-6 py-4 text-lg font-extrabold text-white transition hover:bg-asoka-700 focus:outline-none focus:ring-4 focus:ring-asoka-300"
                >
                    <i class="fas fa-lock" aria-hidden="true"></i>Continuar al
                    pago seguro
                </button>
                <p class="mt-4 text-center text-sm text-slate-500">La suscripción se gestiona de forma segura mediante Stripe y podrás cancelarla cuando lo necesites.</p>
            </form>
        </div>
    </section>
@endsection
