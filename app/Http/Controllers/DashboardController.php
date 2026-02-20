<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProductos = Producto::count();
        $productosStockBajo = Producto::where('cantidad_fisica', '<', 10)->count();
        $totalMovimientos = Movimiento::whereDate('created_at', today())->count();

        // Tabla principal con todos los productos
        $query = Producto::with(['componente', 'categoria', 'familia', 'unidadMedida', 'ubicacion']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('observaciones', 'like', "%{$search}%")
                  ->orWhere('factura', 'like', "%{$search}%");
            });
        }

        $productos = $query->orderBy('codigo')->paginate(50)->withQueryString();

        return view('dashboard', compact(
            'totalProductos',
            'productosStockBajo',
            'totalMovimientos',
            'productos'
        ));
    }
}
