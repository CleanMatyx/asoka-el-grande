<?php

namespace App\Support;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;

class PrevisualizadorMenu
{
    public static function renderizar(string $ubicacion, ?string $titulo, array $elementos, array $opciones = []): HtmlString
    {
        return $ubicacion === 'cabecera'
            ? self::cabecera($elementos)
            : self::pie($titulo, $elementos, $opciones);
    }

    private static function cabecera(array $secciones): HtmlString
    {
        $navegacion = collect($secciones)
            ->filter(fn (array $seccion): bool => ($seccion['visible'] ?? true) && filled($seccion['etiqueta'] ?? null))
            ->map(function (array $seccion): string {
                $subenlaces = collect($seccion['subenlaces'] ?? [])
                    ->filter(fn (array $enlace): bool => ($enlace['visible'] ?? true) && filled($enlace['etiqueta'] ?? null))
                    ->map(fn (array $enlace): string => '<a href="#" onclick="return false" class="block px-4 py-2 text-sm font-semibold text-asoka-700 transition-colors hover:bg-white hover:text-asoka-400">'.e($enlace['etiqueta']).'</a>')
                    ->implode('');

                if (! $subenlaces) {
                    return '<a href="#" onclick="return false" class="rounded px-5 py-4 text-base font-bold text-asoka-400 transition-colors hover:bg-asoka-50 hover:text-asoka-900">'.e($seccion['etiqueta']).'</a>';
                }

                return '<details class="relative"><summary class="cursor-pointer list-none rounded px-5 py-4 text-base font-bold text-asoka-400 transition-colors hover:bg-asoka-50 hover:text-asoka-900">'.e($seccion['etiqueta']).'<i class="fas fa-chevron-down ml-1 text-[10px]" aria-hidden="true"></i></summary><div class="absolute left-0 top-full z-10 min-w-60 border border-asoka-300 bg-asoka-100 py-2 shadow-xl">'.$subenlaces.'</div></details>';
            })
            ->implode('');

        $html = <<<HTML
<div class="asoka-franja border-b border-asoka-300 text-xs text-asoka-800"><div class="container mx-auto flex min-h-10 items-center justify-end gap-4 px-4"><span><i class="fab fa-facebook-f"></i></span><span><i class="fab fa-twitter"></i></span><span><i class="fas fa-heart mr-1"></i>Teaming</span><span><i class="fab fa-instagram"></i></span><span><i class="fas fa-envelope mr-1"></i>Contactar</span></div></div>
<header class="relative z-30 bg-white/95 shadow-sm"><div class="container relative mx-auto flex min-h-[90px] items-center justify-end px-4"><strong class="mr-auto text-lg text-asoka-700">Vista previa de cabecera</strong><nav class="ml-auto flex origin-top-right items-center" aria-label="Vista previa de navegación">{$navegacion}</nav></div></header>
HTML;

        return self::iframe($html, 330, 'Vista previa de la cabecera');
    }

    private static function pie(?string $titulo, array $elementos, array $opciones): HtmlString
    {
        $urlTemporal = $opciones['imagen_identidad_previsualizacion'] ?? null;
        $imagenGuardada = $opciones['imagen_identidad'] ?? null;
        $imagenGuardada = is_array($imagenGuardada) ? reset($imagenGuardada) : $imagenGuardada;
        $imagenGuardada = is_string($imagenGuardada) && $imagenGuardada !== '[]' ? $imagenGuardada : null;
        $imagenIdentidad = filled($urlTemporal)
            ? $urlTemporal
            : (filled($imagenGuardada)
            ? asset('storage/'.ltrim($imagenGuardada, '/'))
            : asset('images/animal-sin-foto.png'));
        $imagenIdentidad = e($imagenIdentidad);
        $textoIdentidad = nl2br(e($opciones['texto_identidad'] ?? 'Asociación para la defensa y protección de los animales.'));
        $columnas = collect($elementos)
            ->filter(fn (array $seccion): bool => ($seccion['visible'] ?? true) && filled($seccion['etiqueta'] ?? null))
            ->map(function (array $seccion): string {
                $tipo = $seccion['tipo_bloque'] ?? 'enlaces';
                $enlaces = collect($seccion['subenlaces'] ?? [])
                    ->filter(fn (array $enlace): bool => ($enlace['visible'] ?? true) && filled($enlace['etiqueta'] ?? null))
                    ->map(fn (array $enlace): string => '<li><a href="#" onclick="return false" class="hover:text-asoka-100">'.e($enlace['etiqueta']).'</a></li>')
                    ->implode('');

                $contenido = match ($tipo) {
                    'texto' => '<p class="mt-3 whitespace-pre-line text-sm leading-6">'.nl2br(e($seccion['texto'] ?? 'Contenido del bloque de texto.')).'</p>',
                    'contacto' => '<p class="mt-3 text-sm leading-6"><i class="fas fa-map-marker-alt mr-2"></i>Datos de contacto configurados en Ajustes de Asoka en el menu lateral izquierdo.</p><p class="mt-2 text-sm">Correo y teléfonos de la protectora</p>',
                    default => '<ul class="mt-3 space-y-2 text-sm">'.$enlaces.'</ul>',
                };

                return '<div><h2 class="font-display text-lg">'.e($seccion['etiqueta']).'</h2>'.$contenido.'</div>';
            })
            ->implode('');

        $html = <<<HTML
<footer class="bg-asoka-400 text-white"><div class="container mx-auto px-4 py-10" style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:2rem"><div><img src="{$imagenIdentidad}" alt="Asoka el Grande" class="h-20 w-auto max-w-full rounded bg-white p-1 object-contain"><p class="mt-4 whitespace-pre-line text-sm leading-relaxed">{$textoIdentidad}</p></div>{$columnas}</div><div class="border-t border-white/40 py-4 text-center text-xs">© Asoka el Grande</div></footer>
HTML;

        return self::iframe($html, 350, 'Vista previa del pie de página');
    }

    private static function iframe(string $contenido, int $altura, string $titulo): HtmlString
    {
        $css = e(Vite::asset('resources/css/app.css'));
        $documento = '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="'.$css.'"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><style>body{margin:0;overflow-x:hidden;font-family:Nunito,sans-serif}.font-display{font-family:Fredoka,sans-serif}.logo-asoka{height:13rem;width:32rem}.logo-asoka__imagen{position:absolute;inset:0;height:100%;width:100%;object-fit:contain;object-position:left top}summary::-webkit-details-marker{display:none}details:not([open])>div{display:none}</style></head><body class="bg-asoka-50 text-slate-800 antialiased">'.$contenido.'<script>document.querySelectorAll("details").forEach(function(menu){menu.addEventListener("mouseenter",function(){menu.open=true});menu.addEventListener("mouseleave",function(){menu.open=false})})</script></body></html>';

        return new HtmlString('<iframe title="'.e($titulo).'" sandbox="allow-scripts" srcdoc="'.e($documento).'" style="display:block;width:100%;height:'.$altura.'px;border:1px solid #cde7fe;border-radius:.75rem;background:#fff"></iframe><p style="margin:.65rem 0 0;color:#6b7280;font-size:.75rem">Pasa el cursor sobre una sección para abrir sus subenlaces. Los enlaces están desactivados en esta vista.</p>');
    }
}
