<?php

namespace App\Http\Controllers;

use App\Models\Pagina;
use Illuminate\Contracts\View\View;

class ControladorPagina extends Controller
{
    public function mostrar(string $clave): View
    {
        $pagina = Pagina::query()
            ->publicadas()
            ->where('clave', $clave)
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
}
