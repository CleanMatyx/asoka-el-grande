<?php

namespace App\Filament\Resources\RecursoPagina\Pages;

use App\Filament\Resources\RecursoPagina;
use App\Models\Pagina;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

class EditarPagina extends EditRecord
{
    protected static string $resource = RecursoPagina::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('guardar_cambios')
                ->label('Guardar cambios')
                ->icon('heroicon-o-document-check')
                ->color('gray')
                ->action(function (): void {
                    $this->save(shouldRedirect: false);
                }),
            Action::make('previsualizar')
                ->label('Previsualizar')
                ->icon('heroicon-o-eye')
                ->action(function (): void {
                    $token = Str::random(48);
                    Cache::put("previsualizacion-pagina:{$token}", [
                        'pagina_id' => $this->getRecord()->getKey(),
                        'datos' => $this->form->getState(),
                    ], now()->addMinutes(30));

                    $url = Js::from(route('paginas.previsualizar-temporal', $token));
                    $this->js("window.open({$url}, '_blank', 'noopener,noreferrer')");
                }),
            Action::make('publicar_cambios')
                ->label('Publicar cambios')
                ->icon('heroicon-o-arrow-up-on-square')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Publicar los módulos guardados')
                ->modalDescription('La versión guardada del editor sustituirá la versión visible en la web.')
                ->action(function (): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    /** @var Pagina $pagina */
                    $pagina = $this->getRecord()->refresh();
                    $pagina->publicarVersion();
                }),
            Action::make('ver_pagina_publicada')
                ->label('Ver página publicada')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(function (): string {
                    /** @var Pagina $pagina */
                    $pagina = $this->getRecord();

                    return $pagina->clave === 'inicio'
                        ? route('inicio')
                        : route('paginas.mostrar', ['clave' => $pagina->clave]);
                })
                ->openUrlInNewTab()
                ->disabled(fn (): bool => ! $this->getRecord()->publicado)
                ->tooltip(fn (): string => $this->getRecord()->publicado
                    ? 'Abrir la versión pública actualmente visible.'
                    : 'Esta página está oculta y no tiene una versión pública accesible.'),
            Action::make('cambiar_publicacion')
                ->label(fn (): string => $this->getRecord()->publicado ? 'Ocultar página' : 'Publicar página')
                ->icon(fn (): string => $this->getRecord()->publicado ? 'heroicon-o-eye-slash' : 'heroicon-o-globe-alt')
                ->color(fn (): string => $this->getRecord()->publicado ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var Pagina $pagina */
                    $pagina = $this->getRecord();

                    if ($pagina->publicado) {
                        $pagina->update(['publicado' => false]);

                        return;
                    }

                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);
                    $this->getRecord()->refresh()->publicarVersion();
                }),
            DeleteAction::make()->label('Borrar'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Guardar cambios');
    }
}
