<?php

use App\Http\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

// Vista del catálogo principal (Sprint 1)
Route::get('/', [BusinessController::class, 'index'])->name('businesses.index');

// TAREA 1.2: Ruta dinámica para capturar el negocio seleccionado
Route::get('/{slug}', [BusinessController::class, 'show'])->name('businesses.show');
