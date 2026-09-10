<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MenuSitio extends Model
{
    protected $table = 'menus_sitio';

    protected $fillable = [
        'nombre',
        'titulo_visible',
        'ubicacion',
        'url_principal',
        'orden',
        'elementos',
        'opciones',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'elementos' => 'array',
            'opciones' => 'array',
            'activo' => 'boolean',
        ];
    }

    public function scopeEnUbicacion(Builder $query, string $ubicacion): Builder
    {
        return $query->where('ubicacion', $ubicacion)->where('activo', true)->orderBy('orden');
    }
}
