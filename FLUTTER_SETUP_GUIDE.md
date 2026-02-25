# 📱 Guía de Configuración - App Flutter de Inventario

## 🎯 Inicio Rápido

Esta guía te ayudará a configurar la aplicación Flutter para consumir el API de inventario Laravel.

---

## 📋 Prerequisitos

- ✅ Flutter SDK 3.0 o superior instalado
- ✅ Dart SDK 2.17 o superior
- ✅ Android Studio / VS Code con extensiones de Flutter
- ✅ Servidor Laravel corriendo en `http://localhost:8000`
- ✅ Dispositivo/Emulador para pruebas

---

## 🏗️ Estructura del Proyecto Flutter

```
lib/
├── main.dart
├── models/
│   ├── producto.dart
│   ├── producto.g.dart (auto-generado)
│   ├── paginated_response.dart
│   ├── paginated_response.g.dart (auto-generado)
│   ├── search_response.dart
│   ├── search_response.g.dart (auto-generado)
│   ├── producto_stats.dart
│   ├── producto_stats.g.dart (auto-generado)
│   ├── catalogo.dart
│   └── catalogo.g.dart (auto-generado)
├── services/
│   ├── producto_api_service.dart
│   ├── movimiento_api_service.dart
│   └── catalogo_api_service.dart
├── providers/ (opcional - si usas Provider)
│   ├── producto_provider.dart
│   └── dashboard_provider.dart
├── screens/
│   ├── dashboard_screen.dart
│   ├── producto_list_screen.dart
│   ├── producto_detail_screen.dart
│   └── search_screen.dart
├── widgets/
│   ├── producto_list_tile.dart
│   ├── producto_search_field.dart
│   └── api_builder.dart
└── utils/
    ├── constants.dart
    └── logger.dart
```

---

## ⚙️ Paso 1: Crear Proyecto Flutter

```bash
# Crear nuevo proyecto
flutter create inventario_app

# Navegar al directorio
cd inventario_app

# Abrir en VS Code
code .
```

---

## 📦 Paso 2: Configurar Dependencias

Edita `pubspec.yaml`:

```yaml
name: inventario_app
description: Aplicación móvil para gestión de inventario
publish_to: 'none'
version: 1.0.0+1

environment:
  sdk: '>=3.0.0 <4.0.0'

dependencies:
  flutter:
    sdk: flutter
  
  # HTTP Client
  http: ^1.1.0
  
  # Serialización JSON
  json_annotation: ^4.8.1
  
  # Gestión de estado (elegir uno)
  provider: ^6.1.1
  # riverpod: ^2.4.9
  # bloc: ^8.1.2
  
  # Utilidades
  intl: ^0.18.1  # Formateo de números y fechas
  shared_preferences: ^2.2.2  # Caché local
  
  # UI
  cupertino_icons: ^1.0.6

dev_dependencies:
  flutter_test:
    sdk: flutter
  
  # Code generation
  build_runner: ^2.4.6
  json_serializable: ^6.7.1
  
  # Linting
  flutter_lints: ^3.0.0

flutter:
  uses-material-design: true
```

Instala las dependencias:

```bash
flutter pub get
```

---

## 🔧 Paso 3: Configurar Constantes

Crea `lib/utils/constants.dart`:

```dart
class ApiConstants {
  // Para emulador Android
  static const String baseUrlEmulator = 'http://10.0.2.2:8000/api/v1';
  
  // Para dispositivo físico (reemplaza con tu IP local)
  static const String baseUrlDevice = 'http://192.168.1.100:8000/api/v1';
  
  // Para iOS Simulator
  static const String baseUrlIosSimulator = 'http://localhost:8000/api/v1';
  
  // Selecciona el apropiado o usa una variable de entorno
  static const String baseUrl = baseUrlEmulator;
  
  // Timeouts
  static const Duration timeout = Duration(seconds: 30);
  static const Duration connectionTimeout = Duration(seconds: 15);
  
  // Paginación
  static const int defaultPerPage = 15;
  static const int maxPerPage = 100;
  
  // Caché
  static const Duration cacheDuration = Duration(hours: 1);
}

class AppConstants {
  static const String appName = 'Inventario Almacén';
  static const String appVersion = '1.0.0';
}
```

### 📱 Configuración por Plataforma

**Android Emulator:**
- Usar `http://10.0.2.2:8000/api/v1`
- 10.0.2.2 es el localhost del host desde el emulador

**iOS Simulator:**
- Usar `http://localhost:8000/api/v1`

