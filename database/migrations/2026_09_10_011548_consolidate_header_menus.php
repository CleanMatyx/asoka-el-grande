<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cabeceras = DB::table('menus_sitio')
            ->where('ubicacion', 'cabecera')
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        if ($cabeceras->count() <= 1) {
            return;
        }

        $secciones = $cabeceras->map(fn (object $menu): array => [
            'etiqueta' => $menu->titulo_visible ?: $menu->nombre,
            'url' => $menu->url_principal,
            'visible' => true,
            'subenlaces' => json_decode($menu->elementos ?: '[]', true) ?: [],
        ])->all();

        DB::table('menus_sitio')->insert([
            'nombre' => 'Cabecera principal',
            'titulo_visible' => null,
            'ubicacion' => 'cabecera',
            'url_principal' => null,
            'orden' => 0,
            'elementos' => json_encode($secciones, JSON_UNESCAPED_UNICODE),
            'opciones' => null,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menus_sitio')
            ->whereIn('id', $cabeceras->pluck('id'))
            ->update(['activo' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('menus_sitio')
            ->where('nombre', 'Cabecera principal')
            ->where('ubicacion', 'cabecera')
            ->delete();
    }
};
