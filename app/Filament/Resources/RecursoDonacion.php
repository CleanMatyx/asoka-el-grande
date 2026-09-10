<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursoDonacion\Pages\EditarDonacion;
use App\Filament\Resources\RecursoDonacion\Pages\ListarDonaciones;
use App\Models\Donacion;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecursoDonacion extends Resource
{
    protected static ?string $model = Donacion::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Donaciones';
    protected static ?string $navigationGroup = 'Gestión de la protectora';
    protected static ?string $modelLabel = 'donación';
    protected static ?string $pluralModelLabel = 'donaciones';

    public static function getSlug(): string
    {
        return 'donaciones';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Datos de la donación')->schema([
                TextInput::make('nombre_donante')->label('Nombre del donante')->disabled()->dehydrated(false),
                TextInput::make('email_donante')->label('Correo electrónico')->email()->disabled()->dehydrated(false),
                TextInput::make('importe')->label('Importe')->prefix('€')->disabled()->dehydrated(false),
                Select::make('metodo_pago')->label('Método de pago')->options([
                    'stripe' => 'Tarjeta · Stripe',
                    'bizum' => 'Bizum',
                    'transferencia' => 'Transferencia',
                ])->disabled()->dehydrated(false),
                TextInput::make('id_transaccion')->label('Identificador de transacción')->disabled()->dehydrated(false),
                Select::make('estado')->label('Estado de conciliación')->options([
                    'pendiente' => 'Pendiente',
                    'completada' => 'Completada',
                    'fallida' => 'Fallida',
                ])->required(),
            ])->columns(['default' => 1, 'md' => 2, 'xl' => 3]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_donante')->label('Donante')->placeholder('Anónimo')->searchable()->sortable(),
                TextColumn::make('email_donante')->label('Correo electrónico')->placeholder('—')->searchable()->copyable(),
                TextColumn::make('importe')->label('Importe')->money('EUR')->sortable(),
                TextColumn::make('metodo_pago')->label('Método')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                    'stripe' => 'Tarjeta · Stripe',
                    'bizum' => 'Bizum',
                    default => 'Transferencia',
                })->color(fn (string $state): string => match ($state) {
                    'stripe' => 'info',
                    'bizum' => 'success',
                    default => 'gray',
                }),
                IconColumn::make('recurrente')->label('Mensual')->boolean(),
                TextColumn::make('estado')->label('Estado')->badge()->formatStateUsing(fn (string $state): string => ucfirst($state))->color(fn (string $state): string => match ($state) {
                    'completada' => 'success',
                    'fallida' => 'danger',
                    default => 'warning',
                }),
                TextColumn::make('id_transaccion')->label('Transacción')->copyable()->toggleable(),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('metodo_pago')->label('Método de pago')->options(['stripe' => 'Tarjeta · Stripe', 'bizum' => 'Bizum', 'transferencia' => 'Transferencia']),
                SelectFilter::make('estado')->label('Estado')->options(['pendiente' => 'Pendiente', 'completada' => 'Completada', 'fallida' => 'Fallida']),
                Filter::make('fecha')->label('Rango de fechas')->form([
                    DatePicker::make('desde')->label('Desde'),
                    DatePicker::make('hasta')->label('Hasta'),
                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['desde'] ?? null, fn (Builder $query, string $fecha): Builder => $query->whereDate('created_at', '>=', $fecha))
                        ->when($data['hasta'] ?? null, fn (Builder $query, string $fecha): Builder => $query->whereDate('created_at', '<=', $fecha));
                }),
            ])
            ->actions([EditAction::make()->label('Conciliar')])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListarDonaciones::route('/'),
            'edit' => EditarDonacion::route('/{record}/editar'),
        ];
    }
}
