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
        Schema::table('animales', function (Blueprint $table): void {
            $table->boolean('fecha_estimada')->default(false)->after('fecha_nacimiento');
            $table->json('galeria')->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animales', function (Blueprint $table): void {
            $table->dropColumn(['fecha_estimada', 'galeria']);
        });
    }
};
