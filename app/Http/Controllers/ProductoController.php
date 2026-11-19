<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * CATÁLOGO DE PRODUCTOS DEL EMPRENDEDOR (Objetivo 4)
     */
    public function index()
    {
        $userId = auth()->id() ?? 2;
        $productos = Producto::where('user_id', $userId)->with('categoria')->get();
        $categorias = Categoria::all();

        return view('productos.index', compact('productos', 'categorias'));
    }

    /**
     * FORMULARIO REGISTRAR PRODUCTO
     */
    public function create()
    {
        $categorias = Categoria::all();
        return view('productos.create', compact('categorias'));
    }

    /**
     * GUARDAR PRODUCTO EN EL CATÁLOGO
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'                   => 'required|string|max:191',
            'categoria_id'             => 'required|exists:categorias,id',
            'descripcion'              => 'nullable|string',
            'presentacion'             => 'required|string|max:100',
            'precio_comercio'          => 'required|numeric|min:0.01',
            'precio_sugerido'          => 'required|numeric|min:0.01',
            'pedido_minimo'            => 'required|string|max:100',
            'capacidad_semanal'        => 'required|string|max:100',
            'vida_util_dias'           => 'required|integer|min:1',
            'requiere_refrigeracion'   => 'nullable|boolean',
            'temperatura_conservacion' => 'nullable|string|max:50',
            'zonas_entrega'            => 'required|string|max:255',
            'frecuencia_entrega'       => 'required|string|max:100',
            'registro_sanitario'       => 'nullable|string|max:100',
            'bpm_disponible'           => 'nullable|boolean',
            'ruc_disponible'           => 'nullable|boolean',
            'tabla_nutricional'        => 'nullable|boolean',
        ]);

        $categoria = Categoria::findOrFail($validated['categoria_id']);
        $validated['categoria_nombre'] = $categoria->nombre;
        $validated['user_id'] = auth()->id() ?? 2;
        $validated['requiere_refrigeracion'] = $request->has('requiere_refrigeracion');
        $validated['bpm_disponible'] = $request->has('bpm_disponible');
        $validated['ruc_disponible'] = $request->has('ruc_disponible');
        $validated['tabla_nutricional'] = $request->has('tabla_nutricional');
        $validated['imagen_url'] = '/images/productos/default.jpg';

        Producto::create($validated);

        return redirect()->route('productos.index')->with('success', '¡Producto añadido exitosamente a tu catálogo!');
    }

    /**
     * VINCULACIÓN ENTRE PRODUCTO Y ESTABLECIMIENTO:
     * "BUSCAR DÓNDE OFRECERLO" (Objetivo 5)
     */
    public function compatibles($id)
    {
        $producto = Producto::findOrFail($id);
        $resultados = $producto->buscarEstablecimientosCompatibles(25);

        return view('productos.compatibilidad', compact('producto', 'resultados'));
    }

    /**
     * FICHA COMERCIAL EN PDF / FORMATO IMPRIMIBLE (Objetivo 4)
     */
    public function fichaPdf($id)
    {
        $producto = Producto::with('user')->findOrFail($id);
        return view('productos.ficha_pdf', compact('producto'));
    }

    /**
     * COMPARTIR CATÁLOGO MEDIANTE ENLACE PÚBLICO (Objetivo 4)
     */
    public function catalogoPublico($userId)
    {
        $usuario = User::findOrFail($userId);
        $productos = Producto::where('user_id', $userId)->where('activo', 1)->get();

        return view('productos.catalogo_publico', compact('usuario', 'productos'));
    }
}
