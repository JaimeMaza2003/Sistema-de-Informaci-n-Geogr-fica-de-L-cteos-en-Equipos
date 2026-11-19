<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use App\Models\Oportunidad;
use App\Models\Producto;
use App\Models\ReporteEstablecimiento;
use Illuminate\Http\Request;

class EstablecimientoController extends Controller
{
    /**
     * PANTALLA PRINCIPAL: MAPA DE OPORTUNIDADES (Objetivo 2)
     */
    public function index(Request $request)
    {
        $query = Establecimiento::query();

        // Búsqueda por texto (nombre, barrio, dirección)
        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where(function ($sub) use ($term) {
                $sub->where('nombre_establecimiento', 'like', $term)
                    ->orWhere('barrio_sector', 'like', $term)
                    ->orWhere('direccion_completa', 'like', $term)
                    ->orWhere('nombre_propietario', 'like', $term);
            });
        }

        // Filtro por Parroquia
        if ($request->filled('parroquia')) {
            $query->where('parroquia', $request->parroquia);
        }

        // Filtro por Tipo de Establecimiento
        if ($request->filled('tipo')) {
            $query->where('tipo_establecimiento', $request->tipo);
        }

        // Filtro: Recepción de nuevos proveedores
        if ($request->filled('recepcion')) {
            if ($request->recepcion === 'si') {
                $query->where(function ($sub) {
                    $sub->where('recepcion_proveedores', 'Recibe propuestas')
                        ->orWhere('interes_nuevos_proveedores', 1);
                });
            } elseif ($request->recepcion === 'artesanal') {
                $query->where('interes_artesanales', 1);
            }
        }

        // Filtro por Categoría de producto lácteo
        if ($request->filled('categoria')) {
            $cat = '%' . $request->categoria . '%';
            $query->where(function ($sub) use ($cat) {
                $sub->where('productos_lacteos', 'like', $cat)
                    ->orWhere('producto_mayor_demanda', 'like', $cat);
            });
        }

        // Filtro por refrigeración
        if ($request->filled('refrigeracion') && $request->refrigeracion === 'si') {
            $query->where('tiene_refrigeracion', 1);
        }

        $totalEncontrados = (clone $query)->count();

        // Obtener datos para el mapa (todos los filtrados)
        $marcadores = (clone $query)->get([
            'id', 'nombre_establecimiento', 'nombre_propietario', 'tipo_establecimiento',
            'direccion_completa', 'parroquia', 'barrio_sector', 'telefono', 'latitud', 'longitud',
            'recepcion_proveedores', 'tiene_refrigeracion', 'productos_lacteos', 'verificado'
        ]);

        // Lista paginada para la vista de listado
        $establecimientos = $query->orderBy('parroquia')->orderBy('nombre_establecimiento')->paginate(12)->withQueryString();

        // Parroquias y tipos disponibles en Latacunga
        $parroquias = Establecimiento::distinct()->whereNotNull('parroquia')->orderBy('parroquia')->pluck('parroquia');
        $tipos = Establecimiento::distinct()->whereNotNull('tipo_establecimiento')->pluck('tipo_establecimiento');

        // Vista preferida (mapa o listado)
        $vista = $request->get('vista', 'mapa');

        // Productos del usuario autenticado (para vincular rápido en el modal de propuesta)
        $productosUsuario = Producto::where('activo', 1)->get();

        return view('mapa.index', compact(
            'establecimientos',
            'marcadores',
            'parroquias',
            'tipos',
            'vista',
            'totalEncontrados',
            'productosUsuario'
        ));
    }

    /**
     * FICHA DE CADA TIENDA O SUPERMERCADO (Objetivo 3)
     */
    public function show($id)
    {
        $establecimiento = Establecimiento::with(['oportunidades.producto', 'propuestas'])->findOrFail($id);

        // Productos del emprendedor para enviar propuesta
        $productos = Producto::where('activo', 1)->get();

        // Verificar si el usuario ya tiene este establecimiento en "Mis Oportunidades"
        $oportunidadExistente = Oportunidad::where('establecimiento_id', $id)->first();

        return view('establecimientos.show', compact('establecimiento', 'productos', 'oportunidadExistente'));
    }

    /**
     * ACCIÓN 1: GUARDAR OPORTUNIDAD (Objetivo 6 y 7)
     */
    public function guardarOportunidad(Request $request, $id)
    {
        $request->validate([
            'producto_id'    => 'nullable|exists:productos,id',
            'notas_privadas' => 'nullable|string',
        ]);

        $userId = auth()->id() ?? 2; // Default demo emprendedor

        $oportunidad = Oportunidad::firstOrCreate(
            [
                'user_id'            => $userId,
                'establecimiento_id' => $id,
                'producto_id'        => $request->producto_id,
            ],
            [
                'estado'                => 'por_contactar',
                'notas_privadas'        => $request->notas_privadas ?? 'Oportunidad guardada desde el mapa de establecimientos.',
                'fecha_proxima_gestion' => now()->addDays(2),
            ]
        );

        return redirect()->route('oportunidades.index')
            ->with('success', '¡Establecimiento guardado exitosamente en "Mis Oportunidades"!');
    }

    /**
     * REPORTAR INFORMACIÓN INCORRECTA (Objetivo 8)
     */
    public function reportarError(Request $request, $id)
    {
        $request->validate([
            'tipo_error'  => 'required|string',
            'descripcion' => 'required|string|max:1000',
        ]);

        ReporteEstablecimiento::create([
            'user_id'            => auth()->id() ?? 2,
            'establecimiento_id' => $id,
            'tipo_error'         => $request->tipo_error,
            'descripcion'        => $request->descripcion,
            'estado'             => 'pendiente',
        ]);

        // Marcar establecimiento como reportado
        Establecimiento::where('id', $id)->update(['reportado' => 1]);

        return back()->with('success', 'Gracias por tu reporte. El administrador revisará y actualizará la información del establecimiento.');
    }

    /**
     * DETECTAR POSIBLES DUPLICADOS (Objetivo 8)
     */
    public function detectarDuplicados(Request $request)
    {
        $nombre = $request->get('nombre');
        $telefono = $request->get('telefono');

        $duplicados = Establecimiento::query()
            ->when($nombre, fn($q) => $q->where('nombre_establecimiento', 'like', "%{$nombre}%"))
            ->when($telefono, fn($q) => $q->orWhere('telefono', $telefono))
            ->limit(5)
            ->get(['id', 'nombre_establecimiento', 'tipo_establecimiento', 'parroquia', 'telefono']);

        return response()->json([
            'hay_duplicados' => $duplicados->count() > 0,
            'establecimientos' => $duplicados,
        ]);
    }

    /**
     * API PARA ACTUALIZAR MARCADORES EN VIVO
     */
    public function apiMarcadores(Request $request)
    {
        $query = Establecimiento::query();

        if ($request->filled('parroquia')) {
            $query->where('parroquia', $request->parroquia);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_establecimiento', $request->tipo);
        }
        if ($request->filled('recepcion') && $request->recepcion === 'si') {
            $query->recibenProveedores();
        }

        $marcadores = $query->get([
            'id', 'nombre_establecimiento', 'nombre_propietario', 'tipo_establecimiento',
            'direccion_completa', 'parroquia', 'telefono', 'latitud', 'longitud',
            'recepcion_proveedores', 'tiene_refrigeracion', 'productos_lacteos'
        ]);

        return response()->json($marcadores);
    }
}
