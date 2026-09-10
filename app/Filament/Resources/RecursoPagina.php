<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursoPagina\Pages\CrearPagina;
use App\Filament\Resources\RecursoPagina\Pages\EditarPagina;
use App\Filament\Resources\RecursoPagina\Pages\ListarPaginas;
use App\Models\Pagina;
use App\Support\PrevisualizadorModulo;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions\Action as AccionModulo;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
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
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class RecursoPagina extends Resource
{
    protected static ?string $model = Pagina::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Páginas';
    protected static ?string $navigationGroup = 'Contenido web';
    protected static ?string $modelLabel = 'página';
    protected static ?string $pluralModelLabel = 'páginas';

    public static function getSlug(): string
    {
        return 'paginas';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Contenido de la página')->tabs([
                Tabs\Tab::make('Configuración')->schema([
                    TextInput::make('titulo')
                        ->label('Título')
                        ->required()
                        ->maxLength(180)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('clave', Str::slug($state))),
                    TextInput::make('clave')->label('URL')->prefix(url('/').'/')->required()->maxLength(120)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true),
                    Select::make('pagina_base_id')->label('Página base')->relationship('paginaBase', 'titulo')->searchable()->preload()->helperText('Sus módulos se mostrarán antes de los módulos propios.'),
                    Placeholder::make('estado_publicacion')
                        ->label('Estado público')
                        ->content(fn (?Pagina $record): string => $record?->publicado
                            ? 'Publicada. Guardar cambios solo modifica el borrador.'
                            : 'Oculta. Puedes trabajar y previsualizar el borrador antes de publicarlo.'),
                    TextInput::make('subtitulo')->label('Subtítulo')->maxLength(255)->columnSpanFull(),
                    RichEditor::make('contenido')->label('Contenido clásico (solo si la página no tiene módulos)')->columnSpanFull(),
                ])->columns(['default' => 1, 'md' => 2]),
                Tabs\Tab::make('Módulos')->schema([
                    Repeater::make('bloques')
                        ->label('Módulos de la página')
                        ->defaultItems(0)
                        ->addActionLabel('Añadir módulo')
                        ->reorderable()
                        ->reorderableWithButtons()
                        ->itemLabel(fn (array $state): string => trim(($state['tipo'] ?? 'Nuevo módulo').' · '.($state['titulo'] ?? 'Sin título')))
                        ->schema([
                            Placeholder::make('vista_previa')
                                ->label('Vista previa en directo')
                                ->content(fn (Get $get): HtmlString => PrevisualizadorModulo::renderizar([
                                    'tipo' => $get('tipo'),
                                    'titulo' => $get('titulo'),
                                    'contenido' => $get('contenido'),
                                    'imagen' => $get('imagen'),
                                    'imagen_url' => $get('imagen_url'),
                                    'imagenes' => $get('imagenes'),
                                    'estilo_galeria' => $get('estilo_galeria'),
                                    'texto_boton' => $get('texto_boton'),
                                    'url_boton' => $get('url_boton'),
                                    'limite' => $get('limite'),
                                    'etiqueta' => $get('etiqueta'),
                                    'fuente_animales' => $get('fuente_animales'),
                                    'tarjetas' => $get('tarjetas'),
                                    'titulo_buscador' => $get('titulo_buscador'),
                                    'campos_buscador' => $get('campos_buscador'),
                                    'color_fondo' => $get('color_fondo'),
                                ]))
                                ->columnSpanFull(),
                            Toggle::make('visible')
                                ->label('Mostrar módulo en la página')
                                ->default(true)
                                ->live(),
                        ])
                        ->extraItemActions([
                            AccionModulo::make('editar_modulo')
                                ->label('Editar módulo')
                                ->icon('heroicon-m-pencil-square')
                                ->modalHeading('Editar módulo')
                                ->modalSubmitActionLabel('Aplicar cambios')
                                ->modalWidth('7xl')
                                ->fillForm(fn (array $arguments, Repeater $component): array => $component->getRawItemState($arguments['item']))
                                ->form(fn (): array => self::camposModulo())
                                ->action(function (array $data, array $arguments, Repeater $component): void {
                                    $bloques = $component->getState();
                                    $bloques[$arguments['item']] = array_replace(
                                        $bloques[$arguments['item']] ?? [],
                                        $data,
                                    );
                                    $component->state($bloques);
                                }),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                ]),
                Tabs\Tab::make('SEO')->schema([
                    TextInput::make('meta_titulo')->label('Título SEO')->maxLength(60)->helperText('Máximo recomendado: 60 caracteres.'),
                    Textarea::make('meta_descripcion')->label('Descripción SEO')->rows(4)->maxLength(160)->helperText('Máximo recomendado: 160 caracteres.')->columnSpanFull(),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    private static function camposModulo(): array
    {
        return [
            Select::make('tipo')->label('Tipo de módulo')->options([
                'hero' => 'Hero / Encabezado',
                'texto' => 'Texto enriquecido',
                'imagen-texto' => 'Imagen y texto',
                'galeria' => 'Galería de imágenes',
                'llamada-accion' => 'Llamada a la acción',
                'animales-destacados' => 'Animales destacados',
                'donacion' => 'Donación y apadrinamiento',
                'contacto' => 'Contacto',
                'inicio-hero-buscador' => 'Portada: hero y buscador',
                'buscador-animales' => 'Buscador de animales',
                'formas-ayudar' => 'Portada: formas de ayudar',
                'estadisticas' => 'Portada: cifras de impacto',
            ])->required()->live(),
            TextInput::make('titulo')->label('Título')->columnSpanFull(),
            Select::make('color_predefinido')
                ->label('Colores predefinidos')
                ->placeholder('Selecciona un color corporativo o sugerido')
                ->options(function (Get $get): array {
                    $sugerido = self::colorSugeridoModulo($get('tipo'));

                    return [
                        'Sugerido para este módulo' => [
                            $sugerido => self::etiquetaColor($sugerido, 'Color sugerido'),
                        ],
                        'Colores corporativos Asoka' => [
                            '#f7fcfe' => self::etiquetaColor('#f7fcfe', 'Asoka 50 · Azul casi blanco'),
                            '#ecf3f9' => self::etiquetaColor('#ecf3f9', 'Asoka 100 · Azul muy claro'),
                            '#cde7fe' => self::etiquetaColor('#cde7fe', 'Asoka 200 · Azul claro'),
                            '#add9ff' => self::etiquetaColor('#add9ff', 'Asoka 300 · Azul cielo'),
                            '#62c5f3' => self::etiquetaColor('#62c5f3', 'Asoka 400 · Azul corporativo claro'),
                            '#24b3fe' => self::etiquetaColor('#24b3fe', 'Asoka 500 · Azul corporativo'),
                            '#1c71fe' => self::etiquetaColor('#1c71fe', 'Asoka 600 · Azul intenso'),
                            '#0f73cd' => self::etiquetaColor('#0f73cd', 'Asoka 700 · Azul principal'),
                            '#407ca6' => self::etiquetaColor('#407ca6', 'Asoka 800 · Azul apagado'),
                            '#6c4675' => self::etiquetaColor('#6c4675', 'Asoka 900 · Morado corporativo'),
                        ],
                        'Colores neutros' => [
                            '#ffffff' => self::etiquetaColor('#ffffff', 'Blanco'),
                            '#f8fafc' => self::etiquetaColor('#f8fafc', 'Gris muy claro'),
                            '#e2e8f0' => self::etiquetaColor('#e2e8f0', 'Gris claro'),
                            '#1e293b' => self::etiquetaColor('#1e293b', 'Gris oscuro'),
                            '#0f172a' => self::etiquetaColor('#0f172a', 'Azul noche'),
                        ],
                    ];
                })
                ->allowHtml()
                ->native(false)
                ->afterStateHydrated(fn (Select $component, Get $get) => $component->state($get('color_fondo')))
                ->afterStateUpdated(fn (Set $set, ?string $state) => filled($state) ? $set('color_fondo', $state) : null)
                ->live()
                ->dehydrated(false),
            ColorPicker::make('color_fondo')
                ->label('Color de fondo')
                ->default('#ffffff')
                ->helperText('Se aplica a todo el módulo. El color predeterminado es blanco.')
                ->live(),
            RichEditor::make('contenido')->label('Texto o contenido')->columnSpanFull(),
            TextInput::make('etiqueta')->label('Etiqueta superior')->maxLength(120)
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['inicio-hero-buscador', 'animales-destacados', 'formas-ayudar', 'estadisticas'], true)),
            TextInput::make('imagen_url')->label('URL externa de imagen')->url()
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['hero', 'imagen-texto'], true)),
            FileUpload::make('imagen')->label('Imagen desde el equipo')->disk('public')->directory('paginas')->visibility('public')->image()->imagePreviewHeight('180')->openable()->downloadable()
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['hero', 'imagen-texto'], true)),
            FileUpload::make('imagenes')->label('Imágenes de la galería')->disk('public')->directory('paginas/galerias')->visibility('public')->image()->multiple()->reorderable()->appendFiles()->imagePreviewHeight('140')->openable()->downloadable()->columnSpanFull()
                ->visible(fn (Get $get): bool => $get('tipo') === 'galeria'),
            Select::make('estilo_galeria')->label('Estilo de galería')->options(['cuadricula' => 'Cuadrícula', 'carrusel' => 'Carrusel'])->default('cuadricula')
                ->visible(fn (Get $get): bool => $get('tipo') === 'galeria'),
            TextInput::make('texto_boton')->label('Texto del botón')
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['hero', 'imagen-texto', 'llamada-accion', 'inicio-hero-buscador', 'buscador-animales'], true)),
            TextInput::make('url_boton')->label('URL del botón')
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['hero', 'imagen-texto', 'llamada-accion'], true)),
            TextInput::make('titulo_buscador')->label('Título del buscador')->default('Busca a tu compañero')->maxLength(120)
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['inicio-hero-buscador', 'buscador-animales'], true)),
            Repeater::make('campos_buscador')
                ->label('Selectores del buscador')
                ->helperText('Reordena, oculta o modifica los campos y sus opciones. El valor se envía al catálogo de animales.')
                ->default(self::camposBuscadorPredeterminados())
                ->afterStateHydrated(function (Repeater $component, ?array $state): void {
                    if (blank($state)) {
                        $component->state(self::camposBuscadorPredeterminados());
                    }
                })
                ->reorderable()
                ->reorderableWithButtons()
                ->itemLabel(fn (array $state): string => $state['etiqueta'] ?? 'Nuevo selector')
                ->schema([
                    Select::make('nombre')->label('Filtro')->options([
                        'especie' => 'Especie',
                        'tamano' => 'Tamaño',
                        'sexo' => 'Sexo',
                        'edad' => 'Edad',
                        'categoria' => 'Categoría',
                        'buscar' => 'Nombre o raza (campo de texto)',
                    ])->required(),
                    TextInput::make('etiqueta')->label('Etiqueta')->required()->maxLength(80),
                    TextInput::make('texto_vacio')->label('Texto inicial')->required()->maxLength(80),
                    Toggle::make('visible')->label('Mostrar selector')->default(true),
                    Repeater::make('opciones')->label('Opciones')->reorderable()->reorderableWithButtons()->schema([
                        TextInput::make('valor')->label('Valor')->required()->maxLength(60),
                        TextInput::make('etiqueta')->label('Texto visible')->required()->maxLength(80),
                    ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
                ])
                ->columns(['default' => 1, 'md' => 2])
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['inicio-hero-buscador', 'buscador-animales'], true)),
            TextInput::make('limite')->label('Número de animales')->numeric()->minValue(1)->maxValue(12)->default(4)
                ->visible(fn (Get $get): bool => $get('tipo') === 'animales-destacados'),
            Select::make('fuente_animales')->label('Animales a mostrar')->options([
                'urgentes' => 'Casos especiales e invisibles',
                'ultimos' => 'Últimos ingresos en adopción o acogida',
            ])->default('ultimos')->visible(fn (Get $get): bool => $get('tipo') === 'animales-destacados'),
            Repeater::make('tarjetas')->label('Tarjetas de ayuda')->reorderable()->reorderableWithButtons()->schema([
                TextInput::make('titulo')->label('Título')->required(),
                Textarea::make('texto')->label('Texto')->rows(3)->required(),
                TextInput::make('texto_boton')->label('Texto del enlace')->required(),
                TextInput::make('url_boton')->label('URL del enlace')->required(),
                Select::make('estilo')->label('Estilo')->options(['claro' => 'Claro', 'destacado' => 'Destacado oscuro'])->default('claro'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull()
                ->visible(fn (Get $get): bool => $get('tipo') === 'formas-ayudar'),
        ];
    }

    private static function camposBuscadorPredeterminados(): array
    {
        return [
            ['nombre' => 'especie', 'etiqueta' => 'Especie', 'texto_vacio' => 'Todas', 'visible' => true, 'opciones' => [['valor' => 'perro', 'etiqueta' => 'Perro'], ['valor' => 'gato', 'etiqueta' => 'Gato'], ['valor' => 'otro', 'etiqueta' => 'Otro']]],
            ['nombre' => 'tamano', 'etiqueta' => 'Tamaño', 'texto_vacio' => 'Cualquiera', 'visible' => true, 'opciones' => [['valor' => 'pequeno', 'etiqueta' => 'Pequeño'], ['valor' => 'mediano', 'etiqueta' => 'Mediano'], ['valor' => 'grande', 'etiqueta' => 'Grande'], ['valor' => 'gigante', 'etiqueta' => 'Gigante']]],
            ['nombre' => 'sexo', 'etiqueta' => 'Sexo', 'texto_vacio' => 'Cualquiera', 'visible' => true, 'opciones' => [['valor' => 'macho', 'etiqueta' => 'Macho'], ['valor' => 'hembra', 'etiqueta' => 'Hembra']]],
        ];
    }

    private static function colorSugeridoModulo(?string $tipo): string
    {
        return match ($tipo) {
            'hero', 'llamada-accion' => '#407ca6',
            'inicio-hero-buscador', 'estadisticas' => '#0f172a',
            'buscador-animales', 'donacion', 'formas-ayudar' => '#ecf3f9',
            'imagen-texto', 'animales-destacados' => '#f7fcfe',
            default => '#ffffff',
        };
    }

    private static function etiquetaColor(string $color, string $etiqueta): string
    {
        return '<span style="display:inline-flex;align-items:center;gap:.55rem"><span style="display:inline-block;width:1.1rem;height:1.1rem;border:1px solid #94a3b8;border-radius:.25rem;background:'.e($color).'"></span><span>'.e($etiqueta).'</span><code style="margin-left:.25rem;color:#64748b;font-size:.75rem">'.strtoupper(e($color)).'</code></span>';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')->label('Título')->searchable()->sortable(),
                TextColumn::make('clave')->label('URL')->prefix('/')->copyable()->searchable(),
                IconColumn::make('publicado')->label('Publicada')->boolean(),
                TextColumn::make('estado_borrador')
                    ->label('Edición')
                    ->getStateUsing(fn (Pagina $record): string => $record->tieneCambiosSinPublicar() ? 'Cambios sin publicar' : 'Al día')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Cambios sin publicar' ? 'warning' : 'success'),
                TextColumn::make('updated_at')->label('Última edición')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->actions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Borrar'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListarPaginas::route('/'),
            'create' => CrearPagina::route('/crear'),
            'edit' => EditarPagina::route('/{record}/editar'),
        ];
    }
}
