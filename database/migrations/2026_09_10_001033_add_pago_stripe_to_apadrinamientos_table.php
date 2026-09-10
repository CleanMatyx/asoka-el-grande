<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apadrinamientos', function (Blueprint $table): void {
            $table->string('id_suscripcion', 191)->nullable()->unique()->after('importe_mensual');
            $table->enum('estado', ['pendiente', 'activo', 'cancelado'])
                ->default('pendiente')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('apadrinamientos', function (Blueprint $table): void {
            $table->enum('estado', ['activo', 'cancelado'])
                ->default('activo')
                ->change();
            $table->dropUnique(['id_suscripcion']);
            $table->dropColumn('id_suscripcion');
        });
    }
};
