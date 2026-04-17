<?php

namespace App\Http\Controllers;

use App\Models\NoConformidad;
use Illuminate\Http\Request;

class NoConformidadController extends Controller
{
    /**
     * Registrar nueva no conformidad.
     * POST /no-conformidades
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|numeric|min:0',
            'descripcion' => 'required|string|max:2000',
        ]);

        $nc = NoConformidad::create([
            'producto_id'     => $request->producto_id,
            'cantidad'        => $request->cantidad,
            'descripcion'     => $request->descripcion,
            'estatus'         => 'abierta',
            'fecha_deteccion' => now()->toDateString(),
            'registrado_por'  => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'id' => $nc->id]);
        }

        return redirect()->route('reportes.no_conforme')->with('success', 'No conformidad registrada correctamente.');
    }

    /**
     * Actualizar estatus / resolución.
     * PATCH /no-conformidades/{noConformidad}
     */
    public function update(Request $request, NoConformidad $noConformidad)
    {
        $request->validate([
            'estatus'    => 'sometimes|in:abierta,en_proceso,resuelta,cerrada',
            'resolucion' => 'nullable|string|max:2000',
            'descripcion'=> 'sometimes|string|max:2000',
        ]);

        $data = $request->only(['estatus', 'resolucion', 'descripcion']);

        // Si se marca como resuelta o cerrada, registrar fecha y quién resolvió
        if (isset($data['estatus']) && in_array($data['estatus'], ['resuelta', 'cerrada'])) {
            $data['fecha_resolucion'] = $data['fecha_resolucion'] ?? now()->toDateString();
            $data['resuelto_por']     = $data['resuelto_por'] ?? auth()->id();
        }

        $noConformidad->update($data);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'nc' => $noConformidad->fresh()]);
        }

        return redirect()->route('reportes.no_conforme')->with('success', 'No conformidad actualizada.');
    }

    /**
     * Eliminar no conformidad.
     * DELETE /no-conformidades/{noConformidad}
     */
    public function destroy(Request $request, NoConformidad $noConformidad)
    {
        $noConformidad->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('reportes.no_conforme')->with('success', 'No conformidad eliminada.');
    }
}
