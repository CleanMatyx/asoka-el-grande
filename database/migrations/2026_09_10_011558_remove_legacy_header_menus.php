<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('menus_sitio')
            ->where('ubicacion', 'cabecera')
            ->where('nombre', '!=', 'Cabecera principal')
            ->delete();
    }

    public function down(): void
    {
        // Los registros se consolidaron en "Cabecera principal" antes de eliminarlos.
    }
};
