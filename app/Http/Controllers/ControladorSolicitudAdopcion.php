<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ControladorSolicitudAdopcion extends Controller
{
    public function almacenar(Request $request, Animal $animal): RedirectResponse
    {
        $datos = $request->validate([
            'tipo' => ['required', Rule::in(['adopcion', 'acogida'])],
            'nombre_solicitante' => ['required', 'string', 'max:160'],
            'email_solicitante' => ['required', 'email', 'max:190'],
            'telefono_solicitante' => ['required', 'string', 'max:40'],
            'ciudad_solicitante' => ['required', 'string', 'max:120'],
            'tipo_vivienda' => ['required', Rule::in(['piso', 'casa_con_jardin', 'casa_de_campo', 'piso_compartido', 'otro'])],
            'tiene_otras_mascotas' => ['required', 'boolean'],
            'cuestionario' => ['required', 'array'],
            'cuestionario.detalles_vivienda' => ['required', 'string', 'max:2000'],
            'cuestionario.experiencia_animales' => ['required', 'string', 'max:2000'],
            'cuestionario.compromiso' => ['required', 'accepted'],
        ]);

        $datos['animal_id'] = $animal->id;
        $datos['estado'] = 'pendiente';

        SolicitudAdopcion::create($datos);

        return to_route('animales.mostrar', $animal->slug)
            ->with('exito', 'Hemos recibido tu solicitud. El equipo de Asoka el Grande la revisará y contactará contigo lo antes posible.');
    }
}
