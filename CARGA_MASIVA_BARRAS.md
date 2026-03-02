# Carga Masiva de Productos - Categoría Barras

## 📋 Descripción
Esta funcionalidad permite importar productos de la categoría Barras desde un archivo Excel, actualizando registros existentes o creando nuevos según sea necesario.

## 🚀 Cómo usar

### 1. Preparar el archivo Excel

Tu archivo Excel debe tener una hoja (tab) con los siguientes encabezados **en la primera fila**:

| CODIGO | # REQUISICIÓN | NP | DIMENSIONES | TIPO MATERIAL | PZ | FIS. | U.M | UBIC. | FACTURA | OC | DESCRIPCIÓN INGRESO | OBSERVACIONES |
|--------|---------------|-----|-------------|---------------|-----|------|-----|-------|---------|-----|---------------------|---------------|

### 2. Especificar el nombre de la hoja

En el modal de carga masiva, ingresa el **nombre exacto** de la hoja (tab) donde están los datos. Por ejemplo:
- `Página 4`
- `Sheet1`
- `Hoja1`
- `Barras`

### 3. Subir el archivo

1. Haz clic en el botón **"Carga Masiva"** (verde)
2. Ingresa el nombre de la hoja en el campo correspondiente
3. Selecciona tu archivo Excel (.xlsx, .xls, .csv)
4. Haz clic en **"Cargar Archivo"**

## 📊 Mapeo de Columnas

El sistema buscará automáticamente estas columnas (no distingue mayúsculas/minúsculas):

| Columna en Excel | Campo en BD | Observaciones |
|------------------|-------------|---------------|
| CODIGO | codigo | **Requerido** - Identificador único del producto |
| # REQUISICIÓN / REQUISICION | numero_requisicion | Opcional |
| NP | numero_parte | Opcional - Número de parte |
| DIMENSIONES | dimensiones | Opcional |
| TIPO MATERIAL | componente_id | Opcional - Se crea automáticamente si no existe |
| PZ | cantidad_entrada | Cantidad de piezas de entrada |
| FIS. / FIS | cantidad_fisica | Cantidad física actual |
| U.M / UM | unidad_medida_id | Unidad de medida - Se crea automáticamente si no existe |
| UBIC. / UBICACION | ubicacion_id | Ubicación - Se crea automáticamente si no existe |
| FACTURA | factura | Número de factura |
| OC | orden_compra | Orden de compra |
| DESCRIPCIÓN INGRESO / DESCRIPCION | descripcion | Descripción del producto |
| OBSERVACIONES | observaciones | Notas adicionales |

## 🔄 Comportamiento

### Si el producto YA EXISTE (mismo CODIGO):
- Se **actualiza** con los nuevos datos
- Se mantiene el ID y la categoría original

### Si el producto NO EXISTE:
- Se **crea** un nuevo registro
- Se asigna automáticamente a la categoría **Barras (BR)**
- Se generan valores por defecto:
  - `consecutivo`: "0001"
  - `moneda`: "MXN"

### Creación automática de catálogos:
Si especificas valores en **TIPO MATERIAL**, **U.M** o **UBIC.** que no existen, el sistema:
1. Busca si ya existe un registro con ese código
2. Si no existe, lo crea automáticamente
3. Vincula el nuevo registro al producto

## ⚠️ Consideraciones

1. **Primera fila = Encabezados**: La primera fila debe contener los nombres de las columnas
2. **Código único**: El campo CODIGO es el identificador principal
3. **Filas vacías**: Se ignoran automáticamente
4. **Errores**: Se registran en el log pero no detienen el proceso completo
5. **Tamaño máximo**: 10 MB por archivo

## 🗑️ Limpiar Todo

El botón **"Limpiar Todo"** (rojo) permite eliminar TODOS los productos de la categoría Barras:
- Requiere confirmación mediante checkbox
- Muestra advertencia con el número de registros a eliminar
- **Acción irreversible**

## 📝 Ejemplo de Excel

```
| CODIGO  | # REQUISICIÓN | NP      | DIMENSIONES | TIPO MATERIAL | PZ  | FIS. | U.M  | UBIC. | FACTURA | OC    | DESCRIPCIÓN INGRESO       | OBSERVACIONES |
|---------|---------------|---------|-------------|---------------|-----|------|------|-------|---------|-------|---------------------------|---------------|
| BR-001  | REQ-2026-001  | NP-1234 | 2x3x4       | ACERO         | 100 | 95   | PZ   | A1    | F-001   | OC-01 | Barra de acero inoxidable | En buen estado|
| BR-002  | REQ-2026-002  | NP-5678 | 3x5x6       | ALUMINIO      | 50  | 50   | PZ   | A2    | F-002   | OC-02 | Barra de aluminio         |               |
```

## 🎯 Resultado

Después de la importación verás un mensaje como:
```
Importación completada: 45 registros procesados (15 creados, 30 actualizados)
```

Si hubo errores:
```
Importación completada: 45 registros procesados (15 creados, 30 actualizados). 2 errores encontrados.
```
Los detalles de los errores se guardan en el log de Laravel.
