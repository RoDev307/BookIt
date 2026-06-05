<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// Vista del catálogo principal (Sprint 1)
Route::get('/', [BusinessController::class, 'index'])->name('businesses.index');

//Route::get('/negocio/{slug}', [BusinessController::class, 'show'])->name('businesses.show');

//----------------- CRUD DE SERVICE CONTROLLER-------------------------
//ahi le cambian los nombres de la ruta y el name si fuera necesario o a conveniencia
//INDEX
Route::get('/services',[ServiceController::class,'index'])->name('services.index');
//CREATE
Route::get('/services/create',[ServiceController::class,'create'])->name('services.create');
//STORE
Route::post('/services',[ServiceController::class,'store'])->name('services.storer');
//SHOW
Route::get('/services/{id}',[ServiceController::class,'show'])->name('services.show');
//EDIT
Route::get('/services/{id}/edit',[ServiceController::class,'edit'])->name('services.edit');
//UPDATE
Route::put('/services/{id}',[ServiceController::class,'update'])->name('services.update');
//DELETE
Route::delete('/services/{id}',[ServiceController::class,'destroy'])->name('services.destroy');
// TAREA 1.2: Ruta dinámica para capturar el negocio seleccionado
Route::get('/negocio/{slug}', [BusinessController::class, 'show'])->name('businesses.show');