**Dispositivo Físico (mismo WiFi):**
1. Obtén tu IP local:
   ```bash
   # Windows
   ipconfig
   # Busca "IPv4 Address" en tu adaptador WiFi
   
   # Mac/Linux
   ifconfig
   # Busca "inet" en tu adaptador WiFi
   ```
2. Usar `http://TU_IP:8000/api/v1`
3. Asegúrate de que el firewall permita conexiones en el puerto 8000

---

## 📝 Paso 4: Crear Modelos

### 4.1 Modelo Producto

Crea `lib/models/producto.dart`:

```dart
import 'package:json_annotation/json_annotation.dart';

part 'producto.g.dart';

@JsonSerializable()
class Producto {
  final int id;
  final String codigo;
  final String descripcion;
  final String? ubicacion;
  final String? um;
  final double fisico;
  final double pu;

  Producto({
    required this.id,
    required this.codigo,
    required this.descripcion,
    this.ubicacion,
    this.um,
    required this.fisico,
    required this.pu,
  });

  factory Producto.fromJson(Map<String, dynamic> json) => 
      _$ProductoFromJson(json);
  
  Map<String, dynamic> toJson() => _$ProductoToJson(this);

  // Utilidades
  String get ubicacionDisplay => ubicacion ?? 'Sin ubicación';
  String get umDisplay => um ?? 'N/A';
  double get valorTotal => fisico * pu;
  bool get tieneStock => fisico > 0;
}
```

### 4.2 Generar Código

```bash
# Ejecuta build_runner para generar archivos .g.dart
flutter pub run build_runner build --delete-conflicting-outputs

# O en modo watch (regenera automáticamente al guardar)
flutter pub run build_runner watch
```

Esto generará automáticamente `lib/models/producto.g.dart`.

---

## 🔌 Paso 5: Crear Servicio API

Crea `lib/services/producto_api_service.dart` (código completo en API_FLUTTER_PRODUCTOS.md).

---

## 🎨 Paso 6: Crear Pantallas

### 6.1 Pantalla Principal (main.dart)

```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'screens/dashboard_screen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Inventario Almacén',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF4A568D),
        ),
        useMaterial3: true,
        appBarTheme: const AppBarTheme(
          centerTitle: true,
          elevation: 2,
        ),
      ),
      home: const DashboardScreen(),
    );
  }
}
```

### 6.2 Implementa las pantallas del archivo API_FLUTTER_PRODUCTOS.md

---

## 🧪 Paso 7: Probar la Conexión

### 7.1 Test Simple

Crea `lib/screens/test_connection_screen.dart`:

```dart
import 'package:flutter/material.dart';
import '../services/producto_api_service.dart';

class TestConnectionScreen extends StatefulWidget {
  const TestConnectionScreen({Key? key}) : super(key: key);

  @override
  State<TestConnectionScreen> createState() => _TestConnectionScreenState();
}

class _TestConnectionScreenState extends State<TestConnectionScreen> {
  final ProductoApiService _apiService = ProductoApiService();
  String _status = 'Esperando prueba...';
  bool _isLoading = false;

  Future<void> _testConnection() async {
    setState(() {
      _isLoading = true;
      _status = 'Probando conexión...';
    });

    try {
      final stats = await _apiService.getStats();
      setState(() {
        _status = '✅ Conexión exitosa!\n\n'
            'Total Productos: ${stats.totalProductos}\n'
            'Total Categorías: ${stats.totalCategorias}\n'
            'Stock Bajo: ${stats.productosStockBajo}';
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _status = '❌ Error de conexión:\n\n$e';
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Test de Conexión'),
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                _status,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 16),
              ),
              const SizedBox(height: 40),
              if (_isLoading)
                const CircularProgressIndicator()
              else
                ElevatedButton.icon(
                  onPressed: _testConnection,
                  icon: const Icon(Icons.wifi),
                  label: const Text('Probar Conexión'),
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 32,
                      vertical: 16,
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
```

---

## 🐛 Solución de Problemas Comunes

### Error: "Connection refused"

**Problema:** No se puede conectar al servidor Laravel

**Soluciones:**
1. Verifica que Laravel esté corriendo: `php artisan serve`
2. Verifica la URL base en `constants.dart`
3. Para emulador Android, usa `http://10.0.2.2:8000/api/v1`
4. Para dispositivo físico, usa tu IP local
5. Desactiva temporalmente el firewall o permite el puerto 8000

### Error: "Failed to build producto.g.dart"

**Problema:** build_runner no puede generar código

