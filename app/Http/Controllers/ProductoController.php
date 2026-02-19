<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Componente;
use App\Models\Categoria;
use App\Models\Familia;
use App\Models\UnidadMedida;
use App\Models\Ubicacion;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['componente', 'categoria', 'familia', 'unidadMedida', 'ubicacion']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('familia_id')) {
            $query->where('familia_id', $request->familia_id);
        }

        $productos = $query->paginate(20);
        
        $categorias = Categoria::all();
        $familias = Familia::all();

        return view('productos.index', compact('productos', 'categorias', 'familias'));
    }

    public function create()
    {
        $componentes = Componente::all();
        $categorias = Categoria::all();
        $familias = Familia::all();
        $unidadesMedida = UnidadMedida::all();
        $ubicaciones = Ubicacion::all();

        return view('productos.create', compact(
            'componentes',
            'categorias',
            'familias',
            'unidadesMedida',
            'ubicaciones'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'componente_id' => 'required|exists:componentes,id',
            'categoria_id' => 'required|exists:categorias,id',
            'familia_id' => 'required|exists:familias,id',
            'consecutivo' => 'required|string|max:10',
            'descripcion' => 'required|string',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'ubicacion_id' => 'nullable|exists:ubicaciones,id',
            'cantidad_entrada' => 'nullable|integer|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',
            'moneda' => 'required|in:MXN,USD',
            'factura' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'hoja_seguridad' => 'nullable|string|max:255',
        ]);

        // Generar código automáticamente
        $componente = Componente::find($request->componente_id);
        $categoria = Categoria::find($request->categoria_id);
        $familia = Familia::find($request->familia_id);
        
        $codigo = Producto::generarCodigo(
            $componente->codigo,
            $categoria->codigo,
            $familia->codigo,
            $request->consecutivo
        );

        $validated['codigo'] = $codigo;
        $validated['cantidad_fisica'] = $request->cantidad_entrada ?? 0;
        $validated['fecha_entrada'] = now();

        $producto = Producto::create($validated);

        // Registrar movimiento de entrada inicial
        if ($producto->cantidad_entrada > 0) {
            Movimiento::create([
                'producto_id' => $producto->id,
                'usuario_id' => auth()->id(),
                'tipo_movimiento' => 'entrada',
                'cantidad' => $producto->cantidad_entrada,
                'cantidad_anterior' => 0,
                'cantidad_nueva' => $producto->cantidad_entrada,
                'descripcion' => 'Entrada inicial de producto',
                'referencia' => $request->factura,
            ]);
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['componente', 'categoria', 'familia', 'unidadMedida', 'ubicacion']);
        $movimientos = $producto->movimientos()->with('usuario')->orderBy('created_at', 'desc')->paginate(10);

        return view('productos.show', compact('producto', 'movimientos'));
    }

    public function edit(Producto $producto)
    {
        $componentes = Componente::all();
        $categorias = Categoria::all();
        $familias = Familia::all();
        $unidadesMedida = UnidadMedida::all();
        $ubicaciones = Ubicacion::all();

        return view('productos.edit', compact(
            'producto',
            'componentes',
            'categorias',
            'familias',
            'unidadesMedida',
            'ubicaciones'
        ));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'ubicacion_id' => 'nullable|exists:ubicaciones,id',
            'precio_unitario' => 'nullable|numeric|min:0',
            'moneda' => 'required|in:MXN,USD',
            'factura' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'hoja_seguridad' => 'nullable|string|max:255',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.show', $producto)->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $productos = Producto::where('codigo', 'like', "%{$query}%")
            ->orWhere('descripcion', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'codigo', 'descripcion']);

        return response()->json($productos);
    }

    public function importForm()
    {
        return view('productos.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'json_file' => 'required|file|mimes:json,txt|max:102400', // 100MB max
        ]);

        try {
            set_time_limit(600); // 10 minutos máximo
            ini_set('memory_limit', '512M'); // Aumentar memoria

            $file = $request->file('json_file');
            $jsonContent = file_get_contents($file->getRealPath());
            $data = json_decode($jsonContent, true);

            if (!is_array($data)) {
                return back()->with('error', 'El archivo JSON no tiene un formato válido.');
            }

            $imported = 0;
            $errors = [];
            $skipped = 0;

            foreach ($data as $index => $item) {
                try {
                    // Validar que tenga código
                    if (empty($item['CODIGO'])) {
                        $errors[] = "Fila " . ($index + 1) . ": Código vacío";
                        $skipped++;
                        continue;
                    }

                    // Buscar o crear Componente
                    $componente = Componente::firstOrCreate(
                        ['codigo' => $item['COMP.'] ?? 'X'],
                        ['nombre' => 'Componente ' . ($item['COMP.'] ?? 'X'), 'descripcion' => 'Auto-creado']
                    );

                    // Buscar o crear Categoría
                    $categoria = Categoria::firstOrCreate(
                        ['codigo' => $item['CAT.'] ?? 'XX'],
                        ['nombre' => 'Categoría ' . ($item['CAT.'] ?? 'XX'), 'descripcion' => 'Auto-creada']
                    );

                    // Buscar o crear Familia
                    $familia = Familia::firstOrCreate(
                        ['codigo' => $item['FAM.'] ?? '000'],
                        ['nombre' => 'Familia ' . ($item['FAM.'] ?? '000'), 'descripcion' => 'Auto-creada']
                    );

                    // Buscar o crear Unidad de Medida
                    $unidadMedida = UnidadMedida::firstOrCreate(
                        ['codigo' => strtoupper($item['UM'] ?? 'PZ')],
                        ['nombre' => strtoupper($item['UM'] ?? 'PZ')]
                    );

                    // Buscar o crear Ubicación
                    $ubicacion = Ubicacion::firstOrCreate(
                        ['codigo' => $item['UBIC.'] ?? 'S/U'],
                        ['nombre' => 'Ubicación ' . ($item['UBIC.'] ?? 'S/U')]
                    );

                    // Determinar cantidades
                    $cantidadFisica = is_numeric($item['FISICO'] ?? 0) ? intval($item['FISICO']) : 0;
                    $cantidadEntrada = is_numeric($item['ENTRADA'] ?? 0) ? intval($item['ENTRADA']) : 0;
                    $cantidadSalida = is_numeric($item['SALIDA'] ?? 0) ? intval($item['SALIDA']) : 0;

                    // Crear o actualizar Producto
                    $producto = Producto::updateOrCreate(
                        ['codigo' => $item['CODIGO']],
                        [
                            'componente_id' => $componente->id,
                            'categoria_id' => $categoria->id,
                            'familia_id' => $familia->id,
                            'consecutivo' => $item['CONS.'] ?? '0001',
                            'descripcion' => !empty($item['DESCRIPCIÓN']) ? $item['DESCRIPCIÓN'] : 'Sin descripción',
                            'unidad_medida_id' => $unidadMedida->id,
                            'ubicacion_id' => $ubicacion->id,
                            'cantidad_entrada' => $cantidadEntrada,
                            'cantidad_salida' => $cantidadSalida,
                            'cantidad_fisica' => $cantidadFisica,
                            'precio_unitario' => (!empty($item['P.U']) && is_numeric($item['P.U'])) ? floatval($item['P.U']) : null,
                            'moneda' => in_array($item['MXN/USD'] ?? 'MXN', ['MXN', 'USD']) ? $item['MXN/USD'] : 'MXN',
                            'factura' => (!empty($item['FACTURA']) && trim($item['FACTURA']) !== '') ? substr($item['FACTURA'], 0, 50) : null,
                            'observaciones' => !empty($item['DN/NP/OBERVACIÓN']) ? $item['DN/NP/OBERVACIÓN'] : null,
                            'fecha_vencimiento' => null,
                            'hoja_seguridad' => !empty($item['HOJAS DE SEGURIDAD']) ? substr($item['HOJAS DE SEGURIDAD'], 0, 255) : null,
                        ]
                    );

                    $imported++;

                    // Solo crear movimiento para productos nuevos con entrada
                    if ($producto->wasRecentlyCreated && $cantidadEntrada > 0) {
                        Movimiento::create([
                            'producto_id' => $producto->id,
                            'tipo_movimiento' => 'entrada',
                            'cantidad' => $cantidadEntrada,
                            'cantidad_anterior' => 0,
                            'cantidad_nueva' => $cantidadEntrada,
                            'referencia' => 'Importación JSON',
                            'descripcion' => 'Importado desde archivo JSON',
                        ]);
                    }

                } catch (\Exception $e) {
                    $errors[] = "Fila " . ($index + 1) . " (Código: " . ($item['CODIGO'] ?? 'N/A') . "): " . $e->getMessage();
                    $skipped++;
                    
                    // Solo guardar los primeros 50 errores para no sobrecargar
                    if (count($errors) >= 50) {
                        $errors[] = "... y más errores (mostrando solo los primeros 50)";
                        break;
                    }
                }
            }

            $message = "✅ Se importaron {$imported} productos exitosamente.";
            if ($skipped > 0) {
                $message .= " ⚠️ Se omitieron {$skipped} registros con errores.";
            }

            // Guardar errores en sesión si no son muchos
            if (count($errors) > 0 && count($errors) <= 100) {
                session(['import_errors' => $errors]);
            }

            return redirect()->route('productos.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('productos.import')->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}
