<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursoMenuSitio\Pages\CrearMenuSitio;
use App\Filament\Resources\RecursoMenuSitio\Pages\EditarMenuSitio;
use App\Filament\Resources\RecursoMenuSitio\Pages\ListarMenusSitio;
use App\Models\MenuSitio;
use App\Models\Pagina;
use App\Support\PrevisualizadorMenu;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;

class RecursoMenuSitio extends Resource
{
    protected static ?string $model = MenuSitio::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'Menús';
    protected static ?string $navigationGroup = 'Contenido web';
    protected static ?string $modelLabel = 'menú';
    protected static ?string $pluralModelLabel = 'menús';

    public static function getSlug(): string
    {
        return 'menus';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Placeholder::make('vista_previa')
                ->label('Vista previa en directo')
                ->content(fn (Get $get): HtmlString => PrevisualizadorMenu::renderizar(
                    $get('ubicacion') ?? 'cabecera',
                    $get('titulo_visible'),
                    $get('elementos') ?? [],
                    $get('opciones') ?? [],
                ))
                ->columnSpanFull(),
            TextInput::make('nombre')->label('Nombre interno')->required()->maxLength(120)->helperText('Solo se utiliza para identificar este menú en el CMS.'),
            TextInput::make('titulo_visible')->label('Título visible')->maxLength(120)->helperText('No se usa en los menús principales unificados.')->hidden(),
            Select::make('ubicacion')->label('Ubicación')->options(['cabecera' => 'Cabecera', 'pie' => 'Pie de página'])->required()->live(),
            TextInput::make('orden')->label('Posición')->numeric()->minValue(0)->default(0)->helperText('Menor número aparece antes. En el pie el orden es de izquierda a derecha.'),
            Toggle::make('activo')->label('Mostrar menú')->default(true)->live(),
            Placeholder::make('imagen_identidad_actual')
                ->label('Imagen que se está mostrando')
                ->content(function (Get $get): HtmlString {
                    $imagen = $get('opciones.imagen_identidad');
                    $imagen = is_array($imagen) ? reset($imagen) : $imagen;
                    $imagen = is_string($imagen) && $imagen !== '[]' ? $imagen : null;
                    $previsualizacion = $get('opciones.imagen_identidad_previsualizacion');
                    $url = filled($previsualizacion)
                        ? $previsualizacion
                        : (filled($imagen)
                        ? Storage::disk('public')->url($imagen)
                        : asset('images/animal-sin-foto.png'));

                    return new HtmlString('<img src="'.e($url).'" alt="Logotipo actual de Asoka el Grande" style="max-height:120px;max-width:100%;border-radius:.5rem;background:#fff;padding:.25rem;object-fit:contain">');
                })
                ->visible(fn (Get $get): bool => $get('ubicacion') === 'pie'),
            FileUpload::make('opciones.imagen_identidad')
                ->label('Imagen de la primera columna')
                ->helperText('Se muestra a la izquierda de las columnas del pie. Si la eliminas se mostrará animal-sin-foto.png.')
                ->disk('public')
                ->directory('identidad')
                ->visibility('public')
                ->image()
                ->imagePreviewHeight('120')
                ->openable()
                ->downloadable()
                ->deletable(true)
                ->live()
                ->afterStateUpdated(function ($state, Set $set): void {
                    $archivo = is_array($state) ? reset($state) : $state;
                    if (! is_object($archivo) || ! method_exists($archivo, 'temporaryUrl')) {
                        $set('opciones.imagen_identidad_previsualizacion', null);

                        return;
                    }

                    $set('opciones.imagen_identidad_previsualizacion', $archivo->temporaryUrl());
                })
                ->visible(fn (Get $get): bool => $get('ubicacion') === 'pie'),
            Hidden::make('opciones.imagen_identidad_previsualizacion')->dehydrated(false),
            Textarea::make('opciones.texto_identidad')
                ->label('Texto bajo la imagen')
                ->helperText('Descripción que se muestra debajo de la imagen en la primera columna del pie.')
                ->rows(4)
                ->afterStateHydrated(function (Textarea $component, ?string $state): void {
                    if (blank($state)) {
                        $component->state('Asociación para la defensa y protección de los animales.');
                    }
                })
                ->live(debounce: 400)
                ->visible(fn (Get $get): bool => $get('ubicacion') === 'pie'),
            Repeater::make('elementos')
                ->label(fn (Get $get): string => $get('ubicacion') === 'cabecera' ? 'Secciones del menú de cabecera' : 'Columnas del pie de página')
                ->helperText(fn (Get $get): string => $get('ubicacion') === 'cabecera'
                    ? 'Reordena las secciones para cambiar su posición en la navegación.'
                    : 'Cada elemento es una columna. El orden de esta lista se muestra de izquierda a derecha después del bloque de identidad de Asoka.')
                ->itemLabel(fn (array $state): string => filled($state['etiqueta'] ?? null)
                    ? ($state['etiqueta'])
                    : 'Nueva columna')
                ->addActionLabel(fn (Get $get): string => $get('ubicacion') === 'cabecera' ? 'Añadir sección' : 'Añadir columna')
                ->reorderable()
                ->reorderableWithButtons()
                ->live()
                ->schema([
                TextInput::make('etiqueta')->label('Texto o nombre de la sección')->required()->live(debounce: 400),
                Select::make('tipo_bloque')->label('Tipo de sección del pie')->options(['enlaces' => 'Enlaces', 'texto' => 'Texto', 'contacto' => 'Contacto'])->default('enlaces')->live()->visible(fn (Get $get): bool => $get('../../ubicacion') === 'pie'),
                Textarea::make('texto')->label('Texto de la sección')->rows(4)->visible(fn (Get $get): bool => $get('../../ubicacion') === 'pie' && $get('tipo_bloque') === 'texto')->columnSpanFull(),
                Select::make('pagina_clave')->label('Página del CMS')->options(fn (): array => Pagina::query()->publicadas()->orderBy('titulo')->pluck('titulo', 'clave')->all())->searchable()->live()->visible(fn (Get $get): bool => collect($get('subenlaces') ?? [])->filter(fn (array $subenlace): bool => filled($subenlace['etiqueta'] ?? null))->isEmpty()),
                TextInput::make('url')->label('URL interna o externa')->helperText('Tiene prioridad sobre Página del CMS.')->live(debounce: 400)->visible(fn (Get $get): bool => collect($get('subenlaces') ?? [])->filter(fn (array $subenlace): bool => filled($subenlace['etiqueta'] ?? null))->isEmpty()),
                Toggle::make('visible')->label(fn (Get $get): string => $get('../../ubicacion') === 'pie' ? 'Mostrar columna' : 'Mostrar sección')->default(true)->live(),
                Repeater::make('subenlaces')->label('Subenlaces de la sección')->helperText('En cabecera se muestran en un desplegable; en el pie se muestran debajo del título de su columna.')->reorderable()->live()->schema([
                    TextInput::make('etiqueta')->label('Texto')->required(),
                    Select::make('pagina_clave')->label('Página del CMS')->options(fn (): array => Pagina::query()->publicadas()->orderBy('titulo')->pluck('titulo', 'clave')->all())->searchable(),
                    TextInput::make('url')->label('URL interna o externa'),
                    Toggle::make('visible')->label('Mostrar subenlace')->default(true),
                ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
        ])->columns(['default' => 1, 'md' => 2]);
    }

    public static function table(Table $table): Table
    {
        return $table->groups([
            Group::make('ubicacion')
                ->label('Tipo de menú')
                ->titlePrefixedWithLabel(false)
                ->getTitleFromRecordUsing(fn (MenuSitio $record): string => $record->ubicacion === 'cabecera'
                    ? 'Menús de cabecera'
                    : 'Menús del pie de página'),
        ])->defaultGroup('ubicacion')->columns([
            TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
            TextColumn::make('titulo_visible')->label('Título visible')->placeholder('—'),
            TextColumn::make('ubicacion')->label('Ubicación')->badge()->formatStateUsing(fn (string $state): string => $state === 'cabecera' ? 'Cabecera' : 'Pie de página')->color(fn (string $state): string => $state === 'cabecera' ? 'info' : 'success'),
            TextColumn::make('orden')->label('Orden')->sortable(),
            IconColumn::make('activo')->label('Visible')->boolean(),
        ])->filters([
            SelectFilter::make('ubicacion')->label('Ubicación')->options(['cabecera' => 'Cabecera', 'pie' => 'Pie de página']),
        ])->actions([
            EditAction::make()->label('Editar'),
            DeleteAction::make()->label('Borrar'),
        ])->defaultSort('orden');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListarMenusSitio::route('/'),
            'create' => CrearMenuSitio::route('/crear'),
            'edit' => EditarMenuSitio::route('/{record}/editar'),
        ];
    }
}
