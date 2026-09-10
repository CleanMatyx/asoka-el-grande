<?php

namespace Database\Seeders;

use App\Models\MenuSitio;
use Illuminate\Database\Seeder;

class MenuSitioSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['nombre' => 'Cabecera principal', 'titulo_visible' => null, 'ubicacion' => 'cabecera', 'orden' => 0, 'elementos' => [
                ['etiqueta' => 'Asoka el Grande', 'visible' => true, 'subenlaces' => [['etiqueta' => 'Inicio', 'url' => '/', 'visible' => true], ['etiqueta' => 'Sobre Asoka', 'pagina_clave' => 'sobre-asoka', 'visible' => true], ['etiqueta' => 'Contáctanos', 'url' => 'mailto:asokaelgrande@gmail.com', 'visible' => true]]],
                ['etiqueta' => 'Adopta', 'visible' => true, 'subenlaces' => [['etiqueta' => 'Perros en adopción', 'url' => '/animales?especie=perro', 'visible' => true], ['etiqueta' => 'Gatos en adopción', 'url' => '/animales?especie=gato', 'visible' => true], ['etiqueta' => 'Casos especiales', 'url' => '/animales?categoria=casos-especiales', 'visible' => true], ['etiqueta' => 'Todos los Asoketes', 'url' => '/animales', 'visible' => true]]],
                ['etiqueta' => 'Ayúdanos', 'visible' => true, 'subenlaces' => [['etiqueta' => 'Acoge un Asokete', 'url' => '/#asoketes', 'visible' => true], ['etiqueta' => 'Apadrina un Asokete', 'url' => '/apadrinar', 'visible' => true], ['etiqueta' => 'Haz una donación', 'url' => '/donar', 'visible' => true], ['etiqueta' => 'Hazte voluntario/a', 'pagina_clave' => 'voluntariado', 'visible' => true]]],
                ['etiqueta' => 'Más información', 'visible' => true, 'subenlaces' => [['etiqueta' => 'Proyecto Nueva Esperanza', 'pagina_clave' => 'proyecto-nueva-esperanza', 'visible' => true], ['etiqueta' => 'Aviso legal', 'pagina_clave' => 'aviso-legal', 'visible' => true], ['etiqueta' => 'Política de privacidad', 'pagina_clave' => 'politica-privacidad', 'visible' => true]]],
            ]],
            ['nombre' => 'Pie de página principal', 'ubicacion' => 'pie', 'orden' => 0, 'elementos' => [
                ['etiqueta' => 'Enlaces', 'visible' => true, 'tipo_bloque' => 'enlaces', 'subenlaces' => [['etiqueta' => 'Inicio', 'url' => '/', 'visible' => true], ['etiqueta' => 'Adopciones', 'url' => '/animales', 'visible' => true], ['etiqueta' => 'Sobre Asoka', 'pagina_clave' => 'sobre-asoka', 'visible' => true], ['etiqueta' => 'Voluntariado', 'pagina_clave' => 'voluntariado', 'visible' => true]]],
                ['etiqueta' => 'Colabora', 'visible' => true, 'tipo_bloque' => 'enlaces', 'subenlaces' => [['etiqueta' => 'Apadrinamientos', 'url' => '/apadrinar', 'visible' => true], ['etiqueta' => 'Donaciones', 'url' => '/donar', 'visible' => true], ['etiqueta' => 'Política de privacidad', 'pagina_clave' => 'politica-privacidad', 'visible' => true]]],
                ['etiqueta' => 'Contacto', 'visible' => true, 'tipo_bloque' => 'contacto', 'subenlaces' => []],
            ]],
        ];

        foreach ($menus as $menu) {
            MenuSitio::query()->firstOrCreate(['nombre' => $menu['nombre']], $menu + ['activo' => true]);
        }
    }
}
