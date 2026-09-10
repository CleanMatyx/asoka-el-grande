<?php

namespace App\Filament\Resources\RecursoAnimal\Pages;

use App\Filament\Resources\RecursoAnimal;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListarAnimales extends ListRecords
{
    protected static string $resource = RecursoAnimal::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Crear animal')];
    }
}
