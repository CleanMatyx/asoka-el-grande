@extends ('layouts.app')

@section ('title', 'Haz una donación')

@section ('content')
    <section
        class="paw-pattern bg-white py-12 sm:py-16"
        x-data="{ importe: 20, metodo: 'stripe', recurrente: false }"
    >
        <div class="container mx-auto max-w-4xl px-4">
            <div class="mx-auto mb-10 max-w-2xl text-center">
                <p class="mb-3 font-bold uppercase tracking-[.2em] text-asoka-700">Tu ayuda transforma vidas</p>
                <h1
                    class="font-display text-4xl font-bold text-asoka-900 sm:text-5xl"
                >
                    Haz una donación
                </h1>
                <p class="mt-4 text-lg text-slate-600">Cada aportación se convierte en alimento, cuidados veterinarios y una segunda oportunidad.</p>
            </div>

            <form
                action="{{ route('donaciones.iniciar') }}"
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

                <fieldset>
                    <legend class="text-xl font-extrabold text-slate-900">
                        1. Elige tu aportación
                    </legend>
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ([10, 20, 50] as $cantidad)
                            <button
                                type="button"
                                @click="importe = {{ $cantidad }}"
                                :class="importe === {{ $cantidad }} ? 'border-asoka-600 bg-asoka-50 text-asoka-900 ring-2 ring-asoka-300' : 'border-slate-200 bg-white text-slate-700 hover:border-asoka-400'"
                                class="rounded-xl border px-4 py-4 text-lg font-extrabold transition"
                                :aria-pressed="importe === {{ $cantidad }}"
                            >
                                {{ $cantidad }} €
                            </button>
                        @endforeach
                        <label
                            class="rounded-xl border border-slate-200 px-3 py-2"
                        >
                            <span class="sr-only">Otro importe</span>
                            <input
                                x-model.number="importe"
                                name="importe"
                                type="number"
                                min="1"
                                step="0.01"
                                required
                                class="w-full bg-transparent text-center text-lg font-extrabold outline-none"
                                aria-label="Otro importe en euros"
                            />
                        </label>
                    </div>
                    <input type="hidden" name="importe" :value="importe" />
                </fieldset>

                <fieldset class="mt-8">
                    <legend class="text-xl font-extrabold text-slate-900">
                        2. ¿Cómo quieres colaborar?
                    </legend>
                    <label
                        class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl border border-asoka-200 bg-asoka-50 p-4"
                    >
                        <input
                            x-model="recurrente"
                            name="recurrente"
                            value="1"
                            type="checkbox"
                            class="mt-1 h-5 w-5 rounded border-slate-300 text-asoka-600 focus:ring-asoka-500"
                        />
                        <span
                            ><strong class="block text-slate-900"
                                >Quiero convertirla en mensual</strong
                            ><span class="text-sm text-slate-600"
                                >Una ayuda estable nos permite planificar mejor
                                sus cuidados.</span
                            ></span
                        >
                    </label>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <label
                            class="cursor-pointer rounded-xl border p-4"
                            :class="metodo === 'stripe'
                                ? 'border-asoka-600 ring-2 ring-asoka-200'
                                : 'border-slate-200'"
                        >
                            <input
                                x-model="metodo"
                                name="metodo_pago"
                                value="stripe"
                                type="radio"
                                class="sr-only"
                            />
                            <strong class="text-slate-900"
                                ><i
                                    class="fas fa-credit-card mr-2 text-asoka-600"
                                ></i
                                >Tarjeta segura</strong
                            ><span class="mt-1 block text-sm text-slate-600"
                                >Pago procesado por Stripe.</span
                            >
                        </label>
                        <label
                            class="cursor-pointer rounded-xl border p-4"
                            :class="metodo === 'bizum'
                                ? 'border-asoka-600 ring-2 ring-asoka-200'
                                : 'border-slate-200'"
                        >
                            <input
                                x-model="metodo"
                                name="metodo_pago"
                                value="bizum"
                                type="radio"
                                class="sr-only"
                            />
                            <strong class="text-slate-900"
                                ><i
                                    class="fas fa-mobile-screen-button mr-2 text-asoka-600"
                                ></i
                                >Bizum</strong
                            ><span class="mt-1 block text-sm text-slate-600"
                                >Te mostraremos el concepto único.</span
                            >
                        </label>
                        <label
                            class="cursor-pointer rounded-xl border p-4"
                            :class="metodo === 'transferencia'
                                ? 'border-asoka-600 ring-2 ring-asoka-200'
                                : 'border-slate-200'"
                        >
                            <input
                                x-model="metodo"
                                name="metodo_pago"
                                value="transferencia"
                                type="radio"
                                class="sr-only"
                            />
                            <strong class="text-slate-900"
                                ><i
                                    class="fas fa-building-columns mr-2 text-asoka-600"
                                ></i
                                >Transferencia</strong
                            ><span class="mt-1 block text-sm text-slate-600"
                                >Recibirás los datos y concepto para
                                conciliarla.</span
                            >
                        </label>
                        <label
                            class="cursor-pointer rounded-xl border p-4"
                            :class="metodo === 'teaming'
                                ? 'border-asoka-600 ring-2 ring-asoka-200'
                                : 'border-slate-200'"
                        >
                            <input
                                x-model="metodo"
                                name="metodo_pago"
                                value="teaming"
                                type="radio"
                                class="sr-only"
                            />
                            <strong class="text-slate-900"
                                ><i class="fas fa-heart mr-2 text-asoka-600"></i
                                >Teaming</strong
                            ><span class="mt-1 block text-sm text-slate-600"
                                >Colabora con 1 € al mes.</span
                            >
                        </label>
                    </div>
                </fieldset>

                <fieldset class="mt-8 grid gap-4 sm:grid-cols-2">
                    <legend class="text-xl font-extrabold text-slate-900">
                        3. Tus datos
                        <span class="text-sm font-normal text-slate-500"
                            >(opcionales para donaciones puntuales)</span
                        >
                    </legend>
                    <label class="block"
                        ><span class="mb-1 block font-bold text-slate-700"
                            >Nombre</span
                        ><input
                            name="nombre_donante"
                            value="{{ old('nombre_donante') }}"
                            autocomplete="name"
                            class="w-full rounded-lg border-slate-300 focus:border-asoka-500 focus:ring-asoka-500"
                    /></label>
                    <label class="block"
                        ><span class="mb-1 block font-bold text-slate-700"
                            >Correo electrónico</span
                        ><input
                            name="email_donante"
                            value="{{ old('email_donante') }}"
                            type="email"
                            autocomplete="email"
                            class="w-full rounded-lg border-slate-300 focus:border-asoka-500 focus:ring-asoka-500"
                    /></label>
                </fieldset>

                <button
                    type="submit"
                    class="mt-8 flex w-full items-center justify-center gap-2 rounded-xl bg-asoka-600 px-6 py-4 text-lg font-extrabold text-white transition hover:bg-asoka-700 focus:outline-none focus:ring-4 focus:ring-asoka-300"
                >
                    <i class="fas fa-lock" aria-hidden="true"></i
                    ><span
                        x-text="
                            metodo === 'teaming'
                                ? 'Continuar a Teaming'
                                : 'Continuar con la donación'
                        "
                        >Continuar con la donación</span
                    >
                </button>
                <p class="mt-4 text-center text-sm text-slate-500"><i class="fas fa-shield-heart mr-1 text-asoka-600"></i> Pago seguro. Tus datos solo se utilizan para gestionar tu aportación.</p>
            </form>
        </div>
    </section>
@endsection
