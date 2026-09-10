<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursoPagina\Pages\CrearPagina;
use App\Filament\Resources\RecursoPagina\Pages\EditarPagina;
use App\Filament\Resources\RecursoPagina\Pages\ListarPaginas;
use App\Models\Pagina;
use App\Models\Noticia;
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
                                    'formato_contenido' => $get('formato_contenido'),
                                    'contenido_markdown' => $get('contenido_markdown'),
                                    'archivo_markdown' => $get('archivo_markdown'),
                                    'imagen' => $get('imagen'),
                                    'imagen_url' => $get('imagen_url'),
                                    'posicion_imagen' => $get('posicion_imagen'),
                                    'ajuste_imagen' => $get('ajuste_imagen'),
                                    'tamano_imagen' => $get('tamano_imagen'),
                                    'url_imagen' => $get('url_imagen'),
                                    'imagenes' => $get('imagenes'),
                                    'estilo_galeria' => $get('estilo_galeria'),
                                    'texto_boton' => $get('texto_boton'),
                                    'url_boton' => $get('url_boton'),
                                    'limite' => $get('limite'),
                                    'etiqueta' => $get('etiqueta'),
                                    'fuente_animales' => $get('fuente_animales'),
                                    'tarjetas' => $get('tarjetas'),
                                    'enlaces' => $get('enlaces'),
                                    'titulo_buscador' => $get('titulo_buscador'),
                                    'campos_buscador' => $get('campos_buscador'),
                                    'color_fondo' => $get('color_fondo'),
                                    'separador_migas' => $get('separador_migas'),
                                    'tamano_migas' => $get('tamano_migas'),
                                    'incluir_paginas_base' => $get('incluir_paginas_base'),
                                    'mostrar_pagina_actual' => $get('mostrar_pagina_actual'),
                                    'campos_tarjeta' => $get('campos_tarjeta'),
                                    'fuente_noticias' => $get('fuente_noticias'),
                                    'noticias_seleccionadas' => $get('noticias_seleccionadas'),
                                    'limite_noticias' => $get('limite_noticias'),
                                    'posicion_boton' => $get('posicion_boton'),
                                    'diapositivas' => $get('diapositivas'),
                                    'modo_carrusel' => $get('modo_carrusel'),
                                    'intervalo_carrusel' => $get('intervalo_carrusel'),
                                    'mostrar_flechas_carrusel' => $get('mostrar_flechas_carrusel'),
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
                'tarjeta-informativa' => 'Tarjeta informativa',
                'noticias' => 'Listado de noticias',
                'carrusel-fotos' => 'Carrusel de fotos',
                'contenido-lateral' => 'Contenido lateral',
                'migas-pan' => 'Migas de pan',
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
            TextInput::make('titulo')
                ->label('Título')
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => $get('tipo') !== 'migas-pan'),
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
            Select::make('formato_contenido')
                ->label('Formato del contenido')
                ->options([
                    'editor' => 'Editor enriquecido',
                    'markdown' => 'Markdown (.md)',
                ])
                ->default('editor')
                ->live()
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['texto', 'imagen-texto'], true)),
            RichEditor::make('contenido')
                ->label('Texto o contenido')
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => $get('tipo') !== 'migas-pan' && (! in_array($get('tipo'), ['texto', 'imagen-texto'], true) || $get('formato_contenido') !== 'markdown')),
            Textarea::make('contenido_markdown')
                ->label('Contenido Markdown')
                ->rows(14)
                ->helperText('Puedes pegar el contenido de un archivo .md. Los encabezados #, ## y ### se respetarán en la web.')
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['texto', 'imagen-texto'], true) && $get('formato_contenido') === 'markdown'),
            FileUpload::make('archivo_markdown')
                ->label('Archivo Markdown (.md)')
                ->disk('public')
                ->directory('paginas/markdown')
                ->acceptedFileTypes(['text/markdown', 'text/plain', 'application/octet-stream'])
                ->downloadable()
                ->helperText('Opcional. Si subes un archivo, se mostrará cuando el campo Markdown esté vacío.')
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['texto', 'imagen-texto'], true) && $get('formato_contenido') === 'markdown'),
            TextInput::make('etiqueta')->label('Etiqueta superior')->maxLength(120)
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['inicio-hero-buscador', 'animales-destacados', 'formas-ayudar', 'estadisticas'], true)),
            Select::make('separador_migas')
                ->label('Separador')
                ->options([
                    'chevron' => 'Chevron ›',
                    'barra' => 'Barra /',
                    'punto' => 'Punto ·',
                ])
                ->default('chevron')
                ->visible(fn (Get $get): bool => $get('tipo') === 'migas-pan'),
            Select::make('tamano_migas')
                ->label('Tamaño del texto')
                ->options([
                    'compacto' => 'Compacto',
                    'normal' => 'Normal (recomendado)',
                    'grande' => 'Grande',
                ])
                ->default('normal')
                ->visible(fn (Get $get): bool => $get('tipo') === 'migas-pan'),
            Toggle::make('incluir_paginas_base')
                ->label('Incluir páginas base')
                ->default(true)
                ->helperText('Añade las páginas base como niveles intermedios cuando esta página herede de una de ellas.')
                ->visible(fn (Get $get): bool => $get('tipo') === 'migas-pan'),
            Toggle::make('mostrar_pagina_actual')
                ->label('Mostrar la página actual')
                ->default(true)
                ->visible(fn (Get $get): bool => $get('tipo') === 'migas-pan'),
            Repeater::make('campos_tarjeta')
                ->label('Datos de la tarjeta')
                ->helperText('Añade los datos en el orden en que deban mostrarse. Una dirección puede incluir un mapa.')
                ->reorderable()
                ->reorderableWithButtons()
                ->itemLabel(fn (array $state): string => $state['etiqueta'] ?? 'Nuevo dato')
                ->schema([
                    Select::make('tipo')
                        ->label('Tipo de dato')
                        ->options([
                            'correo' => 'Correo electrónico',
                            'telefono' => 'Teléfono',
                            'direccion' => 'Dirección',
                            'enlace' => 'Enlace web',
                            'texto' => 'Texto',
                        ])
                        ->default('texto')
                        ->required()
                        ->live(),
                    TextInput::make('etiqueta')->label('Etiqueta')->required()->maxLength(80),
                    TextInput::make('valor')->label('Valor')->required()->maxLength(500)->columnSpanFull(),
                    TextInput::make('url')
                        ->label('URL del enlace (opcional)')
                        ->helperText('Solo para el tipo Enlace web. Si se deja vacío, se usa el valor como URL.')
                        ->url()
                        ->maxLength(2048)
                        ->visible(fn (Get $get): bool => $get('tipo') === 'enlace'),
                    Toggle::make('mostrar_mapa')
                        ->label('Mostrar mapa')
                        ->default(false)
                        ->visible(fn (Get $get): bool => $get('tipo') === 'direccion'),
                    TextInput::make('url_mapa')
                        ->label('Enlace de Google Maps (opcional)')
                        ->url()
                        ->maxLength(2048)
                        ->helperText('Permite abrir una ubicación más precisa. El mapa se genera a partir de la dirección.')
                        ->visible(fn (Get $get): bool => $get('tipo') === 'direccion' && $get('mostrar_mapa')),
                    Toggle::make('visible')->label('Mostrar dato')->default(true),
                ])
                ->columns(['default' => 1, 'md' => 2])
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => $get('tipo') === 'tarjeta-informativa'),
            TextInput::make('imagen_url')->label('URL externa de imagen')->url()
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['hero', 'imagen-texto', 'noticias'], true)),
            FileUpload::make('imagen')->label('Imagen desde el equipo')->disk('public')->directory('paginas')->visibility('public')->image()->imagePreviewHeight('180')->openable()->downloadable()
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['hero', 'imagen-texto', 'noticias'], true)),
            Select::make('posicion_imagen')
                ->label('Posición de la imagen')
                ->options([
                    'arriba_izquierda' => 'Arriba a la izquierda',
                    'arriba_centro' => 'Arriba centrada',
                    'arriba_derecha' => 'Arriba a la derecha',
                    'abajo_izquierda' => 'Abajo a la izquierda',
                    'abajo_centro' => 'Abajo centrada',
                    'abajo_derecha' => 'Abajo a la derecha',
                ])
                ->default('arriba_izquierda')
                ->helperText('Las imágenes laterales permiten que el texto las envuelva. Las centradas se muestran entre el título y el texto.')
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['imagen-texto', 'noticias'], true)),
            Select::make('ajuste_imagen')
                ->label('Ajuste de la imagen')
                ->options([
                    'completa' => 'Mostrar imagen completa (recomendado)',
                    'recortar' => 'Recortar para rellenar el espacio',
                ])
                ->default('completa')
                ->helperText('La opción recomendada respeta la proporción original y evita el efecto de zoom.')
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['imagen-texto', 'noticias'], true)),
            Select::make('tamano_imagen')
                ->label('Tamaño de la imagen')
                ->options([
                    'pequeno' => 'Pequeña',
                    'mediano' => 'Mediana (recomendado)',
                    'grande' => 'Grande',
                    'completo' => 'Ancho completo',
                ])
                ->default('mediano')
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['imagen-texto', 'noticias'], true)),
            TextInput::make('url_imagen')
                ->label('Enlace al pulsar la imagen (opcional)')
                ->url()
                ->maxLength(2048)
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['imagen-texto', 'noticias'], true)),
            FileUpload::make('imagenes')->label('Imágenes de la galería')->disk('public')->directory('paginas/galerias')->visibility('public')->image()->multiple()->reorderable()->appendFiles()->imagePreviewHeight('140')->openable()->downloadable()->columnSpanFull()
                ->visible(fn (Get $get): bool => $get('tipo') === 'galeria'),
            Select::make('estilo_galeria')->label('Estilo de galería')->options(['cuadricula' => 'Cuadrícula', 'carrusel' => 'Carrusel'])->default('cuadricula')
                ->visible(fn (Get $get): bool => $get('tipo') === 'galeria'),
            TextInput::make('texto_boton')->label('Texto del botón')
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['hero', 'imagen-texto', 'llamada-accion', 'inicio-hero-buscador', 'buscador-animales', 'noticias'], true)),
            TextInput::make('url_boton')->label('URL del botón')
                ->helperText('Ruta interna: /noticias. Enlace externo: https://ejemplo.org.')
                ->visible(fn (Get $get): bool => in_array($get('tipo'), ['hero', 'imagen-texto', 'llamada-accion', 'noticias'], true)),
            Select::make('posicion_boton')
                ->label('Posición del botón')
                ->options(['izquierda' => 'Izquierda', 'centro' => 'Centro', 'derecha' => 'Derecha'])
                ->default('izquierda')
                ->visible(fn (Get $get): bool => $get('tipo') === 'noticias'),
            Select::make('fuente_noticias')
                ->label('Noticias a mostrar')
                ->options(['ultimas' => 'Últimas publicadas', 'seleccionadas' => 'Seleccionadas manualmente'])
                ->default('ultimas')
                ->live()
                ->visible(fn (Get $get): bool => $get('tipo') === 'noticias'),
            Select::make('noticias_seleccionadas')
                ->label('Seleccionar noticias')
                ->options(fn (): array => Noticia::query()->orderByDesc('created_at')->pluck('titulo', 'id')->all())
                ->multiple()
                ->searchable()
                ->preload()
                ->visible(fn (Get $get): bool => $get('tipo') === 'noticias' && $get('fuente_noticias') === 'seleccionadas'),
            TextInput::make('limite_noticias')
                ->label('Número de noticias')
                ->numeric()
                ->minValue(1)
                ->maxValue(12)
                ->default(3)
                ->visible(fn (Get $get): bool => $get('tipo') === 'noticias'),
            Select::make('modo_carrusel')
                ->label('Reproducción del carrusel')
                ->options([
                    'automatico' => 'Automática',
                    'manual' => 'Manual',
                ])
                ->default('automatico')
                ->live()
                ->visible(fn (Get $get): bool => $get('tipo') === 'carrusel-fotos'),
            TextInput::make('intervalo_carrusel')
                ->label('Segundos entre diapositivas')
                ->numeric()
                ->minValue(2)
                ->maxValue(20)
                ->default(5)
                ->helperText('Solo se aplica en reproducción automática.')
                ->visible(fn (Get $get): bool => $get('tipo') === 'carrusel-fotos' && $get('modo_carrusel') === 'automatico'),
            Toggle::make('mostrar_flechas_carrusel')
                ->label('Mostrar flechas de navegación')
                ->default(true)
                ->visible(fn (Get $get): bool => $get('tipo') === 'carrusel-fotos'),
            Repeater::make('diapositivas')
                ->label('Diapositivas')
                ->helperText('Sube una imagen o indica una URL. Cada diapositiva puede enlazar a una página del sitio o a una URL externa.')
                ->reorderable()
                ->reorderableWithButtons()
                ->itemLabel(fn (array $state): string => $state['titulo'] ?? 'Nueva diapositiva')
                ->schema([
                    FileUpload::make('imagen')
                        ->label('Imagen desde el equipo')
                        ->disk('public')
                        ->directory('paginas/carruseles')
                        ->visibility('public')
                        ->image()
                        ->imagePreviewHeight('160')
                        ->openable()
                        ->downloadable(),
                    TextInput::make('imagen_url')->label('URL externa de imagen')->url()->maxLength(2048),
                    TextInput::make('titulo')->label('Título alternativo / accesible')->maxLength(180),
                    Textarea::make('texto')->label('Texto sobre la imagen (opcional)')->rows(3)->maxLength(500),
                    TextInput::make('enlace')
                        ->label('Enlace al pulsar la diapositiva (opcional)')
                        ->helperText('Ruta interna: /donar. Enlace externo: https://ejemplo.org.')
                        ->maxLength(2048)
                        ->rule('nullable|regex:/^(?:\/[^\s]*|https?:\/\/[^\s]+)$/i'),
                    Toggle::make('visible')->label('Mostrar diapositiva')->default(true),
                ])
                ->columns(['default' => 1, 'md' => 2])
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => $get('tipo') === 'carrusel-fotos'),
            Repeater::make('enlaces')
                ->label('Enlaces destacados')
                ->helperText('Se muestran uno debajo de otro tras el texto. Puedes reordenarlos u ocultarlos sin eliminarlos.')
                ->reorderable()
                ->reorderableWithButtons()
                ->itemLabel(fn (array $state): string => $state['texto'] ?? 'Nuevo enlace')
                ->schema([
                    TextInput::make('texto')->label('Texto del enlace')->required()->maxLength(120),
                    TextInput::make('url')
                        ->label('Destino del enlace')
                        ->helperText('Para una página de esta web escribe la ruta, por ejemplo: /adopciones. Para otra web, escribe la URL completa: https://ejemplo.org.')
                        ->required()
                        ->maxLength(2048)
                        ->rule('regex:/^(?:\/[^\s]*|https?:\/\/[^\s]+)$/i'),
                    Textarea::make('descripcion')->label('Texto de apoyo (opcional)')->rows(2)->maxLength(255),
                    Toggle::make('visible')->label('Mostrar enlace')->default(true),
                ])
                ->columns(['default' => 1, 'md' => 2])
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => $get('tipo') === 'imagen-texto'),
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
                    ->getStateUsing(fn (Pagina $record): string => $record->estadoEdicion())
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Cambios sin publicar' => 'warning',
                        'Publicada' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('updated_at')->label('Última edición')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('publicado_en')
                    ->label('Última publicación')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin publicar')
                    ->sortable(),
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
