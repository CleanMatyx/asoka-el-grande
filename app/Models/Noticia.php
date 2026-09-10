<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Noticia extends Model
{
    use SoftDeletes;

    protected $table = 'noticias';

    protected $fillable = [
        'titulo',
        'slug',
        'subtitulo',
        'contenido',
        'formato_contenido',
        'contenido_markdown',
        'contenido_markdown_path',
        'imagen_principal',
        'imagen_principal_url',
        'galeria',
        'estilo_imagenes',
        'publicado',
        'fecha_publicacion',
        'publicado_en',
        'meta_titulo',
        'meta_descripcion',
    ];

    protected function casts(): array
    {
        return [
            'galeria' => 'array',
            'publicado' => 'boolean',
            'fecha_publicacion' => 'datetime',
            'publicado_en' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $noticia): void {
            $noticia->slug ??= Str::slug($noticia->titulo);
        });
    }

    public function scopePublicadas(Builder $query): Builder
    {
        return $query
            ->where('publicado', true)
            ->where(function (Builder $query): void {
                $query->whereNull('fecha_publicacion')->orWhere('fecha_publicacion', '<=', now());
            });
    }

    public function estaProgramada(): bool
    {
        return $this->publicado && $this->fecha_publicacion?->isFuture() === true;
    }

    public function urlPublica(): string
    {
        return route('noticias.mostrar', $this->slug);
    }
}
