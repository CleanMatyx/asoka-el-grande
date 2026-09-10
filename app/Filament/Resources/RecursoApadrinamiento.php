<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursoApadrinamiento\Pages\EditarApadrinamiento;
use App\Filament\Resources\RecursoApadrinamiento\Pages\ListarApadrinamientos;
use App\Models\Apadrinamiento;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecursoApadrinamiento extends Resource
{
    protected static ?string $model = Apadrinamiento::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Apadrinamientos';
    protected static ?string $navigationGroup = 'Gestión de la protectora';
    protected static ?string $modelLabel = 'apadrinamiento';
    protected static ?string $pluralModelLabel = 'apadrinamientos';

    public static function getSlug(): string
    {
        return 'apadrinamientos';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Apadrinamiento')->schema([
                TextInput::make('animal.nombre')->label('Animal apadrinado')->disabled()->dehydrated(false),
                TextInput::make('nombre_padrino')->label('Padrino o madrina')->disabled()->dehydrated(false),
                TextInput::make('email_padrino')->label('Correo electrónico')->email()->disabled()->dehydrated(false),
                TextInput::make('importe_mensual')->label('Importe mensual')->prefix('€')->disabled()->dehydrated(false),
                TextInput::make('id_suscripcion')->label('Suscripción de Stripe')->disabled()->dehydrated(false),
                Select::make('estado')->label('Estado')->options([
                    'pendiente' => 'Pendiente de pago',
                    'activo' => 'Activo',
                    'cancelado' => 'Cancelado',
                ])->required(),
            ])->columns(['default' => 1, 'md' => 2, 'xl' => 3]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_padrino')->label('Padrino o madrina')->searchable()->sortable(),
                TextColumn::make('email_padrino')->label('Correo electrónico')->searchable()->copyable(),
                TextColumn::make('animal.nombre')->label('Animal apadrinado')->searchable()->sortable(),
                TextColumn::make('importe_mensual')->label('Importe mensual')->money('EUR')->sortable(),
                TextColumn::make('estado')->label('Estado')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                    'pendiente' => 'Pendiente de pago',
                    'activo' => 'Activo',
                    default => 'Cancelado',
                })->color(fn (string $state): string => match ($state) {
                    'activo' => 'success',
                    'cancelado' => 'danger',
                    default => 'warning',
                }),
                TextColumn::make('created_at')->label('Fecha de inicio')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(['pendiente' => 'Pendiente de pago', 'activo' => 'Activo', 'cancelado' => 'Cancelado']),
            ])
            ->actions([
                Action::make('activar')->label('Activar')->icon('heroicon-o-check')->color('success')->requiresConfirmation()->visible(fn (Apadrinamiento $record): bool => $record->estado !== 'activo')->action(fn (Apadrinamiento $record) => $record->update(['estado' => 'activo'])),
                Action::make('cancelar')->label('Cancelar')->icon('heroicon-o-x-mark')->color('danger')->requiresConfirmation()->visible(fn (Apadrinamiento $record): bool => $record->estado !== 'cancelado')->action(fn (Apadrinamiento $record) => $record->update(['estado' => 'cancelado'])),
                EditAction::make()->label('Gestionar'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListarApadrinamientos::route('/'),
            'edit' => EditarApadrinamiento::route('/{record}/editar'),
        ];
    }
}
