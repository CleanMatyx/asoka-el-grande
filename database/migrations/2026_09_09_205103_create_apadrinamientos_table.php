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
        Schema::create('apadrinamientos', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('animal_id')
                ->constrained('animales')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre_padrino', 160);
            $table->string('email_padrino', 190)->index();
            $table->decimal('importe_mensual', 8, 2);

            $table->enum('estado', [
                'pendiente',
                'activo',
                'cancelado',
            ])->default('pendiente')->index();

            $table->timestamps();

            $table->index(['animal_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apadrinamientos');
    }
};
