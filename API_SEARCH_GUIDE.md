# 🔍 API de Búsqueda de Productos

## Endpoint de Búsqueda Global

### URL
```
GET /api/v1/productos/buscar?q={texto}&limit={numero}
```

### Descripción
Busca productos en múltiples campos simultáneamente usando coincidencias parciales (LIKE %%).

### Campos de Búsqueda
La búsqueda se realiza en los siguientes campos:
- **codigo** - Código del producto
- **descripcion** - Descripción completa
- **ubicacion** - Código de ubicación
- **um** - Unidad de medida

### Parámetros

| Parámetro | Tipo | Requerido | Descripción | Default |
|-----------|------|-----------|-------------|---------|
| `q` | string | ✅ Sí | Texto a buscar (mínimo 1 carácter) | - |
| `limit` | integer | ❌ No | Máximo de resultados (1-100) | 50 |

### Estrategia de Ordenamiento

Los resultados se ordenan por **relevancia**:

1. **Prioridad 1**: Coincidencia exacta en código
2. **Prioridad 2**: Código que empieza con el término
3. **Prioridad 3**: Descripción que empieza con el término
4. **Prioridad 4**: Otras coincidencias parciales

### Ejemplos de Uso

#### Buscar por código
```bash
curl "http://localhost:8000/api/v1/productos/buscar?q=GCN007"
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
      "codigo": "GCN0070002",
      "descripcion": "Rodamiento 6304-Zzc3E",
      "ubicacion": "A-4",
      "um": "PZ",
      "fisico": 3,
      "pu": null
    }
  ],
  "total": 2,
  "query": "GCN007"
}
```

#### Buscar por descripción
```bash
curl "http://localhost:8000/api/v1/productos/buscar?q=rodamiento"
```

#### Buscar por ubicación
```bash
curl "http://localhost:8000/api/v1/productos/buscar?q=A-3"
```

#### Buscar por unidad de medida
```bash
curl "http://localhost:8000/api/v1/productos/buscar?q=PZ"
```

#### Limitar resultados
```bash
curl "http://localhost:8000/api/v1/productos/buscar?q=rodamiento&limit=10"
```

---

## Búsqueda por Código (Legacy)

### URL
```
GET /api/v1/productos/buscar/{codigo}
```

### Descripción
Busca productos solo por código (método legacy, se recomienda usar el nuevo endpoint).

### Ejemplo
```bash
curl "http://localhost:8000/api/v1/productos/buscar/GCN007"
```

**Respuesta:**
```json
{
  "data": [ ... ],
  "total": 5
}
```

---

## Casos de Uso

### 1. Autocompletado en Frontend
```javascript
let timeout;
const searchInput = document.getElementById('search');

searchInput.addEventListener('input', (e) => {
    clearTimeout(timeout);
    const query = e.target.value;
    
    if (query.length < 2) return;
    
    timeout = setTimeout(async () => {
        const response = await fetch(`/api/v1/productos/buscar?q=${query}&limit=10`);
        const data = await response.json();
        mostrarSugerencias(data.data);
    }, 300); // Debounce de 300ms
});
```

### 2. Búsqueda con Enter
```javascript
searchInput.addEventListener('keypress', async (e) => {
    if (e.key === 'Enter') {
        const query = e.target.value;
        const response = await fetch(`/api/v1/productos/buscar?q=${query}&limit=50`);
        const data = await response.json();
        mostrarResultados(data.data);
    }
});
```

### 3. Python - Búsqueda de Inventario
```python
import requests

def buscar_productos(termino, limite=50):
    url = f"http://localhost:8000/api/v1/productos/buscar"
    params = {"q": termino, "limit": limite}
    
    response = requests.get(url, params=params)
    data = response.json()
    
    print(f"Encontrados: {data['total']} productos")
    for producto in data['data']:
        print(f"{producto['codigo']} - {producto['descripcion']} - {producto['ubicacion']}")
    
    return data['data']

# Uso
buscar_productos("rodamiento", 10)
```

### 4. PowerShell - Exportar a CSV
```powershell
$query = "rodamiento"
$response = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/productos/buscar?q=$query&limit=100"

$response.data | Export-Csv -Path "busqueda_$query.csv" -NoTypeInformation
Write-Host "Exportados $($response.total) productos"
```

---

## Ventajas de la Estrategia Implementada

### ✅ 1. Búsqueda Multicriterio
- Un solo parámetro busca en 4 campos diferentes
- No necesitas saber en qué campo está el dato

### ✅ 2. Coincidencias Parciales
- "rod" encuentra "Rodamiento"
- "007" encuentra "GCN0070001"
- "A-3" encuentra todos los productos en esa ubicación

### ✅ 3. Ordenamiento Inteligente
- Resultados más relevantes primero
- Coincidencias exactas tienen prioridad

### ✅ 4. Rendimiento Optimizado
- Límite configurable de resultados
- Índices en campos de búsqueda (recomendado)
- No carga toda la base de datos

### ✅ 5. Sin Paginación Compleja
- Para búsquedas, retorna top N resultados
- Más simple de consumir en frontend
- Evita problemas con paginación de resultados filtrados

---

## Recomendaciones de Índices

Para mejorar el rendimiento, agregar índices en:

```sql
-- Migración recomendada
CREATE INDEX idx_productos_codigo ON productos(codigo);
CREATE INDEX idx_productos_descripcion ON productos(descripcion(50));
CREATE INDEX idx_ubicaciones_codigo ON ubicaciones(codigo);
CREATE INDEX idx_unidades_medida_codigo ON unidades_medida(codigo);
```

---

## Interfaz de Prueba

Abre en el navegador:
```
http://localhost:8000/buscar.html
```

Esta interfaz permite probar la búsqueda con:
- Búsqueda en tiempo real
- Resaltado de coincidencias
- Visualización de resultados en tabla
- JSON raw de la respuesta

---

## Diferencias vs Listado Paginado

| Característica | Búsqueda | Listado Paginado |
|----------------|----------|------------------|
| **URL** | `/productos/buscar?q=...` | `/productos?page=...` |
| **Uso** | Encontrar productos específicos | Navegar todo el inventario |
| **Límite** | Máx 100 resultados | Todo el inventario |
| **Paginación** | No (top N) | Sí (completa) |
| **Filtros** | Búsqueda global | Filtros específicos |
| **Ordenamiento** | Por relevancia | Por código |
| **Rendimiento** | Rápido (limitado) | Más lento (todo) |

---

## Próximos Pasos

Si necesitas funcionalidad más avanzada:

1. **Full-Text Search**: Usar MySQL FULLTEXT o Elasticsearch
2. **Búsqueda Fonética**: Coincidencias por sonido (SOUNDEX)
3. **Fuzzy Search**: Tolerancia a errores tipográficos (Levenshtein)
4. **Búsqueda Facetada**: Filtros combinables (categoría + ubicación + rango precio)
