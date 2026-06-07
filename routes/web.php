<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SuperAdminBusinessController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 🔐 AUTENTICACIÓN Y PERFIL DE USUARIO (BREEZE / ESMERALDA)
// =========================================================================

// Página principal (Landing base)
Route::get('/', [BusinessController::class, 'index'])->name('businesses.index');

// Rutas automáticas de autenticación de Breeze (Login, registro, etc.)
require __DIR__ . '/auth.php';

// Panel base del usuario autenticado e historial de citas
Route::get('/dashboard', [AppointmentController::class, 'misCitas'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Gestión del Perfil del Usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// =========================================================================
// 🛒 CATÁLOGO PÚBLICO (OMAR & BACKEND)
// =========================================================================

// Catálogo de negocios
Route::get('/negocios', [BusinessController::class, 'index'])
    ->name('businesses.index');

// Detalle de un negocio específico
Route::get('/negocios/{slug}', [BusinessController::class, 'show'])
    ->name('businesses.show');


// =========================================================================
// 📅 MOTOR DE CITAS Y RESERVAS (RODRIGO - SPRINT 2)
// =========================================================================

Route::middleware('auth')->group(function () {
    // Crear reserva (Procesamiento del formulario)
    Route::post('/reservar', [AppointmentController::class, 'store'])
        ->name('appointments.store');

    // Pantalla de éxito post-reserva
    Route::get('/reserva-confirmada', function () {
        return view('success');
    })->name('appointments.success');

    // Descargar comprobante PDF de Databox
    Route::get('/descargar-ticket', [AppointmentController::class, 'descargarPDF'])
        ->name('appointments.pdf');

    // Historial y Cancelaciones del Cliente (Esmeralda)
    Route::get('/mis-citas', [AppointmentController::class, 'misCitas'])
        ->name('appointments.mis-citas');

    Route::patch('/citas/{id}/cancelar', [AppointmentController::class, 'cancelar'])
        ->name('appointments.cancel');
});


// =========================================================================
// 🛠️ PANEL ADMINISTRATIVO INTERNO: CRUD DE SERVICIOS (ALEXANDER)
// =========================================================================

Route::middleware(['auth'])->prefix('admin')->group(function () {

    // CRUD Completo del Gestor de Servicios
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::get('/appointments/create', [AppointmentController::class, 'createAdmin'])->name('admin.appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'storeAdmin'])->name('admin.appointments.store');
});

// Dejamos la ruta de testeo aislada con el middleware original para que Esmeralda revise su lógica luego
Route::middleware(['auth', 'role:admin_business'])->prefix('admin')->group(function () {
    Route::get('/admin-test', function () {
        return 'Solo administradores estricto';
    });


    Route::middleware(['auth', 'role:super_admin'])->group(function () {
        Route::get('/master/businesses', [SuperAdminBusinessController::class, 'index'])->name('master.businesses.index');
        Route::get('/master/businesses/{id}/edit', [SuperAdminBusinessController::class, 'edit'])->name('master.businesses.edit');
        Route::put('/master/businesses/{id}', [SuperAdminBusinessController::class, 'update'])->name('master.businesses.update');
    });
});
