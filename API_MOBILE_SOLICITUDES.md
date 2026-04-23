# API Solicitudes - App Móvil

## Descripción

Endpoint para crear solicitudes desde la aplicación móvil. Este endpoint consulta automáticamente el sistema de RH externo para obtener información del usuario y su departamento.

---

## Endpoint

**POST** `/api/v1/solicitudes`

---

## Flujo de Trabajo

1. La app móvil envía: `user_id`, `email`, `producto_id`, `cantidad`, `observaciones`
2. El sistema consulta la API de RH: `https://services.satechenergy.com/api/rh/users/buscar-por-email`
3. Obtiene información del usuario: nombre completo y departamento
4. Busca o crea el departamento por nombre
5. Completa automáticamente los campos: `fecha`, `fecha_requerida`, `solicitante`, `departamento_id`, `estado`, `prioridad`
6. Crea la solicitud y retorna la respuesta

---

## Request desde App Móvil

### Headers
```
Content-Type: application/json
```

### Body (JSON)

```json
{
  "user_id": 5,
  "email": "usuario@empresa.com",
  "producto_id": 25,
  "cantidad": 50.5,
  "observaciones": "Urgente..."
}
```

### Parámetros

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `user_id` | integer | ✅ Sí | ID del usuario en la base de datos local |
| `email` | string | ✅ Sí | Email del usuario (debe existir en sistema de RH) |
| `producto_id` | integer | ✅ Sí | ID del producto solicitado |
| `cantidad` | number | ✅ Sí | Cantidad solicitada (mínimo 0.01) |
| `observaciones` | string | ❌ No | Observaciones adicionales |

---

## Respuesta Exitosa (201 Created)

```json
{
  "success": true,
  "message": "Solicitud creada exitosamente",
  "data": {
    "id": 42,
    "folio": null,
    "fecha": "2026-03-04",
    "fecha_requerida": "2026-03-11",
    "solicitante": "Carlos Ramírez García",
    "departamento": {
      "id": 8,
      "nombre": "Manufactura",
      "codigo": "MAN"
    },
    "producto": {
      "id": 25,
      "codigo": "TOR-M10",
      "descripcion": "Tornillo métrico M10x50"
    },
    "cantidad": 50.5,
    "unidad_medida": {
      "id": 3,
      "codigo": "PZ",
      "nombre": "Piezas"
    },
    "observaciones": "Urgente...",
    "estado": "pendiente",
    "prioridad": "normal",
    "usuario_registro": {
      "id": 5,
      "name": "Carlos Ramírez",
      "email": "usuario@empresa.com"
    },
    "created_at": "2026-03-04 14:25:30"
  }
}
```

---

## Respuestas de Error

### Usuario no encontrado localmente (404)

```json
{
  "success": false,
  "message": "Usuario no encontrado en el sistema local"
}
```

### Usuario no encontrado en RH (404)

```json
{
  "success": false,
  "message": "No se pudo obtener información del usuario desde el sistema de RH"
}
```

### Usuario sin departamento en RH (422)

```json
{
  "success": false,
  "message": "El usuario no tiene departamento asignado en el sistema de RH"
}
```

### Producto no encontrado (404)

```json
{
  "success": false,
  "message": "Producto no encontrado"
}
```

### Error de validación (422)

```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "user_id": [
      "El campo user id es obligatorio."
    ],
    "email": [
      "El campo email debe ser una dirección de correo válida."
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

## Campos Autocompletados

El sistema completa automáticamente los siguientes campos:

| Campo | Valor |
|-------|-------|
| `fecha` | Fecha actual del sistema |
| `fecha_requerida` | Fecha actual + 7 días |
| `solicitante` | Nombre completo del usuario desde RH |
| `departamento_id` | ID del departamento (creado desde RH si no existe) |
| `unidad_medida_id` | Unidad de medida del producto |
| `estado` | `pendiente` |
| `prioridad` | `normal` |

---

## Integración con Sistema de RH

### API Externa Consultada

**Endpoint:** `POST https://services.satechenergy.com/api/rh/users/buscar-por-email`

**Request:**
```json
{
  "email": "usuario@empresa.com"
}
```

**Response Esperada:**
```json
{
  "success": true,
  "data": {
    "id": 275,
    "uuid": "05ed5f77-5319-4677-b84c-2013120c5c12",
    "nombre_completo": "Carlos Ramírez García",
    "nombre": "Carlos",
    "apellido": "Ramírez García",
    "email": "usuario@empresa.com",
    "telefono": "5512345678",
    "fecha_admission": "2023-09-01",
    "activo": true,
    "puesto": {
      "id": 23,
      "nombre": "Técnico Mecánico"
    },
    "departamento": {
      "id": 8,
      "nombre": "Manufactura"
    },
    "area": {
      "id": 13,
      "nombre": "Servicios Técnicos"
    }
  }
}
```

### Sincronización de Departamentos

