# 📱 Documentación API para Desarrollo Móvil

Bienvenido al API REST del Sistema de Inventario de Almacén. Esta documentación contiene toda la información necesaria para integrar tu aplicación móvil con nuestro backend.

---

## 📚 Archivos de Documentación

### 1. **API_DOCUMENTATION.md** (Documentación Completa)
- 📄 **Qué contiene:** Documentación detallada de todos los endpoints
- 🎯 **Para quién:** Desarrolladores que necesitan entender a fondo cada endpoint
- 📝 **Incluye:**
  - Descripción detallada de cada endpoint
  - Todos los parámetros (query, path, body)
  - Ejemplos de request y response
  - Códigos de error
  - Casos de uso completos en JavaScript/React Native

### 2. **API_QUICK_REFERENCE.md** (Guía Rápida)
- 📄 **Qué contiene:** Resumen ejecutivo de 2 páginas
- 🎯 **Para quién:** Referencia rápida durante el desarrollo
- 📝 **Incluye:**
  - Lista de todos los endpoints
  - Estructura de datos principales
  - Ejemplos prácticos más comunes
  - Filtros disponibles
  - Límites y restricciones

### 3. **postman_collection.json** (Colección Postman)
- 📄 **Qué contiene:** Colección importable para Postman/Insomnia
- 🎯 **Para quién:** Testing y pruebas de endpoints
- 📝 **Incluye:**
  - Todos los endpoints configurados
  - Parámetros pre-configurados
  - Variables de entorno
  - Listo para importar y probar

---

## 🚀 Inicio Rápido

### 1. **Configuración Inicial**

```javascript
// Configurar la URL base en tu app
const API_BASE_URL = 'http://localhost:8000/api/v1';

// Headers recomendados
const headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
};
```

### 2. **Primeros Pasos**

```javascript
// Ejemplo 1: Obtener estadísticas del dashboard
const stats = await fetch(`${API_BASE_URL}/productos/stats`)
  .then(res => res.json());

console.log(stats.total_productos); // 1250

// Ejemplo 2: Buscar productos
const resultados = await fetch(`${API_BASE_URL}/productos/buscar?q=rodamiento`)
  .then(res => res.json());

console.log(resultados.data); // Array de productos
```

### 3. **Importar Colección Postman**

1. Abre Postman o Insomnia
2. Click en "Import"
3. Selecciona el archivo `postman_collection.json`
4. ¡Listo! Todos los endpoints estarán disponibles para probar

---

## 📊 Endpoints Principales

### **Productos**
```
GET /productos              - Listar con paginación
GET /productos/buscar       - Búsqueda global
GET /productos/{id}         - Detalle de producto
GET /productos/stats        - Estadísticas
```

### **Movimientos**
```
GET /movimientos                        - Listar con paginación
GET /productos/{id}/movimientos         - Historial de producto
GET /movimientos/stats                  - Estadísticas
```

### **Catálogos**
```
GET /catalogos                          - Todos los catálogos
GET /departamentos/buscar               - Buscar departamentos
GET /unidades-medida/buscar             - Buscar unidades
```

---

## 💡 Casos de Uso Más Comunes

### **Dashboard de la App**
```javascript
// Cargar estadísticas generales
const dashboard = async () => {
  const [productos, movimientos] = await Promise.all([
    fetch(`${API_BASE_URL}/productos/stats`).then(r => r.json()),
    fetch(`${API_BASE_URL}/movimientos/stats`).then(r => r.json())
  ]);
  
  return {
    totalProductos: productos.total_productos,
    stockBajo: productos.productos_stock_bajo,
    valorInventario: productos.valor_total_inventario_mxn,
    movimientosHoy: movimientos.movimientos_hoy
  };
};
```

### **Buscador con Autocompletado**
```javascript
// Implementar búsqueda mientras el usuario escribe
const buscarProductos = async (texto) => {
  if (!texto || texto.length < 2) return [];
  
  const url = `${API_BASE_URL}/productos/buscar?q=${encodeURIComponent(texto)}&limit=10`;
  const { data } = await fetch(url).then(r => r.json());
  
  return data.map(p => ({
    id: p.id,
    label: `${p.codigo} - ${p.descripcion}`,
    stock: p.fisico,
    precio: p.pu
  }));
};
```

