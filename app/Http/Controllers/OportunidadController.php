<?php

namespace App\Http\Controllers;

use App\Models\Oportunidad;
use App\Models\Producto;
use Illuminate\Http\Request;

class OportunidadController extends Controller
{
    /**
     * SEGUIMIENTO DE OPORTUNIDADES ("MIS OPORTUNIDADES" CRM) (Objetivo 7)
     */
    public function index(Request $request)
    {
        $userId = auth()->id() ?? 2;
        $query = Oportunidad::with(['establecimiento', 'producto', 'propuestas'])->where('user_id', $userId);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $oportunidades = $query->orderBy('updated_at', 'desc')->get();

        // Agrupación por columnas de estados para el tablero Kanban
        $porEstado = [
            'por_contactar'     => $oportunidades->where('estado', 'por_contactar'),
            'contactado'        => $oportunidades->where('estado', 'contactado'),
            'muestra_entregada' => $oportunidades->where('estado', 'muestra_entregada'),
            'en_negociacion'    => $oportunidades->where('estado', 'en_negociacion'),
            'primer_pedido'     => $oportunidades->where('estado', 'primer_pedido'),
            'punto_activo'      => $oportunidades->where('estado', 'punto_activo'),
            'no_concretada'     => $oportunidades->where('estado', 'no_concretada'),
        ];

        // Indicadores clave de éxito (KPIs)
        $metricas = [
            'total_gestiones'       => $oportunidades->count(),
            'primeros_pedidos'      => $oportunidades->where('estado', 'primer_pedido')->count(),
            'puntos_venta_activos'  => $oportunidades->where('estado', 'punto_activo')->count(),
            'tasa_conversion'       => $oportunidades->count() > 0 
                ? round((($oportunidades->whereIn('estado', ['primer_pedido', 'punto_activo'])->count()) / $oportunidades->count()) * 100, 1) 
                : 0,
            'total_ventas_pedidos'  => $oportunidades->sum('monto_primer_pedido'),
        ];

        $productos = Producto::where('user_id', $userId)->get();

        return view('oportunidades.index', compact('oportunidades', 'porEstado', 'metricas', 'productos'));
    }

    /**
     * ACTUALIZAR ESTADO, NOTAS Y CONDICIONES DE UNA OPORTUNIDAD
     */
    public function update(Request $request, $id)
    {
        $oportunidad = Oportunidad::findOrFail($id);

        $validated = $request->validate([
            'estado'                 => 'required|in:por_contactar,contactado,muestra_entregada,en_negociacion,primer_pedido,punto_activo,no_concretada',
            'notas_privadas'         => 'nullable|string',
            'fecha_proxima_gestion'  => 'nullable|date',
            'condiciones_acordadas'  => 'nullable|string',
            'monto_primer_pedido'    => 'nullable|numeric|min:0',
            'fecha_primer_pedido'    => 'nullable|date',
            'motivo_cierre'          => 'nullable|string|max:255',
        ]);

        // Si cambia a primer_pedido y no tiene fecha, asignar hoy
        if ($validated['estado'] === 'primer_pedido' && empty($validated['fecha_primer_pedido'])) {
            $validated['fecha_primer_pedido'] = now()->toDateString();
        }

        $oportunidad->update($validated);

        return back()->with('success', '¡Oportunidad comercial actualizada exitosamente!');
    }

    /**
     * ELIMINAR OPORTUNIDAD
     */
    public function destroy($id)
    {
        $oportunidad = Oportunidad::findOrFail($id);
        $oportunidad->delete();

        return back()->with('success', 'Oportunidad eliminada de tu seguimiento.');
    }
}
