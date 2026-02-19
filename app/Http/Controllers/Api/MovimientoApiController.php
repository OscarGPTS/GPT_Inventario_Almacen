<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Http\Request;

class MovimientoApiController extends Controller
{
    /**
     * Listar movimientos con paginación
     * GET /api/movimientos
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $query = Movimiento::with([
            'producto',
            'usuario',
            'solicitud'
        ]);

        // Filtros
        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Simplificar respuesta de paginación
        return response()->json([
            'data' => $movimientos->items(),
            'current_page' => $movimientos->currentPage(),
            'last_page' => $movimientos->lastPage(),
            'per_page' => $movimientos->perPage(),
            'total' => $movimientos->total(),
        ]);
    }

    /**
     * Obtener movimientos de un producto específico
     * GET /api/productos/{producto_id}/movimientos
     */
    public function porProducto($producto_id, Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $producto = Producto::findOrFail($producto_id);

        $movimientos = Movimiento::with(['usuario', 'solicitud'])
            ->where('producto_id', $producto_id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'producto' => [
                'id' => $producto->id,
                'codigo' => $producto->codigo,
                'descripcion' => $producto->descripcion,
            ],
            'movimientos' => [
                'data' => $movimientos->items(),
                'current_page' => $movimientos->currentPage(),
                'last_page' => $movimientos->lastPage(),
                'per_page' => $movimientos->perPage(),
                'total' => $movimientos->total(),
            ]
        ]);
    }

    /**
     * Obtener estadísticas de movimientos
     * GET /api/movimientos/stats
     */
    public function stats(Request $request)
    {
        $query = Movimiento::query();

        // Filtrar por fecha si se proporciona
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $stats = [
            'total_movimientos' => $query->count(),
            'entradas' => (clone $query)->where('tipo_movimiento', 'entrada')->count(),
            'salidas' => (clone $query)->where('tipo_movimiento', 'salida')->count(),
            'ajustes' => (clone $query)->where('tipo_movimiento', 'ajuste')->count(),
            'transferencias' => (clone $query)->where('tipo_movimiento', 'transferencia')->count(),
            'movimientos_hoy' => Movimiento::whereDate('created_at', today())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Obtener un movimiento por ID
     * GET /api/movimientos/{id}
     */
    public function show($id)
    {
        $movimiento = Movimiento::with([
            'producto',
            'usuario',
            'solicitud'
        ])->findOrFail($id);

        return response()->json($movimiento);
    }
}