**Soluciones:**
1. Asegúrate de tener `part 'producto.g.dart';` en el archivo
2. Ejecuta: `flutter pub run build_runner clean`
3. Luego: `flutter pub run build_runner build --delete-conflicting-outputs`

### Error: "type 'int' is not a subtype of type 'double'"

**Problema:** El JSON contiene enteros pero el modelo espera doubles

**Solución:** Usar `JsonKey` con conversión:

```dart
@JsonSerializable()
class Producto {
  @JsonKey(fromJson: _toDouble)
  final double fisico;
  
  @JsonKey(fromJson: _toDouble)
  final double pu;
  
  // ...
  
  static double _toDouble(dynamic value) {
    if (value is int) return value.toDouble();
    if (value is double) return value;
    return 0.0;
  }
}
```

### Certificado SSL en desarrollo

**Problema:** Errores de certificado SSL en desarrollo

**Solución:** Para desarrollo únicamente (NO EN PRODUCCIÓN):

```dart
import 'dart:io';

class MyHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)
      ..badCertificateCallback = 
          (X509Certificate cert, String host, int port) => true;
  }
}

void main() {
  HttpOverrides.global = MyHttpOverrides();
  runApp(const MyApp());
}
```

---

## 📊 Ejemplo Completo de Flujo

### 1. Usuario abre la app
```
main.dart -> DashboardScreen (carga estadísticas)
```

### 2. Ver lista de productos
```
DashboardScreen -> ProductoListScreen (paginación infinita)
```

### 3. Buscar producto
```
ProductoSearchField (debounce 500ms) -> API buscar -> Mostrar resultados
```

### 4. Ver detalle
```
ProductoListScreen -> ProductoDetailScreen -> Cargar producto por ID
```

---

## 🚀 Ejecutar la App

```bash
# Ver dispositivos/emuladores disponibles
flutter devices

# Ejecutar en modo debug
flutter run

# Ejecutar en dispositivo específico
flutter run -d <device_id>

# Ejecutar en modo release (optimizado)
flutter run --release

# Hot reload durante desarrollo
# Presiona 'r' en la terminal mientras la app está corriendo
```

---

## 📖 Documentación Relacionada

- **API_FLUTTER_PRODUCTOS.md** - Documentación completa del API de Productos
- **API_DOCUMENTATION.md** - Documentación completa de todos los endpoints
- **API_QUICK_REFERENCE.md** - Referencia rápida de endpoints
- **postman_collection.json** - Colección Postman para probar endpoints

---

## 📞 Próximos Pasos

1. ✅ Configurar proyecto Flutter
2. ✅ Crear modelos con JSON serialization
3. ✅ Implementar servicio API
4. ✅ Crear pantallas básicas
5. ⏳ Implementar caché offline con SQLite
6. ⏳ Agregar autenticación con Laravel Sanctum
7. ⏳ Implementar push notifications
8. ⏳ Agregar sincronización en background

---

## 📝 Checklist de Implementación

### Configuración Inicial
- [ ] Flutter SDK instalado y configurado
- [ ] Proyecto creado con `flutter create`
- [ ] Dependencias agregadas en `pubspec.yaml`
- [ ] `flutter pub get` ejecutado correctamente
- [ ] Estructura de carpetas creada

### Modelos y Servicios
- [ ] Modelos creados (Producto, PaginatedResponse, etc.)
- [ ] `build_runner` ejecutado sin errores
- [ ] Archivos `.g.dart` generados correctamente
- [ ] ProductoApiService implementado
- [ ] Constantes de API configuradas con IP correcta

### Pruebas de Conexión
- [ ] Servidor Laravel corriendo en puerto 8000
- [ ] Test de conexión exitoso desde la app
- [ ] Estadísticas cargando correctamente
- [ ] Paginación funcionando

### Pantallas UI
- [ ] DashboardScreen con estadísticas
- [ ] ProductoListScreen con scroll infinito
- [ ] ProductoDetailScreen
- [ ] ProductoSearchField con debounce

### Manejo de Errores
- [ ] Try-catch en todas las llamadas API
- [ ] Mensajes de error amigables
- [ ] Estados de loading
- [ ] Refresh en caso de error

### Optimizaciones
- [ ] Caché implementado (opcional)
- [ ] Timeouts configurados
- [ ] Logging para debugging
- [ ] Imágenes optimizadas (si aplica)

---

**¡Listo para desarrollar!** 🎉

Si encuentras problemas, revisa la sección de "Solución de Problemas Comunes" o consulta la documentación completa en `API_FLUTTER_PRODUCTOS.md`.
