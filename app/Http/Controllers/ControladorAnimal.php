<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ControladorAnimal extends Controller
{
    public function catalogo(Request $request): View
    {
        $filtros = $request->validate([
            'especie' => ['nullable', 'in:perro,gato,otro'],
            'sexo' => ['nullable', 'in:macho,hembra'],
            'tamano' => ['nullable', 'in:pequeno,mediano,grande,gigante'],
            'edad' => ['nullable', 'in:cachorro,joven,adulto,senior'],
            'categoria' => ['nullable', 'in:casos-especiales'],
            'buscar' => ['nullable', 'string', 'max:100'],
        ]);

        $animales = Animal::query()
            ->whereIn('estado', ['adoptable', 'en_acogida', 'caso_especial'])
            ->filtrar($filtros)
            ->when(($filtros['categoria'] ?? null) === 'casos-especiales', fn ($query) => $query->where('estado', 'caso_especial'))
            ->when($filtros['edad'] ?? null, function ($query, string $edad): void {
                match ($edad) {
                    'cachorro' => $query->where('fecha_nacimiento', '>=', now()->subYear()),
                    'joven' => $query->whereBetween('fecha_nacimiento', [now()->subYears(4), now()->subYear()]),
                    'adulto' => $query->whereBetween('fecha_nacimiento', [now()->subYears(9), now()->subYears(4)]),
                    'senior' => $query->where('fecha_nacimiento', '<=', now()->subYears(9)),
                };
            })
            ->latest('fecha_llegada')
            ->paginate(12)
            ->withQueryString();

        return view('animales.catalogo', compact('animales', 'filtros'));
    }

    public function mostrar(string $slug): View
    {
        $animal = Animal::query()->where('slug', $slug)->firstOrFail();
        $animal->increment('visualizaciones');
        $animal->refresh();

        $galeria = collect($animal->galeria ?? [])
            ->filter(fn (mixed $imagen): bool => is_string($imagen) && filled($imagen))
            ->map(fn (string $imagen): string => filter_var($imagen, FILTER_VALIDATE_URL)
                ? $imagen
                : Storage::disk('public')->url($imagen))
            ->values();

        if ($galeria->isEmpty()) {
            $galeria->push(asset('images/animal-sin-foto.png'));
        }

        return view('animales.mostrar', [
            'animal' => $animal,
            'imagenes' => $galeria->all(),
        ]);
    }
}
