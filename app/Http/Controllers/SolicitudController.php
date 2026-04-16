<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Producto;
use App\Models\Departamento;
use App\Models\UnidadMedida;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $query = Solicitud::with(['producto', 'departamento', 'usuarioRegistro', 'unidadMedida']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('solicitante', 'like', "%{$search}%");
            });
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $productos = Producto::all();
        $departamentos = Departamento::all();
        $unidadesMedida = UnidadMedida::all();

        return view('solicitudes.create', compact('productos', 'departamentos', 'unidadesMedida'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folio' => 'required|string|max:50|unique:solicitudes,folio',
            'fecha' => 'required|date',
            'solicitante' => 'required|string|max:100',
            'departamento_id' => 'required|exists:departamentos,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'observaciones' => 'nullable|string',
        ]);

        $validated['usuario_registro_id'] = auth()->id();
        $validated['estado'] = 'pendiente';

        $solicitud = Solicitud::create($validated);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada exitosamente.');
    }

    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['producto', 'departamento', 'usuarioRegistro', 'unidadMedida', 'movimientos']);
        return view('solicitudes.show', compact('solicitud'));
    }

    public function edit(Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'pendiente') {
            return redirect()->route('solicitudes.show', $solicitud)
                ->with('error', 'Solo se pueden editar solicitudes pendientes.');
        }

        $productos = Producto::all();
        $departamentos = Departamento::all();
        $unidadesMedida = UnidadMedida::all();

        return view('solicitudes.edit', compact('solicitud', 'productos', 'departamentos', 'unidadesMedida'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'pendiente') {
            return redirect()->route('solicitudes.show', $solicitud)
                ->with('error', 'Solo se pueden editar solicitudes pendientes.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'solicitante' => 'required|string|max:100',
            'departamento_id' => 'required|exists:departamentos,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'observaciones' => 'nullable|string',
        ]);

        $solicitud->update($validated);

        return redirect()->route('solicitudes.show', $solicitud)->with('success', 'Solicitud actualizada exitosamente.');
    }

    public function destroy(Solicitud $solicitud)
    {
        if ($solicitud->estado !== 'pendiente') {
            return redirect()->route('solicitudes.index')
                ->with('error', 'Solo se pueden eliminar solicitudes pendientes.');
        }

        $solicitud->delete();
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada exitosamente.');
    }

    public function cambiarEstado(Request $request, Solicitud $solicitud)
    {
        $validated = $request->validate([
            'estado' => 'required|in:pendiente,aprobada,entregada,cancelada',
        ]);

        $nuevo    = $validated['estado'];
        $anterior = $solicitud->estado;

        $solicitud->estado = $nuevo;
        $solicitud->save();

        $producto = $solicitud->producto;
        $cantidad = (float) $solicitud->cantidad;

        if ($producto && $cantidad > 0) {
            // Reservar al aprobar
            if ($nuevo === 'aprobada' && $anterior !== 'aprobada') {
                $producto->cantidad_apartada = max(0, (float)$producto->cantidad_apartada + $cantidad);
                $producto->save();
            }

            // Liberar reserva al cancelar o regresar a pendiente desde aprobada
            if (in_array($nuevo, ['cancelada', 'pendiente']) && $anterior === 'aprobada') {
                $producto->cantidad_apartada = max(0, (float)$producto->cantidad_apartada - $cantidad);
                $producto->save();
            }

            // Entregar: descontar stock y liberar reserva
            if ($nuevo === 'entregada' && $anterior !== 'entregada') {
                $cantidadAnterior = (float) $producto->cantidad_fisica;
                $producto->cantidad_salida  = (float)$producto->cantidad_salida  + $cantidad;
                $producto->cantidad_fisica  = max(0, $cantidadAnterior - $cantidad);
                $producto->fecha_salida     = now();
                if ($anterior === 'aprobada') {
                    $producto->cantidad_apartada = max(0, (float)$producto->cantidad_apartada - $cantidad);
                }
                $producto->save();

                Movimiento::create([
                    'producto_id'      => $producto->id,
                    'usuario_id'       => auth()->id(),
                    'tipo_movimiento'  => 'salida',
                    'cantidad'         => $cantidad,
                    'cantidad_anterior'=> $cantidadAnterior,
                    'cantidad_nueva'   => $producto->cantidad_fisica,
                    'solicitud_id'     => $solicitud->id,
                    'descripcion'      => "Salida por solicitud {$solicitud->folio} - {$solicitud->solicitante}",
                    'referencia'       => $solicitud->folio,
                ]);
            }
        }

        return redirect()->route('solicitudes.show', $solicitud)
            ->with('success', 'Estado de la solicitud actualizado exitosamente.');
    }
}
