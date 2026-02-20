<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class CatalogoApiController extends Controller
{
    /**
     * Buscar departamentos.
     * GET /api/v1/departamentos/buscar?q=
     */
    public function buscarDepartamentos(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Departamento::select('id', 'nombre')
            ->orderBy('nombre');

        if ($q !== '') {
            $query->where('nombre', 'like', "%{$q}%");
        }

        $items = $query->limit(20)->get()->map(fn($d) => [
            'id'    => $d->id,
            'label' => $d->nombre,
        ]);

        return response()->json(['data' => $items, 'total' => $items->count()]);
    }

    /**
     * Buscar unidades de medida.
     * GET /api/v1/unidades-medida/buscar?q=
     */
    public function buscarUnidades(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = UnidadMedida::select('id', 'codigo', 'nombre')
            ->orderBy('codigo');

        if ($q !== '') {
            $query->where('codigo', 'like', "%{$q}%")
                  ->orWhere('nombre', 'like', "%{$q}%");
        }

        $items = $query->limit(20)->get()->map(fn($u) => [
            'id'    => $u->id,
            'label' => $u->nombre ? "{$u->codigo} — {$u->nombre}" : $u->codigo,
            'codigo'=> $u->codigo,
        ]);

        return response()->json(['data' => $items, 'total' => $items->count()]);
    }
}
