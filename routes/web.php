<?php

use App\Http\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

// Ruta fija para el Catálogo (Muestra las tarjetas de los negocios)
Route::get('/negocios', [BusinessController::class, 'index'])->name('businesses.index');

// Ruta dinámica corregida para el Detalle (Muestra los servicios de un negocio)
Route::get('/comercio/{slug}', [BusinessController::class, 'show'])->name('businesses.show');
