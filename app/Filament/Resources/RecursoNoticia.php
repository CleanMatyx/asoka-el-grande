<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursoNoticia\Pages\CrearNoticia;
use App\Filament\Resources\RecursoNoticia\Pages\EditarNoticia;
use App\Filament\Resources\RecursoNoticia\Pages\ListarNoticias;
use App\Models\Noticia;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RecursoNoticia extends Resource
{
    protected static ?string $model = Noticia::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Noticias';
    protected static ?string $navigationGroup = 'Contenido web';
    protected static ?string $modelLabel = 'noticia';
    protected static ?string $pluralModelLabel = 'noticias';

    public static function getSlug(): string
    {
        return 'noticias';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Noticia')->tabs([
                Tabs\Tab::make('Contenido')->schema([
                    TextInput::make('titulo')
                        ->label('Título')
                        ->required()
                        ->maxLength(180)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state))),
                    TextInput::make('slug')->label('URL')->prefix(url('/noticias').'/')->required()->maxLength(200)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true),
                    Textarea::make('subtitulo')->label('Subtítulo')->rows(3)->maxLength(300)->columnSpanFull(),
                    Select::make('formato_contenido')
                        ->label('Formato del texto')
                        ->options(['editor' => 'Editor enriquecido', 'markdown' => 'Markdown (.md)'])
                        ->default('editor')
                        ->live(),
                    RichEditor::make('contenido')
                        ->label('Contenido')
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('formato_contenido') !== 'markdown'),
                    Textarea::make('contenido_markdown')
                        ->label('Contenido Markdown')
                        ->rows(16)
                        ->helperText('Admite encabezados estándar como ## Título y también ##Título.')
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('formato_contenido') === 'markdown'),
                    FileUpload::make('contenido_markdown_path')
                        ->label('Archivo Markdown (.md)')
                        ->disk('public')
                        ->directory('noticias/markdown')
                        ->acceptedFileTypes(['text/markdown', 'text/plain', 'application/octet-stream'])
                        ->downloadable()
                        ->helperText('Opcional. Se utiliza si el campo Markdown está vacío.')
                        ->visible(fn (Get $get): bool => $get('formato_contenido') === 'markdown'),
                ])->columns(['default' => 1, 'md' => 2]),
                Tabs\Tab::make('Imágenes')->schema([
                    FileUpload::make('imagen_principal')
                        ->label('Foto principal desde el equipo')
                        ->disk('public')
                        ->directory('noticias')
                        ->visibility('public')
                        ->image()
                        ->imagePreviewHeight('220')
                        ->openable()
                        ->downloadable(),
                    TextInput::make('imagen_principal_url')->label('URL externa de foto principal')->url()->maxLength(2048),
                    Select::make('estilo_imagenes')
                        ->label('Presentación de imágenes')
                        ->options(['simple' => 'Solo foto principal', 'cuadricula' => 'Galería en cuadrícula', 'carrusel' => 'Galería en carrusel'])
                        ->default('simple')
                        ->live(),
                    FileUpload::make('galeria')
                        ->label('Galería de imágenes')
                        ->disk('public')
                        ->directory('noticias/galeria')
                        ->visibility('public')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->appendFiles()
                        ->imagePreviewHeight('150')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => in_array($get('estilo_imagenes'), ['cuadricula', 'carrusel'], true)),
                ])->columns(['default' => 1, 'md' => 2]),
                Tabs\Tab::make('Publicación')->schema([
                    Toggle::make('publicado')->label('Publicar noticia')->default(false),
                    DateTimePicker::make('fecha_publicacion')
                        ->label('Fecha y hora de publicación')
                        ->helperText('Si indicas una fecha futura, la noticia queda programada y no será visible hasta entonces.'),
                    TextInput::make('meta_titulo')->label('Título SEO')->maxLength(60),
                    Textarea::make('meta_descripcion')->label('Descripción SEO')->rows(4)->maxLength(160)->columnSpanFull(),
                ])->columns(['default' => 1, 'md' => 2]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen_principal')->label('Foto')->disk('public')->defaultImageUrl(asset('images/animal-sin-foto.png')),
                TextColumn::make('titulo')->label('Título')->searchable()->sortable()->wrap(),
                TextColumn::make('fecha_publicacion')->label('Publicación')->dateTime('d/m/Y H:i')->placeholder('Sin fecha')->sortable(),
                IconColumn::make('publicado')->label('Activa')->boolean(),
                TextColumn::make('estado_publicacion')
                    ->label('Estado')
                    ->getStateUsing(fn (Noticia $record): string => ! $record->publicado ? 'Borrador' : ($record->estaProgramada() ? 'Programada' : 'Publicada'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Publicada' => 'success',
                        'Programada' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('updated_at')->label('Última edición')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('publicado')->label('Visibilidad')->options(['1' => 'Publicadas o programadas', '0' => 'Borradores']),
                SelectFilter::make('estilo_imagenes')->label('Imágenes')->options(['simple' => 'Foto simple', 'cuadricula' => 'Cuadrícula', 'carrusel' => 'Carrusel']),
            ])
            ->actions([
                Action::make('ver_publicada')->label('Ver')->icon('heroicon-o-arrow-top-right-on-square')->url(fn (Noticia $record): string => $record->urlPublica())->openUrlInNewTab()->visible(fn (Noticia $record): bool => $record->publicado && ! $record->estaProgramada()),
                Action::make('publicar_ahora')->label('Publicar ahora')->icon('heroicon-o-globe-alt')->color('success')->visible(fn (Noticia $record): bool => ! $record->publicado || $record->estaProgramada())->requiresConfirmation()->action(fn (Noticia $record) => $record->update(['publicado' => true, 'fecha_publicacion' => now(), 'publicado_en' => now()])),
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Borrar'),
            ])
            ->defaultSort('fecha_publicacion', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListarNoticias::route('/'),
            'create' => CrearNoticia::route('/crear'),
            'edit' => EditarNoticia::route('/{record}/editar'),
        ];
    }
}
