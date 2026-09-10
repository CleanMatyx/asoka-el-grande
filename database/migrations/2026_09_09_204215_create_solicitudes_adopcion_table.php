<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitudes_adopcion', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('animal_id')
                ->constrained('animales')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->enum('tipo', ['adopcion', 'acogida'])->index();
            $table->string('nombre_solicitante', 160);
            $table->string('email_solicitante', 190)->index();
            $table->string('telefono_solicitante', 40)->index();
            $table->string('ciudad_solicitante', 120)->nullable()->index();

            $table->enum('tipo_vivienda', [
                'piso',
                'casa_con_jardin',
                'casa_de_campo',
                'piso_compartido',
                'otro',
            ])->nullable();

            $table->boolean('tiene_otras_mascotas')->default(false);
            $table->json('cuestionario');

            $table->enum('estado', [
                'pendiente',
                'en_revision',
                'aprobada',
                'rechazada',
            ])->default('pendiente')->index();

            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['animal_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_adopcion');
    }
};
