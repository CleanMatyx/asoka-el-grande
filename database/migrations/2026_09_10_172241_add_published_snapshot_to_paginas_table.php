<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paginas', function (Blueprint $table): void {
            $table->json('version_publicada')->nullable()->after('bloques_publicados');
            $table->timestamp('publicado_en')->nullable()->after('version_publicada');
        });

        DB::table('paginas')
            ->where('publicado', true)
            ->orderBy('id')
            ->eachById(function (object $pagina): void {
                DB::table('paginas')->where('id', $pagina->id)->update([
                    'version_publicada' => json_encode([
                        'clave' => $pagina->clave,
                        'titulo' => $pagina->titulo,
                        'subtitulo' => $pagina->subtitulo,
                        'contenido' => $pagina->contenido,
                        'pagina_base_id' => $pagina->pagina_base_id,
                        'bloques' => json_decode($pagina->bloques_publicados ?: $pagina->bloques ?: '[]', true) ?: [],
                        'meta_titulo' => $pagina->meta_titulo,
                        'meta_descripcion' => $pagina->meta_descripcion,
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'publicado_en' => $pagina->updated_at ?? now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('paginas', function (Blueprint $table): void {
            $table->dropColumn(['version_publicada', 'publicado_en']);
        });
    }
};
