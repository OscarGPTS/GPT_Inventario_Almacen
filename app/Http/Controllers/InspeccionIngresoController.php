<?php

namespace App\Http\Controllers;

use App\Models\InspeccionIngreso;
use Illuminate\Http\Request;

class InspeccionIngresoController extends Controller
{
    public function index(Request $request)
    {
        $query = InspeccionIngreso::with('registradoPor')->latest();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('folio', 'like', "%{$buscar}%")
                  ->orWhere('requisicion', 'like', "%{$buscar}%")
                  ->orWhere('orden_compra', 'like', "%{$buscar}%")
                  ->orWhere('departamento_solicitante', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('resultado')) {
            $query->where('resultado_calidad', $request->resultado)
                  ->orWhere('resultado_solicitante', $request->resultado);
        }

        $inspecciones = $query->paginate(20)->withQueryString();

        return view('inspecciones.index', compact('inspecciones'));
    }

    public function create()
    {
        $user = auth()->user();
        return view('inspecciones.create', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_recepcion'              => 'nullable|date',
            'requisicion'                  => 'nullable|string|max:100',
            'orden_compra'                 => 'nullable|string|max:100',
            'tipo_documento'               => 'nullable|in:DN,NP,CP,Otro',
            'tipo_documento_otro'          => 'nullable|string|max:100',
            'requiere_ctrl_calidad'        => 'nullable|boolean',
            'no_solicitud'                 => 'nullable|string|max:100',
            'fecha_ingreso_inventario'     => 'nullable|date',
            'fecha_inspeccion_solicitante' => 'nullable|date',
            'inspeccionado_solicitante'    => 'nullable|string|max:200',
            'departamento_solicitante'     => 'nullable|string|max:100',
            'observaciones_solicitante'    => 'nullable|string',
            'resultado_solicitante'        => 'nullable|in:no_conforme,conforme,a_revision',
            'fecha_inspeccion_calidad'     => 'nullable|date',
            'inspeccionado_calidad'        => 'nullable|string|max:200',
            'departamento_calidad'         => 'nullable|string|max:100',
            'observaciones_calidad'        => 'nullable|string',
            'resultado_calidad'            => 'nullable|in:no_conforme,conforme,a_revision',
        ]);

        $validated['registrado_por']       = auth()->id();
        $validated['folio']                = InspeccionIngreso::generarFolio();
        $validated['requiere_ctrl_calidad'] = $request->boolean('requiere_ctrl_calidad');

        $inspeccion = InspeccionIngreso::create($validated);

        return redirect()->route('inspecciones.show', $inspeccion)
            ->with('success', 'Inspección registrada con folio ' . $inspeccion->folio . '.');
    }

    public function show(InspeccionIngreso $inspeccion)
    {
        return view('inspecciones.show', compact('inspeccion'));
    }

    public function edit(InspeccionIngreso $inspeccion)
    {
        return view('inspecciones.edit', compact('inspeccion'));
    }

    public function update(Request $request, InspeccionIngreso $inspeccion)
    {
        $validated = $request->validate([
            'fecha_recepcion'              => 'nullable|date',
            'requisicion'                  => 'nullable|string|max:100',
            'orden_compra'                 => 'nullable|string|max:100',
            'tipo_documento'               => 'nullable|in:DN,NP,CP,Otro',
            'tipo_documento_otro'          => 'nullable|string|max:100',
            'requiere_ctrl_calidad'        => 'nullable|boolean',
            'no_solicitud'                 => 'nullable|string|max:100',
            'fecha_ingreso_inventario'     => 'nullable|date',
            'fecha_inspeccion_solicitante' => 'nullable|date',
            'inspeccionado_solicitante'    => 'nullable|string|max:200',
            'departamento_solicitante'     => 'nullable|string|max:100',
            'observaciones_solicitante'    => 'nullable|string',
            'resultado_solicitante'        => 'nullable|in:no_conforme,conforme,a_revision',
            'fecha_inspeccion_calidad'     => 'nullable|date',
            'inspeccionado_calidad'        => 'nullable|string|max:200',
            'departamento_calidad'         => 'nullable|string|max:100',
            'observaciones_calidad'        => 'nullable|string',
            'resultado_calidad'            => 'nullable|in:no_conforme,conforme,a_revision',
        ]);

        $validated['requiere_ctrl_calidad'] = $request->boolean('requiere_ctrl_calidad');

        $inspeccion->update($validated);

        return redirect()->route('inspecciones.show', $inspeccion)
            ->with('success', 'Inspección actualizada correctamente.');
    }

    public function destroy(InspeccionIngreso $inspeccion)
    {
        $folio = $inspeccion->folio;
        $inspeccion->delete();

        return redirect()->route('inspecciones.index')
            ->with('success', 'Inspección ' . $folio . ' eliminada.');
    }
}
