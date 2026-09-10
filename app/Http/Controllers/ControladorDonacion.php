<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Apadrinamiento;
use App\Models\Donacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class ControladorDonacion extends Controller
{
    public function donar(): View
    {
        return view('donaciones.donar', [
            'teamingUrl' => config('services.teaming.url'),
        ]);
    }

    public function iniciarDonacion(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre_donante' => ['nullable', 'string', 'max:160'],
            'email_donante' => ['nullable', 'email:rfc,dns', 'max:190'],
            'importe' => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'metodo_pago' => ['required', 'in:stripe,bizum,transferencia,teaming'],
            'recurrente' => ['nullable', 'boolean'],
        ]);

        if ($datos['metodo_pago'] === 'teaming') {
            return redirect()->away(config('services.teaming.url'));
        }

        $donacion = Donacion::create([
            'nombre_donante' => $datos['nombre_donante'] ?? null,
            'email_donante' => $datos['email_donante'] ?? null,
            'importe' => $datos['importe'],
            'metodo_pago' => $datos['metodo_pago'],
            'recurrente' => (bool) ($datos['recurrente'] ?? false),
            'estado' => 'pendiente',
        ]);

        if ($datos['metodo_pago'] !== 'stripe') {
            $donacion->update([
                'id_transaccion' => sprintf('%s-%d-%s', strtoupper($datos['metodo_pago']), $donacion->id, Str::upper(Str::random(6))),
            ]);

            return redirect()->to(URL::signedRoute('donaciones.instrucciones', ['donacion' => $donacion]));
        }

        return $this->crearCheckoutDonacion($donacion);
    }

    public function apadrinar(): View
    {
        return view('donaciones.apadrinar', [
            'animales' => Animal::query()
                ->whereIn('estado', ['adoptable', 'en_acogida', 'caso_especial', 'santuario'])
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'especie']),
        ]);
    }

    public function iniciarApadrinamiento(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'animal_id' => ['required', 'exists:animales,id'],
            'nombre_padrino' => ['required', 'string', 'max:160'],
            'email_padrino' => ['required', 'email:rfc,dns', 'max:190'],
            'importe_mensual' => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ]);

        $apadrinamiento = Apadrinamiento::create([
            ...$datos,
            'estado' => 'pendiente',
        ]);

        if (blank(config('services.stripe.secret'))) {
            $apadrinamiento->delete();

            return back()->withInput()->withErrors([
                'pago' => 'El pago online no está configurado todavía. Contacta con la protectora para apadrinar.',
            ]);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $animal = Animal::findOrFail($apadrinamiento->animal_id);

        try {
            $sesion = $stripe->checkout->sessions->create([
                'mode' => 'subscription',
                'success_url' => route('donaciones.gracias').'?sesion={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('donaciones.cancelada'),
                'customer_email' => $apadrinamiento->email_padrino,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => (int) round($apadrinamiento->importe_mensual * 100),
                        'recurring' => ['interval' => 'month'],
                        'product_data' => ['name' => "Apadrinamiento mensual de {$animal->nombre}"],
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'tipo' => 'apadrinamiento',
                    'apadrinamiento_id' => (string) $apadrinamiento->id,
                ],
                'subscription_data' => [
                    'metadata' => [
                        'tipo' => 'apadrinamiento',
                        'apadrinamiento_id' => (string) $apadrinamiento->id,
                    ],
                ],
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            $apadrinamiento->delete();

            return back()->withInput()->withErrors(['pago' => 'No se pudo iniciar el pago. Inténtalo de nuevo.']);
        }

        return redirect()->away($sesion->url);
    }

    public function instrucciones(Request $request, Donacion $donacion): View
    {
        return view('donaciones.instrucciones', compact('donacion'));
    }

    public function gracias(): View
    {
        return view('donaciones.gracias');
    }

    public function cancelada(): View
    {
        return view('donaciones.cancelada');
    }

    public function webhookStripe(Request $request): Response
    {
        $secreto = config('services.stripe.webhook_secret');

        if (blank($secreto)) {
            Log::warning('Webhook de Stripe recibido sin STRIPE_WEBHOOK_SECRET configurado.');

            return response('Webhook no configurado.', Response::HTTP_SERVICE_UNAVAILABLE);
        }

        try {
            $evento = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                $secreto,
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            return response('Firma inválida.', Response::HTTP_BAD_REQUEST);
        }

        $objeto = $evento->data->object;

        match ($evento->type) {
            'checkout.session.completed' => $this->procesarCheckoutCompletado($objeto),
            'invoice.payment_succeeded' => $this->procesarFacturaCobrada($objeto),
            'charge.failed', 'invoice.payment_failed' => $this->procesarPagoFallido($objeto),
            default => null,
        };

        return response('ok');
    }

    private function crearCheckoutDonacion(Donacion $donacion): RedirectResponse
    {
        if (blank(config('services.stripe.secret'))) {
            $donacion->delete();

            return back()->withInput()->withErrors(['pago' => 'El pago con tarjeta no está configurado todavía.']);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $recurrente = $donacion->recurrente;

        try {
            $parametros = [
                'mode' => $recurrente ? 'subscription' : 'payment',
                'success_url' => route('donaciones.gracias').'?sesion={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('donaciones.cancelada'),
                'customer_email' => $donacion->email_donante,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => (int) round($donacion->importe * 100),
                        'product_data' => ['name' => $recurrente ? 'Donación mensual a Asoka el Grande' : 'Donación a Asoka el Grande'],
                        ...($recurrente ? ['recurring' => ['interval' => 'month']] : []),
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'tipo' => 'donacion',
                    'donacion_id' => (string) $donacion->id,
                ],
            ];

            if ($recurrente) {
                $parametros['subscription_data'] = ['metadata' => $parametros['metadata']];
            } else {
                $parametros['payment_intent_data'] = ['metadata' => $parametros['metadata']];
            }

            $sesion = $stripe->checkout->sessions->create($parametros);
            $donacion->update(['id_transaccion' => $sesion->id]);
        } catch (\Throwable $exception) {
            report($exception);
            $donacion->update(['estado' => 'fallida']);

            return back()->withInput()->withErrors(['pago' => 'No se pudo iniciar el pago. Inténtalo de nuevo.']);
        }

        return redirect()->away($sesion->url);
    }

    private function procesarCheckoutCompletado(object $sesion): void
    {
        $metadata = $sesion->metadata ?? [];

        if (($metadata->tipo ?? null) === 'donacion' && filled($metadata->donacion_id ?? null)) {
            Donacion::whereKey($metadata->donacion_id)->update([
                'estado' => 'completada',
                'id_transaccion' => $sesion->payment_intent ?? $sesion->subscription ?? $sesion->id,
            ]);
        }

        if (($metadata->tipo ?? null) === 'apadrinamiento' && filled($metadata->apadrinamiento_id ?? null)) {
            Apadrinamiento::whereKey($metadata->apadrinamiento_id)->update([
                'estado' => 'activo',
                'id_suscripcion' => $sesion->subscription,
            ]);
        }
    }

    private function procesarFacturaCobrada(object $factura): void
    {
        if (blank($factura->subscription ?? null) || blank(config('services.stripe.secret'))) {
            return;
        }

        $suscripcion = (new StripeClient(config('services.stripe.secret')))->subscriptions->retrieve($factura->subscription);
        $metadata = $suscripcion->metadata ?? [];

        if (($metadata->tipo ?? null) === 'donacion') {
            Donacion::whereKey($metadata->donacion_id ?? null)->update(['estado' => 'completada']);
        }

        if (($metadata->tipo ?? null) === 'apadrinamiento') {
            Apadrinamiento::whereKey($metadata->apadrinamiento_id ?? null)->update([
                'estado' => 'activo',
                'id_suscripcion' => $suscripcion->id,
            ]);
        }
    }

    private function procesarPagoFallido(object $objeto): void
    {
        $metadata = $objeto->metadata ?? [];

        if (($metadata->tipo ?? null) === 'donacion') {
            Donacion::whereKey($metadata->donacion_id ?? null)->update(['estado' => 'fallida']);
        }
    }
}
