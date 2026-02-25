# 📱 Documentación API - Sistema de Inventario de Almacén

**Versión:** 1.0  
**Base URL:** `http://localhost:8000/api/v1`  
**Formato de respuesta:** JSON  
**Charset:** UTF-8  

---

## 📋 Índice

1. [Productos](#productos)
2. [Movimientos](#movimientos)
3. [Catálogos](#catálogos)
4. [Códigos de Respuesta](#códigos-de-respuesta)
5. [Ejemplos de Uso](#ejemplos-de-uso)

---

## 🔷 PRODUCTOS

### 1. Listar Productos (con paginación)

**Endpoint:** `GET /api/v1/productos`

**Descripción:** Obtiene una lista paginada de productos con campos simplificados.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `per_page` | integer | No | 15 | Cantidad de resultados por página (máx: 100) |
| `page` | integer | No | 1 | Número de página |
| `search` | string | No | - | Búsqueda por código o descripción |
| `categoria_id` | integer | No | - | Filtrar por ID de categoría |
| `familia_id` | integer | No | - | Filtrar por ID de familia |
| `componente_id` | integer | No | - | Filtrar por ID de componente |
| `ubicacion_id` | integer | No | - | Filtrar por ID de ubicación |
| `stock_bajo` | boolean | No | - | Filtrar productos con stock bajo |

**Respuesta Exitosa (200 OK):**

```json
{
  "data": [
    {
      "id": 123,
      "codigo": "ROD-001-025",
      "descripcion": "Rodamiento 6205 2RS",
      "ubicacion": "A-12",
      "um": "PZA",
      "fisico": 45.00,
      "pu": 125.50
    },
    {
      "id": 124,
      "codigo": "ENG-002-010",
      "descripcion": "Engrane helicoidal 20T",
      "ubicacion": "B-05",
      "um": "PZA",
      "fisico": 12.00,
      "pu": 850.00
    }
  ],
  "current_page": 1,
  "last_page": 10,
  "per_page": 15,
  "total": 145
}
```

**Ejemplo de Solicitud:**

```bash
# Listar primeros 20 productos
curl -X GET "http://localhost:8000/api/v1/productos?per_page=20"

# Buscar productos con texto "rodamiento"
curl -X GET "http://localhost:8000/api/v1/productos?search=rodamiento"

# Filtrar por categoría específica
curl -X GET "http://localhost:8000/api/v1/productos?categoria_id=5"
```

---

### 2. Obtener Producto por ID

**Endpoint:** `GET /api/v1/productos/{id}`

**Descripción:** Obtiene los detalles de un producto específico.

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | integer | ID del producto |

**Respuesta Exitosa (200 OK):**

```json
{
  "id": 123,
  "codigo": "ROD-001-025",
  "descripcion": "Rodamiento 6205 2RS",
  "ubicacion": "A-12",
  "um": "PZA",
  "fisico": 45.00,
  "pu": 125.50
}
```

**Respuesta de Error (404 Not Found):**

```json
{
  "message": "No query results for model [App\\Models\\Producto] 999"
}
```

**Ejemplo de Solicitud:**

```bash
curl -X GET "http://localhost:8000/api/v1/productos/123"
```

---

### 3. Buscar Productos (Global)

**Endpoint:** `GET /api/v1/productos/buscar`

**Descripción:** Búsqueda global en múltiples campos (código, descripción, ubicación, unidad de medida). Ideal para implementar un buscador con autocompletado.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `q` | string | Sí | - | Término de búsqueda |
| `limit` | integer | No | 50 | Máximo de resultados (máx: 100) |

**Respuesta Exitosa (200 OK):**

```json
{
  "data": [
    {
      "id": 123,
      "codigo": "ROD-001-025",
      "descripcion": "Rodamiento 6205 2RS",
      "ubicacion": "A-12",
      "um": "PZA",
      "fisico": 45.00,
      "pu": 125.50
    }
  ],
  "total": 1,
  "query": "rodamiento"
}
```

**Notas importantes:**
- Los resultados se ordenan por relevancia (coincidencias exactas primero)
- Si el parámetro `q` está vacío, retorna un array vacío

**Ejemplo de Solicitud:**

```bash
# Buscar productos que contengan "rodamiento"
curl -X GET "http://localhost:8000/api/v1/productos/buscar?q=rodamiento"

# Buscar con límite de 10 resultados
curl -X GET "http://localhost:8000/api/v1/productos/buscar?q=eng&limit=10"
```

---

### 4. Buscar Productos por Código (Legacy)

**Endpoint:** `GET /api/v1/productos/buscar/{codigo}`

**Descripción:** Búsqueda específica por código de producto (método legacy, se recomienda usar `/productos/buscar?q=` en su lugar).

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `codigo` | string | Código o parte del código del producto |

**Respuesta Exitosa (200 OK):**

```json
{
  "data": [
    {
      "id": 123,
      "codigo": "ROD-001-025",
      "descripcion": "Rodamiento 6205 2RS",
      "ubicacion": "A-12",
      "um": "PZA",
      "fisico": 45.00,
      "pu": 125.50
    }
  ],
  "total": 1
}
```

**Ejemplo de Solicitud:**

```bash
curl -X GET "http://localhost:8000/api/v1/productos/buscar/ROD-001"
```

---

### 5. Estadísticas de Productos

**Endpoint:** `GET /api/v1/productos/stats`

**Descripción:** Obtiene estadísticas generales del inventario.

**Respuesta Exitosa (200 OK):**

```json
{
  "total_productos": 1250,
  "total_categorias": 22,
  "total_familias": 15,
  "total_componentes": 8,
  "productos_stock_bajo": 45,
  "valor_total_inventario_mxn": 2500000.50,
  "valor_total_inventario_usd": 150000.00
}
```

**Campos de la Respuesta:**

- `total_productos`: Cantidad total de productos registrados
- `total_categorias`: Cantidad de categorías disponibles
- `total_familias`: Cantidad de familias disponibles
- `total_componentes`: Cantidad de componentes disponibles
- `productos_stock_bajo`: Productos donde cantidad_fisica < cantidad_entrada
- `valor_total_inventario_mxn`: Suma total del valor en pesos mexicanos
- `valor_total_inventario_usd`: Suma total del valor en dólares

**Ejemplo de Solicitud:**

```bash
curl -X GET "http://localhost:8000/api/v1/productos/stats"
```

---

## 🔷 MOVIMIENTOS

### 1. Listar Movimientos (con paginación)

**Endpoint:** `GET /api/v1/movimientos`

**Descripción:** Obtiene una lista paginada de movimientos de inventario.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `per_page` | integer | No | 15 | Cantidad de resultados por página (máx: 100) |
| `page` | integer | No | 1 | Número de página |
| `producto_id` | integer | No | - | Filtrar por ID de producto |
| `tipo_movimiento` | string | No | - | Filtrar por tipo: `entrada`, `salida`, `ajuste`, `transferencia` |
| `fecha_desde` | date | No | - | Filtrar desde fecha (formato: YYYY-MM-DD) |
| `fecha_hasta` | date | No | - | Filtrar hasta fecha (formato: YYYY-MM-DD) |

**Respuesta Exitosa (200 OK):**

```json
{
  "data": [
    {
      "id": 456,
      "producto_id": 123,
      "usuario_id": 1,
      "tipo_movimiento": "entrada",
      "cantidad": 100.00,
      "cantidad_anterior": 45.00,
      "cantidad_nueva": 145.00,
      "solicitud_id": null,
      "descripcion": "Recepción de compra OC-2024-001",
      "referencia": "OC-2024-001",
      "created_at": "2026-02-24T10:30:00.000000Z",
      "updated_at": "2026-02-24T10:30:00.000000Z",
      "producto": {
        "id": 123,
        "codigo": "ROD-001-025",
        "descripcion": "Rodamiento 6205 2RS"
      },
      "usuario": {
        "id": 1,
        "name": "Juan Pérez",
        "email": "juan.perez@empresa.com"
      },
      "solicitud": null
    }
  ],
  "current_page": 1,
  "last_page": 5,
  "per_page": 15,
  "total": 68
}
```

**Ejemplo de Solicitud:**

```bash
# Listar movimientos de entrada
curl -X GET "http://localhost:8000/api/v1/movimientos?tipo_movimiento=entrada"

# Movimientos de un producto específico
curl -X GET "http://localhost:8000/api/v1/movimientos?producto_id=123"

# Movimientos en un rango de fechas
curl -X GET "http://localhost:8000/api/v1/movimientos?fecha_desde=2026-02-01&fecha_hasta=2026-02-24"
```

---

### 2. Obtener Movimiento por ID

**Endpoint:** `GET /api/v1/movimientos/{id}`

**Descripción:** Obtiene los detalles de un movimiento específico.

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | integer | ID del movimiento |

**Respuesta Exitosa (200 OK):**

```json
{
  "id": 456,
  "producto_id": 123,
  "usuario_id": 1,
  "tipo_movimiento": "entrada",
  "cantidad": 100.00,
  "cantidad_anterior": 45.00,
  "cantidad_nueva": 145.00,
  "solicitud_id": null,
  "descripcion": "Recepción de compra OC-2024-001",
  "referencia": "OC-2024-001",
  "created_at": "2026-02-24T10:30:00.000000Z",
  "updated_at": "2026-02-24T10:30:00.000000Z",
  "producto": {
    "id": 123,
    "codigo": "ROD-001-025",
    "descripcion": "Rodamiento 6205 2RS",
    "cantidad_fisica": 145.00
  },
  "usuario": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan.perez@empresa.com"
  },
  "solicitud": null
}
```

**Ejemplo de Solicitud:**

```bash
curl -X GET "http://localhost:8000/api/v1/movimientos/456"
```

---

### 3. Movimientos de un Producto

**Endpoint:** `GET /api/v1/productos/{producto_id}/movimientos`

**Descripción:** Obtiene el historial de movimientos de un producto específico.

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `producto_id` | integer | ID del producto |

**Query Parameters:**

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `per_page` | integer | No | 15 | Cantidad de resultados por página (máx: 100) |
| `page` | integer | No | 1 | Número de página |

**Respuesta Exitosa (200 OK):**

```json
{
  "producto": {
    "id": 123,
    "codigo": "ROD-001-025",
    "descripcion": "Rodamiento 6205 2RS"
  },
  "movimientos": {
    "data": [
      {
        "id": 456,
        "producto_id": 123,
        "tipo_movimiento": "entrada",
        "cantidad": 100.00,
        "cantidad_anterior": 45.00,
        "cantidad_nueva": 145.00,
        "descripcion": "Recepción de compra",
        "created_at": "2026-02-24T10:30:00.000000Z",
        "usuario": {
          "id": 1,
          "name": "Juan Pérez"
        }
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```

**Ejemplo de Solicitud:**

```bash
curl -X GET "http://localhost:8000/api/v1/productos/123/movimientos"
```

---

### 4. Estadísticas de Movimientos

**Endpoint:** `GET /api/v1/movimientos/stats`

**Descripción:** Obtiene estadísticas de movimientos del inventario.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `fecha_desde` | date | No | - | Filtrar desde fecha (formato: YYYY-MM-DD) |
| `fecha_hasta` | date | No | - | Filtrar hasta fecha (formato: YYYY-MM-DD) |

**Respuesta Exitosa (200 OK):**

```json
{
  "total_movimientos": 1250,
  "entradas": 450,
  "salidas": 680,
  "ajustes": 85,
  "transferencias": 35,
  "movimientos_hoy": 12
}
```

**Ejemplo de Solicitud:**

```bash
# Estadísticas generales
curl -X GET "http://localhost:8000/api/v1/movimientos/stats"

# Estadísticas de febrero 2026
curl -X GET "http://localhost:8000/api/v1/movimientos/stats?fecha_desde=2026-02-01&fecha_hasta=2026-02-28"
```

---

## 🔷 CATÁLOGOS

### 1. Obtener Todos los Catálogos

**Endpoint:** `GET /api/v1/catalogos`

**Descripción:** Obtiene los catálogos de categorías, familias y componentes.

**Respuesta Exitosa (200 OK):**

```json
{
  "categorias": [
    {
      "id": 1,
      "codigo": "ROD",
      "nombre": "Rodamientos"
    },
    {
      "id": 2,
      "codigo": "ENG",
      "nombre": "Engranes"
    }
  ],
  "familias": [
    {
      "id": 1,
      "codigo": "MEC",
      "nombre": "Mecánicos"
    }
  ],
  "componentes": [
    {
      "id": 1,
      "codigo": "ROT",
      "nombre": "Rotatorios"
    }
  ]
}
```

**Ejemplo de Solicitud:**

```bash
curl -X GET "http://localhost:8000/api/v1/catalogos"
```

---

### 2. Buscar Departamentos

**Endpoint:** `GET /api/v1/departamentos/buscar`

**Descripción:** Búsqueda de departamentos para autocompletado.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `q` | string | No | - | Término de búsqueda |

**Respuesta Exitosa (200 OK):**

```json
{
  "data": [
    {
      "id": 1,
      "label": "Producción"
    },
    {
      "id": 2,
      "label": "Mantenimiento"
    }
  ],
  "total": 2
}
```

**Ejemplo de Solicitud:**

```bash
curl -X GET "http://localhost:8000/api/v1/departamentos/buscar?q=prod"
```

---

### 3. Buscar Unidades de Medida

**Endpoint:** `GET /api/v1/unidades-medida/buscar`

**Descripción:** Búsqueda de unidades de medida para autocompletado.

**Query Parameters:**

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `q` | string | No | - | Término de búsqueda |

**Respuesta Exitosa (200 OK):**

```json
{
  "data": [
    {
      "id": 1,
      "label": "PZA — Pieza",
      "codigo": "PZA"
    },
    {
      "id": 2,
      "label": "KG — Kilogramo",
      "codigo": "KG"
    }
  ],
  "total": 2
}
```

**Ejemplo de Solicitud:**

```bash
curl -X GET "http://localhost:8000/api/v1/unidades-medida/buscar?q=pza"
```

---

## 📊 CÓDIGOS DE RESPUESTA

| Código | Descripción |
|--------|-------------|
| `200` | Solicitud exitosa |
| `404` | Recurso no encontrado |
| `422` | Error de validación |
| `500` | Error interno del servidor |

---

## 💡 EJEMPLOS DE USO

### Ejemplo 1: Implementar Búsqueda de Productos

```javascript
// React Native / JavaScript
const buscarProductos = async (termino) => {
  try {
    const response = await fetch(
      `http://localhost:8000/api/v1/productos/buscar?q=${encodeURIComponent(termino)}&limit=20`
    );
    const data = await response.json();
    
    if (data.total > 0) {
      console.log(`Se encontraron ${data.total} productos`);
      return data.data;
    } else {
      console.log('No se encontraron resultados');
      return [];
    }
  } catch (error) {
    console.error('Error al buscar productos:', error);
    return [];
  }
};

// Uso
const resultados = await buscarProductos('rodamiento');
```

---

### Ejemplo 2: Paginación de Productos

```javascript
// React Native / JavaScript
const cargarProductos = async (pagina = 1) => {
  try {
    const response = await fetch(
      `http://localhost:8000/api/v1/productos?per_page=20&page=${pagina}`
    );
    const data = await response.json();
    
    return {
      productos: data.data,
      paginaActual: data.current_page,
      totalPaginas: data.last_page,
      total: data.total
    };
  } catch (error) {
    console.error('Error al cargar productos:', error);
    return null;
  }
};

// Uso
const { productos, paginaActual, totalPaginas } = await cargarProductos(1);
```

---

### Ejemplo 3: Obtener Detalles de Producto y sus Movimientos

```javascript
// React Native / JavaScript
const obtenerDetalleProducto = async (productoId) => {
  try {
    // Obtener datos del producto
    const respProducto = await fetch(
      `http://localhost:8000/api/v1/productos/${productoId}`
    );
    const producto = await respProducto.json();
    
    // Obtener movimientos del producto
    const respMovimientos = await fetch(
      `http://localhost:8000/api/v1/productos/${productoId}/movimientos?per_page=10`
    );
    const movimientos = await respMovimientos.json();
    
    return {
      producto,
      movimientos: movimientos.movimientos.data,
      totalMovimientos: movimientos.movimientos.total
    };
  } catch (error) {
    console.error('Error al obtener detalles:', error);
    return null;
  }
};

// Uso
const detalle = await obtenerDetalleProducto(123);
console.log(detalle.producto.descripcion);
console.log(`Tiene ${detalle.totalMovimientos} movimientos registrados`);
```

---

### Ejemplo 4: Filtrar Movimientos por Fecha

```javascript
// React Native / JavaScript
const obtenerMovimientosMes = async (año, mes) => {
  const primerDia = `${año}-${mes.toString().padStart(2, '0')}-01`;
  const ultimoDia = new Date(año, mes, 0).getDate();
  const ultimaFecha = `${año}-${mes.toString().padStart(2, '0')}-${ultimoDia}`;
  
  try {
    const response = await fetch(
      `http://localhost:8000/api/v1/movimientos?fecha_desde=${primerDia}&fecha_hasta=${ultimaFecha}&per_page=50`
    );
    const data = await response.json();
    
    return data.data;
  } catch (error) {
    console.error('Error al obtener movimientos:', error);
    return [];
  }
};

// Uso - Obtener movimientos de febrero 2026
const movimientos = await obtenerMovimientosMes(2026, 2);
```

---

### Ejemplo 5: Dashboard con Estadísticas

```javascript
// React Native / JavaScript
const cargarDashboard = async () => {
  try {
    // Cargar estadísticas en paralelo
    const [statsProductos, statsMovimientos] = await Promise.all([
      fetch('http://localhost:8000/api/v1/productos/stats').then(r => r.json()),
      fetch('http://localhost:8000/api/v1/movimientos/stats').then(r => r.json())
    ]);
    
    return {
      productos: {
        total: statsProductos.total_productos,
        stockBajo: statsProductos.productos_stock_bajo,
        valorMXN: statsProductos.valor_total_inventario_mxn,
        valorUSD: statsProductos.valor_total_inventario_usd
      },
      movimientos: {
        total: statsMovimientos.total_movimientos,
        entradas: statsMovimientos.entradas,
        salidas: statsMovimientos.salidas,
        hoy: statsMovimientos.movimientos_hoy
      }
    };
  } catch (error) {
    console.error('Error al cargar dashboard:', error);
    return null;
  }
};

// Uso
const dashboard = await cargarDashboard();
console.log(`Total de productos: ${dashboard.productos.total}`);
console.log(`Movimientos hoy: ${dashboard.movimientos.hoy}`);
```

---

## 🔒 AUTENTICACIÓN (Próximamente)

**Nota:** Actualmente las rutas son públicas. En una versión futura se implementará autenticación con Laravel Sanctum.

Cuando se implemente, se requerirá un token de acceso:

```javascript
const headers = {
  'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
};

const response = await fetch('http://localhost:8000/api/v1/productos', { headers });
```

---

## 📝 NOTAS IMPORTANTES

1. **Límites de Paginación:** El máximo de resultados por página es 100 items
2. **Formato de Fechas:** Todas las fechas usan formato ISO 8601 (YYYY-MM-DD o YYYY-MM-DDTHH:mm:ss.sssZ)
3. **Tipos de Movimiento:** Los valores válidos son: `entrada`, `salida`, `ajuste`, `transferencia`
4. **Campos Numéricos:** Los campos de cantidad y precio se retornan como números decimales con 2 posiciones
5. **Ordenamiento:** Los movimientos siempre se ordenan de más reciente a más antiguo
6. **Búsquedas:** Las búsquedas no son case-sensitive y soportan coincidencias parciales

---

## 🆘 SOPORTE

Para reportar problemas o solicitar nuevas funcionalidades, contactar al equipo de desarrollo backend.

**Última actualización:** 24 de Febrero, 2026
