# API de Inventario - Documentación

## URL Base
```
http://localhost:8000/api/v1
```

## 📋 Campos Retornados (Simplificados)

Todos los endpoints de productos retornan únicamente estos campos para optimizar el rendimiento:

- **codigo**: Código único del producto (ej: GAC0010002)
- **descripcion**: Descripción del producto
- **ubicacion**: Código de ubicación (ej: NV-B-9)
- **um**: Código de unidad de medida (ej: PZ)
- **fisico**: Cantidad física en inventario (número entero)
- **pu**: Precio unitario (decimal)

**Ejemplo de registro:**
```
GAC0010002  |  Placa 1" X 57 1/2 X 44  |  NV-B-9  |  PZ  |  0  |  $0.00
```

## Endpoints Disponibles

### 📦 Productos

#### 1. Listar Productos (Paginado)
```http
GET /api/v1/productos
```

**Parámetros de Query:**
- `per_page` (opcional): Número de resultados por página (máx: 100, default: 15)
- `search` (opcional): Búsqueda por código o descripción
- `categoria_id` (opcional): Filtrar por categoría
- `familia_id` (opcional): Filtrar por familia
- `componente_id` (opcional): Filtrar por componente
- `ubicacion_id` (opcional): Filtrar por ubicación
- `stock_bajo` (opcional): Filtrar productos con stock bajo (true/false)

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/productos?per_page=10&search=rodamiento"
```

**Respuesta:**
```json
{
  "data": [
    {
      "codigo": "GCN0070001",
      "descripcion": "Rodamiento taza h-913810 Timken",
      "ubicacion": "A-3",
      "um": "PZ",
      "fisico": 2,
      "pu": "9752.00"
    },
    {
      "codigo": "GAC0010002",
      "descripcion": "Placa  1\" X 57 1/2 X 44",
      "ubicacion": "NV-B-9",
      "um": "PZ",
      "fisico": 0,
      "pu": "0.00"
    }
  ],
  "current_page": 1,
  "last_page": 128,
  "per_page": 15,
  "total": 1913
}
```

#### 2. Obtener Producto por ID
```http
GET /api/v1/productos/{id}
```

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/productos/1"
```

**Respuesta:**
```json
{
  "id": 1,
  "codigo": "GCN0070001",
  "descripcion": "Rodamiento taza h-913810 Timken",
  "ubicacion": "A-3",
  "um": "PZ",
  "fisico": 2,
  "pu": "9752.00"
}
```

#### 3. Buscar Producto por Código
```http
GET /api/v1/productos/buscar/{codigo}
```

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/productos/buscar/GCN007"
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "codigo": "GCN0070001",
    "descripcion": "Rodamiento taza h-913810 Timken",
    "cantidad_fisica": 2,
    "componente": { ... },
    "categoria": { ... }
  }
]
```

#### 5. Estadísticas de Productos
```http
GET /api/v1/productos/stats
```

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/productos/stats"
```codigo": "GCN0070001",
    "descripcion": "Rodamiento taza h-913810 Timken",
    "ubicacion": "A-3",
    "um": "PZ",
    "fisico": 2,
    "pu": "9752.00"8,
  "total_categorias": 25,
  "total_familias": 150,
  "total_componentes": 10,
  "productos_stock_bajo": 45,
  "valor_total_inventario_mxn": 8500000.50,
  "valor_total_inventario_usd": 125000.00
}
```

---

### 📊 Movimientos

#### 1. Listar Movimientos (Paginado)
```http
GET /api/v1/movimientos
```

**Parámetros de Query:**
- `per_page` (opcional): Resultados por página (máx: 100, default: 15)
- `producto_id` (opcional): Filtrar por producto
- `tipo_movimiento` (opcional): entrada, salida, ajuste, transferencia
- `fecha_desde` (opcional): Fecha inicio (YYYY-MM-DD)
- `fecha_hasta` (opcional): Fecha fin (YYYY-MM-DD)

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/movimientos?tipo_movimiento=entrada&fecha_desde=2024-02-01"
```

