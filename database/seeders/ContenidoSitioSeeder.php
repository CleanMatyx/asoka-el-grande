<?php

namespace Database\Seeders;

use App\Models\AjusteSitio;
use App\Models\Pagina;
use Illuminate\Database\Seeder;

class ContenidoSitioSeeder extends Seeder
{
    public function run(): void
    {
        AjusteSitio::query()->firstOrCreate([], [
            'telefono_alicante' => '602 44 46 42',
            'email_contacto' => 'asokaelgrande@gmail.com',
            'direccion_albergue' => 'Diseminado Fontcalent, 37B, 03113 Alicante',
            'horarios_visita' => 'Visitas con cita previa. Consulta disponibilidad por teléfono o correo electrónico.',
            'facebook_url' => 'https://www.facebook.com/Asoka.Alicante/',
            'instagram_url' => 'https://www.instagram.com/asokaelgrande/',
            'twitter_url' => 'https://twitter.com/AsokaElGrande',
            'teaming_url' => 'https://www.teaming.net/asokaelgrande/invite',
            'titulo_hero' => 'Cada mirada merece un hogar. La tuya puede cambiarlo todo.',
            'subtitulo_hero' => 'Conoce a los animales que esperan una segunda oportunidad, ofrece acogida temporal o ayuda a que nunca les falte cuidado.',
            'etiqueta_hero' => 'Protectora de animales · Alicante',
            'texto_boton_hero' => 'Buscar mi compañero',
        ]);

        foreach ([
            [
                'clave' => 'inicio',
                'titulo' => 'Inicio',
                'subtitulo' => 'Portada de Asoka el Grande',
                'meta_titulo' => 'Adopta, acoge y ayuda | Asoka el Grande · Alicante',
                'meta_descripcion' => 'Encuentra a tu compañero de vida, ofrece acogida o colabora con Asoka el Grande, protectora de animales en Alicante.',
                'bloques' => [
                    [
                        'tipo' => 'inicio-hero-buscador',
                        'visible' => true,
                        'etiqueta' => 'Protectora de animales · Alicante',
                        'titulo' => 'Cada mirada merece un hogar. La tuya puede cambiarlo todo.',
                        'contenido' => 'Conoce a los animales que esperan una segunda oportunidad, ofrece acogida temporal o ayuda a que nunca les falte cuidado.',
                        'texto_boton' => 'Buscar mi compañero',
                    ],
                    [
                        'tipo' => 'animales-destacados',
                        'visible' => true,
                        'etiqueta' => 'Prioridad',
                        'titulo' => 'Necesitan tu ayuda hoy',
                        'fuente_animales' => 'urgentes',
                        'limite' => 6,
                    ],
                    [
                        'tipo' => 'formas-ayudar',
                        'visible' => true,
                        'etiqueta' => 'Tu ayuda transforma vidas',
                        'titulo' => 'Hay muchas formas de estar a su lado',
                        'tarjetas' => [
                            ['titulo' => 'Adopta o acoge', 'texto' => 'Abre tu hogar para siempre o durante el tiempo que más lo necesitan.', 'texto_boton' => 'Conocer animales', 'url_boton' => '/animales', 'estilo' => 'claro'],
                            ['titulo' => 'Hazte padrino o socio', 'texto' => 'Tu aportación mensual da estabilidad, tratamientos y alimento.', 'texto_boton' => 'Quiero colaborar', 'url_boton' => '/apadrinar', 'estilo' => 'claro'],
                            ['titulo' => 'Donación rápida', 'texto' => 'Colabora mediante Bizum, tarjeta con Stripe o Teaming por 1 € al mes.', 'texto_boton' => 'Hacer una donación', 'url_boton' => '/donar', 'estilo' => 'destacado'],
                        ],
                    ],
                    [
                        'tipo' => 'animales-destacados',
                        'visible' => true,
                        'etiqueta' => 'Recién llegados',
                        'titulo' => 'Últimos Asoketes en adopción',
                        'fuente_animales' => 'ultimos',
                        'limite' => 8,
                    ],
                    [
                        'tipo' => 'estadisticas',
                        'visible' => true,
                        'etiqueta' => 'Transparencia',
                        'titulo' => 'Nuestra labor en cifras',
                    ],
                ],
            ],
            ['clave' => 'sobre-asoka', 'titulo' => 'Sobre Asoka', 'subtitulo' => 'Conoce nuestra labor y nuestro compromiso con los animales.', 'contenido' => '<p>Asoka el Grande trabaja cada día para proteger, cuidar y encontrar un hogar responsable para los animales.</p>'],
            ['clave' => 'voluntariado', 'titulo' => 'Voluntariado', 'subtitulo' => 'Tu tiempo también cambia vidas.', 'contenido' => '<p>Las personas voluntarias son fundamentales para nuestra labor. Contacta con nosotros para conocer las formas de colaborar.</p>'],
            ['clave' => 'proyecto-nueva-esperanza', 'titulo' => 'Proyecto Nueva Esperanza', 'subtitulo' => 'Una iniciativa para seguir construyendo oportunidades.', 'contenido' => '<p>Conoce el proyecto y cómo puedes ayudarnos a hacerlo posible.</p>'],
            ['clave' => 'aviso-legal', 'titulo' => 'Aviso legal', 'subtitulo' => null, 'contenido' => '<p>Contenido legal pendiente de revisión y adaptación por la entidad responsable.</p>'],
            ['clave' => 'politica-privacidad', 'titulo' => 'Política de privacidad', 'subtitulo' => null, 'contenido' => '<p>Contenido de privacidad pendiente de revisión y adaptación conforme a la normativa aplicable.</p>'],
            ['clave' => 'contacto', 'titulo' => 'Contacto', 'subtitulo' => 'Estamos aquí para atender tus dudas y ayudarte a colaborar.', 'contenido' => '<p>Escríbenos o consulta nuestros datos de contacto para concertar una visita.</p>'],
        ] as $pagina) {
            Pagina::query()->firstOrCreate(['clave' => $pagina['clave']], $pagina + ['publicado' => true]);
        }
    }
}
