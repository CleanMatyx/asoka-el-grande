<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $colores = [
            'hero' => '#407ca6',
            'inicio-hero-buscador' => '#0f172a',
            'buscador-animales' => '#ecf3f9',
            'texto' => '#ffffff',
            'imagen-texto' => '#f7fcfe',
            'galeria' => '#ffffff',
            'llamada-accion' => '#407ca6',
            'animales-destacados' => '#f7fcfe',
            'donacion' => '#ecf3f9',
            'contacto' => '#ffffff',
            'formas-ayudar' => '#ecf3f9',
            'estadisticas' => '#0f172a',
        ];

        DB::table('paginas')->orderBy('id')->eachById(function (object $pagina) use ($colores): void {
            $actualizaciones = [];

            foreach (['bloques', 'bloques_publicados'] as $columna) {
                if (blank($pagina->{$columna})) {
                    continue;
                }

                $bloques = json_decode($pagina->{$columna}, true);

                if (! is_array($bloques)) {
                    continue;
                }

                $actualizaciones[$columna] = json_encode(array_map(function (array $bloque) use ($colores): array {
                    $bloque['color_fondo'] ??= $colores[$bloque['tipo'] ?? ''] ?? '#ffffff';

                    return $bloque;
                }, $bloques), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            if ($actualizaciones !== []) {
                DB::table('paginas')->where('id', $pagina->id)->update($actualizaciones);
            }
        });
    }

    public function down(): void
    {
        // El color forma parte del contenido JSON y se conserva al revertir.
    }
};
