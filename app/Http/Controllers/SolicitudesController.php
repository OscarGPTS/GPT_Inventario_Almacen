<?php

namespace App\Http\Controllers;

use App\Mail\NuevaSolicitudMail;
use App\Models\Departamento;
use App\Models\Producto;
use App\Models\Solicitud;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SolicitudesController extends Controller
{
    /**
     * Guardar nueva solicitud.
     * POST /solicitudes
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha'              => 'required|date',
            'fecha_requerida'    => 'nullable|date',
            'folio'              => 'nullable|string|max:50',
            'solicitante'        => 'required|string|max:100',
            'departamento_id'    => 'nullable|integer|exists:departamentos,id',
            'departamento_nombre'=> 'nullable|string|max:100',
            'producto_id'        => 'required|integer|exists:productos,id',
            'cantidad'           => 'required|numeric|min:0.01',
            'unidad_medida_id'   => 'nullable|integer|exists:unidades_medida,id',
            'observaciones'      => 'nullable|string',
            'estado'             => 'required|in:pendiente,aprobada,entregada,cancelada',
        ]);

        // Calcular prioridad automáticamente según los días restantes hasta la fecha requerida
        $data['prioridad'] = self::calcularPrioridad($data['fecha_requerida'] ?? null);

        // Resolver departamento: usar ID existente o crear uno nuevo por nombre
        if (empty($data['departamento_id']) && !empty($data['departamento_nombre'])) {
            $depto = Departamento::firstOrCreate(
                ['nombre' => trim($data['departamento_nombre'])]
            );
            $data['departamento_id'] = $depto->id;
        }

        // Si folio está vacío, dejarlo como null
        $data['folio'] = $data['folio'] ? trim($data['folio']) : null;

        // Registrar usuario autenticado
        $data['usuario_registro_id'] = auth()->id();

        // Eliminar campo auxiliar que no es columna de BD
        unset($data['departamento_nombre']);

        $solicitud = Solicitud::create($data);

        // Notificar a almacenistas/admins sobre la nueva solicitud
        try {
            $solicitud->load(['producto', 'unidadMedida', 'departamento', 'usuarioRegistro']);
            $destinatarios = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'admin_almacen', 'almacenista']))
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->values()
                ->toArray();
            if (!empty($destinatarios)) {
                Mail::to($destinatarios)->send(new NuevaSolicitudMail($solicitud));
            }
        } catch (\Exception $e) {
            // El fallo del correo no debe interrumpir el flujo
            \Log::error('Error enviando correo de nueva solicitud: ' . $e->getMessage());
        }

        return redirect()
            ->route('reportes.requisiciones')
            ->with('success', 'Solicitud registrada correctamente.');
    }

    /**
     * Cambiar estado de una solicitud (AJAX inline).
     * PATCH /solicitudes/{id}/cambiar-estado
     */
    public function updateEstado(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobada,entregada,cancelada',
        ]);

        $nuevo    = $request->estado;
        $anterior = $solicitud->estado;

        if ($nuevo === $anterior) {
            return response()->json(['ok' => true, 'estado' => $nuevo]);
        }

        $solicitud->estado = $nuevo;
        $solicitud->save();

        // ── Ajustar inventario según la transición ──────────────────────────
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
                // Si estaba aprobada, liberar lo que estaba apartado
                if ($anterior === 'aprobada') {
                    $producto->cantidad_apartada = max(0, (float)$producto->cantidad_apartada - $cantidad);
                }
                $producto->save();

                \App\Models\Movimiento::create([
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

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'estado' => $nuevo]);
        }

        return back()->with('success', 'Estado actualizado.');
    }

    private static function calcularPrioridad(?string $fechaRequerida): string
    {
        if (!$fechaRequerida) return 'normal';
        $dias = now()->startOfDay()->diffInDays(
            \Carbon\Carbon::parse($fechaRequerida)->startOfDay(),
            false
        );
        if ($dias <= 2)  return 'urgente';
        if ($dias <= 7)  return 'alta';
        if ($dias <= 14) return 'normal';
        return 'baja';
    }
}
