<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\SolicitudAdopcion;
use App\Models\Donacion;
use App\Models\Apadrinamiento;
use Illuminate\Database\Seeder;

class AnimalDemoSeeder extends Seeder
{
    public function run(): void
    {
        // --- ANIMALES ---
        $luna = Animal::updateOrCreate(['slug' => 'luna'], [
            'nombre' => 'Luna', 'especie' => 'perro', 'raza' => 'Mestiza', 'sexo' => 'hembra',
            'fecha_nacimiento' => now()->subYears(3)->toDateString(), 'fecha_estimada' => false, 'tamano' => 'mediano', 'estado' => 'adoptable',
            'vacunado' => true, 'con_chip' => true, 'esterilizado' => true, 'compatible_perros' => true,
            'compatible_gatos' => null, 'compatible_ninos' => true, 'descripcion' => 'Luna busca una familia paciente y activa.',
            'fecha_llegada' => now()->subMonths(2)->toDateString(), 'meta_titulo' => 'Adopta a Luna',
        ]);

        $mimo = Animal::updateOrCreate(['slug' => 'mimo'], [
            'nombre' => 'Mimo', 'especie' => 'gato', 'raza' => 'Común europeo', 'sexo' => 'macho',
            'fecha_nacimiento' => now()->subYears(7)->toDateString(), 'fecha_estimada' => false, 'tamano' => 'pequeno', 'estado' => 'caso_especial',
            'vacunado' => true, 'con_chip' => true, 'esterilizado' => true, 'necesidades_especiales' => true,
            'descripcion_necesidades_especiales' => 'Necesita medicación diaria para la insuficiencia renal.', 'compatible_perros' => false,
            'compatible_gatos' => true, 'compatible_ninos' => null, 'descripcion' => 'Mimo necesita un hogar tranquilo y afectuoso.',
            'fecha_llegada' => now()->subYear()->toDateString(), 'meta_titulo' => 'Apadrina a Mimo',
        ]);

        $thor = Animal::updateOrCreate(['slug' => 'thor'], [
            'nombre' => 'Thor', 'especie' => 'perro', 'raza' => 'Galgo español', 'sexo' => 'macho',
            'fecha_nacimiento' => now()->subYears(4)->toDateString(), 'fecha_estimada' => true, 'tamano' => 'grande', 'estado' => 'adoptable',
            'vacunado' => true, 'con_chip' => true, 'esterilizado' => true, 'compatible_perros' => true,
            'compatible_gatos' => true, 'compatible_ninos' => true, 'descripcion' => 'Rescatado tras la temporada de caza, es extremadamente dulce y sociable.',
            'fecha_llegada' => now()->subMonths(4)->toDateString(), 'meta_titulo' => 'Conoce a Thor',
        ]);

        $nube = Animal::updateOrCreate(['slug' => 'nube'], [
            'nombre' => 'Nube', 'especie' => 'gato', 'raza' => 'Siamés cross', 'sexo' => 'hembra',
            'fecha_nacimiento' => now()->subMonths(5)->toDateString(), 'fecha_estimada' => false, 'tamano' => 'pequeno', 'estado' => 'adoptable',
            'vacunado' => true, 'con_chip' => false, 'esterilizado' => false, 'compatible_perros' => null,
            'compatible_gatos' => true, 'compatible_ninos' => true, 'descripcion' => 'Cachorrita juguetona encontrada en un motor de coche.',
            'fecha_llegada' => now()->subWeeks(3)->toDateString(), 'meta_titulo' => 'Adopta a Nube',
        ]);

        $rocky = Animal::updateOrCreate(['slug' => 'rocky'], [
            'nombre' => 'Rocky', 'especie' => 'perro', 'raza' => 'American Staffordshire', 'sexo' => 'macho',
            'fecha_nacimiento' => now()->subYears(2)->toDateString(), 'fecha_estimada' => false, 'tamano' => 'grande', 'estado' => 'adoptable',
            'vacunado' => true, 'con_chip' => true, 'esterilizado' => true, 'compatible_perros' => false,
            'compatible_gatos' => false, 'compatible_ninos' => false, 'descripcion' => 'Requiere licencia PPP y manejo experimentado. Muy cariñoso con adultos.',
            'fecha_llegada' => now()->subMonths(6)->toDateString(), 'meta_titulo' => 'Adopta a Rocky',
        ]);

        $kira = Animal::updateOrCreate(['slug' => 'kira'], [
            'nombre' => 'Kira', 'especie' => 'perro', 'raza' => 'Podenco', 'sexo' => 'hembra',
            'fecha_nacimiento' => now()->subYears(8)->toDateString(), 'fecha_estimada' => true, 'tamano' => 'mediano', 'estado' => 'adoptado',
            'vacunado' => true, 'con_chip' => true, 'esterilizado' => true, 'compatible_perros' => true,
            'compatible_gatos' => true, 'compatible_ninos' => true, 'descripcion' => 'Kira ya ha encontrado su hogar definitivo.',
            'fecha_llegada' => now()->subYears(2)->toDateString(), 'meta_titulo' => 'Kira adoptada',
        ]);

        $pipa = Animal::updateOrCreate(['slug' => 'pipa'], [
            'nombre' => 'Pipa', 'especie' => 'gato', 'raza' => 'Persa mix', 'sexo' => 'hembra',
            'fecha_nacimiento' => now()->subYears(12)->toDateString(), 'fecha_estimada' => true, 'tamano' => 'pequeno', 'estado' => 'caso_especial',
            'vacunado' => true, 'con_chip' => true, 'esterilizado' => true, 'necesidades_especiales' => true,
            'descripcion_necesidades_especiales' => 'Ciega del ojo izquierdo y dieta hepática.', 'compatible_perros' => true,
            'compatible_gatos' => true, 'compatible_ninos' => true, 'descripcion' => 'Abuelita muy tranquila que busca pasarse el día al sol.',
            'fecha_llegada' => now()->subMonths(8)->toDateString(), 'meta_titulo' => 'Apadrina a Pipa',
        ]);

        // --- SOLICITUDES DE ADOPCIÓN / ACOGIDA ---
        SolicitudAdopcion::firstOrCreate(['animal_id' => $luna->id, 'email_solicitante' => 'ana@example.test'], [
            'tipo' => 'adopcion', 'nombre_solicitante' => 'Ana Pérez', 'telefono_solicitante' => '600000000',
            'ciudad_solicitante' => 'Alicante', 'tipo_vivienda' => 'piso', 'tiene_otras_mascotas' => false,
            'cuestionario' => ['horario' => 'Teletrabajo', 'experiencia' => 'He convivido con perros.'],
            'estado' => 'pendiente',
        ]);

        SolicitudAdopcion::firstOrCreate(['animal_id' => $thor->id, 'email_solicitante' => 'carlos.gomez@example.test'], [
            'tipo' => 'adopcion', 'nombre_solicitante' => 'Carlos Gómez', 'telefono_solicitante' => '611223344',
            'ciudad_solicitante' => 'Elche', 'tipo_vivienda' => 'casa_con_jardin', 'tiene_otras_mascotas' => true,
            'cuestionario' => ['horario' => 'Jornada continua', 'experiencia' => 'Tengo un galgo actualmente.'],
            'estado' => 'aprobada',
        ]);

        SolicitudAdopcion::firstOrCreate(['animal_id' => $nube->id, 'email_solicitante' => 'maria.rodriguez@example.test'], [
            'tipo' => 'acogida', 'nombre_solicitante' => 'María Rodríguez', 'telefono_solicitante' => '622334455',
            'ciudad_solicitante' => 'San Vicente del Raspeig', 'tipo_vivienda' => 'piso', 'tiene_otras_mascotas' => true,
            'cuestionario' => ['horario' => 'Media jornada', 'experiencia' => 'He sido casa de acogida para 3 lactantes.'],
            'estado' => 'en_revision',
        ]);

        SolicitudAdopcion::firstOrCreate(['animal_id' => $rocky->id, 'email_solicitante' => 'pablo.sanchez@example.test'], [
            'tipo' => 'adopcion', 'nombre_solicitante' => 'Pablo Sánchez', 'telefono_solicitante' => '633445566',
            'ciudad_solicitante' => 'Alicante', 'tipo_vivienda' => 'piso', 'tiene_otras_mascotas' => true,
            'cuestionario' => ['horario' => 'Jornada completa', 'experiencia' => 'Sin experiencia previa con PPP.'],
            'estado' => 'rechazada',
        ]);

        // --- APADRINAMIENTOS ---
        Apadrinamiento::firstOrCreate(['animal_id' => $mimo->id, 'email_padrino' => 'sofia@example.test'], [
            'nombre_padrino' => 'Sofía García', 'importe_mensual' => 15, 'estado' => 'activo',
        ]);

        Apadrinamiento::firstOrCreate(['animal_id' => $mimo->id, 'email_padrino' => 'david.martinez@example.test'], [
            'nombre_padrino' => 'David Martínez', 'importe_mensual' => 20, 'estado' => 'activo',
        ]);

        Apadrinamiento::firstOrCreate(['animal_id' => $pipa->id, 'email_padrino' => 'elena.navarro@example.test'], [
            'nombre_padrino' => 'Elena Navarro', 'importe_mensual' => 10, 'estado' => 'activo',
        ]);

        // --- DONACIONES ---
        Donacion::firstOrCreate(['id_transaccion' => 'demo-donacion-001'], [
            'nombre_donante' => 'Donante de prueba', 'email_donante' => 'donante@example.test', 'importe' => 25.00,
            'metodo_pago' => 'stripe', 'recurrente' => false, 'estado' => 'completada',
        ]);

        Donacion::firstOrCreate(['id_transaccion' => 'demo-donacion-002'], [
            'nombre_donante' => 'Laura Beltrán', 'email_donante' => 'laura.b@example.test', 'importe' => 50.00,
            'metodo_pago' => 'bizum', 'recurrente' => false, 'estado' => 'completada',
        ]);

        Donacion::firstOrCreate(['id_transaccion' => 'demo-donacion-003'], [
            'nombre_donante' => 'Anónimo', 'email_donante' => 'anonimo@example.test', 'importe' => 100.00,
            'metodo_pago' => 'transferencia', 'recurrente' => false, 'estado' => 'completada',
        ]);

        Donacion::firstOrCreate(['id_transaccion' => 'demo-donacion-004'], [
            'nombre_donante' => 'Roberto Soler', 'email_donante' => 'roberto.s@example.test', 'importe' => 10.00,
            'metodo_pago' => 'stripe', 'recurrente' => true, 'estado' => 'completada',
        ]);
    }
}
