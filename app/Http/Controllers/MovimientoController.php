<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = Movimiento::with(['producto', 'usuario', 'solicitud', 'ticket', 'ticket.user']);

        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        if ($request->filled('fuente')) {
            $query->where('fuente', $request->fuente);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                  ->orWhere('referencia', 'like', "%{$search}%")
                  ->orWhereHas('producto', function ($pq) use ($search) {
                      $pq->where('codigo', 'like', "%{$search}%")
                         ->orWhere('descripcion', 'like', "%{$search}%");
                  });
            });
        }

        // Stats globales (sin filtros de paginación)
        $statsQuery = Movimiento::query();
        $stats = [
            'total'         => $statsQuery->count(),
            'entrada'       => (clone $statsQuery)->where('tipo_movimiento', 'entrada')->count(),
            'salida'        => (clone $statsQuery)->where('tipo_movimiento', 'salida')->count(),
            'transferencia' => (clone $statsQuery)->where('tipo_movimiento', 'transferencia')->count(),
            'ajuste'        => (clone $statsQuery)->where('tipo_movimiento', 'ajuste')->count(),
            'excel'         => (clone $statsQuery)->where('fuente', 'excel')->count(),
            'solicitud_mat' => (clone $statsQuery)->where('fuente', 'solicitud_material')->count(),
            'solicitud_mov' => (clone $statsQuery)->where('fuente', 'solicitud_movimiento')->count(),
        ];

        $movimientos = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('movimientos.index', compact('movimientos', 'stats'));
    }

    public function porProducto(Producto $producto)
    {
        $movimientos = Movimiento::where('producto_id', $producto->id)
            ->with(['usuario', 'solicitud', 'ticket'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('movimientos.producto', compact('producto', 'movimientos'));
    }
}