**Respuesta:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "producto_id": 1,
      "tipo_movimiento": "entrada",
      "cantidad": 10,
      "cantidad_anterior": 5,
      "cantidad_nueva": 15,
      "referencia": "FAC-001",
      "descripcion": "Entrada inicial",
      "created_at": "2024-02-19T16:30:00.000000Z",
      "producto": {
        "id": 1,
        "codigo": "GCN0070001",
        "descripcion": "Rodamiento taza h-913810 Timken"
      },
      "usuario": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com"
      }
    }
  ],
  "total": 150
}
```

#### 2. Movimientos de un Producto Específico
```http
GET /api/v1/productos/{producto_id}/movimientos
```

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/productos/1/movimientos?per_page=5"
```

**Respuesta:**
```json
{
  "producto": {
    "id": 1,
    "codigo": "GCN0070001",
    "descripcion": "Rodamiento taza h-913810 Timken"
  },
  "movimientos": {
    "data": [ ... ],
    "total": 25
  }
}
```

#### 3. Estadísticas de Movimientos
```http
GET /api/v1/movimientos/stats
```

**Parámetros de Query:**
- `fecha_desde` (opcional): Fecha inicio
- `fecha_hasta` (opcional): Fecha fin

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/movimientos/stats?fecha_desde=2024-02-01&fecha_hasta=2024-02-19"
```

**Respuesta:**
```json
{
  "total_movimientos": 250,
  "entradas": 120,
  "salidas": 100,
  "ajustes": 20,
  "transferencias": 10,
  "movimientos_hoy": 15
}
```

#### 4. Obtener Movimiento por ID
```http
GET /api/v1/movimientos/{id}
```

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/movimientos/1"
```

---

### 📚 Catálogos

#### Obtener Catálogos de Referencia
```http
GET /api/v1/catalogos
```

**Ejemplo:**
```bash
curl "http://localhost:8000/api/v1/catalogos"
```

**Respuesta:**
```json
{
  "categorias": [
    { "id": 1, "codigo": "CN", "nombre": "Categoría CN" }
  ],
  "familias": [
    { "id": 1, "codigo": "007", "nombre": "Familia 007" }
  ],
  "componentes": [
    { "id": 1, "codigo": "G", "nombre": "Componente G" }
  ]
}
```

---

## 🔒 Autenticación (Opcional - Sanctum)

Para habilitar autenticación con tokens, descomentar las rutas protegidas en `routes/api.php` y configurar Sanctum.

### Generar Token
```php
$user = User::find(1);
$token = $user->createToken('api-token')->plainTextToken;
```

### Usar Token en Requests
```bash
curl -H "Authorization: Bearer {token}" "http://localhost:8000/api/v1/productos"
```

---

## 📝 Notas

- Todos los endpoints retornan JSON
- La paginación incluye metadata (current_page, total, per_page, etc.)
- Los códigos de respuesta HTTP estándar se utilizan (200, 404, 500)
- Máximo de 100 resultados por página
- Las relaciones se cargan automáticamente con `with()`
- Las fechas están en formato ISO 8601

---

## 🧪 Testing con cURL

```bash
# Listar productos
curl "http://localhost:8000/api/v1/productos"

# Buscar productos
curl "http://localhost:8000/api/v1/productos?search=rodamiento&per_page=5"

# Stats de productos
curl "http://localhost:8000/api/v1/productos/stats"

# Obtener producto específico
curl "http://localhost:8000/api/v1/productos/1"

# Movimientos del día
curl "http://localhost:8000/api/v1/movimientos?fecha_desde=2024-02-19"

# Catálogos
curl "http://localhost:8000/api/v1/catalogos"
```

---

## 📦 Testing con Postman/Insomnia

Importa la colección base:

**URL Base:** `http://localhost:8000/api/v1`

**Colección:**
1. GET Productos List
2. GET Producto Detail
3. GET Productos Stats
4. GET Movimientos
5. GET Movimientos Stats
6. GET Catálogos
