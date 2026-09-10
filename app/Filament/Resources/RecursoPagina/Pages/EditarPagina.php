<?php

namespace App\Filament\Resources\RecursoPagina\Pages;

use App\Filament\Resources\RecursoPagina;
use App\Models\Pagina;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditarPagina extends EditRecord
{
    protected static string $resource = RecursoPagina::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previsualizar')
                ->label('Previsualizar')
                ->icon('heroicon-o-eye')
                ->url(fn (): string => route('paginas.previsualizar', $this->getRecord()->tokenParaPrevisualizar()))
                ->openUrlInNewTab(),
            Action::make('publicar_cambios')
                ->label('Publicar cambios')
                ->icon('heroicon-o-arrow-up-on-square')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Publicar los módulos guardados')
                ->modalDescription('La versión guardada del editor sustituirá la versión visible en la web.')
                ->action(function (): void {
                    /** @var Pagina $pagina */
                    $pagina = $this->getRecord();
                    $pagina->publicarBloques();
                }),
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

                    $pagina->publicarBloques();
                }),
            DeleteAction::make()->label('Borrar'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Guardar cambios');
    }
}
