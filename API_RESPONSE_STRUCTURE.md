# Estructura de Respuesta API - Simplificada

## 📦 Respuesta Paginada

### Estructura
```json
{
  "data": [ ... ],
  "current_page": 1,
  "last_page": 128,
  "per_page": 15,
  "total": 1913
}
```

### Campos
- **data**: Array de registros (productos o movimientos)
- **current_page**: Número de página actual
- **last_page**: Total de páginas disponibles
- **per_page**: Cantidad de registros por página
- **total**: Total de registros en la base de datos

### Ejemplo Real
```json
{
  "data": [
    {
      "codigo": "GAC0010002",
      "descripcion": "Placa  1\" X 57 1/2 X 44",
      "ubicacion": "NV-B-9",
      "um": "PZ",
      "fisico": 0,
      "pu": "0.00"
    },
    {
      "codigo": "GCN0070001",
      "descripcion": "Rodamiento taza h-913810 Timken",
      "ubicacion": "A-3",
      "um": "PZ",
      "fisico": 2,
      "pu": "9752.00"
    }
  ],
  "current_page": 1,
  "last_page": 128,
  "per_page": 15,
  "total": 1913
}
```

---

## 🎯 Producto Individual

### Estructura
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

### Campos
- **id**: ID interno del producto
- **codigo**: Código único del producto
- **descripcion**: Descripción completa
- **ubicacion**: Código de ubicación física
- **um**: Unidad de medida
- **fisico**: Cantidad física en inventario
- **pu**: Precio unitario

---

## 📊 Estadísticas

### Estructura
```json
{
  "total_productos": 1913,
  "total_categorias": 25,
  "total_familias": 150,
  "total_componentes": 10,
  "productos_stock_bajo": 45,
  "valor_total_inventario_mxn": 8500000.50,
  "valor_total_inventario_usd": 125000.00
}
```

---

## 🔄 Navegación de Páginas

### Cálculos
```javascript
// Verificar si hay página siguiente
const hasNextPage = current_page < last_page;

// Verificar si hay página anterior
const hasPrevPage = current_page > 1;

// Calcular número de registro inicial en página actual
const from = ((current_page - 1) * per_page) + 1;

// Calcular número de registro final en página actual
const to = Math.min(current_page * per_page, total);
```

### Ejemplo de Uso (JavaScript)
```javascript
async function loadPage(page) {
    const response = await fetch(`http://localhost:8000/api/v1/productos?page=${page}&per_page=20`);
    const data = await response.json();
    
    console.log(`Mostrando ${data.data.length} de ${data.total} productos`);
    console.log(`Página ${data.current_page} de ${data.last_page}`);
    
    // Iterar productos
    data.data.forEach(producto => {
        console.log(`${producto.codigo} - ${producto.descripcion} - ${producto.fisico} ${producto.um}`);
    });
}
```

---

## ✅ Ventajas de la Simplificación

1. **Menor tamaño de respuesta** (~60% menos bytes)
2. **Más rápido de parsear**
3. **Menos campos redundantes**
4. **Estructura consistente**
5. **Fácil de consumir desde cualquier lenguaje**

### Comparación de Tamaño

**Antes (Laravel default):**
```json
{
  "current_page": 1,
  "data": [...],
  "first_page_url": "http://localhost:8000/api/v1/productos?page=1",
  "from": 1,
  "last_page": 128,
  "last_page_url": "http://localhost:8000/api/v1/productos?page=128",
  "links": [{...}, {...}, {...}],
  "next_page_url": "http://localhost:8000/api/v1/productos?page=2",
  "path": "http://localhost:8000/api/v1/productos",
  "per_page": 15,
  "prev_page_url": null,
  "to": 15,
  "total": 1913
}
```
**Tamaño metadata:** ~350 bytes

**Ahora (Simplificado):**
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 128,
  "per_page": 15,
  "total": 1913
}
```
**Tamaño metadata:** ~85 bytes

**Reducción:** 75% en metadata overhead
