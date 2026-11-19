<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Establecimiento;
use App\Models\ReporteEstablecimiento;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * PANEL ADMINISTRATIVO PRINCIPAL (Objetivos 1 y 8)
     */
    public function dashboard()
    {
        $metricas = [
            'total_establecimientos' => Establecimiento::count(),
            'verificados'            => Establecimiento::where('verificado', 1)->count(),
            'reportados'             => Establecimiento::where('reportado', 1)->count(),
            'reportes_pendientes'    => ReporteEstablecimiento::where('estado', 'pendiente')->count(),
            'total_emprendedores'    => User::where('role', 'emprendedor')->count(),
            'total_comercios'        => User::where('role', 'comercio')->count(),
        ];

        // Reportes pendientes de revisión
        $reportes = ReporteEstablecimiento::with(['establecimiento', 'user'])
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Establecimientos reportados o pendientes de verificación
        $pendientesVerificacion = Establecimiento::where('verificado', 0)
            ->orWhere('reportado', 1)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $categorias = Categoria::withCount('productos')->get();

        return view('admin.dashboard', compact('metricas', 'reportes', 'pendientesVerificacion', 'categorias'));
    }

    /**
     * VERIFICAR O DESVERIFICAR ESTABLECIMIENTO
     */
    public function toggleVerificacion($id)
    {
        $est = Establecimiento::findOrFail($id);
        $est->verificado = !$est->verificado;
        $est->fecha_verificacion = $est->verificado ? now() : null;
        $est->save();

        return back()->with('success', "Estado de verificación actualizado para {$est->nombre_establecimiento}.");
    }

    /**
     * CORREGIR UBICACIÓN Y COORDENADAS EN EL MAPA (Objetivo 8)
     */
    public function corregirUbicacion(Request $request, $id)
    {
        $request->validate([
            'latitud'  => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ]);

        $est = Establecimiento::findOrFail($id);
        $est->update([
            'latitud'            => $request->latitud,
            'longitud'           => $request->longitud,
            'verificado'         => 1,
            'reportado'          => 0,
            'fecha_verificacion' => now(),
        ]);

        return back()->with('success', "Ubicación GPS corregida exitosamente para {$est->nombre_establecimiento}.");
    }

    /**
     * RESOLVER REPORTE DE INFORMACIÓN INCORRECTA
     */
    public function resolverReporte(Request $request, $id)
    {
        $reporte = ReporteEstablecimiento::findOrFail($id);
        $reporte->update([
            'estado' => $request->get('estado', 'corregido')
        ]);

        // Si no hay más reportes pendientes para este establecimiento, quitar flag reportado
        $pendientes = ReporteEstablecimiento::where('establecimiento_id', $reporte->establecimiento_id)
            ->where('estado', 'pendiente')
            ->count();

        if ($pendientes === 0) {
            Establecimiento::where('id', $reporte->establecimiento_id)->update(['reportado' => 0]);
        }

        return back()->with('success', 'Reporte procesado correctamente.');
    }
}
