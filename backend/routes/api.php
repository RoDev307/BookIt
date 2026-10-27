<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BusinessController;
use App\Http\Controllers\Api\V1\SuperAdmin\MasterBusinessController;
use App\Http\Controllers\Api\V1\Tenant\AdminAppointmentController;
use App\Http\Controllers\Api\V1\Tenant\AdminBusinessController;
use App\Http\Controllers\Api\V1\Tenant\AdminServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BookIt SaaS - API Routes (v1)
|--------------------------------------------------------------------------
|
| Todas las rutas están preparadas para ser consumidas por las aplicaciones
| cliente (Frontend Web Vue y Móvil Ionic) con soporte CORS y Sanctum.
|
*/

Route::prefix('v1')->group(function () {

    // =========================================================================
    // 1. AUTENTICACIÓN PÚBLICA
    // =========================================================================
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // =========================================================================
    // 2. CATÁLOGO PÚBLICO & DISPONIBILIDAD (Sin autenticación obligatoria)
    // =========================================================================
    Route::get('/businesses', [BusinessController::class, 'index']);
    Route::get('/businesses/{slug}', [BusinessController::class, 'show']);
    Route::get('/businesses/{slug}/services', [BusinessController::class, 'services']);
    Route::get('/businesses/{slug}/hours', [BusinessController::class, 'hours']);
    Route::get('/businesses/{slug}/availability', [BusinessController::class, 'availability']);

    // =========================================================================
    // 3. RUTAS PROTEGIDAS (Requieren Bearer Token con Sanctum)
    // =========================================================================
    Route::middleware('auth:sanctum')->group(function () {

        // --- Gestión de sesión y perfil propio ---
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        // --- Citas para clientes ---
        Route::get('/my-appointments', [AppointmentController::class, 'index']);
        Route::post('/appointments', [AppointmentController::class, 'store']);
        Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
        Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);

        // =====================================================================
        // 4. PANEL DEL COMERCIO LOCAL (Inquilino / Tenant Admin)
        // =====================================================================
        Route::middleware('role:admin_business,super_admin')->prefix('tenant')->group(function () {

            // Métricas y perfil de negocio
            Route::get('/dashboard', [AdminBusinessController::class, 'dashboard']);
            Route::get('/business', [AdminBusinessController::class, 'getProfile']);
            Route::put('/business', [AdminBusinessController::class, 'updateProfile']);
            Route::put('/business/hours', [AdminBusinessController::class, 'updateHours']);

            // CRUD de Servicios del comercio
            Route::get('/services', [AdminServiceController::class, 'index']);
            Route::post('/services', [AdminServiceController::class, 'store']);
            Route::get('/services/{id}', [AdminServiceController::class, 'show']);
            Route::put('/services/{id}', [AdminServiceController::class, 'update']);
            Route::delete('/services/{id}', [AdminServiceController::class, 'destroy']);

            // Gestión de Citas y Calendario del comercio
            Route::get('/appointments', [AdminAppointmentController::class, 'index']);
            Route::get('/appointments/calendar', [AdminAppointmentController::class, 'calendar']);
            Route::post('/appointments', [AdminAppointmentController::class, 'store']);
            Route::put('/appointments/{id}', [AdminAppointmentController::class, 'update']);
            Route::patch('/appointments/{id}/status', [AdminAppointmentController::class, 'updateStatus']);
            Route::get('/appointments/{id}/pdf', [AdminAppointmentController::class, 'downloadPdf']);
            Route::get('/appointments/report/pdf', [AdminAppointmentController::class, 'generateReportPdf']);
        });

        // =====================================================================
        // 5. CONSOLA MAESTRA GLOBAL (Super Administrador SaaS)
        // =====================================================================
        Route::middleware('role:super_admin')->prefix('superadmin')->group(function () {
            Route::get('/dashboard', [MasterBusinessController::class, 'dashboard']);
            Route::get('/businesses', [MasterBusinessController::class, 'index']);
            Route::post('/businesses', [MasterBusinessController::class, 'store']);
            Route::get('/businesses/{id}', [MasterBusinessController::class, 'show']);
            Route::put('/businesses/{id}', [MasterBusinessController::class, 'update']);
            Route::delete('/businesses/{id}', [MasterBusinessController::class, 'destroy']);
        });
    });
});
