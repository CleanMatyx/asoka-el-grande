<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajustes_sitio', function (Blueprint $table): void {
            $table->id();
            $table->string('telefono_alicante', 40)->nullable();
            $table->string('telefono_orihuela', 40)->nullable();
            $table->string('email_contacto', 190)->nullable();
            $table->text('direccion_albergue')->nullable();
            $table->text('horarios_visita')->nullable();
            $table->string('facebook_url', 500)->nullable();
            $table->string('instagram_url', 500)->nullable();
            $table->string('twitter_url', 500)->nullable();
            $table->string('teaming_url', 500)->nullable();
            $table->string('wishlist_amazon_url', 500)->nullable();
            $table->string('titulo_hero', 255)->nullable();
            $table->text('subtitulo_hero')->nullable();
            $table->string('etiqueta_hero', 120)->nullable();
            $table->string('texto_boton_hero', 120)->nullable();
            $table->unsignedInteger('contador_adoptados')->nullable();
            $table->unsignedInteger('contador_acogidas')->nullable();
            $table->unsignedInteger('contador_anos_cuidando')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajustes_sitio');
    }
};
