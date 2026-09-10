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
        'version_publicada',
        'publicado_en',
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
            'version_publicada' => 'array',
            'publicado_en' => 'datetime',
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

    public function scopeConClavePublicada(Builder $query, string $clave): Builder
    {
        return $query->where(function (Builder $consulta) use ($clave): void {
            $consulta
                ->where('version_publicada->clave', $clave)
                ->orWhere(function (Builder $legado) use ($clave): void {
                    $legado->whereNull('version_publicada')->where('clave', $clave);
                });
        });
    }

    public function paginaBase(): BelongsTo
    {
        return $this->belongsTo(self::class, 'pagina_base_id');
    }

    public function bloquesParaMostrar(bool $previsualizacion = false): array
    {
        $version = $this->versionParaMostrar($previsualizacion);
        $propios = $version['bloques'] ?? [];
        $paginaBaseId = $version['pagina_base_id'] ?? null;
        $base = $paginaBaseId
            ? (self::query()->find($paginaBaseId)?->bloquesParaMostrar($previsualizacion) ?? [])
            : [];

        return collect([...$base, ...$propios])
            ->filter(fn (array $bloque): bool => $bloque['visible'] ?? true)
            ->values()
            ->all();
    }

    public function publicarBloques(): void
    {
        $this->publicarVersion();
    }

    public function versionBorrador(): array
    {
        return [
            'clave' => $this->clave,
            'titulo' => $this->titulo,
            'subtitulo' => $this->subtitulo,
            'contenido' => $this->contenido,
            'pagina_base_id' => $this->pagina_base_id,
            'bloques' => $this->bloques ?? [],
            'meta_titulo' => $this->meta_titulo,
            'meta_descripcion' => $this->meta_descripcion,
        ];
    }

    public function versionParaMostrar(bool $previsualizacion = false): array
    {
        if ($previsualizacion) {
            return $this->versionBorrador();
        }

        return $this->version_publicada ?? [
            ...$this->versionBorrador(),
            'bloques' => $this->bloques_publicados ?? $this->bloques ?? [],
        ];
    }

    public function publicarVersion(): void
    {
        $version = $this->versionBorrador();

        $this->forceFill([
            'bloques_publicados' => $version['bloques'],
            'version_publicada' => $version,
            'publicado' => true,
            'publicado_en' => now(),
        ])->save();
    }

    public function tieneCambiosSinPublicar(): bool
    {
        if (! $this->publicado) {
            return false;
        }

        if (! is_array($this->version_publicada)) {
            return true;
        }

        return $this->normalizarVersion($this->version_publicada) !== $this->normalizarVersion($this->versionBorrador());
    }

    public function estadoEdicion(): string
    {
        if (! $this->publicado) {
            return 'Oculta';
        }

        return $this->tieneCambiosSinPublicar() ? 'Cambios sin publicar' : 'Publicada';
    }

    public function tokenParaPrevisualizar(): string
    {
        if (blank($this->token_previsualizacion)) {
            $this->forceFill(['token_previsualizacion' => (string) Str::uuid()])->saveQuietly();
        }

        return $this->token_previsualizacion;
    }

    private function normalizarVersion(mixed $valor): mixed
    {
        if (! is_array($valor)) {
            return $valor;
        }

        if (array_is_list($valor)) {
            return array_map(fn (mixed $elemento): mixed => $this->normalizarVersion($elemento), $valor);
        }

        ksort($valor);

        foreach ($valor as $clave => $elemento) {
            $valor[$clave] = $this->normalizarVersion($elemento);
        }

        return $valor;
    }
}
