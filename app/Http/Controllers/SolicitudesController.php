<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Producto;
use App\Models\Solicitud;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

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
            'prioridad'          => 'required|in:urgente,alta,normal,baja',
        ]);

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

        Solicitud::create($data);

        return redirect()
            ->route('reportes.requisiciones')
            ->with('success', 'Solicitud registrada correctamente.');
    }

    /**
     * Cambiar estado de una solicitud.
     * PATCH /solicitudes/{id}/estado
     */
    public function updateEstado(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobada,entregada,cancelada',
        ]);

        $solicitud->update(['estado' => $request->estado]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'estado' => $solicitud->estado]);
        }

        return back()->with('success', 'Estado actualizado.');
    }
}
