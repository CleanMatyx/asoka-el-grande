<?php

use App\Http\Controllers\ControladorDonacion;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/stripe', [ControladorDonacion::class, 'webhookStripe'])
    ->name('webhooks.stripe');
