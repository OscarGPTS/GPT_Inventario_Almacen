<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProductos = Producto::count();
        $productosStockBajo = Producto::where('cantidad_fisica', '<', 10)->count();
        $totalMovimientos = Movimiento::whereDate('created_at', today())->count();
        
        // Productos próximos a vencer (en los próximos 30 días)
        $productosProximosVencer = Producto::whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays(30)])
            ->orderBy('fecha_vencimiento')
            ->limit(10)
            ->get();
        
        // Últimos movimientos
        $ultimosMovimientos = Movimiento::with(['producto', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Productos con stock bajo
        $productosStockBajoDetalle = Producto::where('cantidad_fisica', '<', 10)
            ->orderBy('cantidad_fisica')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalProductos',
            'productosStockBajo',
            'totalMovimientos',
            'productosProximosVencer',
            'ultimosMovimientos',
            'productosStockBajoDetalle'
        ));
    }
}
