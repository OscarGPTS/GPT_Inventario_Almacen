<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\DashboardController;

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
    Route::resource('solicitudes', SolicitudController::class);
    Route::patch('/solicitudes/{solicitud}/estado', [SolicitudController::class, 'cambiarEstado'])->name('solicitudes.estado');
    
    // Movimientos
    Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
    Route::get('/movimientos/producto/{producto}', [MovimientoController::class, 'porProducto'])->name('movimientos.producto');
    
    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

