<?php

namespace App\Filament\Resources\RecursoSolicitudAdopcion\Pages;

use App\Filament\Resources\RecursoSolicitudAdopcion;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditarSolicitudAdopcion extends EditRecord
{
    protected static string $resource = RecursoSolicitudAdopcion::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('aprobar')->label('Aprobar')->color('success')->requiresConfirmation()->action(fn () => $this->record->update(['estado' => 'aprobada'])),
            Actions\Action::make('rechazar')->label('Rechazar')->color('danger')->requiresConfirmation()->action(fn () => $this->record->update(['estado' => 'rechazada'])),
        ];
    }
}
