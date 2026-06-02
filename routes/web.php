<?php

use App\Http\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

// Cambiamos la ruta inicial para que muestre nuestro catálogo
Route::get('/', [BusinessController::class, 'index'])->name('businesses.index');