Los departamentos se crean automáticamente si no existen:

1. **Búsqueda**: Se busca el departamento por `nombre`
2. **Creación**: Si no existe, se crea con el nombre del sistema de RH
3. **Reutilización**: En futuras solicitudes, se reutiliza el departamento existente

**Ejemplo de departamento creado:**

| Campo | Valor |
|-------|-------|
| `id` | 8 (autoincremental local) |
| `nombre` | Manufactura |
| `codigo` | MAN |

---

## Diferencias con API Web

### App Móvil

- ✅ Solo requiere: `user_id`, `email`, `producto_id`, `cantidad`, `observaciones`
- ✅ Consulta automática al sistema de RH
- ✅ Autocompleta campos faltantes
- ✅ Crea departamentos automáticamente por nombre
- ✅ Validación simplificada

### API Web (formato original)

- ✅ Requiere todos los campos explícitos
- ✅ Permite especificar `departamento_id` o `departamento_nombre`
- ✅ Control total sobre fechas, estado y prioridad
- ✅ No consulta sistemas externos

**Ambos flujos están disponibles en el mismo endpoint**. El sistema detecta automáticamente de dónde proviene la solicitud basándose en los parámetros enviados.

---

## Logs del Sistema

El sistema registra todas las operaciones:

```
[2026-03-04 14:25:30] INFO: Departamento para solicitud: ID 8, Nombre: Manufactura
[2026-03-04 14:25:30] INFO: Solicitud creada desde API (App Móvil): ID 42, Usuario ID 5, Producto ID 25
```

Si no se puede consultar el sistema de RH:

```
[2026-03-04 14:25:30] WARNING: No se pudo obtener información de RH para el email: usuario@empresa.com. Status: 404
```

---

## Testing

### cURL

```bash
curl -X POST https://inventario-almacen.tech-energy.lat/api/v1/solicitudes \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 5,
    "email": "usuario@empresa.com",
    "producto_id": 25,
    "cantidad": 50.5,
    "observaciones": "Urgente..."
  }'
```

### JavaScript (Fetch)

```javascript
async function crearSolicitudMovil() {
  try {
    const response = await fetch('https://inventario-almacen.tech-energy.lat/api/v1/solicitudes', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        user_id: 5,
        email: 'usuario@empresa.com',
        producto_id: 25,
        cantidad: 50.5,
        observaciones: 'Urgente...'
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
```

### Dart/Flutter

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

Future<Map<String, dynamic>> crearSolicitud({
  required int userId,
  required String email,
  required int productoId,
  required double cantidad,
  String? observaciones,
}) async {
  final url = Uri.parse('https://inventario-almacen.tech-energy.lat/api/v1/solicitudes');
  
  final response = await http.post(
    url,
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'user_id': userId,
      'email': email,
      'producto_id': productoId,
      'cantidad': cantidad,
      'observaciones': observaciones,
    }),
  );

  return jsonDecode(response.body);
}

// Uso
void main() async {
  final result = await crearSolicitud(
    userId: 5,
    email: 'usuario@empresa.com',
    productoId: 25,
    cantidad: 50.5,
    observaciones: 'Urgente...',
  );
  
  if (result['success'] == true) {
    print('Solicitud creada: ${result['data']['id']}');
  } else {
    print('Error: ${result['message']}');
  }
}
```

---

## Notas Importantes

1. **Timeout de API de RH**: La consulta al sistema de RH tiene un timeout de 10 segundos. Si no responde, la solicitud falla.

2. **Departamentos por Nombre**: Los departamentos se buscan y crean por nombre. Si ya existe un departamento con el mismo nombre, se reutiliza.

3. **Unidad de Medida**: Se usa automáticamente la unidad de medida configurada en el producto.

4. **Fecha Requerida**: Se establece automáticamente a 7 días desde la fecha actual.

5. **Usuario Local**: El `user_id` debe existir en la tabla `users` local. El email se usa solo para consultar el sistema de RH.

---

## Modelo de Datos

### Tabla: `departamentos`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT | ID autoincremental local |
| `nombre` | VARCHAR(100) | Nombre del departamento (unique) |
| `codigo` | VARCHAR(50) | Código del departamento (nullable) |
| `descripcion` | VARCHAR(255) | Descripción (nullable) |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Fecha de actualización |

### Tabla: `solicitudes`

Ver documentación completa en [API_SOLICITUDES_GUIDE.md](API_SOLICITUDES_GUIDE.md)

---

## Soporte y Referencias

- **API General de Solicitudes**: [API_SOLICITUDES_GUIDE.md](API_SOLICITUDES_GUIDE.md)
- **API de Productos**: [API_FLUTTER_PRODUCTOS.md](API_FLUTTER_PRODUCTOS.md)
- **Guía Rápida**: [API_QUICK_GUIDE.md](API_QUICK_GUIDE.md)

---

**Última actualización:** 4 de marzo de 2026
