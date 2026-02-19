<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Familia;
use App\Models\Componente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoApiController extends Controller
{
    /**
     * Listar productos con paginación (campos simplificados)
     * GET /api/productos
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100); // Máximo 100 por página

        $query = Producto::query()
            ->select([
                'productos.id',
                'productos.codigo',
                'productos.descripcion',
                'ubicaciones.codigo as ubicacion',
                'unidades_medida.codigo as um',
                'productos.cantidad_fisica as fisico',
                'productos.precio_unitario as pu'
            ])
            ->leftJoin('ubicaciones', 'productos.ubicacion_id', '=', 'ubicaciones.id')
            ->leftJoin('unidades_medida', 'productos.unidad_medida_id', '=', 'unidades_medida.id');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('productos.codigo', 'like', "%{$search}%")
                  ->orWhere('productos.descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('productos.categoria_id', $request->categoria_id);
        }

        if ($request->filled('familia_id')) {
            $query->where('productos.familia_id', $request->familia_id);
        }

        if ($request->filled('componente_id')) {
            $query->where('productos.componente_id', $request->componente_id);
        }

        if ($request->filled('ubicacion_id')) {
            $query->where('productos.ubicacion_id', $request->ubicacion_id);
        }

        // Filtro por stock
        if ($request->filled('stock_bajo')) {
            $query->whereColumn('productos.cantidad_fisica', '<', 'productos.cantidad_entrada');
        }

        $productos = $query->paginate($perPage);

        // Simplificar respuesta de paginación
        return response()->json([
            'data' => $productos->items(),
            'current_page' => $productos->currentPage(),
            'last_page' => $productos->lastPage(),
            'per_page' => $productos->perPage(),
            'total' => $productos->total(),
        ]);
    }

    /**
     * Obtener un producto por ID (campos simplificados)
     * GET /api/productos/{id}
     */
    public function show($id)
    {
        $producto = Producto::query()
            ->select([
                'productos.id',
                'productos.codigo',
                'productos.descripcion',
                'ubicaciones.codigo as ubicacion',
                'unidades_medida.codigo as um',
                'productos.cantidad_fisica as fisico',
                'productos.precio_unitario as pu'
            ])
            ->leftJoin('ubicaciones', 'productos.ubicacion_id', '=', 'ubicaciones.id')
            ->leftJoin('unidades_medida', 'productos.unidad_medida_id', '=', 'unidades_medida.id')
            ->where('productos.id', $id)
            ->firstOrFail();

        return response()->json($producto);
    }

    /**
     * Buscar productos (búsqueda global en múltiples campos)
     * GET /api/productos/buscar?q=rodamiento
     * 
     * Busca en: codigo, descripcion, ubicacion, um
     * Soporta coincidencias parciales (LIKE %%)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 50); // Máximo 50 resultados por defecto
        $limit = min($limit, 100); // Limitar a 100 máximo

        if (empty($query)) {
            return response()->json([
                'data' => [],
                'total' => 0,
                'message' => 'Parámetro de búsqueda vacío'
            ]);
        }

        $productos = Producto::query()
            ->select([
                'productos.id',
                'productos.codigo',
                'productos.descripcion',
                'ubicaciones.codigo as ubicacion',
                'unidades_medida.codigo as um',
                'productos.cantidad_fisica as fisico',
                'productos.precio_unitario as pu',
                // Agregar un campo de relevancia para ordenar
                DB::raw("CASE 
                    WHEN productos.codigo = '{$query}' THEN 1
                    WHEN productos.codigo LIKE '{$query}%' THEN 2
                    WHEN productos.descripcion LIKE '{$query}%' THEN 3
                    ELSE 4
                END as relevancia")
            ])
            ->leftJoin('ubicaciones', 'productos.ubicacion_id', '=', 'ubicaciones.id')
            ->leftJoin('unidades_medida', 'productos.unidad_medida_id', '=', 'unidades_medida.id')
            ->where(function ($q) use ($query) {
                $q->where('productos.codigo', 'like', "%{$query}%")
                  ->orWhere('productos.descripcion', 'like', "%{$query}%")
                  ->orWhere('ubicaciones.codigo', 'like', "%{$query}%")
                  ->orWhere('unidades_medida.codigo', 'like', "%{$query}%");
            })
            ->orderBy('relevancia')
            ->orderBy('productos.codigo')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                // Remover el campo de relevancia de la respuesta
                unset($item->relevancia);
                return $item;
            });

        return response()->json([
            'data' => $productos,
            'total' => $productos->count(),
            'query' => $query
        ]);
    }

    /**
     * Buscar productos por código específico (legacy)
     * GET /api/productos/buscar/{codigo}
     */
    public function searchByCodigo($codigo)
    {
        $productos = Producto::query()
            ->select([
                'productos.id',
                'productos.codigo',
                'productos.descripcion',
                'ubicaciones.codigo as ubicacion',
                'unidades_medida.codigo as um',
                'productos.cantidad_fisica as fisico',
                'productos.precio_unitario as pu'
            ])
            ->leftJoin('ubicaciones', 'productos.ubicacion_id', '=', 'ubicaciones.id')
            ->leftJoin('unidades_medida', 'productos.unidad_medida_id', '=', 'unidades_medida.id')
            ->where('productos.codigo', 'like', "%{$codigo}%")
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $productos,
            'total' => $productos->count()
        ]);
    }

    /**
     * Obtener estadísticas generales
     * GET /api/productos/stats
     */
    public function stats()
    {
        $stats = [
            'total_productos' => Producto::count(),
            'total_categorias' => Categoria::count(),
            'total_familias' => Familia::count(),
            'total_componentes' => Componente::count(),
            'productos_stock_bajo' => Producto::whereColumn('cantidad_fisica', '<', 'cantidad_entrada')->count(),
            'valor_total_inventario_mxn' => Producto::where('moneda', 'MXN')
                ->sum(DB::raw('precio_unitario * cantidad_fisica')),
            'valor_total_inventario_usd' => Producto::where('moneda', 'USD')
                ->sum(DB::raw('precio_unitario * cantidad_fisica')),
        ];

        return response()->json($stats);
    }

    /**
     * Obtener catálogos de referencia
     * GET /api/catalogos
     */
    public function catalogos()
    {
        $catalogos = [
            'categorias' => Categoria::select('id', 'codigo', 'nombre')->get(),
            'familias' => Familia::select('id', 'codigo', 'nombre')->get(),
            'componentes' => Componente::select('id', 'codigo', 'nombre')->get(),
        ];

        return response()->json($catalogos);
    }
}
