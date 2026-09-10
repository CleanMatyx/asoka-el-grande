<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Contracts\View\View;

class ControladorNoticia extends Controller
{
    public function index(): View
    {
        $noticias = Noticia::query()
            ->publicadas()
            ->latest('fecha_publicacion')
            ->latest('id')
            ->paginate(12);

        return view('noticias.index', compact('noticias'));
    }

    public function mostrar(string $slug): View
    {
        $noticia = Noticia::query()
            ->publicadas()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('noticias.mostrar', compact('noticia'));
    }
}
