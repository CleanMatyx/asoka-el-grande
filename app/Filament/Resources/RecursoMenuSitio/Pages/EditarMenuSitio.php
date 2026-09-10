<?php

namespace App\Filament\Resources\RecursoMenuSitio\Pages;

use App\Filament\Resources\RecursoMenuSitio;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditarMenuSitio extends EditRecord
{
    protected static string $resource = RecursoMenuSitio::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->label('Borrar')];
    }
}
