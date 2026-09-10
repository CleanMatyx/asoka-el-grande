<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table): void {
            $table->id();
            $table->string('titulo', 180);
            $table->string('slug', 200)->unique();
            $table->string('subtitulo', 300)->nullable();
            $table->longText('contenido')->nullable();
            $table->enum('formato_contenido', ['editor', 'markdown'])->default('editor');
            $table->longText('contenido_markdown')->nullable();
            $table->string('contenido_markdown_path')->nullable();
            $table->string('imagen_principal')->nullable();
            $table->string('imagen_principal_url', 2048)->nullable();
            $table->json('galeria')->nullable();
            $table->enum('estilo_imagenes', ['simple', 'cuadricula', 'carrusel'])->default('simple');
            $table->boolean('publicado')->default(false)->index();
            $table->timestamp('fecha_publicacion')->nullable()->index();
            $table->timestamp('publicado_en')->nullable();
            $table->string('meta_titulo', 60)->nullable();
            $table->string('meta_descripcion', 160)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['publicado', 'fecha_publicacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
