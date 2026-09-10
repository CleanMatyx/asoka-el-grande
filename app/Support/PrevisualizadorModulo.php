<?php

namespace App\Support;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;
use Throwable;

class PrevisualizadorModulo
{
    public static function renderizar(array $bloque): HtmlString
    {
        try {
            $tipo = $bloque['tipo'] ?? null;

            if (! $tipo || ! view()->exists("paginas.modulos.{$tipo}")) {
                return new HtmlString('<p class="text-sm text-gray-500">Selecciona un tipo de módulo para ver su previsualización.</p>');
            }

            $html = view("paginas.modulos.{$tipo}", [
                'bloque' => $bloque,
                'modoPrevisualizacion' => true,
            ])->render();

            $css = e(Vite::asset('resources/css/app.css'));
            $documento = <<<HTML
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="{$css}"><style>body{margin:0;background:#fff}.vista-previa-modulo{pointer-events:none}</style></head><body><div class="vista-previa-modulo">{$html}</div></body></html>
HTML;

            return new HtmlString('<iframe title="Vista previa del módulo" sandbox srcdoc="'.e($documento).'" style="display:block;width:100%;height:360px;border:1px solid #d1d5db;border-radius:.75rem;background:#fff"></iframe>');
        } catch (Throwable $exception) {
            report($exception);

            return new HtmlString('<p class="text-sm text-red-700">No se ha podido generar la vista previa de este módulo.</p>');
        }
    }
}
