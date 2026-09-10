<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paginas', function (Blueprint $table): void {
            $table->foreignId('pagina_base_id')
                ->nullable()
                ->after('id')
                ->constrained('paginas')
                ->nullOnDelete();
            $table->json('bloques')->nullable()->after('contenido');
            $table->json('bloques_publicados')->nullable()->after('bloques');
            $table->uuid('token_previsualizacion')->nullable()->unique()->after('bloques_publicados');
        });
    }

    public function down(): void
    {
        Schema::table('paginas', function (Blueprint $table): void {
            $table->dropForeign(['pagina_base_id']);
            $table->dropUnique(['token_previsualizacion']);
            $table->dropColumn(['pagina_base_id', 'bloques', 'bloques_publicados', 'token_previsualizacion']);
        });
    }
};
