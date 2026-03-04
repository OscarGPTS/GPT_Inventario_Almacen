<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\MovimientoApiController;
use App\Http\Controllers\Api\CatalogoApiController;
use App\Http\Controllers\Api\SolicitudApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas API para tu aplicación.
| Todas las rutas están prefijadas con /api y retornan JSON.
|
*/

// Rutas públicas (sin autenticación)
Route::prefix('v1')->group(function () {
    
    // Catálogos dinámicos (departamentos, unidades de medida)
    Route::get('/departamentos/buscar', [CatalogoApiController::class, 'buscarDepartamentos']);
    Route::get('/unidades-medida/buscar', [CatalogoApiController::class, 'buscarUnidades']);

    // Productos
    Route::get('/productos', [ProductoApiController::class, 'index'])->name('api.productos.index');
    Route::get('/productos/stats', [ProductoApiController::class, 'stats'])->name('api.productos.stats');
    Route::get('/productos/buscar', [ProductoApiController::class, 'search'])->name('api.productos.search');
    Route::get('/productos/buscar/{codigo}', [ProductoApiController::class, 'searchByCodigo'])->name('api.productos.buscar.codigo');
    Route::get('/productos/{id}', [ProductoApiController::class, 'show'])->name('api.productos.show');
    Route::get('/productos/{id}', [ProductoApiController::class, 'show'])->name('api.productos.show');
    
    // Catálogos
    Route::get('/catalogos', [ProductoApiController::class, 'catalogos'])->name('api.catalogos');
    
    // Movimientos
    Route::get('/movimientos', [MovimientoApiController::class, 'index'])->name('api.movimientos.index');
    Route::get('/movimientos/stats', [MovimientoApiController::class, 'stats'])->name('api.movimientos.stats');
    Route::get('/movimientos/{id}', [MovimientoApiController::class, 'show'])->name('api.movimientos.show');
    
    // Movimientos por producto
    Route::get('/productos/{producto_id}/movimientos', [MovimientoApiController::class, 'porProducto'])->name('api.productos.movimientos');
    
    // Solicitudes (Requisiciones)
    Route::get('/solicitudes', [SolicitudApiController::class, 'index'])->name('api.solicitudes.index');
    Route::post('/solicitudes', [SolicitudApiController::class, 'store'])->name('api.solicitudes.store');
    Route::get('/solicitudes/{id}', [SolicitudApiController::class, 'show'])->name('api.solicitudes.show');
    Route::patch('/solicitudes/{id}/estado', [SolicitudApiController::class, 'updateEstado'])->name('api.solicitudes.updateEstado');
});

// Rutas protegidas (requieren autenticación con Sanctum)
// Descomentar cuando se configure Sanctum completamente
/*
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
*/
