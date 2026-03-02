# 📱 API REST - Guía Rápida para Desarrollo Móvil

## 🌐 URL Base
```
http://localhost:8000/api/v1
```

---

## 📦 ENDPOINTS PRINCIPALES

### **PRODUCTOS**

```bash
# Listar productos (paginado)
GET /productos?per_page=20&page=1

# Buscar productos (autocompletado)
GET /productos/buscar?q=rodamiento&limit=20

# Detalle de producto
GET /productos/{id}

# Estadísticas
GET /productos/stats
```

### **MOVIMIENTOS**

```bash
# Listar movimientos (paginado)
GET /movimientos?per_page=20&page=1

# Filtrar por tipo
GET /movimientos?tipo_movimiento=entrada

# Filtrar por fechas
GET /movimientos?fecha_desde=2026-02-01&fecha_hasta=2026-02-28

# Movimientos de un producto
GET /productos/{producto_id}/movimientos

# Estadísticas
GET /movimientos/stats
```

### **CATÁLOGOS**

```bash
# Todos los catálogos (categorías, familias, componentes)
GET /catalogos

# Buscar departamentos
GET /departamentos/buscar?q=produccion

# Buscar unidades de medida
GET /unidades-medida/buscar?q=pza
```

---

## 📋 ESTRUCTURA DE DATOS

### **Producto**
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

### **Movimiento**
```json
{
  "id": 456,
  "producto_id": 123,
  "tipo_movimiento": "entrada",
  "cantidad": 100.00,
  "cantidad_anterior": 45.00,
  "cantidad_nueva": 145.00,
  "descripcion": "Recepción de compra",
  "created_at": "2026-02-24T10:30:00Z",
  "producto": { ... },
  "usuario": { ... }
}
```

### **Respuesta Paginada**
```json
{
  "data": [ ... ],
  "current_page": 1,
  "last_page": 10,
  "per_page": 15,
  "total": 145
}
```

---

## 🔍 EJEMPLOS PRÁCTICOS

### **1. Buscar productos con autocompletado**
```javascript
const buscarProductos = async (texto) => {
  const url = `http://localhost:8000/api/v1/productos/buscar?q=${encodeURIComponent(texto)}&limit=20`;
  const response = await fetch(url);
  const { data, total } = await response.json();
  return data;
};
```

### **2. Cargar lista de productos con paginación**
```javascript 
const cargarProductos = async (pagina = 1) => {
  const url = `http://localhost:8000/api/v1/productos?per_page=20&page=${pagina}`;
  const response = await fetch(url);
  return await response.json();
};
```

### **3. Obtener estadísticas del dashboard**
```javascript
const obtenerEstadisticas = async () => {
  const [productos, movimientos] = await Promise.all([
    fetch('http://localhost:8000/api/v1/productos/stats').then(r => r.json()),
    fetch('http://localhost:8000/api/v1/movimientos/stats').then(r => r.json())
  ]);
  return { productos, movimientos };
};
```

### **4. Ver historial de un producto**
```javascript
const historialProducto = async (productoId) => {
  const url = `http://localhost:8000/api/v1/productos/${productoId}/movimientos?per_page=10`;
  const response = await fetch(url);
  const { producto, movimientos } = await response.json();
  return { producto, movimientos: movimientos.data };
};
```

---

## 📊 FILTROS DISPONIBLES

### **Productos**
- `search` - Texto a buscar en código/descripción
- `categoria_id` - ID de categoría
- `familia_id` - ID de familia
- `componente_id` - ID de componente
- `ubicacion_id` - ID de ubicación
- `stock_bajo` - true/false

### **Movimientos**
- `producto_id` - ID del producto
- `tipo_movimiento` - entrada, salida, ajuste, transferencia
- `fecha_desde` - YYYY-MM-DD
- `fecha_hasta` - YYYY-MM-DD

---

## ⚡ LÍMITES Y RESTRICCIONES

| Característica | Límite |
|----------------|--------|
| Máximo resultados por página | 100 |
| Default resultados por página | 15 |
| Máximo búsqueda rápida | 100 |
| Default búsqueda rápida | 50 |

---

## 🎯 CASOS DE USO COMUNES

### **Dashboard Móvil**
```
1. GET /productos/stats (estadísticas generales)
2. GET /movimientos/stats (actividad reciente)
3. GET /movimientos?per_page=5 (últimos 5 movimientos)
```

### **Búsqueda de Producto**
```
1. GET /productos/buscar?q={texto} (mientras escribe)
2. GET /productos/{id} (al seleccionar)
3. GET /productos/{id}/movimientos (historial)
```

### **Registro de Movimiento**
```
1. GET /productos/buscar?q={codigo} (buscar producto)
2. POST /movimientos (crear movimiento) [Endpoint a implementar]
```

---

## 🔒 SEGURIDAD

**Estado Actual:** Endpoints públicos (sin autenticación)  
**Próximamente:** Laravel Sanctum con Bearer Tokens

---

## 📄 DOCUMENTACIÓN COMPLETA

Ver archivo: **API_DOCUMENTATION.md** (100+ páginas con ejemplos detallados)

---

**Fecha:** 24 de Febrero, 2026  
**Equipo:** Backend Development  
**Contacto:** Soporte técnico
