# API de Solicitudes (Requisiciones)

## Descripción General

Esta API permite crear, listar, consultar y actualizar solicitudes (requisiciones) desde aplicaciones externas. Todas las rutas están bajo el prefijo `/api/v1` y son públicas (no requieren autenticación).

---

## Endpoints Disponibles

### 1. **Listar Solicitudes**

**GET** `/api/v1/solicitudes`

Lista todas las solicitudes con paginación y filtros opcionales.

#### Parámetros de Query (opcionales):

| Parámetro | Tipo | Descripción | Valores |
|-----------|------|-------------|---------|
| `estado` | string | Filtrar por estado | `pendiente`, `aprobada`, `entregada`, `cancelada` |
| `prioridad` | string | Filtrar por prioridad | `urgente`, `alta`, `normal`, `baja` |
| `usuario_id` | integer | Filtrar por usuario que creó la solicitud | ID del usuario |
| `search` | string | Búsqueda en folio, solicitante, código o descripción del producto | Texto libre |
| `per_page` | integer | Cantidad de resultados por página | Por defecto: 15 |
| `page` | integer | Página a consultar | Por defecto: 1 |

#### Ejemplo de Request:

```bash
GET http://tu-dominio.com/api/v1/solicitudes?estado=pendiente&per_page=10&page=1
```

#### Ejemplo de Response (200 OK):

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "folio": "SOL-001",
      "fecha": "2024-01-15",
      "fecha_requerida": "2024-01-20",
      "solicitante": "Juan Pérez",
      "departamento": {
        "id": 1,
        "nombre": "Mantenimiento",
        "codigo": "MAN"
      },
      "producto": {
        "id": 15,
        "codigo": "PROD-001",
        "descripcion": "Tuerca hexagonal 1/2\""
      },
      "cantidad": 50,
      "unidad_medida": {
        "id": 3,
        "codigo": "PZ",
        "nombre": "Piezas"
      },
      "observaciones": "Se requiere urgente",
      "estado": "pendiente",
      "prioridad": "alta",
      "usuario_registro": {
        "id": 5,
        "name": "María López",
        "email": "maria@empresa.com"
      },
      "created_at": "2024-01-15 10:30:00"
    }
  ],
  "pagination": {
    "total": 45,
    "per_page": 10,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 10
  }
}
```

---

### 2. **Crear Solicitud**

**POST** `/api/v1/solicitudes`

Crea una nueva solicitud desde un frontend externo.

#### Headers:

```
Content-Type: application/json
```

#### Body (JSON):

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `usuario_registro_id` | integer | ✅ **Sí** | ID del usuario que crea la solicitud (debe existir en tabla `users`) |
| `fecha` | string (date) | ✅ **Sí** | Fecha de la solicitud (formato: YYYY-MM-DD) |
| `solicitante` | string | ✅ **Sí** | Nombre de la persona que solicita |
| `producto_id` | integer | ✅ **Sí** | ID del producto solicitado (debe existir) |
| `cantidad` | number | ✅ **Sí** | Cantidad solicitada (mínimo 0.01) |
| `departamento_id` | integer | ❌ No* | ID del departamento (si se proporciona, debe existir) |
| `departamento_nombre` | string | ❌ No* | Nombre del departamento (si no existe `departamento_id`, se crea automáticamente) |
| `fecha_requerida` | string (date) | ❌ No | Fecha en que se requiere el producto (formato: YYYY-MM-DD) |
| `folio` | string | ❌ No | Folio de la solicitud (máx 50 caracteres) |
| `unidad_medida_id` | integer | ❌ No | ID de la unidad de medida (si no se proporciona, usa la del producto) |
| `observaciones` | string | ❌ No | Observaciones adicionales |
| `estado` | string | ❌ No | Estado inicial (por defecto: `pendiente`) |
| `prioridad` | string | ❌ No | Prioridad (por defecto: `normal`) |

**\*Nota:** Debes proporcionar **al menos uno** de los dos: `departamento_id` o `departamento_nombre`.

##### Valores válidos para `estado`:
- `pendiente`
- `aprobada`
- `entregada`
- `cancelada`

##### Valores válidos para `prioridad`:
- `urgente`
- `alta`
- `normal`
- `baja`

#### Ejemplo de Request:

```json
POST http://tu-dominio.com/api/v1/solicitudes
Content-Type: application/json

