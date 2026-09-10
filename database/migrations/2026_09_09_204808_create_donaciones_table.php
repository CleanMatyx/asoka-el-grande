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
        Schema::create('donaciones', function (Blueprint $table): void {
            $table->id();

            $table->string('nombre_donante', 160)->nullable();
            $table->string('email_donante', 190)->nullable()->index();

            $table->decimal('importe', 8, 2);

            $table->enum('metodo_pago', [
                'stripe',
                'bizum',
                'transferencia',
            ])->index();

            $table->string('id_transaccion', 191)->nullable()->unique();
            $table->boolean('recurrente')->default(false)->index();

            $table->enum('estado', [
                'pendiente',
                'completada',
                'fallida',
            ])->default('pendiente')->index();

            $table->timestamps();

            $table->index(['estado', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donaciones');
    }
};
