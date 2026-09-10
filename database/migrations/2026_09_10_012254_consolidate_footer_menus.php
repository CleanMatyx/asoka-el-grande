<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pies = DB::table('menus_sitio')->where('ubicacion', 'pie')->where('activo', true)->orderBy('orden')->get();

        if ($pies->count() <= 1) {
            return;
        }

        $secciones = $pies->map(fn (object $menu): array => [
            'etiqueta' => $menu->titulo_visible ?: $menu->nombre,
            'visible' => true,
            'tipo_bloque' => data_get(json_decode($menu->opciones ?: '[]', true), 'tipo_bloque', 'enlaces'),
            'texto' => data_get(json_decode($menu->opciones ?: '[]', true), 'texto'),
            'subenlaces' => json_decode($menu->elementos ?: '[]', true) ?: [],
        ])->all();

        DB::table('menus_sitio')->insert([
            'nombre' => 'Pie de página principal', 'ubicacion' => 'pie', 'orden' => 0,
            'elementos' => json_encode($secciones, JSON_UNESCAPED_UNICODE), 'activo' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('menus_sitio')->whereIn('id', $pies->pluck('id'))->delete();
    }

    public function down(): void
    {
        DB::table('menus_sitio')->where('nombre', 'Pie de página principal')->where('ubicacion', 'pie')->delete();
    }
};