{
  "usuario_registro_id": 5,
  "fecha": "2024-01-15",
  "fecha_requerida": "2024-01-20",
  "solicitante": "Carlos Ramírez",
  "departamento_nombre": "Producción",
  "producto_id": 25,
  "cantidad": 100,
  "observaciones": "Se requiere para línea de producción B",
  "estado": "pendiente",
  "prioridad": "alta"
}
```

#### Ejemplo de Response (201 Created):

```json
{
  "success": true,
  "message": "Solicitud creada exitosamente",
  "data": {
    "id": 42,
    "folio": null,
    "fecha": "2024-01-15",
    "fecha_requerida": "2024-01-20",
    "solicitante": "Carlos Ramírez",
    "departamento": {
      "id": 8,
      "nombre": "Producción",
      "codigo": "PRO"
    },
    "producto": {
      "id": 25,
      "codigo": "TOR-M10",
      "descripcion": "Tornillo métrico M10x50"
    },
    "cantidad": 100,
    "unidad_medida": {
      "id": 3,
      "codigo": "PZ",
      "nombre": "Piezas"
    },
    "observaciones": "Se requiere para línea de producción B",
    "estado": "pendiente",
    "prioridad": "alta",
    "usuario_registro": {
      "id": 5,
      "name": "María López",
      "email": "maria@empresa.com"
    },
    "created_at": "2024-01-15 14:25:30"
  }
}
```

#### Ejemplo de Response (422 Validation Error):

```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "usuario_registro_id": [
      "El campo usuario registro id es obligatorio."
    ],
    "producto_id": [
      "El producto seleccionado no existe."
    ],
    "cantidad": [
      "El campo cantidad debe ser al menos 0.01."
    ]
  }
}
```

---

### 3. **Consultar Solicitud Individual**

**GET** `/api/v1/solicitudes/{id}`

Obtiene los detalles completos de una solicitud específica, incluyendo sus movimientos relacionados.

#### Parámetros de URL:

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | integer | ID de la solicitud |

#### Ejemplo de Request:

```bash
GET http://tu-dominio.com/api/v1/solicitudes/42
```

#### Ejemplo de Response (200 OK):

```json
{
  "success": true,
  "data": {
    "id": 42,
    "folio": "SOL-042",
    "fecha": "2024-01-15",
    "fecha_requerida": "2024-01-20",
    "solicitante": "Carlos Ramírez",
    "departamento": {
      "id": 8,
      "nombre": "Producción",
      "codigo": "PRO"
    },
    "producto": {
      "id": 25,
      "codigo": "TOR-M10",
      "descripcion": "Tornillo métrico M10x50"
    },
    "cantidad": 100,
    "unidad_medida": {
      "id": 3,
      "codigo": "PZ",
      "nombre": "Piezas"
    },
    "observaciones": "Se requiere para línea de producción B",
    "estado": "aprobada",
    "prioridad": "alta",
    "usuario_registro": {
      "id": 5,
      "name": "María López",
      "email": "maria@empresa.com"
    },
    "movimientos": [
      {
        "id": 125,
        "tipo": "salida",
        "cantidad": 100,
        "fecha": "2024-01-16"
      }
    ],
    "created_at": "2024-01-15 14:25:30",
    "updated_at": "2024-01-16 09:15:00"
  }
}
```

#### Ejemplo de Response (404 Not Found):

```json
{
  "success": false,
  "message": "Solicitud no encontrada"
}
```

---

### 4. **Actualizar Estado de Solicitud**

**PATCH** `/api/v1/solicitudes/{id}/estado`

Actualiza únicamente el estado de una solicitud existente.

#### Parámetros de URL:

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | integer | ID de la solicitud |

#### Headers:

```
Content-Type: application/json
```

#### Body (JSON):

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `estado` | string | ✅ **Sí** | Nuevo estado de la solicitud |

##### Valores válidos:
- `pendiente`
- `aprobada`
- `entregada`
- `cancelada`

#### Ejemplo de Request:

```json
PATCH http://tu-dominio.com/api/v1/solicitudes/42/estado
Content-Type: application/json

{
  "estado": "aprobada"
}
```

#### Ejemplo de Response (200 OK):

```json
{
  "success": true,
  "message": "Estado actualizado exitosamente",
  "data": {
    "id": 42,
    "estado": "aprobada"
  }
}
```

#### Ejemplo de Response (404 Not Found):

```json
{
  "success": false,
  "message": "Solicitud no encontrada"
}
```

---

## Códigos de Respuesta HTTP

| Código | Descripción |
|--------|-------------|
| 200 | OK - Solicitud procesada exitosamente |
| 201 | Created - Recurso creado exitosamente |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Error de validación |
| 500 | Internal Server Error - Error del servidor |

---

## Estructura General de Respuestas

### Respuesta Exitosa

```json
{
  "success": true,
  "message": "Mensaje descriptivo (opcional)",
  "data": { ... }
}
```

### Respuesta con Error

```json
{
  "success": false,
  "message": "Mensaje de error",
  "error": "Detalle técnico del error (opcional)"
}
```

### Respuesta con Error de Validación

```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "campo1": ["mensaje de error 1"],
    "campo2": ["mensaje de error 2"]
  }
}
```

---

## Ejemplos de Uso

### JavaScript (Fetch API):

```javascript
// Crear solicitud
async function crearSolicitud() {
  try {
    const response = await fetch('http://tu-dominio.com/api/v1/solicitudes', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        usuario_registro_id: 5,
        fecha: '2024-01-15',
        fecha_requerida: '2024-01-20',
        solicitante: 'Carlos Ramírez',
        departamento_nombre: 'Producción',
        producto_id: 25,
        cantidad: 100,
        observaciones: 'Urgente para línea B',
        estado: 'pendiente',
        prioridad: 'alta'
      })
    });

    const data = await response.json();
    
    if (data.success) {
      console.log('Solicitud creada:', data.data);
    } else {
      console.error('Error:', data.message);
    }
  } catch (error) {
    console.error('Error de red:', error);
  }
}

