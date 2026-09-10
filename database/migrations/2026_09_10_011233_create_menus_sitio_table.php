<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus_sitio', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 120);
            $table->string('titulo_visible', 120)->nullable();
            $table->enum('ubicacion', ['cabecera', 'pie'])->index();
            $table->string('url_principal', 500)->nullable();
            $table->unsignedSmallInteger('orden')->default(0)->index();
            $table->json('elementos')->nullable();
            $table->json('opciones')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();

            $table->index(['ubicacion', 'activo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus_sitio');
    }
};
