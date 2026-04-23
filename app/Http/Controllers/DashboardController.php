<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\Solicitud;
use App\Models\Categoria;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user    = Auth::user();
        $esAdmin = $user->hasRole(['admin', 'admin_almacen']);

        // ── Inventario ──────────────────────────────────────────────────────
        $totalProductos         = Producto::count();
        $productosStockBajo     = Producto::where('cantidad_fisica', '<', 10)->where('cantidad_fisica', '>', 0)->count();
        $productosNoConformes   = Producto::where('no_conforme', true)->count();
        $productosProximosVencer = Producto::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->where('fecha_vencimiento', '>=', now())
            ->count();
        $entradasRecientes      = Producto::where('fecha_entrada', '>=', now()->subDays(7))->count();
        $valorInventario        = Producto::selectRaw('SUM(precio_unitario * COALESCE(cantidad_fisica, 0)) as total')
            ->value('total') ?? 0;

        // ── Movimientos ──────────────────────────────────────────────────────
        $totalMovimientos   = Movimiento::whereDate('created_at', today())->count();
        $movimientosSemana  = Movimiento::where('created_at', '>=', now()->subDays(7))->count();
        $ultimosMovimientos = Movimiento::with(['producto', 'usuario', 'solicitud'])
            ->orderByDesc('created_at')->limit(8)->get();

        // ── Requisiciones (Solicitud) ─────────────────────────────────────────
        $totalSolicitudes      = Solicitud::count();
        $solicitudesPendientes = Solicitud::where('estado', 'pendiente')->count();
        $ultimasRequisiciones  = Solicitud::with(['producto', 'departamento', 'usuarioRegistro'])
            ->orderByDesc('created_at')->limit(8)->get();

        // ── Tickets (Solicitudes de Movimiento) ──────────────────────────────
        $ticketsPendientes  = Ticket::where('status', 'pendiente')->count();
        $ticketsEnProceso   = Ticket::where('status', 'en_proceso')->count();
        $ticketsFinalizados = Ticket::where('status', 'finalizado')->count();
        $ticketsCancelados  = Ticket::where('status', 'cancelado')->count();

        // Tickets pendientes de asignación (para panel de admin)
        $ticketsPendientesList = Ticket::with(['user', 'producto'])
            ->where('status', 'pendiente')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Tickets en proceso (para panel de admin)
        $ticketsEnProcesoList = Ticket::with(['user', 'producto', 'assignedTo'])
            ->where('status', 'en_proceso')
            ->orderByDesc('assigned_at')
            ->limit(10)
            ->get();

        // Almacenistas para asignación (solo admin ve esto)
        $almacenUsers = $esAdmin
            ? User::whereHasRole(['almacenista', 'admin_almacen'])->orderBy('name')->get()
            : collect();

        // ── Categorías ────────────────────────────────────────────────────────
        $topCategorias = Producto::select('categoria_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('categoria_id')
            ->groupBy('categoria_id')
            ->with('categoria')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Tabla de búsqueda rápida ──────────────────────────────────────────
        $query = Producto::with(['componente', 'categoria', 'familia', 'unidadMedida', 'ubicacion']);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%{$s}%")
                  ->orWhere('descripcion', 'like', "%{$s}%")
                  ->orWhere('observaciones', 'like', "%{$s}%")
                  ->orWhere('factura', 'like', "%{$s}%");
            });
        }
        $productos = $query->orderBy('codigo')->paginate(50)->withQueryString();

        return view('dashboard', compact(
            'totalProductos', 'productosStockBajo', 'productosNoConformes',
            'productosProximosVencer', 'entradasRecientes', 'valorInventario',
            'totalMovimientos', 'movimientosSemana', 'ultimosMovimientos',
            'totalSolicitudes', 'solicitudesPendientes', 'ultimasRequisiciones',
            'ticketsPendientes', 'ticketsEnProceso', 'ticketsFinalizados', 'ticketsCancelados',
            'ticketsPendientesList', 'ticketsEnProcesoList',
            'almacenUsers', 'esAdmin',
            'topCategorias', 'productos'
        ));
    }

    /**
     * Asignación rápida de ticket desde el dashboard
     */
    public function assignTicket(Request $request, Ticket $ticket)
    {
        if (!Auth::user()->hasRole(['admin', 'admin_almacen'])) {
            abort(403);
        }

        $request->validate(['assigned_to' => 'required|exists:users,id']);

        $assignee = User::findOrFail($request->assigned_to);
        $ticket->assignTo($assignee);

        return back()->with('success', "Ticket #{$ticket->id} asignado a {$assignee->name}.");
    }
}
