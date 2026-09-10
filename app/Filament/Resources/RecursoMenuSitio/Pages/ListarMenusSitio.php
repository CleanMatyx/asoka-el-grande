<?php

namespace App\Filament\Resources\RecursoMenuSitio\Pages;

use App\Filament\Resources\RecursoMenuSitio;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListarMenusSitio extends ListRecords
{
    protected static string $resource = RecursoMenuSitio::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Crear menú')];
    }
}
