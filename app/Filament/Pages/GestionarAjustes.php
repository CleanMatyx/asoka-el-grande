<?php

namespace App\Filament\Pages;

use App\Models\AjusteSitio;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class GestionarAjustes extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Ajustes de Asoka';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $title = 'Ajustes de Asoka';
    protected static string $view = 'filament.pages.gestionar-ajustes';

    public ?array $data = [];

    public function mount(): void
    {
        $datos = AjusteSitio::actual()->attributesToArray();
        $datos['logo_con_fondo'] ??= 'asoka_logo_completo.png';
        $datos['logo_sin_fondo'] ??= 'asoka_logo_completo_sin_fondo.png';
        $datos['favicon'] ??= 'favicon.ico';

        $this->form->fill($datos);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Ajustes')->tabs([
                    Tabs\Tab::make('Contacto y albergue')->schema([
                        TextInput::make('telefono_alicante')->label('Teléfono Alicante')->tel()->maxLength(40),
                        TextInput::make('telefono_orihuela')->label('Teléfono Orihuela')->tel()->maxLength(40),
                        TextInput::make('email_contacto')->label('Correo de contacto')->email()->maxLength(190),
                        Textarea::make('direccion_albergue')->label('Dirección del albergue')->rows(3)->columnSpanFull(),
                        Textarea::make('horarios_visita')->label('Horarios de visita')->rows(3)->helperText('Indica también si hay que concertar cita.')->columnSpanFull(),
                    ])->columns(['default' => 1, 'md' => 2]),
                    Tabs\Tab::make('Redes sociales')->schema([
                        TextInput::make('facebook_url')->label('Facebook')->url()->maxLength(500),
                        TextInput::make('instagram_url')->label('Instagram')->url()->maxLength(500),
                        TextInput::make('twitter_url')->label('X / Twitter')->url()->maxLength(500),
                        TextInput::make('teaming_url')->label('Teaming')->url()->maxLength(500),
                        TextInput::make('wishlist_amazon_url')->label('Lista de deseos de Amazon')->url()->maxLength(500)->columnSpanFull(),
                    ])->columns(['default' => 1, 'md' => 2]),
                    Tabs\Tab::make('Contenidos de portada')->schema([
                        TextInput::make('etiqueta_hero')->label('Etiqueta superior')->maxLength(120),
                        TextInput::make('titulo_hero')->label('Título principal')->maxLength(255)->columnSpanFull(),
                        Textarea::make('subtitulo_hero')->label('Subtítulo')->rows(3)->columnSpanFull(),
                        TextInput::make('texto_boton_hero')->label('Texto del botón del buscador')->maxLength(120),
                        TextInput::make('contador_adoptados')->label('Adoptados (manual, opcional)')->numeric()->minValue(0)->helperText('Déjalo vacío para mostrar el cálculo automático.'),
                        TextInput::make('contador_acogidas')->label('En acogida (manual, opcional)')->numeric()->minValue(0)->helperText('Déjalo vacío para mostrar el cálculo automático.'),
                        TextInput::make('contador_anos_cuidando')->label('Años cuidando (manual, opcional)')->numeric()->minValue(0)->helperText('Déjalo vacío para mostrar el cálculo automático.'),
                    ])->columns(['default' => 1, 'md' => 2]),
                    Tabs\Tab::make('Identidad visual')->schema([
                        FileUpload::make('logo_con_fondo')
                            ->label('Logo con fondo')
                            ->helperText('Al guardar sustituye directamente public/images/asoka_logo_completo.png.')
                            ->disk('imagenes_publicas')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes(['image/png'])
                            ->getUploadedFileNameForStorageUsing(fn (): string => 'asoka_logo_completo.png')
                            ->deletable(false)
                            ->imagePreviewHeight('160')
                            ->openable()
                            ->downloadable(),
                        FileUpload::make('logo_sin_fondo')
                            ->label('Logo sin fondo')
                            ->helperText('Al guardar sustituye directamente public/images/asoka_logo_completo_sin_fondo.png.')
                            ->disk('imagenes_publicas')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes(['image/png'])
                            ->getUploadedFileNameForStorageUsing(fn (): string => 'asoka_logo_completo_sin_fondo.png')
                            ->deletable(false)
                            ->imagePreviewHeight('160')
                            ->openable()
                            ->downloadable(),
                        FileUpload::make('favicon')
                            ->label('Favicon')
                            ->helperText('Al guardar sustituye directamente public/favicon.ico.')
                            ->disk('raiz_publica')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon', 'application/octet-stream'])
                            ->getUploadedFileNameForStorageUsing(fn (): string => 'favicon.ico')
                            ->deletable(false)
                            ->openable()
                            ->downloadable(),
                    ])->columns(['default' => 1, 'md' => 2]),
                ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function guardar(): void
    {
        $datos = $this->form->getState();
        AjusteSitio::actual()->update($datos);

        Notification::make()
            ->success()
            ->title('Ajustes guardados')
            ->body('Los cambios ya están disponibles en la web pública.')
            ->send();
    }
}
