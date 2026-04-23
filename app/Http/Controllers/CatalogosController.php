<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Componente;
use App\Models\Categoria;
use App\Models\Familia;
use App\Models\UnidadMedida;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogosController extends Controller
{
    /**
     * Mapa de catálogos disponibles.
     */
    private function catalogs(): array
    {
        return [
            'componentes'    => ['model' => Componente::class,  'label' => 'Componente',      'table' => 'componentes',     'max_codigo' => 1],
            'categorias'     => ['model' => Categoria::class,   'label' => 'Categoría',       'table' => 'categorias',      'max_codigo' => 10],
            'familias'       => ['model' => Familia::class,     'label' => 'Familia',         'table' => 'familias',        'max_codigo' => 10],
            'unidades_medida'=> ['model' => UnidadMedida::class,'label' => 'Unidad de Medida','table' => 'unidades_medida', 'max_codigo' => 10],
            'ubicaciones'    => ['model' => Ubicacion::class,   'label' => 'Ubicación',       'table' => 'ubicaciones',     'max_codigo' => 20],
        ];
    }

    /**
     * Vista principal de catálogos.
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user->hasPermission('catalogos-read') && !$user->hasRole('admin')) {
            abort(403, 'No tienes permisos para acceder a los catálogos.');
        }

        $catalogs = $this->catalogs();
        $data = [];
        foreach ($catalogs as $key => $cfg) {
            $data[$key] = $cfg['model']::orderBy('codigo')->get();
        }

        $logs = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('catalogos.index', compact('data', 'catalogs', 'logs'));
    }

    /**
     * Crear registro en un catálogo.
     */
    public function store(Request $request, string $catalogo)
    {
        $user = auth()->user();
        if (!$user->hasPermission('catalogos-create') && !$user->hasRole('admin')) {
            abort(403);
        }

        $cfg = $this->catalogs()[$catalogo] ?? null;
        if (!$cfg) {
            abort(404);
        }

        $request->validate([
            'codigo'      => ['required', 'string', 'max:' . $cfg['max_codigo'], Rule::unique($cfg['table'], 'codigo')],
            'descripcion' => 'required|string|max:100',
        ]);

        $record = $cfg['model']::create($request->only('codigo', 'descripcion'));

        AuditLog::registrar('created', $cfg['table'], $record->id, null, $record->toArray());

        return redirect()->route('catalogos.index')
            ->with('success', "{$cfg['label']} «{$record->codigo}» creado exitosamente.");
    }

    /**
     * Actualizar registro de un catálogo.
     */
    public function update(Request $request, string $catalogo, int $id)
    {
        $user = auth()->user();
        if (!$user->hasPermission('catalogos-update') && !$user->hasRole('admin')) {
            abort(403);
        }

        $cfg = $this->catalogs()[$catalogo] ?? null;
        if (!$cfg) {
            abort(404);
        }

        $record = $cfg['model']::findOrFail($id);

        $request->validate([
            'codigo'      => ['required', 'string', 'max:' . $cfg['max_codigo'], Rule::unique($cfg['table'], 'codigo')->ignore($record->id)],
            'descripcion' => 'required|string|max:100',
        ]);

        $old = $record->toArray();
        $record->update($request->only('codigo', 'descripcion'));

        AuditLog::registrar('updated', $cfg['table'], $record->id, $old, $record->fresh()->toArray());

        return redirect()->route('catalogos.index')
            ->with('success', "{$cfg['label']} «{$record->codigo}» actualizado.");
    }

    /**
     * Eliminar registro de un catálogo (solo admin).
     */
    public function destroy(string $catalogo, int $id)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Solo los administradores pueden eliminar catálogos.');
        }

        $cfg = $this->catalogs()[$catalogo] ?? null;
        if (!$cfg) {
            abort(404);
        }

        $record = $cfg['model']::findOrFail($id);
        $old = $record->toArray();

        // Verificar si el catálogo está en uso
        $inUse = \App\Models\Producto::where($this->foreignKey($catalogo), $id)->exists();
        if ($inUse) {
            return redirect()->route('catalogos.index')
                ->with('error', "No se puede eliminar «{$record->codigo}» porque está en uso por uno o más productos.");
        }

        $record->delete();

        AuditLog::registrar('deleted', $cfg['table'], $id, $old, null);

        return redirect()->route('catalogos.index')
            ->with('success', "{$cfg['label']} «{$old['codigo']}» eliminado.");
    }

    /**
     * Devuelve la llave foránea correspondiente en productos.
     */
    private function foreignKey(string $catalogo): string
    {
        return match ($catalogo) {
            'componentes'     => 'componente_id',
            'categorias'      => 'categoria_id',
            'familias'        => 'familia_id',
            'unidades_medida' => 'unidad_medida_id',
            'ubicaciones'     => 'ubicacion_id',
            default           => 'id',
        };
    }
}
