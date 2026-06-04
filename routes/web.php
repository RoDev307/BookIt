<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

// Ruta fija para el Catálogo
Route::get('/negocios', [BusinessController::class, 'index'])->name('businesses.index');

// Ruta dinámica para el Detalle 
Route::get('/comercio/{slug}', [BusinessController::class, 'show'])->name('businesses.show');

// Ruta para recibir los datos del formulario de reserva 
Route::post('/reservar', [AppointmentController::class, 'store'])->name('appointments.store');

// Muestra la pantalla de éxito al cliente
Route::get('/reserva-confirmada', function () {
    return view('success');
})->name('appointments.success');

Route::get('/descargar-ticket', [AppointmentController::class, 'descargarPDF'])->name('appointments.pdf');
