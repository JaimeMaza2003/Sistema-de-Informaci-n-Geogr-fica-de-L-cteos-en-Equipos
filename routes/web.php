<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstablecimientoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\OportunidadController;
use App\Http\Controllers\PropuestaController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - SIG Lácteos Cotopaxi
|--------------------------------------------------------------------------
*/

// Redirección principal al Mapa de Oportunidades
Route::get('/', function () {
    return redirect()->route('mapa.index');
});

// 1. PANTALLA PRINCIPAL: MAPA DE OPORTUNIDADES & LISTADO (Objetivo 2)
Route::get('/mapa', [EstablecimientoController::class, 'index'])->name('mapa.index');

// 2. FICHA DE ESTABLECIMIENTO (Objetivo 3)
Route::get('/establecimientos/{id}', [EstablecimientoController::class, 'show'])->name('establecimientos.show');
Route::post('/establecimientos/{id}/guardar-oportunidad', [EstablecimientoController::class, 'guardarOportunidad'])->name('establecimientos.guardar-oportunidad');
Route::post('/establecimientos/{id}/reportar', [EstablecimientoController::class, 'reportarError'])->name('establecimientos.reportar');
Route::post('/establecimientos/detectar-duplicados', [EstablecimientoController::class, 'detectarDuplicados'])->name('establecimientos.detectar-duplicados');

// 3. CATÁLOGO DE PRODUCTOS DEL EMPRENDEDOR (Objetivo 4)
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/productos/{id}/ficha-pdf', [ProductoController::class, 'fichaPdf'])->name('productos.ficha-pdf');
Route::get('/catalogo-publico/{userId}', [ProductoController::class, 'catalogoPublico'])->name('productos.catalogo-publico');

// 4. VINCULACIÓN: "BUSCAR DÓNDE OFRECERLO" (Objetivo 5)
Route::get('/productos/{id}/compatibles', [ProductoController::class, 'compatibles'])->name('productos.compatibles');

// 5. PROPUESTAS COMERCIALES (Objetivo 6)
Route::post('/propuestas', [PropuestaController::class, 'store'])->name('propuestas.store');
Route::post('/propuestas/{id}/responder', [PropuestaController::class, 'responder'])->name('propuestas.responder');

// 6. SEGUIMIENTO DE OPORTUNIDADES ("MIS OPORTUNIDADES" CRM) (Objetivo 7)
Route::get('/mis-oportunidades', [OportunidadController::class, 'index'])->name('oportunidades.index');
Route::put('/mis-oportunidades/{id}', [OportunidadController::class, 'update'])->name('oportunidades.update');
Route::delete('/mis-oportunidades/{id}', [OportunidadController::class, 'destroy'])->name('oportunidades.destroy');

// 7. PANEL ADMINISTRATIVO (Objetivo 8)
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/establecimientos/{id}/verificar', [AdminController::class, 'toggleVerificacion'])->name('admin.verificar');
Route::post('/admin/establecimientos/{id}/corregir-ubicacion', [AdminController::class, 'corregirUbicacion'])->name('admin.corregir-ubicacion');
Route::post('/admin/reportes/{id}/resolver', [AdminController::class, 'resolverReporte'])->name('admin.resolver-reporte');
