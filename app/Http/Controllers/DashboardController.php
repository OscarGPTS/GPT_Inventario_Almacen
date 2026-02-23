<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\Solicitud;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Estadísticas generales
        $totalProductos = Producto::count();
        $productosStockBajo = Producto::where('cantidad_fisica', '<', 10)->where('cantidad_fisica', '>', 0)->count();
        $totalMovimientos = Movimiento::whereDate('created_at', today())->count();
        $totalSolicitudes = Solicitud::count();
        $solicitudesPendientes = Solicitud::where('estado', 'pendiente')->count();
        
        // Productos no conformes
        $productosNoConformes = Producto::where('no_conforme', true)->count();
        
        // Valor total del inventario
        $valorInventario = Producto::selectRaw('SUM(precio_unitario * COALESCE(cantidad_fisica, 0)) as total')
            ->value('total') ?? 0;
        
        // Productos categoría Barras
        $categoriaBarras = Categoria::where('codigo', 'BR')->first();
        $productosBarras = $categoriaBarras ? Producto::where('categoria_id', $categoriaBarras->id)->count() : 0;
        
        // Entradas recientes (últimos 7 días)
        $entradasRecientes = Producto::where('fecha_entrada', '>=', now()->subDays(7))->count();
        
        // Productos próximos a vencer (30 días)
        $productosProximosVencer = Producto::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->where('fecha_vencimiento', '>=', now())
            ->count();
        
        // Distribución por categorías (top 5)
        $topCategorias = Producto::select('categoria_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('categoria_id')
            ->groupBy('categoria_id')
            ->with('categoria')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        
        // Movimientos de la semana
        $movimientosSemana = Movimiento::where('created_at', '>=', now()->subDays(7))->count();
        
        // Últimas requisiciones (10 más recientes)
        $ultimasRequisiciones = Solicitud::with(['producto', 'departamento', 'usuarioRegistro'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Últimos movimientos (10 más recientes)
        $ultimosMovimientos = Movimiento::with(['producto', 'usuario', 'solicitud'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

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
            'totalSolicitudes',
            'solicitudesPendientes',
            'productosNoConformes',
            'valorInventario',
            'productosBarras',
            'entradasRecientes',
            'productosProximosVencer',
            'topCategorias',
            'movimientosSemana',
            'ultimasRequisiciones',
            'ultimosMovimientos',
            'productos'
        ));
    }
}
