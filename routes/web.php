<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudesController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportesController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Google OAuth Routes
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Productos
    Route::get('/productos/search', [ProductoController::class, 'search'])->name('productos.search');
    Route::get('/productos/importar', [ProductoController::class, 'importForm'])->name('productos.import');
    Route::post('/productos/importar', [ProductoController::class, 'import'])->name('productos.import.process');
    Route::resource('productos', ProductoController::class);
    
    // Solicitudes
    Route::post('/solicitudes/nueva', [SolicitudesController::class, 'store'])->name('solicitudes.nueva');
    Route::patch('/solicitudes/{solicitud}/cambiar-estado', [SolicitudesController::class, 'updateEstado'])->name('solicitudes.cambiarEstado');
    Route::resource('solicitudes', SolicitudController::class);
    Route::patch('/solicitudes/{solicitud}/estado', [SolicitudController::class, 'cambiarEstado'])->name('solicitudes.estado');
    
    // Movimientos
    Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
    Route::get('/movimientos/producto/{producto}', [MovimientoController::class, 'porProducto'])->name('movimientos.producto');

    // Reportes / Secciones
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/entradas',           [ReportesController::class, 'entradas'])->name('entradas');
        Route::get('/requisiciones',      [ReportesController::class, 'requisiciones'])->name('requisiciones');
        Route::get('/barras',             [ReportesController::class, 'barras'])->name('barras');
        Route::get('/resguardo',          [ReportesController::class, 'resguardo'])->name('resguardo');
        Route::get('/no-conforme',        [ReportesController::class, 'noConforme'])->name('no_conforme');
        Route::get('/inventario-general', [ReportesController::class, 'inventarioGeneral'])->name('inventario_general');
    });
    
    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

