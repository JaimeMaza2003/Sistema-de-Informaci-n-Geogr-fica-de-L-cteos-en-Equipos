<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use App\Models\Oportunidad;
use App\Models\Propuesta;
use Illuminate\Http\Request;

class PropuestaController extends Controller
{
    /**
     * ENVIAR PROPUESTA FORMAL (Objetivo 6)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'establecimiento_id'      => 'required|exists:establecimientos,id',
            'mensaje'                 => 'required|string|max:2000',
            'productos_seleccionados' => 'nullable|array',
            'productos_seleccionados.*'=> 'exists:productos,id',
        ]);

        $userId = auth()->id() ?? 2;
        $estId = $validated['establecimiento_id'];

        // Crear o asociar a oportunidad
        $oportunidad = Oportunidad::firstOrCreate(
            [
                'user_id'            => $userId,
                'establecimiento_id' => $estId,
            ],
            [
                'estado'                => 'contactado',
                'notas_privadas'        => 'Propuesta formal enviada el ' . now()->format('d/m/Y H:i'),
                'fecha_proxima_gestion' => now()->addDays(3),
            ]
        );

        // Si ya existía, avanzar de por_contactar a contactado
        if ($oportunidad->estado === 'por_contactar') {
            $oportunidad->update(['estado' => 'contactado']);
        }

        Propuesta::create([
            'user_id'            => $userId,
            'establecimiento_id' => $estId,
            'oportunidad_id'     => $oportunidad->id,
            'mensaje'            => $validated['mensaje'],
            'productos_json'     => $validated['productos_seleccionados'] ?? [],
            'estado'             => 'enviada',
        ]);

        return redirect()->route('establecimientos.show', $estId)
            ->with('success', '¡Propuesta comercial enviada con éxito al establecimiento!');
    }

    /**
     * RESPUESTA DEL COMERCIO A LA PROPUESTA (Objetivo 6)
     * Opciones: Me interesa / Solicito información / Agendar visita / No interesado por ahora
     */
    public function responder(Request $request, $id)
    {
        $propuesta = Propuesta::with('oportunidad')->findOrFail($id);

        $validated = $request->validate([
            'estado'                 => 'required|in:me_interesa,solicito_info,agendar_visita,no_interesado',
            'respuesta_comercio'     => 'nullable|string',
            'fecha_visita_propuesta' => 'nullable|date',
        ]);

        $propuesta->update($validated);

        // Actualizar automáticamente el estado del CRM del emprendedor
        if ($propuesta->oportunidad) {
            if ($validated['estado'] === 'agendar_visita') {
                $propuesta->oportunidad->update([
                    'estado' => 'muestra_entregada',
                    'fecha_proxima_gestion' => $validated['fecha_visita_propuesta'] ?? now()->addDays(2),
                    'notas_privadas' => $propuesta->oportunidad->notas_privadas . "\n[Comercio aceptó visita para: " . ($validated['fecha_visita_propuesta'] ?? 'Fecha por definir') . "]",
                ]);
            } elseif ($validated['estado'] === 'me_interesa') {
                $propuesta->oportunidad->update([
                    'estado' => 'en_negociacion',
                    'notas_privadas' => $propuesta->oportunidad->notas_privadas . "\n[Comercio respondió: Me interesa la propuesta]",
                ]);
            } elseif ($validated['estado'] === 'no_interesado') {
                $propuesta->oportunidad->update([
                    'estado' => 'no_concretada',
                    'motivo_cierre' => 'Comercio indicó no interesado en este momento.',
                ]);
            }
        }

        return back()->with('success', 'Respuesta registrada con éxito.');
    }
}
