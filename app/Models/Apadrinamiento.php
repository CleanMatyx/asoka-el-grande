<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apadrinamiento extends Model
{
    protected $table = 'apadrinamientos';

    protected $fillable = [
        'animal_id',
        'nombre_padrino',
        'email_padrino',
        'importe_mensual',
        'id_suscripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'importe_mensual' => 'decimal:2',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
