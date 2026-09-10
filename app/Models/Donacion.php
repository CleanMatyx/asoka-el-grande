<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donacion extends Model
{
    protected $table = 'donaciones';

    protected $fillable = [
        'nombre_donante',
        'email_donante',
        'importe',
        'metodo_pago',
        'id_transaccion',
        'recurrente',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'importe' => 'decimal:2',
            'recurrente' => 'boolean',
        ];
    }
}
