<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

// Página principal
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de autenticación de Breeze
require __DIR__.'/auth.php';

// ======================
// CATÁLOGO Y CITAS
// ======================

// Catálogo de negocios
Route::get('/negocios', [BusinessController::class, 'index'])
    ->name('businesses.index');

// Detalle de negocio
Route::get('/negocios/{slug}', [BusinessController::class, 'show'])
    ->name('businesses.show');

// Crear reserva
Route::post('/reservar', [AppointmentController::class, 'store'])
    ->name('appointments.store');

// Pantalla de éxito
Route::get('/reserva-confirmada', function () {
    return view('success');
})->name('appointments.success');

// Descargar PDF
Route::get('/descargar-ticket', [AppointmentController::class, 'descargarPDF'])
    ->name('appointments.pdf');

//Mostrar citas
Route::get('/mis-citas',[AppointmentController::class, 'misCitas'])->middleware('auth')->name('appointments.mis-citas');
//Cancelar citas
Route::patch('/citas/{id}/cancelar',[AppointmentController::class, 'cancelar'])->middleware('auth')->name('appointments.cancel');
//Autenticacion de usuario 
Route::get('/admin-test', function () {return 'Solo administradores';})->middleware(['auth', 'role:admin_business']);

