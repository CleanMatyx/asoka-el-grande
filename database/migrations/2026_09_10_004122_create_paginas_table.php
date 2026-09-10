<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paginas', function (Blueprint $table): void {
            $table->id();
            $table->string('clave', 120)->unique();
            $table->string('titulo', 180);
            $table->string('subtitulo', 255)->nullable();
            $table->longText('contenido')->nullable();
            $table->string('meta_titulo', 60)->nullable();
            $table->string('meta_descripcion', 160)->nullable();
            $table->boolean('publicado')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paginas');
    }
};
