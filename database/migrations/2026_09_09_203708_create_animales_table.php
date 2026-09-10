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
        Schema::create('animales', function (Blueprint $table): void {
            $table->id();

            $table->string('nombre', 120);
            $table->string('slug', 140)->unique();

            $table->enum('especie', ['perro', 'gato', 'otro'])->index();
            $table->string('raza', 120)->nullable()->index();
            $table->enum('sexo', ['macho', 'hembra'])->nullable()->index();

            $table->date('fecha_nacimiento')->nullable()->index();
            $table->enum('tamano', ['pequeno', 'mediano', 'grande', 'gigante'])
                ->nullable()
                ->index();

            $table->enum('estado', [
                'adoptable',
                'en_acogida',
                'adoptado',
                'caso_especial',
                'invisible',
                'santuario',
            ])->default('adoptable')->index();

            $table->boolean('vacunado')->default(false);
            $table->boolean('con_chip')->default(false);
            $table->boolean('esterilizado')->default(false);

            $table->boolean('necesidades_especiales')->default(false)->index();
            $table->text('descripcion_necesidades_especiales')->nullable();

            $table->boolean('compatible_perros')->nullable()->index();
            $table->boolean('compatible_gatos')->nullable()->index();
            $table->boolean('compatible_ninos')->nullable()->index();

            $table->longText('descripcion')->nullable();
            $table->date('fecha_llegada')->nullable()->index();

            $table->unsignedBigInteger('visualizaciones')->default(0);

            $table->string('meta_titulo', 60)->nullable();
            $table->string('meta_descripcion', 160)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['estado', 'especie', 'sexo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animales');
    }
};
