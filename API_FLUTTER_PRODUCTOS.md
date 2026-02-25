# API de Productos - Documentación para Flutter

## 📋 Índice

1. [Información General](#información-general)
2. [Modelos Dart](#modelos-dart)
3. [Servicio API ProductoApiService](#servicio-api-productoapiservice)
4. [Endpoints Disponibles](#endpoints-disponibles)
5. [Ejemplos de Uso en Widgets](#ejemplos-de-uso-en-widgets)
6. [Manejo de Errores](#manejo-de-errores)
7. [Paginación](#paginación)
8. [Mejores Prácticas](#mejores-prácticas)

---

## 📡 Información General

**Base URL:** `http://localhost:8000/api/v1`  
**Controlador:** `App\Http\Controllers\Api\ProductoApiController`  
**Autenticación:** Sin autenticación (próximamente Laravel Sanctum)  
**Formato de Respuesta:** JSON  
**Content-Type:** `application/json`

### Dependencias Recomendadas (pubspec.yaml)

```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0  # Para peticiones HTTP
  # O alternativamente:
  # dio: ^5.4.0  # Cliente HTTP más robusto
  
  # Para manejo de estado (opcional):
  provider: ^6.1.1
  # O: riverpod, bloc, get, etc.
  
  # Para serialización JSON:
  json_annotation: ^4.8.1

dev_dependencies:
  build_runner: ^2.4.6
  json_serializable: ^6.7.1
```

---

## 🎯 Modelos Dart

### Modelo Producto

```dart
// lib/models/producto.dart

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

  // Métodos de utilidad
  String get ubicacionDisplay => ubicacion ?? 'Sin ubicación';
  String get umDisplay => um ?? 'N/A';
  double get valorTotal => fisico * pu;
  bool get tieneStock => fisico > 0;
}
```

### Modelo de Respuesta Paginada

```dart
// lib/models/paginated_response.dart

import 'package:json_annotation/json_annotation.dart';
import 'producto.dart';

part 'paginated_response.g.dart';

@JsonSerializable(genericArgumentFactories: true)
class PaginatedResponse<T> {
  final List<T> data;
  @JsonKey(name: 'current_page')
  final int currentPage;
  @JsonKey(name: 'last_page')
  final int lastPage;
  @JsonKey(name: 'per_page')
  final int perPage;
  final int total;

  PaginatedResponse({
    required this.data,
    required this.currentPage,
    required this.lastPage,
    required this.perPage,
    required this.total,
  });

  factory PaginatedResponse.fromJson(
    Map<String, dynamic> json,
    T Function(Object? json) fromJsonT,
  ) =>
      _$PaginatedResponseFromJson(json, fromJsonT);

  Map<String, dynamic> toJson(Object Function(T value) toJsonT) =>
      _$PaginatedResponseToJson(this, toJsonT);

  // Utilidades
  bool get hasMore => currentPage < lastPage;
  bool get isEmpty => data.isEmpty;
  int get itemsCount => data.length;
}
```

### Modelo de Respuesta de Búsqueda

```dart
// lib/models/search_response.dart

import 'package:json_annotation/json_annotation.dart';
import 'producto.dart';

part 'search_response.g.dart';

@JsonSerializable()
class SearchResponse {
  final List<Producto> data;
  final int total;
  final String query;

  SearchResponse({
    required this.data,
    required this.total,
    required this.query,
  });

  factory SearchResponse.fromJson(Map<String, dynamic> json) =>
      _$SearchResponseFromJson(json);

  Map<String, dynamic> toJson() => _$SearchResponseToJson(this);

  bool get hasResults => data.isNotEmpty;
  bool get isEmpty => data.isEmpty;
}
```

### Modelo de Estadísticas

```dart
// lib/models/producto_stats.dart

import 'package:json_annotation/json_annotation.dart';

part 'producto_stats.g.dart';

@JsonSerializable()
class ProductoStats {
  @JsonKey(name: 'total_productos')
  final int totalProductos;
  
  @JsonKey(name: 'total_categorias')
  final int totalCategorias;
  
  @JsonKey(name: 'total_familias')
  final int totalFamilias;
  
  @JsonKey(name: 'total_componentes')
  final int totalComponentes;
  
  @JsonKey(name: 'productos_stock_bajo')
  final int productosStockBajo;
  
  @JsonKey(name: 'valor_total_inventario_mxn')
  final double valorTotalInventarioMxn;
  
  @JsonKey(name: 'valor_total_inventario_usd')
  final double valorTotalInventarioUsd;

  ProductoStats({
    required this.totalProductos,
    required this.totalCategorias,
    required this.totalFamilias,
    required this.totalComponentes,
    required this.productosStockBajo,
    required this.valorTotalInventarioMxn,
    required this.valorTotalInventarioUsd,
  });

  factory ProductoStats.fromJson(Map<String, dynamic> json) =>
      _$ProductoStatsFromJson(json);

  Map<String, dynamic> toJson() => _$ProductoStatsToJson(this);

  // Getters de utilidad
  double get valorTotalInventario => 
      valorTotalInventarioMxn + (valorTotalInventarioUsd * 17); // Aprox
  
  double get porcentajeStockBajo =>
      totalProductos > 0 ? (productosStockBajo / totalProductos * 100) : 0;
}
```

### Modelo de Catálogo

```dart
// lib/models/catalogo.dart

import 'package:json_annotation/json_annotation.dart';

part 'catalogo.g.dart';

@JsonSerializable()
class CatalogoItem {
  final int id;
  final String codigo;
  final String nombre;

  CatalogoItem({
    required this.id,
    required this.codigo,
    required this.nombre,
  });

  factory CatalogoItem.fromJson(Map<String, dynamic> json) =>
      _$CatalogoItemFromJson(json);

  Map<String, dynamic> toJson() => _$CatalogoItemToJson(this);

  String get displayName => '$codigo - $nombre';
}

@JsonSerializable()
class Catalogos {
  final List<CatalogoItem> categorias;
  final List<CatalogoItem> familias;
  final List<CatalogoItem> componentes;

  Catalogos({
    required this.categorias,
    required this.familias,
    required this.componentes,
  });

  factory Catalogos.fromJson(Map<String, dynamic> json) =>
      _$CatalogosFromJson(json);

  Map<String, dynamic> toJson() => _$CatalogosToJson(this);
}
```

---

## 🔌 Servicio API ProductoApiService

```dart
// lib/services/producto_api_service.dart

import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/producto.dart';
import '../models/paginated_response.dart';
import '../models/search_response.dart';
import '../models/producto_stats.dart';
import '../models/catalogo.dart';

class ProductoApiService {
  static const String baseUrl = 'http://localhost:8000/api/v1';
  
  // Para dispositivos móviles, usar la IP de tu máquina:
  // static const String baseUrl = 'http://192.168.1.100:8000/api/v1';
  
  final http.Client _client;

  ProductoApiService({http.Client? client}) 
      : _client = client ?? http.Client();

  /// Headers comunes para todas las peticiones
  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  /// Manejar errores HTTP
  void _handleHttpError(http.Response response) {
    if (response.statusCode >= 400) {
      throw HttpException(
        statusCode: response.statusCode,
        message: response.body,
      );
    }
  }

  // ========== ENDPOINTS ==========

  /// 1. Listar productos paginados
  /// GET /api/v1/productos
  Future<PaginatedResponse<Producto>> getProductos({
    int page = 1,
    int perPage = 15,
    String? search,
    int? categoriaId,
    int? familiaId,
    int? componenteId,
    int? ubicacionId,
    bool? stockBajo,
  }) async {
    final queryParams = <String, String>{
      'page': page.toString(),
      'per_page': perPage.toString(),
      if (search != null && search.isNotEmpty) 'search': search,
      if (categoriaId != null) 'categoria_id': categoriaId.toString(),
      if (familiaId != null) 'familia_id': familiaId.toString(),
      if (componenteId != null) 'componente_id': componenteId.toString(),
      if (ubicacionId != null) 'ubicacion_id': ubicacionId.toString(),
      if (stockBajo != null) 'stock_bajo': stockBajo ? '1' : '0',
    };

    final uri = Uri.parse('$baseUrl/productos')
        .replace(queryParameters: queryParams);

    final response = await _client.get(uri, headers: _headers);
    _handleHttpError(response);

    final json = jsonDecode(response.body) as Map<String, dynamic>;
    return PaginatedResponse<Producto>.fromJson(
      json,
      (item) => Producto.fromJson(item as Map<String, dynamic>),
    );
  }

  /// 2. Obtener producto específico por ID
  /// GET /api/v1/productos/{id}
  Future<Producto> getProducto(int id) async {
    final uri = Uri.parse('$baseUrl/productos/$id');
    
    final response = await _client.get(uri, headers: _headers);
    _handleHttpError(response);

    final json = jsonDecode(response.body) as Map<String, dynamic>;
    return Producto.fromJson(json);
  }

  /// 3. Buscar productos (búsqueda global)
  /// GET /api/v1/productos/buscar?q=...
  Future<SearchResponse> searchProductos({
    required String query,
    int limit = 50,
  }) async {
    final queryParams = <String, String>{
      'q': query,
      'limit': limit.toString(),
    };

    final uri = Uri.parse('$baseUrl/productos/buscar')
        .replace(queryParameters: queryParams);

    final response = await _client.get(uri, headers: _headers);
    _handleHttpError(response);

    final json = jsonDecode(response.body) as Map<String, dynamic>;
    return SearchResponse.fromJson(json);
  }

  /// 4. Buscar productos por código (legacy)
  /// GET /api/v1/productos/buscar/{codigo}
  Future<SearchResponse> searchByCodigo(String codigo) async {
    final uri = Uri.parse('$baseUrl/productos/buscar/$codigo');
    
    final response = await _client.get(uri, headers: _headers);
    _handleHttpError(response);

    final json = jsonDecode(response.body) as Map<String, dynamic>;
    return SearchResponse.fromJson(json);
  }

  /// 5. Obtener estadísticas generales
  /// GET /api/v1/productos/stats
  Future<ProductoStats> getStats() async {
    final uri = Uri.parse('$baseUrl/productos/stats');
    
    final response = await _client.get(uri, headers: _headers);
    _handleHttpError(response);

    final json = jsonDecode(response.body) as Map<String, dynamic>;
    return ProductoStats.fromJson(json);
  }

  /// 6. Obtener catálogos de referencia
  /// GET /api/v1/catalogos
  Future<Catalogos> getCatalogos() async {
    final uri = Uri.parse('$baseUrl/catalogos');
    
    final response = await _client.get(uri, headers: _headers);
    _handleHttpError(response);

    final json = jsonDecode(response.body) as Map<String, dynamic>;
    return Catalogos.fromJson(json);
  }

  /// Cerrar cliente HTTP
  void dispose() {
    _client.close();
  }
}

/// Excepción personalizada para errores HTTP
class HttpException implements Exception {
  final int statusCode;
  final String message;

  HttpException({required this.statusCode, required this.message});

  @override
  String toString() => 'HttpException: $statusCode - $message';
}
```

---

## 📚 Endpoints Disponibles

### 1. Listar Productos (Paginado)

**Endpoint:** `GET /api/v1/productos`

**Parámetros Query:**

| Parámetro       | Tipo    | Requerido | Default | Descripción                                    |
|-----------------|---------|-----------|---------|------------------------------------------------|
| `page`          | int     | No        | 1       | Número de página                               |
| `per_page`      | int     | No        | 15      | Productos por página (máx. 100)                |
| `search`        | string  | No        | -       | Búsqueda en código y descripción               |
| `categoria_id`  | int     | No        | -       | Filtrar por categoría                          |
| `familia_id`    | int     | No        | -       | Filtrar por familia                            |
| `componente_id` | int     | No        | -       | Filtrar por componente                         |
| `ubicacion_id`  | int     | No        | -       | Filtrar por ubicación                          |
| `stock_bajo`    | boolean | No        | -       | Filtrar productos con stock bajo               |

**Ejemplo en Dart:**

```dart
// Obtener página 1 con 20 productos
final response = await productoService.getProductos(
  page: 1,
  perPage: 20,
);

print('Total de productos: ${response.total}');
print('Página actual: ${response.currentPage} de ${response.lastPage}');

for (var producto in response.data) {
  print('${producto.codigo} - ${producto.descripcion}');
}
```

**Respuesta JSON:**

```json
{
  "data": [
    {
      "id": 1,
      "codigo": "ABC123",
      "descripcion": "Rodamiento 6205",
      "ubicacion": "A1",
      "um": "PZA",
      "fisico": 100.0,
      "pu": 25.50
    }
  ],
  "current_page": 1,
  "last_page": 10,
  "per_page": 15,
  "total": 150
}
```

---

### 2. Obtener Producto por ID

**Endpoint:** `GET /api/v1/productos/{id}`

**Parámetros Path:**

| Parámetro | Tipo | Descripción        |
|-----------|------|--------------------|
| `id`      | int  | ID del producto    |

**Ejemplo en Dart:**

```dart
try {
  final producto = await productoService.getProducto(123);
  
  print('Código: ${producto.codigo}');
  print('Descripción: ${producto.descripcion}');
  print('Valor total: \$${producto.valorTotal}');
} catch (e) {
  if (e is HttpException && e.statusCode == 404) {
    print('Producto no encontrado');
  } else {
    print('Error: $e');
  }
}
```

**Respuesta JSON:**

```json
{
  "id": 123,
  "codigo": "ROD-001",
  "descripcion": "Rodamiento industrial 6205",
  "ubicacion": "RACK-A1",
  "um": "PZA",
  "fisico": 50.0,
  "pu": 125.00
}
```

**Errores:**

- `404 Not Found` - Producto no existe

---

### 3. Buscar Productos (Búsqueda Global)

**Endpoint:** `GET /api/v1/productos/buscar`

**Parámetros Query:**

| Parámetro | Tipo   | Requerido | Default | Descripción                           |
|-----------|--------|-----------|---------|---------------------------------------|
| `q`       | string | Sí        | -       | Término de búsqueda                   |
| `limit`   | int    | No        | 50      | Límite de resultados (máx. 100)       |

**Campos de búsqueda:** codigo, descripcion, ubicacion, um

**Ordenamiento:** Por relevancia (coincidencia exacta primero)

**Ejemplo en Dart:**

```dart
// Búsqueda con autocompletado
final results = await productoService.searchProductos(
  query: 'rodamiento',
  limit: 10,
);

if (results.hasResults) {
  print('Se encontraron ${results.total} resultados para "${results.query}"');
  
  for (var producto in results.data) {
    print('${producto.codigo} - ${producto.descripcion}');
  }
} else {
  print('No se encontraron resultados');
}
```

**Respuesta JSON:**

```json
{
  "data": [
    {
      "id": 1,
      "codigo": "ROD-6205",
      "descripcion": "Rodamiento 6205 sellado",
      "ubicacion": "A1",
      "um": "PZA",
      "fisico": 25.0,
      "pu": 85.00
    }
  ],
  "total": 5,
  "query": "rodamiento"
}
```

---

### 4. Buscar Productos por Código (Legacy)

**Endpoint:** `GET /api/v1/productos/buscar/{codigo}`

**Parámetros Path:**

| Parámetro | Tipo   | Descripción                |
|-----------|--------|----------------------------|
| `codigo`  | string | Código a buscar (parcial)  |

**Límite:** 20 resultados máximo

**Ejemplo en Dart:**

```dart
final results = await productoService.searchByCodigo('ABC');

print('Productos con código que contiene "ABC": ${results.total}');
```

**Respuesta JSON:**

```json
{
  "data": [
    {
      "id": 10,
      "codigo": "ABC-001",
      "descripcion": "Tornillo ABC",
      "ubicacion": "B2",
      "um": "PZA",
      "fisico": 500.0,
      "pu": 2.50
    }
  ],
  "total": 3
}
```

---

### 5. Obtener Estadísticas Generales

**Endpoint:** `GET /api/v1/productos/stats`

**Sin parámetros**

**Ejemplo en Dart:**

```dart
final stats = await productoService.getStats();

print('Total de productos: ${stats.totalProductos}');
print('Productos con stock bajo: ${stats.productosStockBajo}');
print('Porcentaje stock bajo: ${stats.porcentajeStockBajo.toStringAsFixed(2)}%');
print('Valor total inventario MXN: \$${stats.valorTotalInventarioMxn}');
print('Valor total inventario USD: \$${stats.valorTotalInventarioUsd}');
```

**Respuesta JSON:**

```json
{
  "total_productos": 1523,
  "total_categorias": 22,
  "total_familias": 15,
  "total_componentes": 8,
  "productos_stock_bajo": 45,
  "valor_total_inventario_mxn": 2500000.00,
  "valor_total_inventario_usd": 150000.00
}
```

---

### 6. Obtener Catálogos

**Endpoint:** `GET /api/v1/catalogos`

**Sin parámetros**

**Ejemplo en Dart:**

```dart
final catalogos = await productoService.getCatalogos();

// Mostrar categorías en un Dropdown
for (var categoria in catalogos.categorias) {
  print('${categoria.id}: ${categoria.displayName}');
}

// Total de opciones disponibles
final totalOpciones = catalogos.categorias.length +
                      catalogos.familias.length +
                      catalogos.componentes.length;
print('Total de opciones de catálogo: $totalOpciones');
```

**Respuesta JSON:**

```json
{
  "categorias": [
    {
      "id": 1,
      "codigo": "HE",
      "nombre": "Herramientas"
    },
    {
      "id": 2,
      "codigo": "BR",
      "nombre": "Barras"
    }
  ],
  "familias": [
    {
      "id": 1,
      "codigo": "MEC",
      "nombre": "Mecánica"
    }
  ],
  "componentes": [
    {
      "id": 1,
      "codigo": "ROD",
      "nombre": "Rodamientos"
    }
  ]
}
```

---

## 🎨 Ejemplos de Uso en Widgets

### Ejemplo 1: Lista de Productos con Paginación

```dart
// lib/screens/producto_list_screen.dart

import 'package:flutter/material.dart';
import '../models/producto.dart';
import '../models/paginated_response.dart';
import '../services/producto_api_service.dart';

class ProductoListScreen extends StatefulWidget {
  const ProductoListScreen({Key? key}) : super(key: key);

  @override
  State<ProductoListScreen> createState() => _ProductoListScreenState();
}

class _ProductoListScreenState extends State<ProductoListScreen> {
  final ProductoApiService _apiService = ProductoApiService();
  final ScrollController _scrollController = ScrollController();
  
  List<Producto> _productos = [];
  int _currentPage = 1;
  bool _isLoading = false;
  bool _hasMore = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadProductos();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _apiService.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >=
        _scrollController.position.maxScrollExtent * 0.9) {
      _loadMore();
    }
  }

  Future<void> _loadProductos() async {
    if (_isLoading) return;

    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final response = await _apiService.getProductos(
        page: _currentPage,
        perPage: 20,
      );

      setState(() {
        _productos.addAll(response.data);
        _hasMore = response.hasMore;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  Future<void> _loadMore() async {
    if (!_hasMore || _isLoading) return;
    
    _currentPage++;
    await _loadProductos();
  }

  Future<void> _refresh() async {
    setState(() {
      _productos.clear();
      _currentPage = 1;
      _hasMore = true;
    });
    await _loadProductos();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Productos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _refresh,
          ),
        ],
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_error != null && _productos.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 48, color: Colors.red),
            const SizedBox(height: 16),
            Text('Error: $_error'),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _refresh,
              child: const Text('Reintentar'),
            ),
          ],
        ),
      );
    }

    if (_productos.isEmpty && _isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    return RefreshIndicator(
      onRefresh: _refresh,
      child: ListView.builder(
        controller: _scrollController,
        itemCount: _productos.length + (_hasMore ? 1 : 0),
        itemBuilder: (context, index) {
          if (index >= _productos.length) {
            return const Padding(
              padding: EdgeInsets.all(16.0),
              child: Center(child: CircularProgressIndicator()),
            );
          }

          final producto = _productos[index];
          return ProductoListTile(
            producto: producto,
            onTap: () {
              // Navegar a detalles
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => ProductoDetailScreen(
                    productoId: producto.id,
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }
}

class ProductoListTile extends StatelessWidget {
  final Producto producto;
  final VoidCallback onTap;

  const ProductoListTile({
    Key? key,
    required this.producto,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: ListTile(
        onTap: onTap,
        leading: CircleAvatar(
          backgroundColor: producto.tieneStock 
              ? Colors.green 
              : Colors.red,
          child: Text(
            producto.fisico.toInt().toString(),
            style: const TextStyle(color: Colors.white, fontSize: 12),
          ),
        ),
        title: Text(
          producto.codigo,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(producto.descripcion),
            const SizedBox(height: 4),
            Row(
              children: [
                Icon(Icons.location_on, size: 14, color: Colors.grey[600]),
                const SizedBox(width: 4),
                Text(
                  producto.ubicacionDisplay,
                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                ),
                const SizedBox(width: 12),
                Icon(Icons.straighten, size: 14, color: Colors.grey[600]),
                const SizedBox(width: 4),
                Text(
                  producto.umDisplay,
                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                ),
              ],
            ),
          ],
        ),
        trailing: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            Text(
              '\$${producto.pu.toStringAsFixed(2)}',
              style: const TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
            Text(
              'Total: \$${producto.valorTotal.toStringAsFixed(2)}',
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
          ],
        ),
      ),
    );
  }
}
```

---

### Ejemplo 2: Búsqueda con Autocompletado

```dart
// lib/widgets/producto_search_field.dart

import 'package:flutter/material.dart';
import 'dart:async';
import '../models/producto.dart';
import '../services/producto_api_service.dart';

class ProductoSearchField extends StatefulWidget {
  final Function(Producto) onProductoSelected;

  const ProductoSearchField({
    Key? key,
    required this.onProductoSelected,
  }) : super(key: key);

  @override
  State<ProductoSearchField> createState() => _ProductoSearchFieldState();
}

class _ProductoSearchFieldState extends State<ProductoSearchField> {
  final ProductoApiService _apiService = ProductoApiService();
  final TextEditingController _controller = TextEditingController();
  
  Timer? _debounce;
  List<Producto> _suggestions = [];
  bool _isSearching = false;
  OverlayEntry? _overlayEntry;
  final LayerLink _layerLink = LayerLink();

  @override
  void dispose() {
    _debounce?.cancel();
    _controller.dispose();
    _apiService.dispose();
    _removeOverlay();
    super.dispose();
  }

  void _onSearchChanged(String query) {
    if (_debounce?.isActive ?? false) _debounce!.cancel();
    
    if (query.isEmpty) {
      _removeOverlay();
      return;
    }

    _debounce = Timer(const Duration(milliseconds: 500), () {
      _performSearch(query);
    });
  }

  Future<void> _performSearch(String query) async {
    setState(() {
      _isSearching = true;
    });

    try {
      final response = await _apiService.searchProductos(
        query: query,
        limit: 10,
      );

      setState(() {
        _suggestions = response.data;
        _isSearching = false;
      });

      if (_suggestions.isNotEmpty) {
        _showOverlay();
      } else {
        _removeOverlay();
      }
    } catch (e) {
      setState(() {
        _suggestions = [];
        _isSearching = false;
      });
      _removeOverlay();
    }
  }

  void _showOverlay() {
    _removeOverlay();
    
    _overlayEntry = OverlayEntry(
      builder: (context) => Positioned(
        width: MediaQuery.of(context).size.width - 32,
        child: CompositedTransformFollower(
          link: _layerLink,
          showWhenUnlinked: false,
          offset: const Offset(0, 60),
          child: Material(
            elevation: 4,
            borderRadius: BorderRadius.circular(8),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxHeight: 300),
              child: ListView.builder(
                padding: EdgeInsets.zero,
                shrinkWrap: true,
                itemCount: _suggestions.length,
                itemBuilder: (context, index) {
                  final producto = _suggestions[index];
                  return ListTile(
                    dense: true,
                    leading: CircleAvatar(
                      radius: 16,
                      backgroundColor: Colors.indigo,
                      child: Text(
                        producto.fisico.toInt().toString(),
                        style: const TextStyle(
                          fontSize: 10,
                          color: Colors.white,
                        ),
                      ),
                    ),
                    title: Text(
                      producto.codigo,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    subtitle: Text(
                      producto.descripcion,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12),
                    ),
                    trailing: Text(
                      '\$${producto.pu}',
                      style: const TextStyle(fontSize: 12),
                    ),
                    onTap: () {
                      _controller.text = producto.codigo;
                      _removeOverlay();
                      widget.onProductoSelected(producto);
                    },
                  );
                },
              ),
            ),
          ),
        ),
      ),
    );

    Overlay.of(context).insert(_overlayEntry!);
  }

  void _removeOverlay() {
    _overlayEntry?.remove();
    _overlayEntry = null;
  }

  @override
  Widget build(BuildContext context) {
    return CompositedTransformTarget(
      link: _layerLink,
      child: TextField(
        controller: _controller,
        decoration: InputDecoration(
          labelText: 'Buscar producto',
          hintText: 'Código o descripción',
          prefixIcon: const Icon(Icons.search),
          suffixIcon: _isSearching
              ? const Padding(
                  padding: EdgeInsets.all(12),
                  child: SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  ),
                )
              : _controller.text.isNotEmpty
                  ? IconButton(
                      icon: const Icon(Icons.clear),
                      onPressed: () {
                        _controller.clear();
                        _removeOverlay();
                      },
                    )
                  : null,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
          ),
        ),
        onChanged: _onSearchChanged,
      ),
    );
  }
}
```

---

### Ejemplo 3: Dashboard con Estadísticas

```dart
// lib/screens/dashboard_screen.dart

import 'package:flutter/material.dart';
import '../models/producto_stats.dart';
import '../services/producto_api_service.dart';
import 'package:intl/intl.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final ProductoApiService _apiService = ProductoApiService();
  final NumberFormat _currencyFormat = NumberFormat.currency(
    symbol: '\$',
    decimalDigits: 2,
  );

  ProductoStats? _stats;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  Future<void> _loadStats() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final stats = await _apiService.getStats();
      setState(() {
        _stats = stats;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadStats,
          ),
        ],
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_error != null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 48, color: Colors.red),
            const SizedBox(height: 16),
            Text('Error: $_error'),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _loadStats,
              child: const Text('Reintentar'),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadStats,
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            _buildStatsGrid(),
            const SizedBox(height: 24),
            _buildInventoryValueCard(),
            const SizedBox(height: 24),
            _buildStockAlert(),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsGrid() {
    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 2,
      crossAxisSpacing: 16,
      mainAxisSpacing: 16,
      children: [
        _buildStatCard(
          title: 'Total Productos',
          value: _stats!.totalProductos.toString(),
          icon: Icons.inventory,
          color: Colors.indigo,
        ),
        _buildStatCard(
          title: 'Categorías',
          value: _stats!.totalCategorias.toString(),
          icon: Icons.category,
          color: Colors.purple,
        ),
        _buildStatCard(
          title: 'Familias',
          value: _stats!.totalFamilias.toString(),
          icon: Icons.folder,
          color: Colors.blue,
        ),
        _buildStatCard(
          title: 'Componentes',
          value: _stats!.totalComponentes.toString(),
          icon: Icons.build,
          color: Colors.orange,
        ),
      ],
    );
  }

  Widget _buildStatCard({
    required String title,
    required String value,
    required IconData icon,
    required Color color,
  }) {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 40, color: color),
            const SizedBox(height: 8),
            Text(
              value,
              style: const TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInventoryValueCard() {
    return Card(
      elevation: 2,
      color: Colors.indigo[50],
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Row(
              children: [
                Icon(Icons.attach_money, color: Colors.indigo),
                SizedBox(width: 8),
                Text(
                  'Valor Total del Inventario',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'MXN',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey,
                      ),
                    ),
                    Text(
                      _currencyFormat.format(_stats!.valorTotalInventarioMxn),
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                        color: Colors.indigo,
                      ),
                    ),
                  ],
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    const Text(
                      'USD',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey,
                      ),
                    ),
                    Text(
                      _currencyFormat.format(_stats!.valorTotalInventarioUsd),
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                        color: Colors.indigo,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStockAlert() {
    final porcentaje = _stats!.porcentajeStockBajo;
    final color = porcentaje > 10 ? Colors.red : Colors.orange;

    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(Icons.warning, color: color),
                const SizedBox(width: 8),
                const Text(
                  'Stock Bajo',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Row(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(
                  _stats!.productosStockBajo.toString(),
                  style: TextStyle(
                    fontSize: 48,
                    fontWeight: FontWeight.bold,
                    color: color,
                  ),
                ),
                const SizedBox(width: 8),
                Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: Text(
                    'productos (${porcentaje.toStringAsFixed(1)}%)',
                    style: TextStyle(
                      fontSize: 16,
                      color: Colors.grey[600],
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            LinearProgressIndicator(
              value: porcentaje / 100,
              backgroundColor: Colors.grey[300],
              valueColor: AlwaysStoppedAnimation<Color>(color),
            ),
          ],
        ),
      ),
    );
  }
}
```

---

### Ejemplo 4: Detalle de Producto

```dart
// lib/screens/producto_detail_screen.dart

import 'package:flutter/material.dart';
import '../models/producto.dart';
import '../services/producto_api_service.dart';

class ProductoDetailScreen extends StatefulWidget {
  final int productoId;

  const ProductoDetailScreen({
    Key? key,
    required this.productoId,
  }) : super(key: key);

  @override
  State<ProductoDetailScreen> createState() => _ProductoDetailScreenState();
}

class _ProductoDetailScreenState extends State<ProductoDetailScreen> {
  final ProductoApiService _apiService = ProductoApiService();
  
  Producto? _producto;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadProducto();
  }

  Future<void> _loadProducto() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final producto = await _apiService.getProducto(widget.productoId);
      setState(() {
        _producto = producto;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_producto?.codigo ?? 'Producto'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadProducto,
          ),
        ],
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_error != null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 48, color: Colors.red),
            const SizedBox(height: 16),
            Text('Error: $_error'),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _loadProducto,
              child: const Text('Reintentar'),
            ),
          ],
        ),
      );
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildHeader(),
          const SizedBox(height: 24),
          _buildInfoSection(),
          const SizedBox(height: 24),
          _buildStockSection(),
        ],
      ),
    );
  }

  Widget _buildHeader() {
    return Card(
      elevation: 2,
      color: Colors.indigo,
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              _producto!.codigo,
              style: const TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              _producto!.descripcion,
              style: const TextStyle(
                fontSize: 16,
                color: Colors.white70,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoSection() {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Información',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const Divider(),
            _buildInfoRow('Ubicación', _producto!.ubicacionDisplay),
            _buildInfoRow('Unidad de Medida', _producto!.umDisplay),
            _buildInfoRow(
              'Precio Unitario',
              '\$${_producto!.pu.toStringAsFixed(2)}',
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey[600],
            ),
          ),
          Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStockSection() {
    final stockColor = _producto!.tieneStock ? Colors.green : Colors.red;
    
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Stock',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const Divider(),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Cantidad Física',
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey[600],
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      _producto!.fisico.toStringAsFixed(2),
                      style: TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.bold,
                        color: stockColor,
                      ),
                    ),
                  ],
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(
                      'Valor Total',
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey[600],
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      '\$${_producto!.valorTotal.toStringAsFixed(2)}',
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
```

---

## ⚠️ Manejo de Errores

### Tipos de Errores Comunes

```dart
try {
  final producto = await productoService.getProducto(123);
  // Usar producto
} on HttpException catch (e) {
  // Error HTTP del servidor
  if (e.statusCode == 404) {
    print('Producto no encontrado');
  } else if (e.statusCode == 500) {
    print('Error del servidor');
  } else {
    print('Error HTTP ${e.statusCode}: ${e.message}');
  }
} on SocketException {
  // Sin conexión a internet
  print('Sin conexión a internet');
} on TimeoutException {
  // Tiempo de espera agotado
  print('La petición tardó demasiado');
} catch (e) {
  // Otros errores
  print('Error inesperado: $e');
}
```

### Widget de Manejo de Estados

```dart
// lib/widgets/api_builder.dart

import 'package:flutter/material.dart';

class ApiBuilder<T> extends StatelessWidget {
  final Future<T> Function() future;
  final Widget Function(BuildContext, T) builder;
  final Widget Function(BuildContext, Object)? errorBuilder;
  final Widget Function(BuildContext)? loadingBuilder;

  const ApiBuilder({
    Key? key,
    required this.future,
    required this.builder,
    this.errorBuilder,
    this.loadingBuilder,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<T>(
      future: future(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return loadingBuilder?.call(context) ??
              const Center(child: CircularProgressIndicator());
        }

        if (snapshot.hasError) {
          return errorBuilder?.call(context, snapshot.error!) ??
              Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(
                      Icons.error_outline,
                      size: 48,
                      color: Colors.red,
                    ),
                    const SizedBox(height: 16),
                    Text('Error: ${snapshot.error}'),
                  ],
                ),
              );
        }

        if (!snapshot.hasData) {
          return const Center(child: Text('Sin datos'));
        }

        return builder(context, snapshot.data as T);
      },
    );
  }
}

// Uso:
ApiBuilder<ProductoStats>(
  future: () => productoService.getStats(),
  builder: (context, stats) {
    return Text('Total productos: ${stats.totalProductos}');
  },
)
```

---

## 📄 Paginación

### Estrategia de Scroll Infinito

```dart
class InfiniteScrollController {
  final ScrollController scrollController = ScrollController();
  final VoidCallback onLoadMore;
  
  bool _isLoading = false;
  bool _hasMore = true;

  InfiniteScrollController({required this.onLoadMore}) {
    scrollController.addListener(_onScroll);
  }

  void _onScroll() {
    if (_isLoading || !_hasMore) return;

    final maxScroll = scrollController.position.maxScrollExtent;
    final currentScroll = scrollController.position.pixels;
    final threshold = maxScroll * 0.9; // Cargar al 90% del scroll

    if (currentScroll >= threshold) {
      onLoadMore();
    }
  }

  void setLoading(bool loading) {
    _isLoading = loading;
  }

  void setHasMore(bool hasMore) {
    _hasMore = hasMore;
  }

  void dispose() {
    scrollController.dispose();
  }
}
```

### Límites de Paginación

- **`per_page` mínimo:** 1
- **`per_page` máximo:** 100
- **`per_page` por defecto:** 15

---

## ✅ Mejores Prácticas

### 1. Usar Provider para Gestión de Estado

```dart
// lib/providers/producto_provider.dart

import 'package:flutter/foundation.dart';
import '../models/producto.dart';
import '../services/producto_api_service.dart';

class ProductoProvider with ChangeNotifier {
  final ProductoApiService _apiService = ProductoApiService();
  
  List<Producto> _productos = [];
  bool _isLoading = false;
  String? _error;

  List<Producto> get productos => _productos;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> loadProductos() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.getProductos();
      _productos = response.data;
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  @override
  void dispose() {
    _apiService.dispose();
    super.dispose();
  }
}
```

### 2. Implementar Caché Local

```dart
// lib/services/cached_producto_service.dart

import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';

class CachedProductoService {
  static const String _cacheKey = 'productos_cache';
  static const Duration _cacheDuration = Duration(hours: 1);

  Future<void> cacheProductos(List<Producto> productos) async {
    final prefs = await SharedPreferences.getInstance();
    final data = {
      'timestamp': DateTime.now().toIso8601String(),
      'productos': productos.map((p) => p.toJson()).toList(),
    };
    await prefs.setString(_cacheKey, jsonEncode(data));
  }

  Future<List<Producto>?> getCachedProductos() async {
    final prefs = await SharedPreferences.getInstance();
    final cached = prefs.getString(_cacheKey);
    
    if (cached == null) return null;

    final data = jsonDecode(cached) as Map<String, dynamic>;
    final timestamp = DateTime.parse(data['timestamp'] as String);
    
    // Verificar si el caché ha expirado
    if (DateTime.now().difference(timestamp) > _cacheDuration) {
      return null;
    }

    final productosJson = data['productos'] as List;
    return productosJson
        .map((json) => Producto.fromJson(json as Map<String, dynamic>))
        .toList();
  }
}
```

### 3. Configurar Timeout

```dart
class ProductoApiService {
  final http.Client _client;
  static const Duration timeout = Duration(seconds: 30);

  Future<PaginatedResponse<Producto>> getProductos({...}) async {
    final uri = Uri.parse('$baseUrl/productos')
        .replace(queryParameters: queryParams);

    try {
      final response = await _client
          .get(uri, headers: _headers)
          .timeout(timeout);
      
      _handleHttpError(response);
      // ... resto del código
    } on TimeoutException {
      throw Exception('La petición tardó demasiado tiempo');
    }
  }
}
```

### 4. Configurar para Dispositivos Reales

Para probar en dispositivos físicos o emuladores, cambia la base URL:

```dart
// En desarrollo con dispositivo físico en la misma red WiFi
static const String baseUrl = 'http://192.168.1.100:8000/api/v1';

// Reemplaza 192.168.1.100 con la IP de tu máquina
// Ejecuta: ipconfig (Windows) o ifconfig (Mac/Linux)
```

### 5. Logging para Debugging

```dart
// lib/services/logger.dart

void logApiCall(String method, String url, [dynamic data]) {
  if (kDebugMode) {
    print('[$method] $url');
    if (data != null) {
      print('Data: $data');
    }
  }
}

// Uso en ProductoApiService
Future<Producto> getProducto(int id) async {
  final uri = Uri.parse('$baseUrl/productos/$id');
  logApiCall('GET', uri.toString());
  
  final response = await _client.get(uri, headers: _headers);
  // ...
}
```

---

## 📝 Generar Código con build_runner

Después de crear los modelos con `@JsonSerializable`, ejecuta:

```bash
# Generar archivos .g.dart una vez
flutter pub run build_runner build

# O en modo watch (regenera automáticamente)
flutter pub run build_runner watch

# Limpiar y regenerar
flutter pub run build_runner build --delete-conflicting-outputs
```

---

## 🚀 Próximos Pasos

1. **Autenticación con Laravel Sanctum**
   - Tokens de acceso
   - Login/Logout
   - Refresh tokens

2. **Endpoints de Escritura**
   - POST /api/v1/productos (crear)
   - PUT /api/v1/productos/{id} (actualizar)
   - DELETE /api/v1/productos/{id} (eliminar)

3. **Sincronización Offline**
   - SQLite local
   - Sync queue
   - Conflict resolution

4. **Push Notifications**
   - Firebase Cloud Messaging
   - Notificaciones de stock bajo
   - Alertas de movimientos

---

## 📞 Soporte

Para más información sobre otros endpoints (Movimientos, Catálogos), consulta:
- `API_DOCUMENTATION.md` - Documentación completa de todos los endpoints
- `API_QUICK_REFERENCE.md` - Referencia rápida

---

**Versión:** 1.0  
**Última actualización:** Febrero 2026  
**Controlador:** `app\Http\Controllers\Api\ProductoApiController.php`
