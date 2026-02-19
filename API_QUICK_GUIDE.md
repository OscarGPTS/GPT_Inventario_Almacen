# 📖 Guía Rápida de API - Inventario

## 🔥 Endpoints Principales

### Productos

| Endpoint | Método | Descripción | Paginado | Ejemplo |
|----------|--------|-------------|----------|---------|
| `/api/v1/productos` | GET | Listar todos los productos | ✅ Sí | `?per_page=10&page=1` |
| `/api/v1/productos/buscar` | GET | **Buscar productos (RECOMENDADO)** | ❌ No (Top 50) | `?q=rodamiento&limit=10` |
| `/api/v1/productos/buscar/{codigo}` | GET | Buscar por código (legacy) | ❌ No | `/buscar/GCN007` |
| `/api/v1/productos/{id}` | GET | Ver producto específico | N/A | `/productos/1` |
| `/api/v1/productos/stats` | GET | Estadísticas generales | N/A | - |

### Movimientos

| Endpoint | Método | Descripción | Paginado | Ejemplo |
|----------|--------|-------------|----------|---------|
| `/api/v1/movimientos` | GET | Listar movimientos | ✅ Sí | `?per_page=10&tipo_movimiento=entrada` |
| `/api/v1/movimientos/{id}` | GET | Ver movimiento específico | N/A | `/movimientos/1` |
| `/api/v1/movimientos/stats` | GET | Estadísticas de movimientos | N/A | `?fecha_desde=2024-02-01` |
| `/api/v1/productos/{id}/movimientos` | GET | Movimientos de un producto | ✅ Sí | `/productos/1/movimientos` |

### Catálogos

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `/api/v1/catalogos` | GET | Categorías, familias y componentes |

---

## 🎯 ¿Cuándo usar qué endpoint?

### 🔍 Para Buscar Productos
**Usa:** `GET /api/v1/productos/buscar?q={texto}`

```bash
# Buscar por cualquier campo
curl "http://localhost:8000/api/v1/productos/buscar?q=rodamiento"
curl "http://localhost:8000/api/v1/productos/buscar?q=A-3"
curl "http://localhost:8000/api/v1/productos/buscar?q=GCN007"
```

**Ventajas:**
- ✅ Busca en código, descripción, ubicación y UM
- ✅ Coincidencias parciales (no necesitas escribir completo)
- ✅ Ordenado por relevancia
- ✅ Rápido (límite de resultados)

---

### 📋 Para Listar Todo el Inventario
**Usa:** `GET /api/v1/productos?page={num}`

```bash
# Navegar por páginas
curl "http://localhost:8000/api/v1/productos?per_page=20&page=1"
curl "http://localhost:8000/api/v1/productos?per_page=20&page=2"

# Con filtros
curl "http://localhost:8000/api/v1/productos?categoria_id=1&per_page=50"
```

**Ventajas:**
- ✅ Acceso a todo el inventario
- ✅ Paginación completa
- ✅ Filtros específicos por categoría, familia, etc.

---

### 👀 Para Ver Detalles de UN Producto
**Usa:** `GET /api/v1/productos/{id}`

```bash
curl "http://localhost:8000/api/v1/productos/1"
```

---

### 📊 Para Dashboard o Reportes
**Usa:** `GET /api/v1/productos/stats`

```bash
curl "http://localhost:8000/api/v1/productos/stats"
```

**Retorna:**
```json
{
  "total_productos": 1913,
  "total_categorias": 25,
  "productos_stock_bajo": 45,
  "valor_total_inventario_mxn": 8500000.50
}
```

---

## 🚀 Ejemplos de Uso Común

### 1. Autocompletado en un Input
```javascript
const input = document.getElementById('buscar');
let timeout;

input.addEventListener('input', async (e) => {
    clearTimeout(timeout);
    const query = e.target.value;
    
    if (query.length < 2) return;
    
    timeout = setTimeout(async () => {
        const res = await fetch(`/api/v1/productos/buscar?q=${query}&limit=10`);
        const data = await res.json();
        mostrarSugerencias(data.data);
    }, 300);
});
```

### 2. Tabla con Paginación
```javascript
async function cargarProductos(pagina = 1) {
    const res = await fetch(`/api/v1/productos?page=${pagina}&per_page=20`);
    const data = await res.json();
    
    mostrarTabla(data.data);
    mostrarBotonesPaginacion(data.current_page, data.last_page);
}
```

### 3. Buscar al Presionar Enter
```javascript
document.getElementById('buscar').addEventListener('keypress', async (e) => {
    if (e.key === 'Enter') {
        const query = e.target.value;
        const res = await fetch(`/api/v1/productos/buscar?q=${query}&limit=50`);
        const data = await res.json();
        mostrarResultados(data.data);
    }
});
```

### 4. Python - Exportar Inventario
```python
import requests
import pandas as pd

def exportar_inventario():
    all_products = []
    page = 1
    
    while True:
        response = requests.get(
            f"http://localhost:8000/api/v1/productos",
            params={"per_page": 100, "page": page}
        )
        data = response.json()
        
        all_products.extend(data['data'])
        
        if page >= data['last_page']:
            break
        page += 1
    
    df = pd.DataFrame(all_products)
    df.to_excel('inventario_completo.xlsx', index=False)
    print(f"Exportados {len(all_products)} productos")

exportar_inventario()
```

### 5. PowerShell - Buscar y Filtrar
```powershell
# Buscar productos
$productos = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/productos/buscar?q=rodamiento&limit=100"

# Filtrar solo los que tienen stock
$conStock = $productos.data | Where-Object { $_.fisico -gt 0 }

# Exportar a CSV
$conStock | Export-Csv -Path "productos_con_stock.csv" -NoTypeInformation
```

---

## 🎨 Interfaces de Prueba

| URL | Descripción |
|-----|-------------|
| `http://localhost:8000/buscar.html` | 🔍 Buscador interactivo |
| `http://localhost:8000/test-api.html` | 📋 Visor de inventario paginado |
| `http://localhost:8000/api-tester.html` | 🧪 Probador de endpoints |

---

## 📦 Estructura de Respuestas

### Búsqueda
```json
{
  "data": [...],
  "total": 15,
  "query": "rodamiento"
}
```

### Paginado
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 128,
  "per_page": 15,
  "total": 1913
}
```

### Individual
```json
{
  "codigo": "GAC0010002",
  "descripcion": "Placa 1\" X 57 1/2 X 44",
  "ubicacion": "NV-B-9",
  "um": "PZ",
  "fisico": 0,
  "pu": "0.00"
}
```

---

## 🔧 Tips de Rendimiento

1. **Usa búsqueda para encontrar productos específicos** - Es más rápido que filtrar con paginación
2. **Limita los resultados** - Usa `?limit=10` para búsquedas rápidas
3. **Cachea las estadísticas** - Los stats cambian poco, cachea por 5-10 minutos
4. **Debounce en autocomplete** - Espera 300ms después de que el usuario deje de escribir
5. **Carga lazy para tablas grandes** - Usa paginación con `per_page` pequeño (10-20)

---

## 📚 Documentación Completa

- **API_DOCUMENTATION.md** - Documentación detallada de todos los endpoints
- **API_SEARCH_GUIDE.md** - Guía completa de búsqueda
- **API_EXAMPLES.md** - Ejemplos en múltiples lenguajes
- **API_RESPONSE_STRUCTURE.md** - Estructura de respuestas simplificadas