### **Lista de Productos con Scroll Infinito**
```javascript
// Cargar productos con paginación
let paginaActual = 1;

const cargarMasProductos = async () => {
  const url = `${API_BASE_URL}/productos?per_page=20&page=${paginaActual}`;
  const response = await fetch(url).then(r => r.json());
  
  // Agregar a la lista existente
  listaProductos = [...listaProductos, ...response.data];
  
  // Verificar si hay más páginas
  if (response.current_page < response.last_page) {
    paginaActual++;
    return true; // Hay más productos
  }
  
  return false; // No hay más productos
};
```

### **Detalle de Producto con Historial**
```javascript
// Pantalla de detalle de producto
const cargarDetalleProducto = async (productoId) => {
  // Cargar producto y sus movimientos en paralelo
  const [producto, historial] = await Promise.all([
    fetch(`${API_BASE_URL}/productos/${productoId}`).then(r => r.json()),
    fetch(`${API_BASE_URL}/productos/${productoId}/movimientos?per_page=10`)
      .then(r => r.json())
  ]);
  
  return {
    ...producto,
    movimientos: historial.movimientos.data,
    totalMovimientos: historial.movimientos.total
  };
};
```

---

## 🔍 Estructura de Datos

### **Producto**
```typescript
interface Producto {
  id: number;
  codigo: string;          // "ROD-001-025"
  descripcion: string;     // "Rodamiento 6205 2RS"
  ubicacion: string;       // "A-12"
  um: string;              // "PZA"
  fisico: number;          // 45.00
  pu: number;              // 125.50
}
```

### **Movimiento**
```typescript
interface Movimiento {
  id: number;
  producto_id: number;
  tipo_movimiento: 'entrada' | 'salida' | 'ajuste' | 'transferencia';
  cantidad: number;
  cantidad_anterior: number;
  cantidad_nueva: number;
  descripcion: string;
  created_at: string;      // ISO 8601
  producto: Producto;
  usuario: Usuario;
}
```

### **Respuesta Paginada**
```typescript
interface RespuestaPaginada<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}
```

---

## ⚠️ Notas Importantes

1. **Sin Autenticación (Por ahora)**
   - Actualmente los endpoints son públicos
   - En el futuro se implementará Laravel Sanctum

2. **Límites**
   - Máximo 100 resultados por página
   - Búsquedas limitadas a 100 resultados

3. **Formatos**
   - Fechas: ISO 8601 (YYYY-MM-DD o YYYY-MM-DDTHH:mm:ss.sssZ)
   - Números: Decimales con 2 posiciones
   - Búsquedas: No case-sensitive

4. **Rendimiento**
   - Los endpoints están optimizados con campos simplificados
   - Usa paginación para listas grandes
   - Implementa debounce en búsquedas (mínimo 300ms)

---

## 🐛 Soporte y Reportes

**¿Encontraste un bug o necesitas ayuda?**

1. Revisa primero la documentación completa en `API_DOCUMENTATION.md`
2. Prueba el endpoint en Postman usando la colección
3. Contacta al equipo de backend

---

## 📝 Changelog

**v1.0 - 24 de Febrero, 2026**
- ✅ Endpoints de Productos (listar, buscar, detalle, stats)
- ✅ Endpoints de Movimientos (listar, filtrar, historial, stats)
- ✅ Endpoints de Catálogos (categorías, departamentos, unidades)
- ⏳ Autenticación (próximamente)
- ⏳ Endpoints POST/PUT/DELETE (próximamente)

---

## 🎯 Próximos Pasos

1. **Lee** `API_QUICK_REFERENCE.md` para familiarizarte con los endpoints
2. **Importa** `postman_collection.json` y prueba los endpoints
3. **Implementa** los casos de uso básicos en tu app móvil
4. **Consulta** `API_DOCUMENTATION.md` cuando necesites detalles específicos

---

**¡Feliz codificación! 🚀**

*Última actualización: 24 de Febrero, 2026*
