# DOCUMENTACIÓN SISTEMA DE INVENTARIO DE ALMACÉN

**Versión:** 1.0  
**Fecha:** Marzo 2026  
**Framework:** Laravel 12  
**Base URL:** http://localhost:8000/api/v1

---

## TABLA DE CONTENIDOS

1. [Descripción General](#1-descripción-general)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Instalación y Configuración](#3-instalación-y-configuración)
4. [Configuración Técnica](#4-configuración-técnica)
5. [API REST - Endpoints](#5-api-rest---endpoints)
6. [Estructuras de Datos](#6-estructuras-de-datos)
7. [Sistema de Búsqueda](#7-sistema-de-búsqueda)
8. [Carga Masiva de Productos](#8-carga-masiva-de-productos)
9. [Interfaz Web](#9-interfaz-web)
10. [Códigos de Respuesta HTTP](#10-códigos-de-respuesta-http)
11. [Optimizaciones y Buenas Prácticas](#11-optimizaciones-y-buenas-prácticas)
12. [Apéndices](#12-apéndices)

---

## 1. DESCRIPCIÓN GENERAL

### 1.1 Propósito del Sistema

El Sistema de Inventario de Almacén es una aplicación web empresarial desarrollada con Laravel 12 para la gestión integral de inventarios en almacenes y centros de distribución. 

**Objetivo:** Proporcionar control completo sobre el inventario de productos, desde su entrada hasta su salida, con trazabilidad total de movimientos, integración con sistemas móviles, y capacidad de importación masiva de datos.

**Usuarios objetivo:**
- Personal de almacén
- Supervisores de inventario
- Departamentos de compras
- Personal operativo con dispositivos móviles

### 1.2 Características Principales

**Gestión de Inventario:**
- Registro completo de productos con múltiples atributos
- Catálogos relacionados (categorías, familias, componentes, ubicaciones)
- Sistema de movimientos de inventario (entradas, salidas, ajustes, transferencias)
- Historial completo de transacciones por producto
- Control de stock y alertas de niveles bajos

**Sistema de Solicitudes:**
- Solicitudes de materiales entre departamentos
- Aprobación y seguimiento de solicitudes
- Historial de solicitudes por usuario

**Búsqueda y Reportes:**
- Búsqueda multicriterio avanzada
- Filtros por categoría, familia, componente, ubicación
- Estadísticas generales de inventario
- Reportes de entradas y salidas

**Integración:**
- API REST pública para aplicaciones móviles y sistemas externos
- Importación masiva de productos desde archivos Excel
- Soporte completo para caracteres especiales Unicode

**Seguridad:**
- Autenticación con Google OAuth2 (interfaz web)
- Log de actividades de usuarios
- Control de acceso por roles

### 1.3 Requisitos del Sistema

**Backend:**
- PHP 8.2 o superior
- Composer 2.x
- MySQL 8.0 o MariaDB 10.5+
- Laravel 12
- PhpSpreadsheet 1.29+ (incluido en dependencias)

**Frontend:**
- Node.js 18.x o superior
- NPM 9.x o superior
- Tailwind CSS 3.x
- Vite 5.x

**Servicios Externos:**
- Cuenta Google Cloud (para OAuth2)
- Servidor web Apache/Nginx recomendado para producción

**Recomendaciones hardware:**
- Mínimo 2GB RAM
- 1GB espacio en disco
- Procesador dual-core

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Arquitectura General

El sistema sigue el patrón MVC (Model-View-Controller) de Laravel con las siguientes capas:

```
┌─────────────────────────────────────────────────┐
│          CAPA DE PRESENTACIÓN                   │
│  ┌──────────────┐        ┌──────────────┐      │
│  │  Blade Views │        │  API REST    │      │
│  │  + Tailwind  │        │  (JSON)      │      │
│  └──────────────┘        └──────────────┘      │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│          CAPA DE CONTROLADORES                  │
│  ┌──────────────────────────────────────┐      │
│  │  Web Controllers  │  API Controllers │      │
│  │  - Dashboard      │  - ProductoAPI   │      │
│  │  - Productos      │  - MovimientoAPI │      │
│  │  - Movimientos    │  - CatalogosAPI  │      │
│  │  - Solicitudes    │                  │      │
│  │  - Reportes       │                  │      │
│  └──────────────────────────────────────┘      │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│          CAPA DE LÓGICA DE NEGOCIO              │
│  ┌──────────────────────────────────────┐      │
│  │  Eloquent Models                     │      │
│  │  - Producto        - Movimiento      │      │
│  │  - Categoria       - Solicitud       │      │
│  │  - Familia         - User            │      │
│  │  - Componente      - Ubicacion       │      │
│  └──────────────────────────────────────┘      │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│          CAPA DE DATOS                          │
│  ┌──────────────────────────────────────┐      │
│  │  MySQL Database (UTF8MB4)            │      │
│  └──────────────────────────────────────┘      │
└─────────────────────────────────────────────────┘
```

### 2.2 Estructura de Directorios

```
inventario_almacen/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # Controladores API REST
│   │   │   │   ├── ProductoController.php
│   │   │   │   ├── MovimientoController.php
│   │   │   │   └── CatalogoController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── GoogleController.php    # OAuth2 Google
│   │   │   ├── ProductoController.php
│   │   │   ├── MovimientoController.php
│   │   │   ├── SolicitudController.php
│   │   │   └── ReportesController.php  # Importaciones masivas
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Producto.php
│   │   ├── Categoria.php
│   │   ├── Familia.php
│   │   ├── Componente.php
│   │   ├── Ubicacion.php
│   │   ├── UnidadMedida.php
│   │   ├── Movimiento.php
│   │   ├── Solicitud.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
├── config/                       # Configuración del sistema
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── services.php             # Config OAuth2
├── database/
│   ├── migrations/              # Esquema de base de datos
│   └── seeders/                 # Datos iniciales
├── public/
│   ├── index.php               # Entry point
│   ├── buscar.html             # Buscador público
│   └── build/                  # Assets compilados
├── resources/
│   ├── css/
│   │   └── app.css             # Tailwind CSS
│   ├── js/
│   │   └── app.js              # JavaScript principal
│   └── views/                  # Vistas Blade
│       ├── layouts/
│       │   └── app.blade.php
│       ├── dashboard.blade.php
│       ├── productos/
│       ├── movimientos/
│       ├── solicitudes/
│       └── reportes/
│           ├── entradas.blade.php
│           └── barras.blade.php
├── routes/
│   ├── web.php                 # Rutas interfaz web
│   ├── api.php                 # Rutas API REST
│   └── console.php
├── storage/
│   ├── app/                    # Archivos cargados
│   ├── framework/              # Cache, sesiones
│   └── logs/                   # Logs del sistema
├── tests/                      # Pruebas unitarias/features
└── vendor/                     # Dependencias PHP
```

### 2.3 Módulos Principales

**1. Módulo de Productos**
- CRUD completo de productos
- Gestión de catálogos relacionados
- Búsqueda y filtrado avanzado
- Importación masiva desde Excel

**2. Módulo de Movimientos**
- Registro de entradas/salidas
- Ajustes de inventario
- Transferencias entre ubicaciones
- Historial completo por producto

**3. Módulo de Solicitudes**
- Solicitudes de materiales
- Workflow de aprobación
- Seguimiento de estado

**4. Módulo de Reportes**
- Reportes por categoría (Barras, Entradas)
- Importación masiva con preview
- Validación de datos
- Logs detallados

**5. Módulo API REST**
- Endpoints públicos sin autenticación
- Formato JSON estándar
- Paginación optimizada
- Búsqueda multicriterio

### 2.4 Tecnologías Utilizadas

**Backend:**
- Framework: Laravel 12
- Lenguaje: PHP 8.2
- ORM: Eloquent
- Autenticación: Laravel Socialite (Google OAuth2)
- Excel: PhpSpreadsheet 1.29

**Frontend:**
- Plantillas: Blade
- CSS: Tailwind CSS 3.x
- JavaScript: Vanilla JS (búsqueda dinámica)
- Build: Vite 5.x
- Librería Excel: XLSX.js (preview)

**Base de Datos:**
- Motor: MySQL 8.0 / MariaDB 10.5+
- Charset: utf8mb4
- Migraciones: Laravel Migrations

**Desarrollo:**
- Composer: Gestión de dependencias PHP
- NPM: Gestión de dependencias Node
- Git: Control de versiones

---

## 3. INSTALACIÓN Y CONFIGURACIÓN

### 3.1 Requisitos Previos

Antes de instalar, asegurar que estén instalados:
- XAMPP/WAMP/LAMP o servidor PHP independiente
- Composer global
- Node.js y NPM
- MySQL en ejecución

### 3.2 Pasos de Instalación

**1. Clonar o descargar el proyecto**
```bash
cd c:\xampp\htdocs\inventario_almacen
```

**2. Instalar dependencias PHP**
```bash
composer install
```

**3. Instalar dependencias Node**
```bash
npm install
```

**4. Configurar archivo .env**
```bash
# Copiar archivo de ejemplo
copy .env.example .env

# Editar .env con tus configuraciones
```

**Configuración mínima en .env:**
```env
APP_NAME="Sistema de Inventario"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario_almacen
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

SESSION_DRIVER=database
```

**5. Generar key de aplicación**
```bash
php artisan key:generate
```

**6. Crear base de datos**
```sql
CREATE DATABASE inventario_almacen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**7. Ejecutar migraciones**
```bash
php artisan migrate
```

**8. (Opcional) Ejecutar seeders**
```bash
php artisan db:seed
```

**9. Compilar assets**
```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

**10. Iniciar servidor**
```bash
# Servidor de desarrollo Laravel
php artisan serve

# O usar Apache/Nginx apuntando a /public
```

**11. Acceder al sistema**
- Web: http://localhost:8000
- API: http://localhost:8000/api/v1

### 3.3 Configuración de Google OAuth2

Para habilitar autenticación con Google:

**1. Crear proyecto en Google Cloud Console**
- Ir a https://console.cloud.google.com
- Crear nuevo proyecto
- Habilitar Google+ API

**2. Crear credenciales OAuth 2.0**
- Ir a "Credenciales"
- Crear credenciales > ID de cliente de OAuth 2.0
- Tipo de aplicación: Aplicación web
- URIs de redireccionamiento autorizados:
  ```
  http://localhost:8000/auth/google/callback
  http://tu-dominio.com/auth/google/callback
  ```

**3. Configurar .env**
```env
GOOGLE_CLIENT_ID=tu_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

**4. Instalar Laravel Socialite** (si no está instalado)
```bash
composer require laravel/socialite
```

**5. Configurar config/services.php**
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

**6. Rutas configuradas**
- Login: `/auth/google`
- Callback: `/auth/google/callback`
- Logout: `/logout`

### 3.4 Configuración de Permisos

**Linux/Mac:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Windows (XAMPP):**
No requiere configuración adicional, XAMPP maneja permisos automáticamente.

### 3.5 Configuración para Producción

**1. Cambiar .env para producción:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

**2. Optimizar aplicación:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

**3. Compilar assets en modo producción:**
```bash
npm run build
```

**4. Configurar servidor web:**
- Document root debe apuntar a `/public`
- Habilitar mod_rewrite (Apache) o try_files (Nginx)

**Ejemplo Apache (.htaccess en /public):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Ejemplo Nginx:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 3.6 Verificación de Instalación

**Verificar conexión a BD:**
```bash
php artisan migrate:status
```

**Verificar rutas:**
```bash
php artisan route:list
```

**Verificar permisos:**
```bash
php artisan storage:link
```

**Acceder al buscador público:**
http://localhost:8000/buscar.html

**Probar API:**
```bash
curl http://localhost:8000/api/v1/productos/stats
```

---

## 4. CONFIGURACIÓN TÉCNICA

### 4.1 Base de Datos

**Motor:** MySQL/MariaDB  
**Charset:** utf8mb4  
**Collation:** utf8mb4_unicode_ci  

**Soporte de caracteres especiales:**
- Símbolos de diámetro: ⌀, Ø
- Fracciones: 3/8", 1/4", ½, ¾
- Símbolos matemáticos: ×, °, №

### 4.2 API REST

**URL Base:** http://localhost:8000/api/v1  
**Formato:** JSON  
**Autenticación:** No requerida (público)  
**Charset:** UTF-8  

**Límites:**
- Resultados por página: 15 (default), máximo 100
- Búsqueda: máximo 100 resultados
- Tamaño archivo importación: 10 MB

### 4.3 Logging

Los archivos de log se almacenan en `storage/logs/laravel.log`

Para importaciones masivas, se registran las primeras 5 filas con información detallada:
```
[BARRAS] Fila 2 - Descripción leída: 'Tubo Ø 25mm' [12 bytes]
[ENTRADAS] Fila 3 - Descripción leída: 'Placa 3/4" x 1/2"' [18 bytes]
```

### 4.4 Configuración de PhpSpreadsheet

Para importaciones Excel, PhpSpreadsheet está configurado para preservar caracteres UTF-8:

```php
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setDecimalSeparator('.');
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setThousandsSeparator(',');
```

Esto asegura que caracteres especiales como ⌀, 3/8", etc. se guarden correctamente.

**Consultar logs en diferentes sistemas:**
```bash
# Ver últimas líneas en tiempo real (Linux/Mac)
tail -f storage/logs/laravel.log

# Buscar errores de importación (Windows PowerShell)
Select-String -Path storage/logs/laravel.log -Pattern "BARRAS|ENTRADAS"

# Linux/Mac
grep "BARRAS\|ENTRADAS" storage/logs/laravel.log
```

---

## 5. API REST - ENDPOINTS

### 5.1 Productos

#### Listar Productos con Paginación
```
GET /api/v1/productos
```

**Parámetros Query:**

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| page | integer | No | 1 | Número de página |
| per_page | integer | No | 15 | Resultados por página (máx: 100) |
| search | string | No | - | Búsqueda por código o descripción |
| categoria_id | integer | No | - | Filtrar por categoría |
| familia_id | integer | No | - | Filtrar por familia |
| componente_id | integer | No | - | Filtrar por componente |
| ubicacion_id | integer | No | - | Filtrar por ubicación |
| stock_bajo | boolean | No | - | Filtrar productos con stock bajo |

**Respuesta:**
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 128,
  "per_page": 15,
  "total": 1913
}
```

---

#### Buscar Productos (Recomendado)
```
GET /api/v1/productos/buscar
```

**Parámetros Query:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| q | string | Sí | Término de búsqueda (mínimo 1 carácter) |
| limit | integer | No | Máximo de resultados (default: 50, máx: 100) |

**Respuesta:**
```json
{
  "data": [...],
  "total": 15,
  "query": "rodamiento"
}
```

**Nota:** Este endpoint busca simultáneamente en: codigo, descripcion, ubicacion, unidad_medida

---

#### Buscar por Código (Legacy)
```
GET /api/v1/productos/buscar/{codigo}
```

**Respuesta:**
```json
{
  "data": [...],
  "total": 5
}
```

**Límite:** 20 resultados máximo

---

#### Ver Detalle de Producto
```
GET /api/v1/productos/{id}
```

**Respuesta:**
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

---

#### Estadísticas Generales
```
GET /api/v1/productos/stats
```

**Respuesta:**
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

---

#### Historial de Movimientos por Producto
```
GET /api/v1/productos/{producto_id}/movimientos
```

**Parámetros Query:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| per_page | integer | Resultados por página (default: 15) |
| page | integer | Número de página |

**Respuesta:** Lista paginada de movimientos

---

### 3.2 Movimientos

#### Listar Movimientos
```
GET /api/v1/movimientos
```

**Parámetros Query:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| per_page | integer | Resultados por página |
| producto_id | integer | Filtrar por producto |
| tipo_movimiento | string | entrada, salida, ajuste, transferencia |
| fecha_desde | date | Filtrar desde fecha (YYYY-MM-DD) |
| fecha_hasta | date | Filtrar hasta fecha (YYYY-MM-DD) |

---

#### Ver Detalle de Movimiento
```
GET /api/v1/movimientos/{id}
```

**Respuesta:**
```json
{
  "id": 456,
  "producto_id": 123,
  "tipo_movimiento": "entrada",
  "cantidad": 100.00,
  "cantidad_anterior": 45.00,
  "cantidad_nueva": 145.00,
  "descripcion": "Recepción de compra",
  "referencia": "OC-2024-001",
  "created_at": "2026-02-24T10:30:00.000000Z",
  "producto": {...},
  "usuario": {...}
}
```

---

#### Estadísticas de Movimientos
```
GET /api/v1/movimientos/stats
```

**Respuesta:**
```json
{
  "total_movimientos": 15234,
  "hoy": 45,
  "esta_semana": 312,
  "este_mes": 1456,
  "por_tipo": {
    "entrada": 6500,
    "salida": 7200,
    "ajuste": 1234,
    "transferencia": 300
  }
}
```

---

### 3.3 Catálogos

#### Obtener Todos los Catálogos
```
GET /api/v1/catalogos
```

**Respuesta:**
```json
{
  "categorias": [...],
  "familias": [...],
  "componentes": [...],
  "unidades_medida": [...]
}
```

---

## 6. ESTRUCTURAS DE DATOS

### 6.1 Producto (Simplificado)

Estructura mínima retornada en listados y búsquedas:

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

**Campos:**
- `id`: Identificador único
- `codigo`: Código del producto
- `descripcion`: Descripción completa
- `ubicacion`: Código de ubicación física
- `um`: Unidad de medida
- `fisico`: Cantidad física en stock
- `pu`: Precio unitario

### 6.2 Producto (Completo)

Estructura completa con todas las relaciones:

```json
{
  "id": 123,
  "codigo": "GAC0010001",
  "componente_id": 5,
  "categoria_id": 2,
  "familia_id": 8,
  "consecutivo": "0001",
  "descripcion": "Rodamiento 6205 2RS",
  "unidad_medida_id": 3,
  "ubicacion_id": 12,
  "cantidad_entrada": 150,
  "cantidad_salida": 105,
  "cantidad_fisica": 45,
  "fecha_entrada": "2026-01-15",
  "fecha_salida": "2026-02-20",
  "precio_unitario": 125.50,
  "moneda": "MXN",
  "factura": "FAC-2026-001",
  "observaciones": "Proveedor ABC",
  "fecha_vencimiento": null,
  "hoja_seguridad": null,
  "created_at": "2026-01-15T08:30:00.000000Z",
  "updated_at": "2026-02-24T15:45:00.000000Z"
}
```

### 6.3 Movimiento

```json
{
  "id": 456,
  "producto_id": 123,
  "tipo_movimiento": "entrada",
  "cantidad": 100.00,
  "cantidad_anterior": 45.00,
  "cantidad_nueva": 145.00,
  "descripcion": "Recepción de compra OC-2024-001",
  "referencia": "OC-2024-001",
  "usuario_id": 5,
  "solicitud_id": null,
  "created_at": "2026-02-24T10:30:00.000000Z",
  "producto": {
    "id": 123,
    "codigo": "ROD-001-025",
    "descripcion": "Rodamiento 6205 2RS"
  },
  "usuario": {
    "id": 5,
    "name": "Juan Pérez",
    "email": "juan@example.com"
  }
}
```

**Tipos de movimiento:**
- `entrada`: Recepción de productos
- `salida`: Despacho de productos
- `ajuste`: Corrección de inventario
- `transferencia`: Movimiento entre ubicaciones

### 6.4 Respuesta Paginada

Estructura estándar para endpoints con paginación:

```json
{
  "data": [],
  "current_page": 1,
  "last_page": 128,
  "per_page": 15,
  "total": 1913
}
```

**Optimización:** Se eliminó metadata redundante (links, from, to, path) reduciendo el tamaño de respuesta en ~60%

---

## 7. SISTEMA DE BÚSQUEDA

### 7.1 Búsqueda Global Multicriterio

**Endpoint:** `GET /api/v1/productos/buscar`

**Campos buscados simultáneamente:**
1. Código del producto
2. Descripción
3. Código de ubicación
4. Unidad de medida

**Ordenamiento por relevancia:**
1. Coincidencia exacta en código
2. Código que inicia con el término
3. Descripción que inicia con el término
4. Otras coincidencias parciales

**Ejemplos:**

Buscar "rod":
```
GET /api/v1/productos/buscar?q=rod&limit=10
```

Buscar ubicación "A-3":
```
GET /api/v1/productos/buscar?q=A-3&limit=20
```

Buscar unidad "PZ":
```
GET /api/v1/productos/buscar?q=PZ&limit=50
```

### 7.2 Casos de Uso

**Autocompletado en formularios:**
- Implementar debounce de 300ms
- Limitar a 10 resultados
- Mostrar solo a partir de 2 caracteres

**Búsqueda completa:**
- Permitir búsquedas desde 1 carácter
- Limitar a 50 resultados
- Mostrar indicador cuando hay más resultados

**Búsqueda por código exacto:**
- Usar endpoint legacy: `/productos/buscar/{codigo}`
- Más rápido para códigos conocidos

### 7.3 Comparación: Búsqueda vs Listado

| Aspecto | /productos/buscar | /productos |
|---------|-------------------|------------|
| **Propósito** | Encontrar productos específicos | Navegar inventario completo |
| **Límite resultados** | Top 100 | Todo el inventario |
| **Paginación** | No (top N) | Sí (completa) |
| **Ordenamiento** | Por relevancia | Por código |
| **Performance** | Rápido | Más lento |
| **Uso recomendado** | Búsquedas de usuario | Listados completos |

---

## 8. CARGA MASIVA DE PRODUCTOS

### 8.1 Categoría Barras

**Ruta web:** http://localhost:8000/reportes/barras

#### 8.1.1 Formato del Archivo

**Tipo:** Excel (.xlsx, .xls) o CSV  
**Tamaño máximo:** 10 MB  
**Estructura:** Primera fila debe contener encabezados

**Columnas esperadas:**

| Columna | Campo BD | Obligatorio | Notas |
|---------|----------|-------------|-------|
| CODIGO | codigo | Sí | Identificador único |
| # REQUISICIÓN | numero_requisicion | No | |
| NP | numero_parte | No | Número de parte |
| DIMENSIONES | dimensiones | No | Ej: "3/8\" x 1/4\"" |
| TIPO MATERIAL | componente_id | No | Se crea si no existe |
| PZ | cantidad_entrada | No | Cantidad entrada |
| FIS. | cantidad_fisica | No | Stock físico |
| U.M | unidad_medida_id | No | Se crea si no existe |
| UBIC. | ubicacion_id | No | Se crea si no existe |
| FACTURA | factura | No | |
| OC | orden_compra | No | Orden de compra |
| DESCRIPCIÓN INGRESO | descripcion | No | |
| OBSERVACIONES | observaciones | No | |

#### 8.1.2 Comportamiento

**Actualización inteligente:**
- Si el código existe: actualiza el registro
- Si el código no existe: crea nuevo producto
- Si catálogos no existen: crea automáticamente

**Detección automática de encabezados:**
- Busca en las primeras 10 filas
- No sensible a mayúsculas/minúsculas
- Reconoce variantes: "DESCRIPCIÓN" / "DESCRIPCION"

**Validaciones:**
- Código es obligatorio (filas sin código se descartan o generan temporal)
- Campos obligatorios de BD reciben valores por defecto
- Log detallado de errores

#### 8.1.3 Opciones de Importación

**Página Excel:**
- Especificar número de hoja (1, 2, 3, etc.)
- Validación automática de número de hojas

**Acción para productos sin código:**
- **Descartar:** Ignora filas sin código
- **Generar:** Asigna código temporal (TEM0001, TEM0002, etc.)

### 8.2 Categoría Entradas

**Ruta web:** http://localhost:8000/reportes/entradas

#### 8.2.1 Formato del Archivo

**Columnas adicionales vs Barras:**

| Columna | Campo BD | Notas |
|---------|----------|-------|
| COMP. | componente_id | Componente |
| CAT. | categoria_id | Categoría |
| FAM. | familia_id | Familia |
| CONS. | consecutivo | Consecutivo |
| ENTRADA | cantidad_entrada | |
| FECHA ENTRADA | fecha_entrada | |
| SALIDA | cantidad_salida | |
| FISICO | cantidad_fisica | |
| FECHA SALIDA | fecha_salida | |
| P.U | precio_unitario | |
| MXN/USD | moneda | |
| DN/NP/OBSERVACIÓN | observaciones | |
| FECHA DE VENCIMIENTO | fecha_vencimiento | |
| HOJAS DE SEGURIDAD | hoja_seguridad | |

#### 8.2.2 Modos de Importación

**Actualizar y crear (update_create):**
- Actualiza existentes
- Crea nuevos
- Modo por defecto

**Solo nuevos (only_new):**
- Solo crea productos no existentes
- Ignora productos existentes

**Solo actualizar (only_update):**
- Solo actualiza productos existentes
- Ignora productos nuevos

### 8.3 Soporte de Caracteres Especiales

**Configuración:**
- Base de datos: UTF-8mb4
- PhpSpreadsheet: Configurado para UTF-8
- Sin normalización de caracteres

**Caracteres soportados:**
- Diámetros: ⌀, Ø
- Fracciones: 3/8", 1/4", ½, ¾, ⅛, ⅜, ⅝, ⅞
- Matemáticos: ×, °, №
- Comillas dobles: "

**Logging:**
Las primeras 5 filas de cada importación se registran en el log con información detallada de caracteres:
```
[BARRAS] Fila 2 - Descripción leída: '⌀ 18 3/8" x 1/4"' [20 bytes]
```

**Recomendaciones:**
1. Crear archivos en Excel o Google Sheets
2. Evitar Notepad o editores de texto plano
3. No usar formato CSV si hay caracteres especiales
4. Verificar preview antes de importar

---

## 9. INTERFAZ WEB

### 9.1 Dashboard Principal

**Ruta:** http://localhost:8000/dashboard

**Características:**
- Resumen general de inventario
- Estadísticas en tiempo real
- Productos con stock bajo
- Últimos movimientos
- Accesos rápidos a módulos

**Autenticación:** Requerida (Google OAuth2)

### 9.2 Módulo de Productos

**Ruta:** http://localhost:8000/productos

**Funcionalidades:**
- Listado completo con paginación
- Búsqueda dinámica con sugerencias
- Filtros por categoría, familia, componente
- CRUD completo (Crear, Leer, Actualizar, Eliminar)
- Vista de historial de movimientos por producto

**Búsqueda dinámica:**
- Sugerencias en tiempo real (300ms debounce)
- Búsqueda mientras se escribe
- Navegación con teclado (flechas, Enter, Escape)
- Resaltado de coincidencias

### 9.3 Módulo de Movimientos

**Ruta:** http://localhost:8000/movimientos

**Funcionalidades:**
- Registro de entradas/salidas
- Ajustes de inventario
- Transferencias entre ubicaciones
- Historial completo con filtros
- Exportación a Excel

**Tipos de movimiento:**
1. **Entrada:** Recepción de productos al almacén
2. **Salida:** Despacho de productos del almacén
3. **Ajuste:** Corrección de cantidades por inventario físico
4. **Transferencia:** Movimiento entre ubicaciones

### 9.4 Módulo de Solicitudes

**Ruta:** http://localhost:8000/solicitudes

**Funcionalidades:**
- Crear solicitudes de materiales
- Aprobar/Rechazar solicitudes
- Seguimiento de estado
- Historial de solicitudes por usuario
- Notificaciones de cambios de estado

**Estados de solicitud:**
- Pendiente
- Aprobada
- Rechazada
- Completada

### 9.5 Módulo de Reportes

**Ruta:** http://localhost:8000/reportes

**Reportes disponibles:**

#### Reporte de Barras
**Ruta:** http://localhost:8000/reportes/barras

- Productos de categoría "Barras"
- Búsqueda dinámica integrada
- Importación masiva desde Excel
- Preview de archivo antes de importar
- Validación de datos

#### Reporte de Entradas
**Ruta:** http://localhost:8000/reportes/entradas

- Productos de categoría "Entradas"
- Búsqueda dinámica integrada
- Importación masiva desde Excel
- Modos de importación flexibles
- Preview y validación

### 9.6 Buscador Público

**Ruta:** http://localhost:8000/buscar.html

**Características:**
- Acceso sin autenticación
- Búsqueda multicriterio
- Resultados en tiempo real
- Interfaz limpia y rápida
- Ideal para consultas rápidas de almacén

**Uso típico:**
- Personal operativo sin cuenta
- Consultas rápidas de disponibilidad
- Búsqueda de ubicaciones
- Verificación de códigos de producto

### 9.7 Tecnologías Frontend

**CSS Framework:** Tailwind CSS 3.x
- Utilidades CSS pre-construidas
- Diseño responsivo automático
- Personalización mediante configuración

**JavaScript:**
- Vanilla JavaScript (sin frameworks pesados)
- Búsqueda dinámica con fetch API
- XLSX.js para preview de archivos Excel
- Interactividad sin dependencias innecesarias

**Build Tool:** Vite 5.x
- Hot Module Replacement (HMR)
- Build rápido para producción
- Optimización automática de assets

**Template Engine:** Blade
- Motor de plantillas de Laravel
- Sintaxis limpia y expresiva
- Componentes reutilizables
- Directivas personalizadas

---

## 10. CÓDIGOS DE RESPUESTA HTTP

### 10.1 Códigos Exitosos

**200 OK**
- Solicitud procesada correctamente
- Datos retornados en formato JSON

### 10.2 Códigos de Error del Cliente

**404 Not Found**
- Recurso no encontrado
- ID de producto o movimiento inexistente

Ejemplo:
```json
{
  "message": "Producto no encontrado"
}
```

**422 Unprocessable Entity**
- Error de validación
- Parámetros inválidos o faltantes

Ejemplo:
```json
{
  "message": "Los datos proporcionados no son válidos",
  "errors": {
    "q": ["El campo q es obligatorio"]
  }
}
```

### 10.3 Códigos de Error del Servidor

**500 Internal Server Error**
- Error interno del servidor
- Revisar logs para diagnóstico

---

## 11. OPTIMIZACIONES Y BUENAS PRÁCTICAS

### 11.1 Performance de API

**Reducción de payload:**
- Estructuras simplificadas: ~75% menos datos
- Metadata mínima en paginación
- Campos esenciales únicamente

**Recomendaciones:**

1. **Para búsquedas específicas:** Usar `/productos/buscar`
2. **Para navegación completa:** Usar `/productos` con paginación
3. **Limitar resultados:** No solicitar más de 20-50 por página en UI
4. **Implementar debounce:** 300ms mínimo en búsquedas en tiempo real
5. **Cachear estadísticas:** 5-10 minutos en cliente

### 11.2 Índices de Base de Datos

Para óptimo rendimiento, asegurar índices en:

**Tabla productos:**
- `codigo` (único)
- `descripcion` (texto completo)
- `categoria_id`, `familia_id`, `componente_id`, `ubicacion_id` (relaciones)

**Tablas catálogo:**
- `ubicaciones.codigo`
- `unidades_medida.codigo`

### 11.3 Casos de Uso Optimizados

**Dashboard móvil:**
```
Paralelo:
1. GET /api/v1/productos/stats
2. GET /api/v1/movimientos?per_page=5
```

**Búsqueda con autocompletado:**
```javascript
// Implementación recomendada
let timeout;
input.addEventListener('input', (e) => {
    clearTimeout(timeout);
    if (e.target.value.length < 2) return;
    
    timeout = setTimeout(() => {
        fetch(`/api/v1/productos/buscar?q=${e.target.value}&limit=10`)
            .then(r => r.json())
            .then(data => mostrarSugerencias(data.data));
    }, 300);
});
```

**Listado con scroll infinito:**
```javascript
let page = 1;
window.addEventListener('scroll', () => {
    if (isNearBottom()) {
        page++;
        fetch(`/api/v1/productos?page=${page}&per_page=20`)
            .then(r => r.json())
            .then(data => appendProductos(data.data));
    }
});
```

**Detalle de producto con historial:**
```
Paralelo:
1. GET /api/v1/productos/{id}
2. GET /api/v1/productos/{id}/movimientos?per_page=10
```

### 11.4 Manejo de Errores

**Patrón recomendado:**

```javascript
async function buscarProducto(query) {
    try {
        const response = await fetch(`/api/v1/productos/buscar?q=${query}`);
        
        if (!response.ok) {
            if (response.status === 404) {
                return { error: 'Producto no encontrado' };
            }
            if (response.status === 422) {
                const error = await response.json();
                return { error: error.message };
            }
            throw new Error('Error del servidor');
        }
        
        return await response.json();
        
    } catch (error) {
        console.error('Error de red:', error);
        return { error: 'Error de conexión' };
    }
}
```

### 11.5 Logs y Diagnóstico

**Consultar logs:**
```bash
# Ver últimas líneas
tail -f storage/logs/laravel.log

# Buscar errores de importación
grep "BARRAS\|ENTRADAS" storage/logs/laravel.log
```

**Logs de importación:**
- Primeras 5 filas logueadas con detalle
- Errores completos registrados
- IDs de productos creados/actualizados

---

## 12. APÉNDICES

### 12.1 Desarrollo y Testing

**Ejecutar servidor de desarrollo:**
```bash
# Servidor Laravel con hot reload
php artisan serve

# Compilar assets en modo desarrollo (con watch)
npm run dev
```

**Ejecutar tests:**
```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=ProductoTest

# Con cobertura
php artisan test --coverage
```

**Limpiar cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Regenerar autoload:**
```bash
composer dump-autoload
```

### 12.2 Estructura de Código del Producto

**Formato:** `CCFFFFNNNN`

**Ejemplo:** `GAC0010001`

- **G** (1 carácter): Componente
- **AC** (2 caracteres): Categoría
- **001** (3 caracteres): Familia
- **0001** (4 caracteres): Consecutivo

**Extracción automática:**

En importaciones, si no se especifican componente/categoría/familia:
1. Se extraen del código (si tiene 10 caracteres)
2. Se crean catálogos automáticamente si no existen
3. Se asignan valores por defecto si el código es muy corto

**Valores por defecto:**
- Componente: 'X' - "Sin Componente"
- Categoría: 'XX' - "Categoría por defecto"
- Familia: '000' - "Familia por defecto"  
- Unidad Medida: 'PZA' - "PIEZA"

### 12.3 Variables de Entorno Importantes

```env
# Aplicación
APP_NAME="Sistema de Inventario"
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost:8000

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario_almacen
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

# Google OAuth2
GOOGLE_CLIENT_ID=tu_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Sesiones
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug|info|error
```

### 12.4 Configuración de Base de Datos MySQL

**my.cnf / my.ini recomendado:**
```ini
[mysqld]
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
max_allowed_packet=16M

[client]
default-character-set=utf8mb4
```

**Crear base de datos:**
```sql
CREATE DATABASE inventario_almacen 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Verificar configuración
SHOW VARIABLES LIKE 'char%';
SHOW VARIABLES LIKE 'collation%';
```

### 12.5 Solución de Problemas Comunes

**Error: "No application encryption key has been specified"**
```bash
php artisan key:generate
```

**Error: "SQLSTATE[HY000] [1049] Unknown database"**
- Crear la base de datos manualmente
- Verificar credenciales en .env

**Error: "Class 'Socialite' not found"**
```bash
composer require laravel/socialite
```

**Error: Assets no se cargan**
```bash
npm run build
php artisan storage:link
```

**Permisos en Linux/Mac:**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

**Logs no se generan:**
```bash
# Verificar permisos
ls -la storage/logs/

# Crear archivo si no existe
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log
```

### 12.6 Comandos Artisan Útiles

```bash
# Ver todas las rutas
php artisan route:list

# Ver estado de migraciones
php artisan migrate:status

# Rollback última migración
php artisan migrate:rollback

# Refrescar BD completa (¡CUIDADO EN PRODUCCIÓN!)
php artisan migrate:fresh --seed

# Crear nueva migración
php artisan make:migration nombre_migracion

# Crear nuevo modelo
php artisan make:model NombreModelo -m

# Crear nuevo controlador
php artisan make:controller NombreController

# Limpiar todo el cache
php artisan optimize:clear

# Optimizar para producción
php artisan optimize
```

### 12.7 Endpoints de Desarrollo

**Durante desarrollo, endpoints adicionales disponibles:**

- `/telescope` - Monitoreo y debugging (si está instalado)
- `/horizon` - Monitoreo de colas (si está instalado)
- `/api/documentation` - Documentación interactiva (si está configurado)

**Para habilitar Telescope:**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

---

## VERSIÓN Y FECHA

**Documentación:** v1.0  
**Sistema:** Laravel 12  
**Fecha de actualización:** 2 de marzo de 2026

**Contacto:**
Para reportar problemas, sugerencias o solicitar nuevas funcionalidades, contactar al equipo de desarrollo.

**Licencia:**
Este sistema es software propietario desarrollado específicamente para gestión de inventarios de almacén.

---

**FIN DE LA DOCUMENTACIÓN**
