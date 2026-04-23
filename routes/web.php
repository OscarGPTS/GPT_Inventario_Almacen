<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudesController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\CatalogosController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\NoConformidadController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\TicketController;

use App\Http\Middleware\SoloAdmin;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('reportes.entradas');
    }
    return view('welcome');
})->name('welcome');

// Login route (redirects to Google OAuth)
Route::get('/login', function () {
    return redirect()->route('google.redirect');
})->name('login');

// Google OAuth Routes
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// Ruta pública de inventario
Route::get('/inventario', [ReportesController::class, 'inventarioPublico'])->name('inventario.publico');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/tickets/{ticket}/asignar', [DashboardController::class, 'assignTicket'])->name('dashboard.tickets.assign');
    
    // Productos
    Route::get('/productos/search', [ProductoController::class, 'search'])->name('productos.search');
    Route::get('/productos/importar', [ProductoController::class, 'importForm'])->name('productos.import');
    Route::post('/productos/importar', [ProductoController::class, 'import'])->name('productos.import.process');
    Route::patch('/productos/{producto}/no-conforme', [ProductoController::class, 'toggleNoConforme'])->name('productos.no_conforme');
    Route::resource('productos', ProductoController::class);
    
    // Solicitudes — concentrado (Material + Movimiento) — debe ir ANTES del resource
    Route::get('/solicitudes/concentrado', [TicketController::class, 'concentrado'])->name('solicitudes.concentrado');

    // Solicitudes
    Route::post('/solicitudes/nueva', [SolicitudesController::class, 'store'])->name('solicitudes.nueva');
    Route::patch('/solicitudes/{solicitud}/cambiar-estado', [SolicitudesController::class, 'updateEstado'])->name('solicitudes.cambiarEstado');
    Route::resource('solicitudes', SolicitudController::class);
    Route::patch('/solicitudes/{solicitud}/estado', [SolicitudController::class, 'cambiarEstado'])->name('solicitudes.estado');
    
    // Movimientos
    Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
    Route::get('/movimientos/producto/{producto}', [MovimientoController::class, 'porProducto'])->name('movimientos.producto');

    // Usuarios
    // Usuarios (solo admin, excepto info-rh que es para todos)
    Route::get('/usuarios/info-rh', [UsuariosController::class, 'infoRhUsuario'])->name('usuarios.info_rh');
    Route::middleware(SoloAdmin::class)->group(function () {
        Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/buscar-api', [UsuariosController::class, 'buscarApi'])->name('usuarios.buscar_api');
        Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}/rol', [UsuariosController::class, 'updateRole'])->name('usuarios.update_role');
        Route::delete('/usuarios/{usuario}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
    });

    // Catálogos
    Route::get('/catalogos', [CatalogosController::class, 'index'])->name('catalogos.index');
    Route::post('/catalogos/{catalogo}', [CatalogosController::class, 'store'])->name('catalogos.store');
    Route::put('/catalogos/{catalogo}/{id}', [CatalogosController::class, 'update'])->name('catalogos.update');
    Route::delete('/catalogos/{catalogo}/{id}', [CatalogosController::class, 'destroy'])->name('catalogos.destroy');

    // Reportes / Secciones
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/entradas',[ReportesController::class, 'entradas'])->name('entradas');
        Route::get('/entradas/proximo-consecutivo',[ReportesController::class, 'proximoConsecutivo'])->name('entradas.proximo_consecutivo');
        Route::post('/entradas/importar',[ReportesController::class, 'importarEntradas'])->name('entradas.importar');
        Route::post('/entradas/guardar-producto',[ReportesController::class, 'guardarProducto'])->name('entradas.guardar_producto');
        Route::patch('/entradas/{producto}',[ReportesController::class, 'actualizarProducto'])->name('entradas.actualizar_producto');
        Route::get('/requisiciones',[ReportesController::class, 'requisiciones'])->name('requisiciones');
        Route::get('/barras',[ReportesController::class, 'barras'])->name('barras');
        Route::post('/barras/importar',[ReportesController::class, 'importarBarras'])->name('barras.importar');
        Route::post('/barras/guardar-producto',[ReportesController::class, 'guardarProductoBarra'])->name('barras.guardar_producto');
        Route::delete('/barras/borrar',[ReportesController::class, 'borrarBarras'])->name('barras.borrar');
        Route::get('/resguardo',[ReportesController::class, 'resguardo'])->name('resguardo');
        Route::get('/no-conforme',[ReportesController::class, 'noConforme'])->name('no_conforme');
        Route::get('/inventario-general',[ReportesController::class, 'inventarioGeneral'])->name('inventario_general');
    });

    // No Conformidades
    Route::post('/no-conformidades', [NoConformidadController::class, 'store'])->name('no_conformidades.store');
    Route::patch('/no-conformidades/{noConformidad}', [NoConformidadController::class, 'update'])->name('no_conformidades.update');
    Route::delete('/no-conformidades/{noConformidad}', [NoConformidadController::class, 'destroy'])->name('no_conformidades.destroy');

    // Tickets (Solicitudes de movimiento de piezas)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/crear', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/json', [TicketController::class, 'detalle'])->name('tickets.detalle');
    Route::post('/tickets/{ticket}/asignar', [TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/completar', [TicketController::class, 'complete'])->name('tickets.complete');
    Route::post('/tickets/{ticket}/cancelar', [TicketController::class, 'cancel'])->name('tickets.cancel');
    Route::patch('/tickets/{ticket}/cambiar-status', [TicketController::class, 'updateStatus'])->name('tickets.cambiarStatus');
    Route::post('/tickets/{ticket}/update-producto', [TicketController::class, 'updateProducto'])->name('tickets.updateProducto');
    Route::post('/tickets/{ticket}/encuesta', [TicketController::class, 'survey'])->name('tickets.survey');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    // Notificaciones web
    Route::post('/notificaciones/{id}/leer', [NotificacionesController::class, 'leer'])->name('notificaciones.leer');
    Route::post('/notificaciones/leer-todas', [NotificacionesController::class, 'leerTodas'])->name('notificaciones.leerTodas');

    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

