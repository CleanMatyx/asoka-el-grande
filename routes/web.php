<?php

use App\Http\Controllers\ControladorAnimal;
use App\Http\Controllers\ControladorDonacion;
use App\Http\Controllers\ControladorInicio;
use App\Http\Controllers\ControladorNoticia;
use App\Http\Controllers\ControladorPagina;
use App\Http\Controllers\ControladorSolicitudAdopcion;
use Illuminate\Support\Facades\Route;

Route::get('/', [ControladorInicio::class, 'mostrar'])->name('inicio');

Route::get('/animales', [ControladorAnimal::class, 'catalogo'])
    ->name('animales.catalogo');

Route::get('/animales/{slug}', [ControladorAnimal::class, 'mostrar'])
    ->name('animales.mostrar');

Route::post('/animales/{animal}/solicitudes-adopcion', [ControladorSolicitudAdopcion::class, 'almacenar'])
    ->name('solicitudes-adopcion.almacenar')
    ->middleware('throttle:10,1');

Route::get('/donar', [ControladorDonacion::class, 'donar'])->name('donaciones.donar');
Route::post('/donar', [ControladorDonacion::class, 'iniciarDonacion'])
    ->name('donaciones.iniciar')
    ->middleware('throttle:10,1');
Route::get('/donar/instrucciones/{donacion}', [ControladorDonacion::class, 'instrucciones'])
    ->name('donaciones.instrucciones')
    ->middleware('signed');
Route::get('/donar/gracias', [ControladorDonacion::class, 'gracias'])->name('donaciones.gracias');
Route::get('/donar/cancelada', [ControladorDonacion::class, 'cancelada'])->name('donaciones.cancelada');

Route::get('/apadrinar', [ControladorDonacion::class, 'apadrinar'])->name('apadrinamientos.crear');
Route::post('/apadrinar', [ControladorDonacion::class, 'iniciarApadrinamiento'])
    ->name('apadrinamientos.iniciar')
    ->middleware('throttle:10,1');

Route::get('/noticias', [ControladorNoticia::class, 'index'])->name('noticias.index');
Route::get('/noticias/{slug}', [ControladorNoticia::class, 'mostrar'])->name('noticias.mostrar');

Route::get('/previsualizar/{token}', [ControladorPagina::class, 'previsualizar'])
    ->name('paginas.previsualizar');

Route::get('/previsualizar-borrador/{token}', [ControladorPagina::class, 'previsualizarTemporal'])
    ->name('paginas.previsualizar-temporal');

Route::get('/{clave}', [ControladorPagina::class, 'mostrar'])
    ->where('clave', '^(?!admin$|animales$|donar$|apadrinar$|webhooks$|up$)[a-z0-9-]+$')
    ->name('paginas.mostrar');
