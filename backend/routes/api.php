<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SuperAdminBusinessController;

// Autenticación
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Funciones exclusivas del superadministrador
Route::prefix('v1/superadmin')
    ->middleware(['auth:sanctum', 'role:super_admin'])
    ->group(function () {
        Route::get('/businesses', [SuperAdminBusinessController::class, 'index']);
        Route::get('/businesses/{id}', [SuperAdminBusinessController::class, 'show']);
        Route::post('/businesses', [SuperAdminBusinessController::class, 'store']);
        Route::put('/businesses/{id}', [SuperAdminBusinessController::class, 'update']);
    });
