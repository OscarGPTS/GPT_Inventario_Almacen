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
            
            // Cargar el archivo Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo->getRealPath());
            
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

                    $descripcion = $colMap['DESCRIPCION'] !== false ? trim($row[$colMap['DESCRIPCION']] ?? '') : '';
                    
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
}
