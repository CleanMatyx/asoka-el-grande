<?php

namespace App\Filament\Resources\RecursoPagina\Pages;

use App\Filament\Resources\RecursoPagina;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListarPaginas extends ListRecords
{
    protected static string $resource = RecursoPagina::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Crear página')];
    }
}
