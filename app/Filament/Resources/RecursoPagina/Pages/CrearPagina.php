<?php

namespace App\Filament\Resources\RecursoPagina\Pages;

use App\Filament\Resources\RecursoPagina;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CrearPagina extends CreateRecord
{
    protected static string $resource = RecursoPagina::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label('Guardar borrador');
    }
}
