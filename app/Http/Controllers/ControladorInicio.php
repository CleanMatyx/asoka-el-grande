<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AjusteSitio;
use App\Models\Pagina;
use Illuminate\Contracts\View\View;

class ControladorInicio extends Controller
{
    public function mostrar(): View
    {
        $paginaInicio = Pagina::query()
            ->publicadas()
            ->conClavePublicada('inicio')
            ->first();

        if ($paginaInicio) {
            return view('paginas.mostrar', [
                'pagina' => $paginaInicio,
                'esInicio' => true,
            ]);
        }

        $animalesUrgentes = Animal::query()
            ->whereIn('estado', ['caso_especial', 'invisible'])
            ->latest('fecha_llegada')
            ->limit(6)
            ->get();

        $ultimosIngresos = Animal::query()
            ->whereIn('estado', ['adoptable', 'en_acogida'])
            ->latest('fecha_llegada')
            ->limit(8)
            ->get();

        $ajustesSitio = AjusteSitio::actual();

        $estadisticas = [
            'adoptados_este_ano' => $ajustesSitio->contador_adoptados ?? Animal::query()->where('estado', 'adoptado')->whereYear('updated_at', now()->year)->count(),
            'en_acogida' => $ajustesSitio->contador_acogidas ?? Animal::query()->where('estado', 'en_acogida')->count(),
            'en_albergue' => Animal::query()->whereIn('estado', ['adoptable', 'caso_especial', 'santuario'])->count(),
            'anos_cuidando' => $ajustesSitio->contador_anos_cuidando ?? max(1, now()->year - 2001),
        ];

        return view('inicio', compact('animalesUrgentes', 'ultimosIngresos', 'estadisticas', 'ajustesSitio'));
    }
}
