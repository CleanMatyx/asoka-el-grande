<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursoSolicitudAdopcion\Pages\EditarSolicitudAdopcion;
use App\Filament\Resources\RecursoSolicitudAdopcion\Pages\ListarSolicitudesAdopcion;
use App\Models\SolicitudAdopcion;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecursoSolicitudAdopcion extends Resource
{
    protected static ?string $model = SolicitudAdopcion::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Solicitudes';
    protected static ?string $navigationGroup = 'Gestión de la protectora';
    protected static ?string $modelLabel = 'solicitud';
    protected static ?string $pluralModelLabel = 'solicitudes de adopción';

    public static function getSlug(): string
    {
        return 'solicitudes-adopcion';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Solicitud')->schema([
                TextInput::make('animal.nombre')->label('Animal')->disabled()->dehydrated(false),
                Select::make('tipo')->label('Tipo de solicitud')->options(['adopcion' => 'Adopción', 'acogida' => 'Acogida'])->disabled()->dehydrated(false),
                Select::make('estado')->label('Estado')->options(['pendiente' => 'Pendiente', 'en_revision' => 'En revisión', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada'])->required(),
            ])->columns(['default' => 1, 'md' => 3]),
            Section::make('Datos de contacto')->schema([
                TextInput::make('nombre_solicitante')->label('Nombre completo')->disabled()->dehydrated(false),
                TextInput::make('email_solicitante')->label('Correo electrónico')->disabled()->dehydrated(false),
                TextInput::make('telefono_solicitante')->label('Teléfono')->disabled()->dehydrated(false),
                TextInput::make('ciudad_solicitante')->label('Ciudad')->disabled()->dehydrated(false),
                TextInput::make('tipo_vivienda')->label('Tipo de vivienda')->disabled()->dehydrated(false),
                TextInput::make('tiene_otras_mascotas')->label('Tiene otras mascotas')->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')->disabled()->dehydrated(false),
            ])->columns(['default' => 1, 'md' => 2, 'xl' => 3]),
            Section::make('Respuestas del cuestionario')->schema([
                KeyValue::make('cuestionario')->label('Cuestionario')->keyLabel('Pregunta')->valueLabel('Respuesta')->disabled()->dehydrated(false)->columnSpanFull(),
            ]),
            Section::make('Notas internas')->schema([
                Textarea::make('notas')->label('Notas para el equipo de voluntariado')->rows(6)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nombre_solicitante')->label('Solicitante')->searchable()->sortable(),
            TextColumn::make('email_solicitante')->label('Correo electrónico')->searchable()->copyable(),
            TextColumn::make('telefono_solicitante')->label('Teléfono')->searchable()->copyable(),
            TextColumn::make('animal.nombre')->label('Animal')->searchable()->sortable(),
            TextColumn::make('tipo')->label('Tipo')->badge()->formatStateUsing(fn (string $state): string => $state === 'adopcion' ? 'Adopción' : 'Acogida')->color('info'),
            TextColumn::make('estado')->label('Estado')->badge()->formatStateUsing(fn (string $state): string => match ($state) { 'en_revision' => 'En revisión', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada', default => 'Pendiente' })->color(fn (string $state): string => match ($state) { 'pendiente' => 'warning', 'en_revision' => 'info', 'aprobada' => 'success', 'rechazada' => 'danger' }),
            TextColumn::make('created_at')->label('Recibida')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('estado')->label('Estado')->options(['pendiente' => 'Pendiente', 'en_revision' => 'En revisión', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada']),
            SelectFilter::make('tipo')->label('Tipo')->options(['adopcion' => 'Adopción', 'acogida' => 'Acogida']),
        ])->actions([
            Action::make('aprobar')->label('Aprobar')->icon('heroicon-o-check')->color('success')->requiresConfirmation()->visible(fn (SolicitudAdopcion $record): bool => $record->estado !== 'aprobada')->action(fn (SolicitudAdopcion $record) => $record->update(['estado' => 'aprobada'])),
            Action::make('rechazar')->label('Rechazar')->icon('heroicon-o-x-mark')->color('danger')->requiresConfirmation()->visible(fn (SolicitudAdopcion $record): bool => $record->estado !== 'rechazada')->action(fn (SolicitudAdopcion $record) => $record->update(['estado' => 'rechazada'])),
            EditAction::make()->label('Revisar'),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListarSolicitudesAdopcion::route('/'), 'edit' => EditarSolicitudAdopcion::route('/{record}/editar')];
    }
}
