<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursoAnimal\Pages\CrearAnimal;
use App\Filament\Resources\RecursoAnimal\Pages\EditarAnimal;
use App\Filament\Resources\RecursoAnimal\Pages\ListarAnimales;
use App\Models\Animal;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class RecursoAnimal extends Resource
{
    protected static ?string $model = Animal::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Animales';
    protected static ?string $navigationGroup = 'Gestión de la protectora';
    protected static ?string $modelLabel = 'animal';
    protected static ?string $pluralModelLabel = 'animales';

    public static function getSlug(): string
    {
        return 'animales';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Ficha del animal')->tabs([
                Tabs\Tab::make('Información básica')->schema([
                    Section::make()->schema([
                        TextInput::make('nombre')->label('Nombre')->required()->maxLength(120)->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')->label('Slug')->required()->maxLength(140)->unique(ignoreRecord: true),
                        Select::make('especie')->label('Especie')->options(['perro' => 'Perro', 'gato' => 'Gato', 'otro' => 'Otro'])->required(),
                        TextInput::make('raza')->label('Raza')->maxLength(120),
                        Select::make('sexo')->label('Sexo')->options(['macho' => 'Macho', 'hembra' => 'Hembra']),
                        DatePicker::make('fecha_nacimiento')->label('Fecha de nacimiento'),
                        Toggle::make('fecha_estimada')->label('La fecha de nacimiento es estimada'),
                        Select::make('tamano')->label('Tamaño')->options(['pequeno' => 'Pequeño', 'mediano' => 'Mediano', 'grande' => 'Grande', 'gigante' => 'Gigante']),
                        Select::make('estado')->label('Estado')->options(['adoptable' => 'En adopción', 'en_acogida' => 'En acogida', 'adoptado' => 'Adoptado', 'caso_especial' => 'Caso especial', 'invisible' => 'Invisible', 'santuario' => 'Santuario'])->required(),
                        DatePicker::make('fecha_llegada')->label('Fecha de ingreso'),
                    ])->columns(['default' => 1, 'md' => 2, 'xl' => 3]),
                ]),
                Tabs\Tab::make('Salud y estado')->schema([
                    Section::make()->schema([
                        Toggle::make('vacunado')->label('Vacunado'),
                        Toggle::make('con_chip')->label('Tiene microchip'),
                        Toggle::make('esterilizado')->label('Esterilizado'),
                        Toggle::make('necesidades_especiales')->label('Tiene necesidades especiales')->live(),
                        Textarea::make('descripcion_necesidades_especiales')->label('Descripción de necesidades especiales')->visible(fn (Get $get): bool => (bool) $get('necesidades_especiales'))->columnSpanFull(),
                    ])->columns(['default' => 1, 'md' => 2]),
                ]),
                Tabs\Tab::make('Compatibilidad y comportamiento')->schema([
                    Section::make()->description('Deja el valor sin seleccionar si todavía no se conoce.')->schema([
                        Toggle::make('compatible_perros')->label('Apto con perros'),
                        Toggle::make('compatible_gatos')->label('Apto con gatos'),
                        Toggle::make('compatible_ninos')->label('Apto con niños'),
                    ])->columns(['default' => 1, 'md' => 3]),
                ]),
                Tabs\Tab::make('Biografía y galería')->schema([
                    Section::make()->schema([
                        RichEditor::make('descripcion')->label('Biografía')->columnSpanFull(),
                        FileUpload::make('galeria')->label('Galería de fotos')->disk('public')->directory('animales')->visibility('public')->image()->multiple()->reorderable()->appendFiles()->imagePreviewHeight('160')->openable()->downloadable()->columnSpanFull(),
                    ]),
                ]),
                Tabs\Tab::make('SEO')->schema([
                    Section::make()->schema([
                        TextInput::make('meta_titulo')->label('Título SEO')->maxLength(60),
                        Textarea::make('meta_descripcion')->label('Descripción SEO')->maxLength(160)->rows(3)->columnSpanFull(),
                    ]),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('galeria')->label('Foto')->disk('public')->getStateUsing(fn (Animal $record): ?string => $record->galeria[0] ?? null),
            TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
            TextColumn::make('especie')->label('Especie')->badge()->formatStateUsing(fn (string $state): string => ucfirst($state)),
            TextColumn::make('sexo')->label('Sexo')->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : '—'),
            TextColumn::make('estado')->label('Estado')->badge()->formatStateUsing(fn (string $state): string => match ($state) { 'adoptable' => 'En adopción', 'en_acogida' => 'En acogida', 'caso_especial' => 'Caso especial', default => ucfirst($state) })->color(fn (string $state): string => match ($state) { 'adoptable' => 'success', 'en_acogida' => 'warning', 'caso_especial', 'invisible' => 'info', 'adoptado' => 'gray', default => 'gray' }),
            TextColumn::make('visualizaciones')->label('Visitas')->numeric()->sortable(),
            TextColumn::make('fecha_llegada')->label('Fecha de ingreso')->date('d/m/Y')->sortable(),
        ])->filters([
            SelectFilter::make('especie')->label('Especie')->options(['perro' => 'Perro', 'gato' => 'Gato', 'otro' => 'Otro']),
            SelectFilter::make('estado')->label('Estado')->options(['adoptable' => 'En adopción', 'en_acogida' => 'En acogida', 'adoptado' => 'Adoptado', 'caso_especial' => 'Caso especial', 'invisible' => 'Invisible', 'santuario' => 'Santuario']),
            SelectFilter::make('tamano')->label('Tamaño')->options(['pequeno' => 'Pequeño', 'mediano' => 'Mediano', 'grande' => 'Grande', 'gigante' => 'Gigante']),
            Filter::make('invisibles')->label('Solo invisibles')->toggle()->query(fn (Builder $query): Builder => $query->where('estado', 'invisible')),
            Filter::make('casos_especiales')->label('Solo casos especiales')->toggle()->query(fn (Builder $query): Builder => $query->where('estado', 'caso_especial')),
        ])->actions([
            Action::make('marcar_adoptado')->label('Marcar adoptado')->icon('heroicon-o-check-circle')->color('success')->requiresConfirmation()->visible(fn (Animal $record): bool => $record->estado !== 'adoptado')->action(fn (Animal $record) => $record->update(['estado' => 'adoptado'])),
            EditAction::make()->label('Editar'),
            DeleteAction::make()->label('Borrar'),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListarAnimales::route('/'), 'create' => CrearAnimal::route('/crear'), 'edit' => EditarAnimal::route('/{record}/editar')];
    }
}
