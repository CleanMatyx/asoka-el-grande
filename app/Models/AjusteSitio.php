<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AjusteSitio extends Model
{
    private const CLAVE_CACHE = 'ajustes_sitio.actual';

    protected $table = 'ajustes_sitio';

    protected $fillable = [
        'telefono_alicante',
        'telefono_orihuela',
        'email_contacto',
        'direccion_albergue',
        'horarios_visita',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'teaming_url',
        'wishlist_amazon_url',
        'logo_con_fondo',
        'logo_sin_fondo',
        'favicon',
        'titulo_hero',
        'subtitulo_hero',
        'etiqueta_hero',
        'texto_boton_hero',
        'contador_adoptados',
        'contador_acogidas',
        'contador_anos_cuidando',
    ];

    public static function actual(): self
    {
        return Cache::rememberForever(self::CLAVE_CACHE, fn (): self => self::query()->firstOrCreate([]));
    }

    protected static function booted(): void
    {
        static::saved(fn (): bool => Cache::forget(self::CLAVE_CACHE));
        static::deleted(fn (): bool => Cache::forget(self::CLAVE_CACHE));
    }
}
