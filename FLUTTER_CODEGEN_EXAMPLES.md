# Ejemplos de Archivos Generados (.g.dart)

Este archivo muestra ejemplos de cómo se ven los archivos `.g.dart` generados por `build_runner` después de ejecutar:

```bash
flutter pub run build_runner build
```

**⚠️ IMPORTANTE:** Estos archivos NO deben editarse manualmente. Son generados automáticamente.

---

## 📄 producto.g.dart

```dart
// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'producto.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

Producto _$ProductoFromJson(Map<String, dynamic> json) => Producto(
      id: json['id'] as int,
      codigo: json['codigo'] as String,
      descripcion: json['descripcion'] as String,
      ubicacion: json['ubicacion'] as String?,
      um: json['um'] as String?,
      fisico: (json['fisico'] as num).toDouble(),
      pu: (json['pu'] as num).toDouble(),
    );

Map<String, dynamic> _$ProductoToJson(Producto instance) => <String, dynamic>{
      'id': instance.id,
      'codigo': instance.codigo,
      'descripcion': instance.descripcion,
      'ubicacion': instance.ubicacion,
      'um': instance.um,
      'fisico': instance.fisico,
      'pu': instance.pu,
    };
```

---

## 📄 paginated_response.g.dart

```dart
// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'paginated_response.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

PaginatedResponse<T> _$PaginatedResponseFromJson<T>(
  Map<String, dynamic> json,
  T Function(Object? json) fromJsonT,
) =>
    PaginatedResponse<T>(
      data: (json['data'] as List<dynamic>).map(fromJsonT).toList(),
      currentPage: json['current_page'] as int,
      lastPage: json['last_page'] as int,
      perPage: json['per_page'] as int,
      total: json['total'] as int,
    );

Map<String, dynamic> _$PaginatedResponseToJson<T>(
  PaginatedResponse<T> instance,
  Object? Function(T value) toJsonT,
) =>
    <String, dynamic>{
      'data': instance.data.map(toJsonT).toList(),
      'current_page': instance.currentPage,
      'last_page': instance.lastPage,
      'per_page': instance.perPage,
      'total': instance.total,
    };
```

---

## 📄 search_response.g.dart

```dart
// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'search_response.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

SearchResponse _$SearchResponseFromJson(Map<String, dynamic> json) =>
    SearchResponse(
      data: (json['data'] as List<dynamic>)
          .map((e) => Producto.fromJson(e as Map<String, dynamic>))
          .toList(),
      total: json['total'] as int,
      query: json['query'] as String,
    );

Map<String, dynamic> _$SearchResponseToJson(SearchResponse instance) =>
    <String, dynamic>{
      'data': instance.data.map((e) => e.toJson()).toList(),
      'total': instance.total,
      'query': instance.query,
    };
```

---

## 📄 producto_stats.g.dart

```dart
// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'producto_stats.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

ProductoStats _$ProductoStatsFromJson(Map<String, dynamic> json) =>
    ProductoStats(
      totalProductos: json['total_productos'] as int,
      totalCategorias: json['total_categorias'] as int,
      totalFamilias: json['total_familias'] as int,
      totalComponentes: json['total_componentes'] as int,
      productosStockBajo: json['productos_stock_bajo'] as int,
      valorTotalInventarioMxn:
          (json['valor_total_inventario_mxn'] as num).toDouble(),
      valorTotalInventarioUsd:
          (json['valor_total_inventario_usd'] as num).toDouble(),
    );

Map<String, dynamic> _$ProductoStatsToJson(ProductoStats instance) =>
    <String, dynamic>{
      'total_productos': instance.totalProductos,
      'total_categorias': instance.totalCategorias,
      'total_familias': instance.totalFamilias,
      'total_componentes': instance.totalComponentes,
      'productos_stock_bajo': instance.productosStockBajo,
      'valor_total_inventario_mxn': instance.valorTotalInventarioMxn,
      'valor_total_inventario_usd': instance.valorTotalInventarioUsd,
    };
```

---

## 📄 catalogo.g.dart

```dart
// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'catalogo.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

CatalogoItem _$CatalogoItemFromJson(Map<String, dynamic> json) =>
    CatalogoItem(
      id: json['id'] as int,
      codigo: json['codigo'] as String,
      nombre: json['nombre'] as String,
    );

Map<String, dynamic> _$CatalogoItemToJson(CatalogoItem instance) =>
    <String, dynamic>{
      'id': instance.id,
      'codigo': instance.codigo,
      'nombre': instance.nombre,
    };

Catalogos _$CatalogosFromJson(Map<String, dynamic> json) => Catalogos(
      categorias: (json['categorias'] as List<dynamic>)
          .map((e) => CatalogoItem.fromJson(e as Map<String, dynamic>))
          .toList(),
      familias: (json['familias'] as List<dynamic>)
          .map((e) => CatalogoItem.fromJson(e as Map<String, dynamic>))
          .toList(),
      componentes: (json['componentes'] as List<dynamic>)
          .map((e) => CatalogoItem.fromJson(e as Map<String, dynamic>))
          .toList(),
    );

Map<String, dynamic> _$CatalogosToJson(Catalogos instance) =>
    <String, dynamic>{
      'categorias': instance.categorias.map((e) => e.toJson()).toList(),
      'familias': instance.familias.map((e) => e.toJson()).toList(),
      'componentes': instance.componentes.map((e) => e.toJson()).toList(),
    };
```

---

## 🔧 Comandos de build_runner

