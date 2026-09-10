<?php

namespace App\Http\Controllers;

use App\Models\Pagina;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class ControladorPagina extends Controller
{
    public function mostrar(string $clave): View
    {
        $pagina = Pagina::query()
            ->publicadas()
            ->conClavePublicada($clave)
            ->firstOrFail();

        return view('paginas.mostrar', compact('pagina'));
    }

    public function previsualizar(string $token): View
    {
        $pagina = Pagina::query()
            ->where('token_previsualizacion', $token)
            ->firstOrFail();

        return view('paginas.mostrar', [
            'pagina' => $pagina,
            'previsualizacion' => true,
        ]);
    }

    public function previsualizarTemporal(string $token): View
    {
        $previsualizacion = Cache::get("previsualizacion-pagina:{$token}");
        abort_unless(is_array($previsualizacion), 404);

        $pagina = Pagina::query()->findOrFail($previsualizacion['pagina_id']);
        $pagina->forceFill($previsualizacion['datos']);

        return view('paginas.mostrar', [
            'pagina' => $pagina,
            'previsualizacion' => true,
            'esInicio' => ($previsualizacion['datos']['clave'] ?? null) === 'inicio',
        ]);
    }
}
