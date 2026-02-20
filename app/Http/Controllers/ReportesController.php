<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    /**
     * Entradas — tabla completa de productos (19 columnas)
     */
    public function entradas(Request $request)
    {
        $query = Producto::with(['componente', 'categoria', 'familia', 'unidadMedida', 'ubicacion'])
            ->orderBy('codigo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%{$s}%")
                  ->orWhere('descripcion', 'like', "%{$s}%")
                  ->orWhere('factura', 'like', "%{$s}%")
                  ->orWhere('observaciones', 'like', "%{$s}%")
                  ->orWhereHas('ubicacion', fn($uq) => $uq->where('codigo', 'like', "%{$s}%"));
            });
        }

        $registros = $query->paginate(50)->withQueryString();
        return view('reportes.entradas', compact('registros'));
    }

    /**
     * Concentrado de Requisiciones del Centro de Operaciones
     */
    public function requisiciones(Request $request)
    {
        $query = Solicitud::with(['departamento', 'producto', 'unidadMedida', 'usuarioRegistro'])
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('folio', 'like', "%{$s}%")
                  ->orWhere('solicitante', 'like', "%{$s}%")
                  ->orWhereHas('producto', fn($pq) => $pq->where('codigo', 'like', "%{$s}%")->orWhere('descripcion', 'like', "%{$s}%"))
                  ->orWhereHas('departamento', fn($dq) => $dq->where('nombre', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        $registros = $query->paginate(50)->withQueryString();
        return view('reportes.requisiciones', compact('registros'));
    }

    /**
     * Barras
     */
    public function barras(Request $request)
    {
        $query = Producto::with(['unidadMedida', 'ubicacion'])
            ->select('productos.*',
                DB::raw('(productos.cantidad_entrada - productos.cantidad_fisica) as diferencia')
            )
            ->orderBy('codigo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%{$s}%")
                  ->orWhere('descripcion', 'like', "%{$s}%")
                  ->orWhere('observaciones', 'like', "%{$s}%")
                  ->orWhere('factura', 'like', "%{$s}%");
            });
        }

        $registros = $query->paginate(50)->withQueryString();
        return view('reportes.barras', compact('registros'));
    }

    /**
     * Resguardo de Almacén
     */
    public function resguardo(Request $request)
    {
        $query = Producto::with(['unidadMedida', 'ubicacion'])
            ->whereNotNull('cantidad_entrada')
            ->orderBy('fecha_entrada', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('descripcion', 'like', "%{$s}%")
                  ->orWhere('codigo', 'like', "%{$s}%")
                  ->orWhere('observaciones', 'like', "%{$s}%");
            });
        }

        $registros = $query->paginate(50)->withQueryString();
        return view('reportes.resguardo', compact('registros'));
    }

    /**
     * No Conforme
     */
    public function noConforme(Request $request)
    {
        $query = Producto::with(['componente', 'categoria', 'familia', 'unidadMedida', 'ubicacion'])
            ->orderBy('codigo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%{$s}%")
                  ->orWhere('descripcion', 'like', "%{$s}%")
                  ->orWhere('observaciones', 'like', "%{$s}%")
                  ->orWhere('factura', 'like', "%{$s}%");
            });
        }

        $registros = $query->paginate(50)->withQueryString();
        return view('reportes.no_conforme', compact('registros'));
    }

    /**
     * Inventario General
     */
    public function inventarioGeneral(Request $request)
    {
        $query = Producto::with(['ubicacion', 'unidadMedida'])
            ->select(
                'productos.id',
                'productos.codigo',
                'productos.descripcion',
                'productos.ubicacion_id',
                'productos.unidad_medida_id',
                DB::raw('SUM(productos.cantidad_fisica) as sum_fisico'),
                DB::raw('SUM(productos.precio_unitario) as sum_pu')
            )
            ->groupBy('productos.id', 'productos.codigo', 'productos.descripcion', 'productos.ubicacion_id', 'productos.unidad_medida_id')
            ->orderBy('codigo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('productos.codigo', 'like', "%{$s}%")
                  ->orWhere('productos.descripcion', 'like', "%{$s}%");
            });
        }

        $registros = $query->paginate(50)->withQueryString();

        $totales = [
            'sum_fisico' => Producto::sum('cantidad_fisica'),
            'sum_pu'     => Producto::sum('precio_unitario'),
        ];

        return view('reportes.inventario_general', compact('registros', 'totales'));
    }
}
