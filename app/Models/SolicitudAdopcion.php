<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudAdopcion extends Model
{
    protected $table = 'solicitudes_adopcion';

    protected $fillable = [
        'animal_id',
        'tipo',
        'nombre_solicitante',
        'email_solicitante',
        'telefono_solicitante',
        'ciudad_solicitante',
        'tipo_vivienda',
        'tiene_otras_mascotas',
        'cuestionario',
        'estado',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'tiene_otras_mascotas' => 'boolean',
            'cuestionario' => 'array',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
