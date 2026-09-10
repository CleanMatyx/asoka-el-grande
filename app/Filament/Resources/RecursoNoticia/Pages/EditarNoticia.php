<?php

namespace App\Filament\Resources\RecursoNoticia\Pages;

use App\Filament\Resources\RecursoNoticia;
use App\Models\Noticia;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditarNoticia extends EditRecord
{
    protected static string $resource = RecursoNoticia::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ver_publicada')
                ->label('Ver noticia publicada')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => $this->getRecord()->urlPublica())
                ->openUrlInNewTab()
                ->disabled(fn (): bool => ! $this->getRecord()->publicado || $this->getRecord()->estaProgramada()),
            Action::make('publicar_ahora')
                ->label('Publicar ahora')
                ->icon('heroicon-o-globe-alt')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var Noticia $noticia */
                    $noticia = $this->getRecord();
                    $noticia->update(['publicado' => true, 'fecha_publicacion' => now(), 'publicado_en' => now()]);
                }),
            DeleteAction::make()->label('Borrar'),
        ];
    }
}
