# Ejemplos de Uso de la API

## 🧪 Testing Rápido

### 1. Abrir en el navegador:

**Ver estadísticas de productos:**
```
http://localhost:8000/api/v1/productos/stats
```

**Listar primeros 10 productos:**
```
http://localhost:8000/api/v1/productos?per_page=10
```

**Ver catálogos:**
```
http://localhost:8000/api/v1/catalogos
```

**Ver estadísticas de movimientos:**
```
http://localhost:8000/api/v1/movimientos/stats
```

---

## 📡 Ejemplos con cURL (PowerShell)

### Productos

**Listar productos paginados:**
```powershell
curl "http://localhost:8000/api/v1/productos?per_page=5"
```

**Buscar productos:**
```powershell
curl "http://localhost:8000/api/v1/productos?search=rodamiento"
```

**Filtrar por categoría:**
```powershell
curl "http://localhost:8000/api/v1/productos?categoria_id=1&per_page=10"
```

**Ver producto específico (ID 1):**
```powershell
curl "http://localhost:8000/api/v1/productos/1"
```

**Buscar por código:**
```powershell
curl "http://localhost:8000/api/v1/productos/buscar/GCN007"
```

**Estadísticas generales:**
```powershell
curl "http://localhost:8000/api/v1/productos/stats"
```

### Movimientos

**Listar movimientos:**
```powershell
curl "http://localhost:8000/api/v1/movimientos?per_page=5"
```

**Filtrar movimientos por tipo:**
```powershell
curl "http://localhost:8000/api/v1/movimientos?tipo_movimiento=entrada"
```

**Filtrar por fechas:**
```powershell
curl "http://localhost:8000/api/v1/movimientos?fecha_desde=2024-02-01&fecha_hasta=2024-02-19"
```

**Movimientos de un producto:**
```powershell
curl "http://localhost:8000/api/v1/productos/1/movimientos"
```

**Estadísticas de movimientos:**
```powershell
curl "http://localhost:8000/api/v1/movimientos/stats"
```

### Catálogos

**Obtener todos los catálogos:**
```powershell
curl "http://localhost:8000/api/v1/catalogos"
```

---

## 🔥 Ejemplos con Invoke-RestMethod (PowerShell nativo)

```powershell
# Listar productos
$productos = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/productos?per_page=5"
$productos | ConvertTo-Json -Depth 3

# Ver estadísticas
$stats = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/productos/stats"
$stats

# Buscar producto
$resultado = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/productos/1"
$resultado | Select-Object codigo, descripcion, cantidad_fisica, precio_unitario

# Listar movimientos
$movimientos = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/movimientos?per_page=5"
$movimientos.data | Select-Object id, tipo_movimiento, cantidad, created_at
```

---

## 📱 Ejemplo de Consumo en JavaScript (Frontend)

```javascript
// Fetch productos
fetch('http://localhost:8000/api/v1/productos?per_page=10')
  .then(response => response.json())
  .then(data => {
    console.log('Total productos:', data.total);
    console.log('Productos:', data.data);
  });

// Buscar producto
fetch('http://localhost:8000/api/v1/productos/buscar/GCN007')
  .then(response => response.json())
  .then(productos => {
    console.log('Resultados:', productos);
  });

// Stats
fetch('http://localhost:8000/api/v1/productos/stats')
  .then(response => response.json())
  .then(stats => {
    console.log('Total productos:', stats.total_productos);
    console.log('Valor MXN:', stats.valor_total_inventario_mxn);
  });
```

---

## 🐍 Ejemplo de Consumo en Python

```python
import requests

# Base URL
BASE_URL = "http://localhost:8000/api/v1"

# Listar productos
response = requests.get(f"{BASE_URL}/productos", params={"per_page": 10})
productos = response.json()
print(f"Total: {productos['total']}")

# Ver producto específico
producto = requests.get(f"{BASE_URL}/productos/1").json()
print(f"Código: {producto['codigo']}")
print(f"Descripción: {producto['descripcion']}")

# Estadísticas
stats = requests.get(f"{BASE_URL}/productos/stats").json()
print(f"Total productos: {stats['total_productos']}")
print(f"Valor total MXN: {stats['valor_total_inventario_mxn']}")

# Filtrar movimientos por fecha
movimientos = requests.get(
    f"{BASE_URL}/movimientos",
    params={
        "fecha_desde": "2024-02-01",
        "fecha_hasta": "2024-02-19",
        "per_page": 20
    }
).json()
print(f"Movimientos encontrados: {len(movimientos['data'])}")
```

---

## 📋 Estructura de Respuesta Paginada

```json
{
  "current_page": 1,
  "data": [ ... ],
  "first_page_url": "http://localhost:8000/api/v1/productos?page=1",
  "from": 1,
  "last_page": 10,
  "last_page_url": "http://localhost:8000/api/v1/productos?page=10",
  "links": [ ... ],
  "next_page_url": "http://localhost:8000/api/v1/productos?page=2",
  "path": "http://localhost:8000/api/v1/productos",
  "per_page": 15,
  "prev_page_url": null,
  "to": 15,
  "total": 150
}
```

---

## ⚠️ Códigos de Respuesta HTTP

- **200 OK**: Solicitud exitosa
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Error de validación
- **500 Internal Server Error**: Error del servidor

---

## 🎯 Próximos Pasos

Si necesitas autenticación con tokens (API privada), puedes:

1. Instalar Sanctum: `composer require laravel/sanctum`
2. Publicar configuración: `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
3. Ejecutar migraciones: `php artisan migrate`
4. Descomentar rutas protegidas en `routes/api.php`
5. Generar tokens para usuarios específicos