### Generar código una vez

```bash
flutter pub run build_runner build
```

### Generar código con limpieza (recomendado)

```bash
flutter pub run build_runner build --delete-conflicting-outputs
```

### Modo watch (regenera automáticamente al guardar)

```bash
flutter pub run build_runner watch
```

### Limpiar archivos generados

```bash
flutter pub run build_runner clean
```

---

## 🎯 Cómo Funciona

### 1. Defines el Modelo con Anotaciones

```dart
import 'package:json_annotation/json_annotation.dart';

part 'producto.g.dart';  // ← Indica que habrá un archivo generado

@JsonSerializable()  // ← Anotación que activa la generación
class Producto {
  final int id;
  final String codigo;
  // ...

  factory Producto.fromJson(Map<String, dynamic> json) => 
      _$ProductoFromJson(json);  // ← Función generada automáticamente
  
  Map<String, dynamic> toJson() => 
      _$ProductoToJson(this);  // ← Función generada automáticamente
}
```

### 2. build_runner Genera el Código

Cuando ejecutas `flutter pub run build_runner build`:

1. **Analiza** tus clases con `@JsonSerializable()`
2. **Genera** funciones `fromJson` y `toJson`
3. **Crea** el archivo `.g.dart` correspondiente
4. **Maneja** conversiones de tipos automáticamente

### 3. Usas los Métodos Generados

```dart
// De JSON a Objeto
final json = {'id': 1, 'codigo': 'ABC', ...};
final producto = Producto.fromJson(json);

// De Objeto a JSON
final producto = Producto(id: 1, codigo: 'ABC', ...);
final json = producto.toJson();
```

---

## 📝 Anotaciones Avanzadas

### Cambiar nombre de campo JSON

```dart
@JsonSerializable()
class Producto {
  @JsonKey(name: 'precio_unitario')
  final double pu;
  
  // El JSON usa "precio_unitario"
  // Pero en Dart usamos "pu"
}
```

### Valores por defecto

```dart
@JsonSerializable()
class Producto {
  @JsonKey(defaultValue: 0.0)
  final double fisico;
  
  // Si "fisico" no viene en el JSON, usa 0.0
}
```

### Ignorar campos

```dart
@JsonSerializable()
class Producto {
  final String codigo;
  
  @JsonKey(ignore: true)
  final String? cachedData;  // No se serializa/deserializa
}
```

### Conversión personalizada

```dart
@JsonSerializable()
class Producto {
  @JsonKey(fromJson: _dateFromJson, toJson: _dateToJson)
  final DateTime? fechaEntrada;
  
  static DateTime? _dateFromJson(String? json) {
    return json != null ? DateTime.parse(json) : null;
  }
  
  static String? _dateToJson(DateTime? date) {
    return date?.toIso8601String();
  }
}
```

### Clases genéricas

```dart
@JsonSerializable(genericArgumentFactories: true)
class PaginatedResponse<T> {
  final List<T> data;
  final int total;

  factory PaginatedResponse.fromJson(
    Map<String, dynamic> json,
    T Function(Object? json) fromJsonT,
  ) => _$PaginatedResponseFromJson(json, fromJsonT);
}

// Uso:
final response = PaginatedResponse<Producto>.fromJson(
  jsonData,
  (json) => Producto.fromJson(json as Map<String, dynamic>),
);
```

---

## ⚠️ Errores Comunes

### Error: "Part directive is missing"

```
Error: The part directive is missing in 'producto.dart'
```

**Solución:** Agrega `part 'producto.g.dart';` al inicio del archivo:

```dart
import 'package:json_annotation/json_annotation.dart';

part 'producto.g.dart';  // ← Agregar esta línea

@JsonSerializable()
class Producto { ... }
```

### Error: "Could not find a generator"

```
Error: Could not find a generator for 'Producto'
```

**Solución:** Asegúrate de tener las dependencias correctas en `pubspec.yaml`:

```yaml
dependencies:
  json_annotation: ^4.8.1

dev_dependencies:
  build_runner: ^2.4.6
  json_serializable: ^6.7.1
```

### Error: "Conflicting outputs"

```
Error: Conflicting outputs were detected
```

**Solución:** Usa el flag `--delete-conflicting-outputs`:

```bash
flutter pub run build_runner build --delete-conflicting-outputs
```

### Error: Tipo incompatible

```
type 'int' is not a subtype of type 'double'
```

**Solución:** El JSON contiene un int pero tu modelo espera double. Usa `.toDouble()`:

```dart
@JsonSerializable()
class Producto {
  @JsonKey(fromJson: _toDouble)
  final double fisico;
  
  static double _toDouble(dynamic value) {
    if (value is int) return value.toDouble();
    if (value is double) return value;
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }
}
```

---

## 🚀 Ventajas de usar json_serializable

✅ **Type-safe:** Errores en tiempo de compilación, no runtime  
✅ **Rápido:** Generación de código en build time  
✅ **Mantenible:** Cambios en el modelo actualizan automáticamente  
✅ **Menos código:** No escribes fromJson/toJson manualmente  
✅ **Robusto:** Maneja casos edge automáticamente  

---

## 📚 Recursos Adicionales

- [json_serializable Documentation](https://pub.dev/packages/json_serializable)
- [build_runner Documentation](https://pub.dev/packages/build_runner)
- [Dart JSON Serialization Guide](https://docs.flutter.dev/data-and-backend/json)

---

**Última actualización:** Febrero 2026
