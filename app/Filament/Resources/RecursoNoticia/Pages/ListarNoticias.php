<?php

namespace App\Filament\Resources\RecursoNoticia\Pages;

use App\Filament\Resources\RecursoNoticia;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListarNoticias extends ListRecords
{
    protected static string $resource = RecursoNoticia::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Crear noticia')];
    }
}