// Listar solicitudes
async function listarSolicitudes() {
  try {
    const response = await fetch('http://tu-dominio.com/api/v1/solicitudes?estado=pendiente&per_page=20');
    const data = await response.json();
    
    if (data.success) {
      console.log('Solicitudes:', data.data);
      console.log('Paginación:', data.pagination);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}
```

### Python (requests):

```python
import requests

# Crear solicitud
def crear_solicitud():
    url = 'http://tu-dominio.com/api/v1/solicitudes'
    data = {
        'usuario_registro_id': 5,
        'fecha': '2024-01-15',
        'fecha_requerida': '2024-01-20',
        'solicitante': 'Carlos Ramírez',
        'departamento_nombre': 'Producción',
        'producto_id': 25,
        'cantidad': 100,
        'observaciones': 'Urgente para línea B',
        'estado': 'pendiente',
        'prioridad': 'alta'
    }
    
    response = requests.post(url, json=data)
    
    if response.status_code == 201:
        print('Solicitud creada:', response.json())
    else:
        print('Error:', response.json())

# Listar solicitudes
def listar_solicitudes():
    url = 'http://tu-dominio.com/api/v1/solicitudes'
    params = {'estado': 'pendiente', 'per_page': 20}
    
    response = requests.get(url, params=params)
    
    if response.status_code == 200:
        data = response.json()
        print('Solicitudes:', data['data'])
        print('Paginación:', data['pagination'])
```

### cURL:

```bash
# Crear solicitud
curl -X POST http://tu-dominio.com/api/v1/solicitudes \
  -H "Content-Type: application/json" \
  -d '{
    "usuario_registro_id": 5,
    "fecha": "2024-01-15",
    "fecha_requerida": "2024-01-20",
    "solicitante": "Carlos Ramírez",
    "departamento_nombre": "Producción",
    "producto_id": 25,
    "cantidad": 100,
    "observaciones": "Urgente para línea B",
    "estado": "pendiente",
    "prioridad": "alta"
  }'

# Listar solicitudes
curl -X GET "http://tu-dominio.com/api/v1/solicitudes?estado=pendiente&per_page=20"

# Consultar solicitud
curl -X GET http://tu-dominio.com/api/v1/solicitudes/42

# Actualizar estado
curl -X PATCH http://tu-dominio.com/api/v1/solicitudes/42/estado \
  -H "Content-Type: application/json" \
  -d '{"estado": "aprobada"}'
```

---

## Notas Importantes

1. **usuario_registro_id**: Debe ser un ID válido de un usuario existente en la tabla `users`. Este campo identifica quién está creando la solicitud desde el frontend externo.

2. **Departamento**: Puedes proporcionar:
   - `departamento_id`: Si ya conoces el ID del departamento
   - `departamento_nombre`: Si el departamento no existe, se creará automáticamente con un código generado a partir de las primeras 3 letras del nombre

3. **Unidad de Medida**: Si no proporcionas `unidad_medida_id`, el sistema usará automáticamente la unidad de medida asociada al producto.

4. **Producto**: El `producto_id` debe existir en la base de datos. Puedes consultar productos disponibles usando la API de productos: `GET /api/v1/productos/buscar?q=termino`

5. **Valores por Defecto**:
   - `estado`: `pendiente`
   - `prioridad`: `normal`

6. **Logs**: Todas las operaciones se registran en los logs del sistema para auditoría.

---

## Testing

Puedes probar la API usando herramientas como:
- **Postman**: Importa las rutas y prueba los endpoints
- **Insomnia**: Cliente REST moderno
- **cURL**: Desde la línea de comandos
- **Thunder Client**: Extensión de VS Code

---

## Soporte

Para más información sobre otras APIs disponibles, consulta:
- `API_QUICK_GUIDE.md`: Guía rápida de todas las APIs
- `API_DOCUMENTATION.md`: Documentación completa del sistema
- `API_FLUTTER_PRODUCTOS.md`: API de productos específica para Flutter

---

**Fecha de creación:** 2024
**Última actualización:** 2024
