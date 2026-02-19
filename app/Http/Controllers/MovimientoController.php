<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = Movimiento::with(['producto', 'usuario', 'solicitud']);

        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('producto', function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $movimientos = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('movimientos.index', compact('movimientos'));
    }

    public function porProducto(Producto $producto)
    {
        $movimientos = Movimiento::where('producto_id', $producto->id)
            ->with(['usuario', 'solicitud'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('movimientos.producto', compact('producto', 'movimientos'));
    }
}
