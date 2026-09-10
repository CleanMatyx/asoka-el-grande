<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pagina extends Model
{
    protected $table = 'paginas';

    protected $fillable = [
        'clave',
        'titulo',
        'subtitulo',
        'contenido',
        'pagina_base_id',
        'bloques',
        'bloques_publicados',
        'token_previsualizacion',
        'meta_titulo',
        'meta_descripcion',
        'publicado',
    ];

    protected function casts(): array
    {
        return [
            'publicado' => 'boolean',
            'bloques' => 'array',
            'bloques_publicados' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $pagina) => $pagina->token_previsualizacion ??= (string) Str::uuid());
    }

    public function scopePublicadas(Builder $query): Builder
    {
        return $query->where('publicado', true);
    }

    public function paginaBase(): BelongsTo
    {
        return $this->belongsTo(self::class, 'pagina_base_id');
    }

    public function bloquesParaMostrar(bool $previsualizacion = false): array
    {
        $propiedad = $previsualizacion ? 'bloques' : 'bloques_publicados';
        $propios = $this->{$propiedad} ?? ($this->bloques ?? []);
        $base = $this->paginaBase?->bloquesParaMostrar($previsualizacion) ?? [];

        return collect([...$base, ...$propios])
            ->filter(fn (array $bloque): bool => $bloque['visible'] ?? true)
            ->values()
            ->all();
    }

    public function publicarBloques(): void
    {
        $this->forceFill([
            'bloques_publicados' => $this->bloques ?? [],
            'publicado' => true,
        ])->save();
    }

    public function tokenParaPrevisualizar(): string
    {
        if (blank($this->token_previsualizacion)) {
            $this->forceFill(['token_previsualizacion' => (string) Str::uuid()])->saveQuietly();
        }

        return $this->token_previsualizacion;
    }
}
