<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 🛒 RUTAS PÚBLICAS DEL CATÁLOGO (OMAR & BACKEND)
// =========================================================================

// Ruta fija para el Catálogo principal
Route::get('/negocios', [BusinessController::class, 'index'])->name('businesses.index');

// Ruta dinámica para el Detalle del negocio (Sincronizada con el catálogo)
Route::get('/negocios/{slug}', [BusinessController::class, 'show'])->name('businesses.show');


// =========================================================================
// 📅 MOTOR DE CITAS Y RESERVAS (TUS TAREAS SPRINT 2)
// =========================================================================

// Ruta para recibir los datos del formulario de reserva (Tarea 2.4)
Route::post('/reservar', [AppointmentController::class, 'store'])->name('appointments.store');

// Muestra la pantalla de éxito al cliente (Tarea 2.6)
Route::get('/reserva-confirmada', function () {
    return view('success');
})->name('appointments.success');

// Ruta para la generación del comprobante en PDF (Opcional/Extra)
Route::get('/descargar-ticket', [AppointmentController::class, 'descargarPDF'])->name('appointments.pdf');


// =========================================================================
// 🛠️ PANEL ADMINISTRATIVO: CRUD DE SERVICIOS (ALEXANDER / EQUIPO)
// =========================================================================

Route::prefix('admin')->group(function () {
    // INDEX
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    // CREATE
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    // STORE
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    // SHOW
    Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
    // EDIT
    Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    // UPDATE
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
    // DELETE
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
});