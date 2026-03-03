<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        
        // Catálogos para el formulario de nuevo producto
        $componentes = \App\Models\Componente::orderBy('codigo')->get();
        $categorias = \App\Models\Categoria::orderBy('codigo')->get();
        $familias = \App\Models\Familia::orderBy('codigo')->get();
        $unidadesMedida = \App\Models\UnidadMedida::orderBy('codigo')->get();
        $ubicaciones = \App\Models\Ubicacion::orderBy('codigo')->get();
        
        return view('reportes.entradas', compact('registros', 'componentes', 'categorias', 'familias', 'unidadesMedida', 'ubicaciones'));
    }

    /**
     * Guardar nuevo producto desde el formulario
     */
    public function guardarProducto(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'componente_id' => 'required|exists:componentes,id',
            'categoria_id' => 'required|exists:categorias,id',
            'familia_id' => 'required|exists:familias,id',
            'consecutivo' => 'required|string|max:10',
            'descripcion' => 'required|string',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'ubicacion_id' => 'nullable|exists:ubicaciones,id',
            'cantidad_entrada' => 'nullable|integer|min:0',
            'cantidad_salida' => 'nullable|integer|min:0',
            'cantidad_fisica' => 'nullable|integer|min:0',
            'fecha_entrada' => 'nullable|date',
            'fecha_salida' => 'nullable|date',
            'precio_unitario' => 'nullable|numeric|min:0',
            'moneda' => 'nullable|in:MXN,USD',
            'factura' => 'nullable|string|max:50',
            'numero_requisicion' => 'nullable|string|max:50',
            'numero_parte' => 'nullable|string|max:100',
            'dimensiones' => 'nullable|string|max:100',
            'orden_compra' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'hoja_seguridad' => 'nullable|string|max:255',
        ]);

        try {
            $producto = Producto::create($request->all());
            
            return redirect()->route('reportes.entradas')
                ->with('success', "Producto {$producto->codigo} creado exitosamente");
        } catch (\Exception $e) {
            return redirect()->route('reportes.entradas')
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
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
        $query = Producto::with(['componente', 'unidadMedida', 'ubicacion'])
            ->select('productos.*',
                DB::raw('(productos.cantidad_entrada - productos.cantidad_fisica) as diferencia')
            )
            ->orderBy('codigo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%{$s}%")
                  ->orWhere('descripcion', 'like', "%{$s}%")
                  ->orWhere('numero_requisicion', 'like', "%{$s}%")
                  ->orWhere('numero_parte', 'like', "%{$s}%")
                  ->orWhere('dimensiones', 'like', "%{$s}%")
                  ->orWhere('observaciones', 'like', "%{$s}%")
                  ->orWhere('factura', 'like', "%{$s}%")
                  ->orWhere('orden_compra', 'like', "%{$s}%");
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
     * No Conforme - solo productos marcados con incidencia
     */
    public function noConforme(Request $request)
    {
        $query = Producto::with(['componente', 'categoria', 'familia', 'unidadMedida', 'ubicacion'])
            ->where('no_conforme', true)
            ->orderBy('fecha_nc', 'desc')
            ->orderBy('codigo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%{$s}%")
                  ->orWhere('descripcion', 'like', "%{$s}%")
                  ->orWhere('observacion_nc', 'like', "%{$s}%")
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

    /**
     * Vista pública de inventario (sin autenticación)
     */
    public function inventarioPublico(Request $request)
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

        return view('reportes.inventario_publico', compact('registros', 'totales'));
    }

    /**
     * Importar productos de Barras desde Excel
     */
    public function importarBarras(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'sheet_number' => 'required|integer|min:1',
            'action_sin_codigo' => 'required|in:descartar,generar',
        ]);

        try {
            $archivo = $request->file('archivo');
            $sheetNumber = (int) $request->sheet_number;
            $actionSinCodigo = $request->action_sin_codigo;
            
            // Cargar el archivo Excel con configuración UTF-8
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo->getRealPath());
            
            // Asegurar que PhpSpreadsheet use UTF-8 para la lectura
            \PhpOffice\PhpSpreadsheet\Shared\StringHelper::setDecimalSeparator('.');
            \PhpOffice\PhpSpreadsheet\Shared\StringHelper::setThousandsSeparator(',');
            
            Log::info("[BARRAS] Iniciando importación - Archivo: {$archivo->getClientOriginalName()}, Hoja: {$sheetNumber}");
            
            // Obtener el número total de hojas
            $totalSheets = $spreadsheet->getSheetCount();
            
            // Verificar que el número de página sea válido (los índices empiezan en 0)
            $sheetIndex = $sheetNumber - 1; // Convertir número de página a índice (base 0)
            
            if ($sheetIndex < 0 || $sheetIndex >= $totalSheets) {
                return redirect()->route('reportes.barras')
                    ->with('error', "El archivo tiene {$totalSheets} página(s). Por favor, ingresa un número entre 1 y {$totalSheets}.");
            }
            
            // Obtener la hoja por índice
            try {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                if (!$sheet) {
                    return redirect()->route('reportes.barras')
                        ->with('error', "No se pudo acceder a la página {$sheetNumber} del archivo Excel.");
                }
            } catch (\Exception $e) {
                return redirect()->route('reportes.barras')
                    ->with('error', "Error al acceder a la página {$sheetNumber}: " . $e->getMessage());
            }

            $rows = $sheet->toArray();
            
            if (empty($rows)) {
                return redirect()->route('reportes.barras')
                    ->with('error', 'La hoja está vacía o no contiene datos.');
            }

            // DETECCIÓN AUTOMÁTICA DEL HEADER
            // Buscar la fila que contiene el encabezado (debe tener 'CODIGO' u otras columnas clave)
            $headerRowIndex = -1;
            $headers = [];
            
            // Buscar en las primeras 10 filas
            for ($i = 0; $i < min(10, count($rows)); $i++) {
                $row = $rows[$i];
                
                // Saltar filas vacías
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Convertir a mayúsculas y limpiar
                $potentialHeaders = array_map('trim', array_map('strtoupper', $row));
                
                // Verificar si esta fila contiene columnas clave del header
                $tieneCodigoCol = in_array('CODIGO', $potentialHeaders);
                $tieneDescripcionCol = in_array('DESCRIPCIÓN INGRESO', $potentialHeaders) || 
                                        in_array('DESCRIPCION', $potentialHeaders) ||
                                        in_array('DESCRIPCIÓN', $potentialHeaders);
                $tieneNPCol = in_array('NP', $potentialHeaders);
                $tienePZCol = in_array('PZ', $potentialHeaders);
                
                // Si tiene al menos CODIGO y otra columna clave, es probablemente el header
                if ($tieneCodigoCol && ($tieneDescripcionCol || $tieneNPCol || $tienePZCol)) {
                    $headerRowIndex = $i;
                    $headers = $potentialHeaders;
                    Log::info("Header detectado automáticamente en la fila " . ($i + 1));
                    break;
                }
            }
            
            // Si no se encontró el header, reportar error
            if ($headerRowIndex === -1) {
                // Mostrar las primeras 3 filas para ayudar al usuario
                $preview = [];
                for ($i = 0; $i < min(3, count($rows)); $i++) {
                    $preview[] = "Fila " . ($i + 1) . ": " . implode(', ', array_slice($rows[$i], 0, 5));
                }
                $previewText = implode(' | ', $preview);
                
                return redirect()->route('reportes.barras')
                    ->with('error', "❌ No se pudo detectar el encabezado automáticamente. Asegúrate de que hay una fila con columnas como 'CODIGO', 'DESCRIPCION', 'NP', etc. Primeras filas: {$previewText}");
            }
            
            // Validar que existe la columna CODIGO (redundante pero por seguridad)
            if (!in_array('CODIGO', $headers)) {
                $columnasEncontradas = implode(', ', array_slice($headers, 0, 10));
                return redirect()->route('reportes.barras')
                    ->with('error', "❌ No se encontró la columna 'CODIGO' en los encabezados. Columnas encontradas: {$columnasEncontradas}. Verifica que estés usando la hoja correcta.");
            }
            
            // Mapeo de columnas (ajusta según tu Excel)
            $colMap = [
                'CODIGO' => array_search('CODIGO', $headers),
                'REQUISICION' => array_search('# REQUISICIÓN', $headers) !== false 
                    ? array_search('# REQUISICIÓN', $headers) 
                    : (array_search('REQUISICION', $headers) !== false 
                        ? array_search('REQUISICION', $headers)
                        : array_search('#REQUISICIÓN', $headers)),
                'NP' => array_search('NP', $headers),
                'DIMENSIONES' => array_search('DIMENSIONES', $headers),
                'TIPO_MATERIAL' => array_search('TIPO MATERIAL', $headers) !== false
                    ? array_search('TIPO MATERIAL', $headers)
                    : array_search('TIPO', $headers),
                'PZ' => array_search('PZ', $headers),
                'FIS' => array_search('FIS.', $headers) !== false 
                    ? array_search('FIS.', $headers) 
                    : (array_search('FIS', $headers) !== false
                        ? array_search('FIS', $headers)
                        : array_search('FISICA', $headers)),
                'UM' => array_search('U.M', $headers) !== false 
                    ? array_search('U.M', $headers) 
                    : (array_search('UM', $headers) !== false
                        ? array_search('UM', $headers)
                        : array_search('U.M.', $headers)),
                'UBIC' => array_search('UBIC.', $headers) !== false 
                    ? array_search('UBIC.', $headers) 
                    : (array_search('UBICACION', $headers) !== false
                        ? array_search('UBICACION', $headers)
                        : array_search('UBIC', $headers)),
                'FACTURA' => array_search('FACTURA', $headers),
                'OC' => array_search('OC', $headers) !== false
                    ? array_search('OC', $headers)
                    : array_search('ORDEN COMPRA', $headers),
                'DESCRIPCION' => array_search('DESCRIPCIÓN INGRESO', $headers) !== false 
                    ? array_search('DESCRIPCIÓN INGRESO', $headers) 
                    : (array_search('DESCRIPCION', $headers) !== false
                        ? array_search('DESCRIPCION', $headers)
                        : array_search('DESCRIPCIÓN', $headers)),
                'OBSERVACIONES' => array_search('OBSERVACIONES', $headers),
            ];

            $procesados = 0;
            $actualizados = 0;
            $creados = 0;
            $errores = 0;
            $erroresDetalle = [];
            $codigosGeneradosCount = 0;
            
            // ========== CREAR VALORES POR DEFECTO PARA CAMPOS OBLIGATORIOS ==========
            // Estos valores se usarán cuando no se puedan extraer del código o estén vacíos
            
            // Componente por defecto
            $componenteDefault = \App\Models\Componente::firstOrCreate(
                ['codigo' => 'X'],
                ['nombre' => 'Sin Componente', 'descripcion' => 'Componente por defecto para importación']
            );
            
            // Categoría por defecto (BR = Barras)
            $categoriaDefault = \App\Models\Categoria::where('codigo', 'BR')->first();
            if (!$categoriaDefault) {
                $categoriaDefault = \App\Models\Categoria::firstOrCreate(
                    ['codigo' => 'XX'],
                    ['descripcion' => 'Categoría por defecto']
                );
            }
            
            // Familia por defecto
            $familiaDefault = \App\Models\Familia::firstOrCreate(
                ['codigo' => '000'],
                ['descripcion' => 'Familia por defecto para importación']
            );
            
            // Unidad de Medida por defecto
            $unidadMedidaDefault = \App\Models\UnidadMedida::firstOrCreate(
                ['codigo' => 'PZA'],
                ['nombre' => 'PIEZA', 'descripcion' => 'Unidad por defecto']
            );
            
            Log::info("Valores por defecto creados: Componente={$componenteDefault->id}, Categoría={$categoriaDefault->id}, Familia={$familiaDefault->id}, UM={$unidadMedidaDefault->id}");
            
            // Función helper para generar código temporal único
            $generarCodigoTemporal = function() use (&$codigosGeneradosCount) {
                do {
                    $codigosGeneradosCount++;
                    $codigo = 'TEM' . str_pad($codigosGeneradosCount, 4, '0', STR_PAD_LEFT);
                    // Verificar que no exista en la BD
                    $existe = \App\Models\Producto::where('codigo', $codigo)->exists();
                } while ($existe);
                
                return $codigo;
            };

            // Procesar desde la fila después del header
            $startRow = $headerRowIndex + 1;
            Log::info("Iniciando procesamiento desde la fila " . ($startRow + 1) . " (después del header en fila " . ($headerRowIndex + 1) . ")");
            
            for ($i = $startRow; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Saltar filas vacías
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $codigo = $colMap['CODIGO'] !== false ? trim($row[$colMap['CODIGO']] ?? '') : '';
                    
                    // Si no hay código, aplicar acción según preferencia del usuario
                    if (empty($codigo)) {
                        if ($actionSinCodigo === 'descartar') {
                            // Descartar este registro
                            $errores++;
                            $erroresDetalle[] = "Fila " . ($i + 1) . ": Sin código (descartado)";
                            continue;
                        } else if ($actionSinCodigo === 'generar') {
                            // Generar código temporal
                            $codigo = $generarCodigoTemporal();
                            Log::info("Fila " . ($i + 1) . ": Código temporal generado: {$codigo}");
                        }
                    }

                    // EXTRAER COMPONENTE, CATEGORÍA, FAMILIA Y CONSECUTIVO DEL CÓDIGO
                    // Formato esperado: GAC0010001 
                    // G = Componente (posición 0)
                    // AC = Categoría (posiciones 1-2)
                    // 001 = Familia (posiciones 3-5)
                    // 0001 = Consecutivo (últimos 4 caracteres)
                    
                    // Inicializar con valores por defecto (nunca serán NULL)
                    $componenteId = $componenteDefault->id;
                    $categoriaId = $categoriaDefault->id;
                    $familiaId = $familiaDefault->id;
                    $consecutivo = '0001';
                    
                    if (strlen($codigo) >= 10) {
                        // Extraer partes del código
                        $componenteCodigo = strtoupper(substr($codigo, 0, 1));
                        $categoriaCodigo = strtoupper(substr($codigo, 1, 2));
                        $familiaCodigo = substr($codigo, 3, 3);
                        $consecutivo = substr($codigo, -4);
                        
                        // Buscar o crear Componente
                        $componente = \App\Models\Componente::where('codigo', $componenteCodigo)->first();
                        if (!$componente) {
                            $componente = \App\Models\Componente::create([
                                'codigo' => $componenteCodigo,
                                'nombre' => 'Componente ' . $componenteCodigo,
                                'descripcion' => 'Creado automáticamente desde código'
                            ]);
                            Log::info("Componente creado: {$componenteCodigo}");
                        }
                        $componenteId = $componente->id;
                        
                        // Buscar o crear Categoría
                        $categoria = \App\Models\Categoria::where('codigo', $categoriaCodigo)->first();
                        if (!$categoria) {
                            $categoria = \App\Models\Categoria::create([
                                'codigo' => $categoriaCodigo,
                                'descripcion' => 'Categoría ' . $categoriaCodigo,
                            ]);
                            Log::info("Categoría creada: {$categoriaCodigo}");
                        }
                        $categoriaId = $categoria->id;
                        
                        // Buscar o crear Familia
                        $familia = \App\Models\Familia::where('codigo', $familiaCodigo)->first();
                        if (!$familia) {
                            $familia = \App\Models\Familia::create([
                                'codigo' => $familiaCodigo,
                                'descripcion' => 'Familia ' . $familiaCodigo,
                            ]);
                            Log::info("Familia creada: {$familiaCodigo}");
                        }
                        $familiaId = $familia->id;
                        
                        Log::info("Fila " . ($i + 1) . " - Código: {$codigo} -> COMP: {$componenteCodigo}, CAT: {$categoriaCodigo}, FAM: {$familiaCodigo}, CONS: {$consecutivo}");
                    } else {
                        Log::warning("Fila " . ($i + 1) . " - Código '{$codigo}' demasiado corto para extraer componentes (mínimo 10 caracteres)");
                    }

                    // Leer descripción preservando caracteres especiales UTF-8
                    $descripcionRaw = $colMap['DESCRIPCION'] !== false ? ($row[$colMap['DESCRIPCION']] ?? '') : '';
                    $descripcion = trim($descripcionRaw);
                    
                    // Log para debug de caracteres especiales (solo primeras 5 filas)
                    if ($i - $startRow < 5 && !empty($descripcion)) {
                        Log::info("Fila " . ($i + 1) . " - Descripción leída: '{$descripcion}' [" . strlen($descripcion) . " bytes]");
                    }
                    
                    // Validar descripción obligatoria
                    if (empty($descripcion)) {
                        $descripcion = 'Sin descripción';
                    }
                    
                    $numeroRequisicion = $colMap['REQUISICION'] !== false ? trim($row[$colMap['REQUISICION']] ?? '') : null;
                    $numeroParte = $colMap['NP'] !== false ? trim($row[$colMap['NP']] ?? '') : null;
                    $dimensiones = $colMap['DIMENSIONES'] !== false ? trim($row[$colMap['DIMENSIONES']] ?? '') : null;
                    $cantidadEntrada = $colMap['PZ'] !== false ? floatval($row[$colMap['PZ']] ?? 0) : 0;
                    $cantidadFisica = $colMap['FIS'] !== false ? floatval($row[$colMap['FIS']] ?? 0) : 0;
                    $factura = $colMap['FACTURA'] !== false ? trim($row[$colMap['FACTURA']] ?? '') : null;
                    $ordenCompra = $colMap['OC'] !== false ? trim($row[$colMap['OC']] ?? '') : null;
                    $observaciones = $colMap['OBSERVACIONES'] !== false ? trim($row[$colMap['OBSERVACIONES']] ?? '') : null;

                    // Procesar Unidad de Medida (con valor por defecto obligatorio)
                    $unidadMedidaId = $unidadMedidaDefault->id; // Siempre usar default inicialmente
                    if ($colMap['UM'] !== false && !empty($row[$colMap['UM']])) {
                        $um = trim($row[$colMap['UM']]);
                        $unidadMedida = \App\Models\UnidadMedida::firstOrCreate(
                            ['codigo' => strtoupper($um)],
                            ['nombre' => strtoupper($um), 'descripcion' => 'Creada desde importación']
                        );
                        $unidadMedidaId = $unidadMedida->id;
                    }

                    // Procesar Ubicación
                    $ubicacionId = null;
                    if ($colMap['UBIC'] !== false && !empty($row[$colMap['UBIC']])) {
                        $ubic = trim($row[$colMap['UBIC']]);
                        $ubicacion = \App\Models\Ubicacion::firstOrCreate(
                            ['codigo' => strtoupper($ubic)],
                            ['nombre' => 'Ubicación ' . strtoupper($ubic)]
                        );
                        $ubicacionId = $ubicacion->id;
                    }

                    // ========== VALIDACIÓN FINAL DE CAMPOS OBLIGATORIOS ==========
                    // Asegurar que NINGÚN campo obligatorio sea NULL
                    if (!$componenteId || !$categoriaId || !$familiaId || !$unidadMedidaId || empty($descripcion) || empty($consecutivo)) {
                        $errores++;
                        $erroresDetalle[] = "Fila " . ($i + 1) . ": Código '{$codigo}' - Faltan campos obligatorios (componente_id={$componenteId}, categoria_id={$categoriaId}, familia_id={$familiaId}, unidad_medida_id={$unidadMedidaId}, descripcion='" . substr($descripcion, 0, 20) . "', consecutivo='{$consecutivo}')";
                        Log::error("Fila " . ($i + 1) . ": Validación falló - campos obligatorios NULL o vacíos");
                        continue; // Saltar este registro
                    }

                    // Buscar si el producto ya existe
                    $producto = \App\Models\Producto::where('codigo', $codigo)->first();

                    if ($producto) {
                        // Actualizar producto existente
                        $producto->update([
                            'numero_requisicion' => $numeroRequisicion,
                            'numero_parte' => $numeroParte,
                            'dimensiones' => $dimensiones,
                            'componente_id' => $componenteId,
                            'categoria_id' => $categoriaId,
                            'familia_id' => $familiaId,
                            'consecutivo' => $consecutivo,
                            'descripcion' => $descripcion,
                            'unidad_medida_id' => $unidadMedidaId,
                            'ubicacion_id' => $ubicacionId,
                            'cantidad_entrada' => $cantidadEntrada,
                            'cantidad_fisica' => $cantidadFisica,
                            'factura' => $factura,
                            'orden_compra' => $ordenCompra,
                            'observaciones' => $observaciones,
                        ]);
                        $actualizados++;
                        Log::info("Fila " . ($i + 1) . ": Producto '{$codigo}' actualizado");
                    } else {
                        // Crear nuevo producto
                        \App\Models\Producto::create([
                            'codigo' => $codigo,
                            'numero_requisicion' => $numeroRequisicion,
                            'numero_parte' => $numeroParte,
                            'dimensiones' => $dimensiones,
                            'componente_id' => $componenteId,
                            'categoria_id' => $categoriaId,
                            'familia_id' => $familiaId,
                            'consecutivo' => $consecutivo,
                            'descripcion' => $descripcion,
                            'unidad_medida_id' => $unidadMedidaId,
                            'ubicacion_id' => $ubicacionId,
                            'cantidad_entrada' => $cantidadEntrada,
                            'cantidad_fisica' => $cantidadFisica,
                            'factura' => $factura,
                            'orden_compra' => $ordenCompra,
                            'observaciones' => $observaciones,
                            'moneda' => 'MXN',
                        ]);
                        $creados++;
                        Log::info("Fila " . ($i + 1) . ": Producto '{$codigo}' creado");
                    }

                    $procesados++;
                } catch (\Exception $e) {
                    $errores++;
                    $codigoMostrar = isset($codigo) ? $codigo : 'N/A';
                    $errorMsg = "Fila " . ($i + 1) . " (Código: {$codigoMostrar}): " . $e->getMessage();
                    $erroresDetalle[] = $errorMsg;
                    Log::error("Error procesando fila " . ($i + 1) . " (Código: {$codigoMostrar}): " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
                }
            }

            // Preparar mensaje de resultado
            $mensaje = "✅ Importación completada: {$procesados} registros procesados";
            if ($creados > 0) {
                $mensaje .= " ({$creados} creados";
            }
            if ($actualizados > 0) {
                $mensaje .= $creados > 0 ? ", {$actualizados} actualizados)" : " ({$actualizados} actualizados)";
            } else if ($creados > 0) {
                $mensaje .= ")";
            }
            
            // Agregar información de códigos temporales generados
            if ($codigosGeneradosCount > 0) {
                $mensaje .= ". 🏷️ Se generaron {$codigosGeneradosCount} códigos temporales (TEM0001-TEM" . str_pad($codigosGeneradosCount, 4, '0', STR_PAD_LEFT) . ")";
            }
            
            if ($errores > 0) {
                $mensaje .= ". ⚠️ {$errores} errores encontrados";
                
                // Registrar TODOS los errores en el log para análisis
                Log::warning("Errores en importación de barras ({$errores} total):", $erroresDetalle);
                
                // Clasificar errores
                $erroresSinCodigo = array_filter($erroresDetalle, function($err) {
                    return strpos($err, 'Sin código (descartado)') !== false;
                });
                
                $erroresCamposObligatorios = array_filter($erroresDetalle, function($err) {
                    return strpos($err, 'Faltan campos obligatorios') !== false;
                });
                
                $erroresOtros = count($erroresDetalle) - count($erroresSinCodigo) - count($erroresCamposObligatorios);
                
                // Mensaje detallado
                if (count($erroresSinCodigo) > 0) {
                    $mensaje .= " (" . count($erroresSinCodigo) . " sin código descartados";
                }
                
                if (count($erroresCamposObligatorios) > 0) {
                    if (count($erroresSinCodigo) > 0) {
                        $mensaje .= ", " . count($erroresCamposObligatorios) . " con campos faltantes";
                    } else {
                        $mensaje .= " (" . count($erroresCamposObligatorios) . " con campos faltantes";
                    }
                }
                
                if ($erroresOtros > 0) {
                    if (count($erroresSinCodigo) > 0 || count($erroresCamposObligatorios) > 0) {
                        $mensaje .= ", {$erroresOtros} otros errores";
                    } else {
                        $mensaje .= " ({$erroresOtros} diversos";
                    }
                }
                
                if (count($erroresSinCodigo) > 0 || count($erroresCamposObligatorios) > 0 || $erroresOtros > 0) {
                    $mensaje .= ")";
                }
                
                // Agregar sugerencia si hay muchos errores
                if ($errores > 5) {
                    $mensaje .= ". 💡 Revisa el archivo de logs para ver detalles de todos los errores.";
                }
            }

            return redirect()->route('reportes.barras')->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('Error en importación de barras: ' . $e->getMessage());
            return redirect()->route('reportes.barras')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Borrar todos los productos (soft delete)
     */
    public function borrarBarras()
    {
        try {
            $count = \App\Models\Producto::count();
            
            if ($count === 0) {
                return redirect()->route('reportes.barras')
                    ->with('info', 'No hay registros para eliminar.');
            }

            // Eliminar todos los productos usando soft delete
            $productos = \App\Models\Producto::all();
            foreach ($productos as $producto) {
                $producto->delete(); // Soft delete
            }

            return redirect()->route('reportes.barras')
                ->with('success', "Se ocultaron {$count} productos exitosamente (Soft Delete).");

        } catch (\Exception $e) {
            Log::error('Error al borrar productos: ' . $e->getMessage());
            return redirect()->route('reportes.barras')
                ->with('error', 'Error al eliminar los registros: ' . $e->getMessage());
        }
    }

    /**
     * Importar productos desde Excel para pantalla de ENTRADAS
     */
    public function importarEntradas(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'sheet_number' => 'required|integer|min:1',
            'import_mode' => 'required|in:update_create,only_new,only_update',
        ]);

        try {
            $archivo = $request->file('archivo');
            $sheetNumber = (int) $request->sheet_number;
            $importMode = $request->import_mode; // update_create, only_new, only_update
            
            // Cargar el archivo Excel con configuración UTF-8
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo->getRealPath());
            
            // Asegurar que PhpSpreadsheet use UTF-8 para la lectura
            \PhpOffice\PhpSpreadsheet\Shared\StringHelper::setDecimalSeparator('.');
            \PhpOffice\PhpSpreadsheet\Shared\StringHelper::setThousandsSeparator(',');
            
            Log::info("[ENTRADAS] Iniciando importación - Archivo: {$archivo->getClientOriginalName()}, Hoja: {$sheetNumber}, Modo: {$importMode}");
            
            // Obtener el número total de hojas
            $totalSheets = $spreadsheet->getSheetCount();
            
            // Verificar que el número de página sea válido
            $sheetIndex = $sheetNumber - 1;
            
            if ($sheetIndex < 0 || $sheetIndex >= $totalSheets) {
                return redirect()->route('reportes.entradas')
                    ->with('error', "El archivo tiene {$totalSheets} página(s). Por favor, ingresa un número entre 1 y {$totalSheets}.");
            }
            
            // Obtener la hoja por índice
            try {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                if (!$sheet) {
                    return redirect()->route('reportes.entradas')
                        ->with('error', "No se pudo acceder a la página {$sheetNumber} del archivo Excel.");
                }
            } catch (\Exception $e) {
                return redirect()->route('reportes.entradas')
                    ->with('error', "Error al acceder a la página {$sheetNumber}: " . $e->getMessage());
            }

            $rows = $sheet->toArray();
            
            if (empty($rows)) {
                return redirect()->route('reportes.entradas')
                    ->with('error', 'La hoja está vacía o no contiene datos.');
            }

            // DETECCIÓN AUTOMÁTICA DEL HEADER
            $headerRowIndex = -1;
            $headers = [];
            
            for ($i = 0; $i < min(10, count($rows)); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) continue;
                
                $potentialHeaders = array_map('trim', array_map('strtoupper', $row));
                
                $tieneCodigoCol = in_array('CODIGO', $potentialHeaders);
                $tieneDescripcionCol = in_array('DESCRIPCIÓN', $potentialHeaders) || 
                                        in_array('DESCRIPCION', $potentialHeaders);
                
                if ($tieneCodigoCol && $tieneDescripcionCol) {
                    $headerRowIndex = $i;
                    $headers = $potentialHeaders;
                    Log::info("Header detectado en la fila " . ($i + 1) . " para importación de entradas");
                    break;
                }
            }
            
            if ($headerRowIndex === -1) {
                $preview = [];
                for ($i = 0; $i < min(3, count($rows)); $i++) {
                    $preview[] = "Fila " . ($i + 1) . ": " . implode(', ', array_slice($rows[$i], 0, 5));
                }
                $previewText = implode(' | ', $preview);
                
                return redirect()->route('reportes.entradas')
                    ->with('error', "❌ No se pudo detectar el encabezado automáticamente. Asegúrate de que hay una fila con 'CODIGO' y 'DESCRIPCION'. Primeras filas: {$previewText}");
            }
            
            if (!in_array('CODIGO', $headers)) {
                $columnasEncontradas = implode(', ', array_slice($headers, 0, 10));
                return redirect()->route('reportes.entradas')
                    ->with('error', "❌ No se encontró la columna 'CODIGO'. Columnas encontradas: {$columnasEncontradas}");
            }
            
            // Mapeo de columnas según el Excel de ENTRADAS
            $colMap = [
                'CODIGO' => array_search('CODIGO', $headers),
                'COMP' => array_search('COMP.', $headers) !== false ? array_search('COMP.', $headers) : array_search('COMP', $headers),
                'CAT' => array_search('CAT.', $headers) !== false ? array_search('CAT.', $headers) : array_search('CAT', $headers),
                'FAM' => array_search('FAM.', $headers) !== false ? array_search('FAM.', $headers) : array_search('FAM', $headers),
                'CONS' => array_search('CONS.', $headers) !== false ? array_search('CONS.', $headers) : array_search('CONS', $headers),
                'DESCRIPCION' => array_search('DESCRIPCIÓN', $headers) !== false ? array_search('DESCRIPCIÓN', $headers) : array_search('DESCRIPCION', $headers),
                'UM' => array_search('UM', $headers),
                'ENTRADA' => array_search('ENTRADA', $headers),
                'UBIC' => array_search('UBIC.', $headers) !== false ? array_search('UBIC.', $headers) : array_search('UBICACION', $headers),
                'FECHA_ENTRADA' => array_search('FECHA ENTRADA', $headers) !== false ? array_search('FECHA ENTRADA', $headers) : array_search('FECHA DE ENTRADA', $headers),
                'SALIDA' => array_search('SALIDA', $headers),
                'FISICO' => array_search('FISICO', $headers),
                'FECHA_SALIDA' => array_search('FECHA SALIDA', $headers) !== false ? array_search('FECHA SALIDA', $headers) : array_search('FECHA DE SALIDA', $headers),
                'PU' => array_search('P.U', $headers) !== false ? array_search('P.U', $headers) : array_search('PU', $headers),
                'MONEDA' => array_search('MXN/USD', $headers) !== false ? array_search('MXN/USD', $headers) : array_search('MONEDA', $headers),
                'FACTURA' => array_search('FACTURA', $headers),
                'OBSERVACIONES' => array_search('DN/NP/OBERVACIÓN', $headers) !== false 
                    ? array_search('DN/NP/OBERVACIÓN', $headers) 
                    : (array_search('DN/NP/OBSERVACIÓN', $headers) !== false
                        ? array_search('DN/NP/OBSERVACIÓN', $headers)
                        : (array_search('OBSERVACIONES', $headers) !== false
                            ? array_search('OBSERVACIONES', $headers)
                            : array_search('OBSERVACION', $headers))),
                'FECHA_VENCIMIENTO' => array_search('FECHA DE VENCIMIENTO', $headers) !== false 
                    ? array_search('FECHA DE VENCIMIENTO', $headers) 
                    : array_search('FECHA VENCIMIENTO', $headers),
                'HOJA_SEGURIDAD' => array_search('HOJAS DE SEGURIDAD', $headers) !== false 
                    ? array_search('HOJAS DE SEGURIDAD', $headers) 
                    : array_search('HOJA SEGURIDAD', $headers),
            ];

            $procesados = 0;
            $actualizados = 0;
            $creados = 0;
            $ignorados = 0;
            $errores = 0;
            $erroresDetalle = [];
            
            // ========== CREAR VALORES POR DEFECTO PARA CAMPOS OBLIGATORIOS ==========
            $componenteDefault = \App\Models\Componente::firstOrCreate(
                ['codigo' => 'X'],
                ['nombre' => 'Sin Componente', 'descripcion' => 'Componente por defecto para importación']
            );
            
            $categoriaDefault = \App\Models\Categoria::firstOrCreate(
                ['codigo' => 'XX'],
                ['descripcion' => 'Categoría por defecto']
            );
            
            $familiaDefault = \App\Models\Familia::firstOrCreate(
                ['codigo' => '000'],
                ['descripcion' => 'Familia por defecto para importación']
            );
            
            $unidadMedidaDefault = \App\Models\UnidadMedida::firstOrCreate(
                ['codigo' => 'PZA'],
                ['nombre' => 'PIEZA', 'descripcion' => 'Unidad por defecto']
            );
            
            Log::info("Valores por defecto creados para importación de entradas: Componente={$componenteDefault->id}, Categoría={$categoriaDefault->id}, Familia={$familiaDefault->id}, UM={$unidadMedidaDefault->id}");

            // Procesar desde la fila después del header
            $startRow = $headerRowIndex + 1;
            Log::info("Iniciando procesamiento de entradas desde la fila " . ($startRow + 1));
            
            for ($i = $startRow; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Saltar filas vacías
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $codigo = $colMap['CODIGO'] !== false ? trim($row[$colMap['CODIGO']] ?? '') : '';
                    
                    // Si no hay código, saltar registro
                    if (empty($codigo)) {
                        $errores++;
                        $erroresDetalle[] = "Fila " . ($i + 1) . ": Sin código (descartado)";
                        continue;
                    }

                    // EXTRAER O BUSCAR COMPONENTE, CATEGORÍA, FAMILIA
                    // Prioridad 1: Leer de las columnas COMP, CAT, FAM del Excel
                    $componenteId = $componenteDefault->id;
                    $categoriaId = $categoriaDefault->id;
                    $familiaId = $familiaDefault->id;
                    $consecutivo = '0001';
                    
                    // Intentar leer del Excel primero
                    if ($colMap['COMP'] !== false && !empty($row[$colMap['COMP']])) {
                        $compCodigo = trim($row[$colMap['COMP']]);
                        $componente = \App\Models\Componente::firstOrCreate(
                            ['codigo' => strtoupper($compCodigo)],
                            ['nombre' => 'Componente ' . strtoupper($compCodigo), 'descripcion' => 'Creado desde importación']
                        );
                        $componenteId = $componente->id;
                    }
                    
                    if ($colMap['CAT'] !== false && !empty($row[$colMap['CAT']])) {
                        $catCodigo = trim($row[$colMap['CAT']]);
                        $categoria = \App\Models\Categoria::firstOrCreate(
                            ['codigo' => strtoupper($catCodigo)],
                            ['descripcion' => 'Categoría ' . strtoupper($catCodigo)]
                        );
                        $categoriaId = $categoria->id;
                    }
                    
                    if ($colMap['FAM'] !== false && !empty($row[$colMap['FAM']])) {
                        $famCodigo = trim($row[$colMap['FAM']]);
                        $familia = \App\Models\Familia::firstOrCreate(
                            ['codigo' => strtoupper($famCodigo)],
                            ['descripcion' => 'Familia ' . strtoupper($famCodigo)]
                        );
                        $familiaId = $familia->id;
                    }
                    
                    if ($colMap['CONS'] !== false && !empty($row[$colMap['CONS']])) {
                        $consecutivo = trim($row[$colMap['CONS']]);
                    } else {
                        // Intentar extraer del código si tiene formato completo
                        if (strlen($codigo) >= 10) {
                            $consecutivo = substr($codigo, -4);
                        }
                    }
                    
                    // Prioridad 2: Si no se encontraron en el Excel, intentar extraer del código
                    if ($componenteId === $componenteDefault->id && strlen($codigo) >= 10) {
                        $componenteCodigo = strtoupper(substr($codigo, 0, 1));
                        $componente = \App\Models\Componente::firstOrCreate(
                            ['codigo' => $componenteCodigo],
                            ['nombre' => 'Componente ' . $componenteCodigo, 'descripcion' => 'Extraído del código']
                        );
                        $componenteId = $componente->id;
                    }
                    
                    if ($categoriaId === $categoriaDefault->id && strlen($codigo) >= 10) {
                        $categoriaCodigo = strtoupper(substr($codigo, 1, 2));
                        $categoria = \App\Models\Categoria::firstOrCreate(
                            ['codigo' => $categoriaCodigo],
                            ['descripcion' => 'Categoría ' . $categoriaCodigo]
                        );
                        $categoriaId = $categoria->id;
                    }
                    
                    if ($familiaId === $familiaDefault->id && strlen($codigo) >= 10) {
                        $familiaCodigo = substr($codigo, 3, 3);
                        $familia = \App\Models\Familia::firstOrCreate(
                            ['codigo' => $familiaCodigo],
                            ['descripcion' => 'Familia ' . $familiaCodigo]
                        );
                        $familiaId = $familia->id;
                    }

                    // Descripción - Preservando caracteres especiales UTF-8
                    $descripcionRaw = $colMap['DESCRIPCION'] !== false ? ($row[$colMap['DESCRIPCION']] ?? '') : '';
                    $descripcion = trim($descripcionRaw);
                    
                    // Log para debug de caracteres especiales (solo primeras 5 filas)
                    if ($i - $startRow < 5 && !empty($descripcion)) {
                        Log::info("[ENTRADAS] Fila " . ($i + 1) . " - Descripción leída: '{$descripcion}' [" . strlen($descripcion) . " bytes]");
                    }
                    
                    if (empty($descripcion)) {
                        $descripcion = 'Sin descripción';
                    }
                    
                    // Cantidades
                    $cantidadEntrada = $colMap['ENTRADA'] !== false ? floatval($row[$colMap['ENTRADA']] ?? 0) : 0;
                    $cantidadSalida = $colMap['SALIDA'] !== false ? floatval($row[$colMap['SALIDA']] ?? 0) : 0;
                    $cantidadFisica = $colMap['FISICO'] !== false ? floatval($row[$colMap['FISICO']] ?? 0) : 0;
                    
                    // Precio unitario
                    $precioUnitario = $colMap['PU'] !== false ? floatval($row[$colMap['PU']] ?? 0) : null;
                    
                    // Moneda
                    $moneda = 'MXN'; // default
                    if ($colMap['MONEDA'] !== false && !empty($row[$colMap['MONEDA']])) {
                        $monedaVal = strtoupper(trim($row[$colMap['MONEDA']]));
                        if (in_array($monedaVal, ['MXN', 'USD'])) {
                            $moneda = $monedaVal;
                        }
                    }
                    
                    // Factura y observaciones
                    $factura = $colMap['FACTURA'] !== false ? trim($row[$colMap['FACTURA']] ?? '') : null;
                    $observaciones = $colMap['OBSERVACIONES'] !== false ? trim($row[$colMap['OBSERVACIONES']] ?? '') : null;
                    
                    // Hoja de seguridad
                    $hojaSeguridad = $colMap['HOJA_SEGURIDAD'] !== false ? trim($row[$colMap['HOJA_SEGURIDAD']] ?? '') : null;
                    
                    // Fechas
                    $fechaEntrada = null;
                    if ($colMap['FECHA_ENTRADA'] !== false && !empty($row[$colMap['FECHA_ENTRADA']])) {
                        $fechaEntrada = $this->parsearFecha($row[$colMap['FECHA_ENTRADA']]);
                    }
                    
                    $fechaSalida = null;
                    if ($colMap['FECHA_SALIDA'] !== false && !empty($row[$colMap['FECHA_SALIDA']])) {
                        $fechaSalida = $this->parsearFecha($row[$colMap['FECHA_SALIDA']]);
                    }
                    
                    $fechaVencimiento = null;
                    if ($colMap['FECHA_VENCIMIENTO'] !== false && !empty($row[$colMap['FECHA_VENCIMIENTO']])) {
                        $fechaVencimiento = $this->parsearFecha($row[$colMap['FECHA_VENCIMIENTO']]);
                    }

                    // Unidad de Medida (con valor por defecto obligatorio)
                    $unidadMedidaId = $unidadMedidaDefault->id;
                    if ($colMap['UM'] !== false && !empty($row[$colMap['UM']])) {
                        $um = trim($row[$colMap['UM']]);
                        $unidadMedida = \App\Models\UnidadMedida::firstOrCreate(
                            ['codigo' => strtoupper($um)],
                            ['nombre' => strtoupper($um), 'descripcion' => 'Creada desde importación']
                        );
                        $unidadMedidaId = $unidadMedida->id;
                    }

                    // Ubicación
                    $ubicacionId = null;
                    if ($colMap['UBIC'] !== false && !empty($row[$colMap['UBIC']])) {
                        $ubic = trim($row[$colMap['UBIC']]);
                        $ubicacion = \App\Models\Ubicacion::firstOrCreate(
                            ['codigo' => strtoupper($ubic)],
                            ['nombre' => 'Ubicación ' . strtoupper($ubic)]
                        );
                        $ubicacionId = $ubicacion->id;
                    }

                    // ========== VALIDACIÓN FINAL DE CAMPOS OBLIGATORIOS ==========
                    if (!$componenteId || !$categoriaId || !$familiaId || !$unidadMedidaId || empty($descripcion) || empty($consecutivo)) {
                        $errores++;
                        $erroresDetalle[] = "Fila " . ($i + 1) . ": Código '{$codigo}' - Faltan campos obligatorios";
                        Log::error("Fila " . ($i + 1) . ": Validación falló - campos obligatorios NULL o vacíos");
                        continue;
                    }

                    // Buscar si el producto ya existe
                    $producto = \App\Models\Producto::where('codigo', $codigo)->first();

                    // APLICAR LÓGICA SEGÚN EL MODO DE IMPORTACIÓN
                    if ($producto) {
                        // El producto YA existe
                        if ($importMode === 'only_new') {
                            // Modo: Solo agregar nuevos -> Ignorar existentes
                            $ignorados++;
                            Log::info("Fila " . ($i + 1) . ": Producto '{$codigo}' ignorado (ya existe, modo only_new)");
                            continue;
                        } else {
                            // Modo: update_create o only_update -> Actualizar
                            $producto->update([
                                'componente_id' => $componenteId,
                                'categoria_id' => $categoriaId,
                                'familia_id' => $familiaId,
                                'consecutivo' => $consecutivo,
                                'descripcion' => $descripcion,
                                'unidad_medida_id' => $unidadMedidaId,
                                'ubicacion_id' => $ubicacionId,
                                'cantidad_entrada' => $cantidadEntrada,
                                'cantidad_salida' => $cantidadSalida,
                                'cantidad_fisica' => $cantidadFisica,
                                'fecha_entrada' => $fechaEntrada,
                                'fecha_salida' => $fechaSalida,
                                'precio_unitario' => $precioUnitario,
                                'moneda' => $moneda,
                                'factura' => $factura,
                                'observaciones' => $observaciones,
                                'fecha_vencimiento' => $fechaVencimiento,
                                'hoja_seguridad' => $hojaSeguridad,
                            ]);
                            $actualizados++;
                            $procesados++;
                            Log::info("Fila " . ($i + 1) . ": Producto '{$codigo}' actualizado");
                        }
                    } else {
                        // El producto NO existe (es nuevo)
                        if ($importMode === 'only_update') {
                            // Modo: Solo actualizar existentes -> Ignorar nuevos
                            $ignorados++;
                            Log::info("Fila " . ($i + 1) . ": Producto '{$codigo}' ignorado (no existe, modo only_update)");
                            continue;
                        } else {
                            // Modo: update_create o only_new -> Crear
                            \App\Models\Producto::create([
                                'codigo' => $codigo,
                                'componente_id' => $componenteId,
                                'categoria_id' => $categoriaId,
                                'familia_id' => $familiaId,
                                'consecutivo' => $consecutivo,
                                'descripcion' => $descripcion,
                                'unidad_medida_id' => $unidadMedidaId,
                                'ubicacion_id' => $ubicacionId,
                                'cantidad_entrada' => $cantidadEntrada,
                                'cantidad_salida' => $cantidadSalida,
                                'cantidad_fisica' => $cantidadFisica,
                                'fecha_entrada' => $fechaEntrada,
                                'fecha_salida' => $fechaSalida,
                                'precio_unitario' => $precioUnitario,
                                'moneda' => $moneda,
                                'factura' => $factura,
                                'observaciones' => $observaciones,
                                'fecha_vencimiento' => $fechaVencimiento,
                                'hoja_seguridad' => $hojaSeguridad,
                            ]);
                            $creados++;
                            $procesados++;
                            Log::info("Fila " . ($i + 1) . ": Producto '{$codigo}' creado");
                        }
                    }
                } catch (\Exception $e) {
                    $errores++;
                    $codigoMostrar = isset($codigo) ? $codigo : 'N/A';
                    $errorMsg = "Fila " . ($i + 1) . " (Código: {$codigoMostrar}): " . $e->getMessage();
                    $erroresDetalle[] = $errorMsg;
                    Log::error("Error procesando fila " . ($i + 1) . " (Código: {$codigoMostrar}): " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
                }
            }

            // Preparar mensaje de resultado
            $mensaje = "✅ {$procesados} registros procesados";
            
            // Agregar detalles de creados y actualizados
            $detalles = [];
            if ($creados > 0) {
                $detalles[] = "{$creados} nuevos";
            }
            if ($actualizados > 0) {
                $detalles[] = "{$actualizados} actualizados";
            }
            if ($ignorados > 0) {
                $detalles[] = "{$ignorados} omitidos";
            }
            
            if (!empty($detalles)) {
                $mensaje .= " (" . implode(", ", $detalles) . ")";
            }
            
            if ($errores > 0) {
                $mensaje .= ". ⚠️ {$errores} con errores";
                
                // Clasificar tipos de errores
                $erroresSinCodigo = array_filter($erroresDetalle, function($err) {
                    return strpos($err, 'Sin código (descartado)') !== false;
                });
                
                if (count($erroresSinCodigo) > 0) {
                    $mensaje .= " (" . count($erroresSinCodigo) . " sin código)";
                }
                
                // Log para el administrador
                Log::warning("Errores en importación de entradas ({$errores} total):", $erroresDetalle);
            }

            return redirect()->route('reportes.entradas')->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('Error en importación de entradas: ' . $e->getMessage());
            return redirect()->route('reportes.entradas')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Helper para parsear fechas de Excel
     */
    private function parsearFecha($valor)
    {
        if (empty($valor)) {
            return null;
        }
        
        // Si es un número (fecha serial de Excel)
        if (is_numeric($valor)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valor);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                Log::warning("No se pudo parsear fecha serial de Excel: {$valor}");
                return null;
            }
        }
        
        // Si es texto, intentar parsearlo con Carbon
        try {
            return \Carbon\Carbon::parse($valor)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("No se pudo parsear fecha texto: {$valor}");
            return null;
        }
    }
}
