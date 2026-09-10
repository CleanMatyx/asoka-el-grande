<?php

namespace App\Filament\Resources\RecursoAnimal\Pages;

use App\Filament\Resources\RecursoAnimal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditarAnimal extends EditRecord
{
    protected static string $resource = RecursoAnimal::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->label('Borrar animal')];
    }
}
