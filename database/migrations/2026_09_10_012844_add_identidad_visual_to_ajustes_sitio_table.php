<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ajustes_sitio', function (Blueprint $table): void {
            $table->string('logo_con_fondo', 500)->nullable()->after('wishlist_amazon_url');
            $table->string('logo_sin_fondo', 500)->nullable()->after('logo_con_fondo');
            $table->string('favicon', 500)->nullable()->after('logo_sin_fondo');
        });
    }

    public function down(): void
    {
        Schema::table('ajustes_sitio', function (Blueprint $table): void {
            $table->dropColumn(['logo_con_fondo', 'logo_sin_fondo', 'favicon']);
        });
    }
};
