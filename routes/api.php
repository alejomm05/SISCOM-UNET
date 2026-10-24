<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\EmergencyContactController;
use App\Http\Controllers\Api\EmergencyController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;

// --- RUTAS PÚBLICAS ---
Route::post('/login', [LoginController::class, 'login']);

// Autorización de canales privados de Reverb con el token de Sanctum (app Flutter): POST /api/broadcasting/auth
// Sin 'prefix': este archivo ya se carga con el prefijo /api (con prefix => 'api' quedaba /api/api/...).
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// --- RUTAS AUTENTICADAS ---
// El usuario se toma siempre del token (Bearer), nunca de un user_id enviado en el cuerpo.
// Los tokens llevan la habilidad 'supervisor' (Admin) o 'guard' (User), asignada en LoginController.
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [LoginController::class, 'logout']);

    // --- SOLO SUPERVISOR ---
    Route::middleware('abilities:supervisor')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::post('/emergency-contacts', [EmergencyContactController::class, 'store']);
        Route::put('/emergency-contacts/{id}', [EmergencyContactController::class, 'update']);
        Route::delete('/emergency-contacts/{id}', [EmergencyContactController::class, 'destroy']);
    });

    // --- SOLO GUARDIA ---
    Route::middleware('abilities:guard')->group(function () {
        // Mensajes (Chat)
        Route::post('/messages', [MessageController::class, 'store']);

        // Reportes
        Route::post('/reports', [ReportController::class, 'store']);

        // Tareas propias
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::put('/tasks/{id}', [TaskController::class, 'update']);

        // Emergencias
        Route::post('/emergencies', [EmergencyController::class, 'store']);
    });

    // --- SUPERVISOR Y GUARDIA ---
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/{id}', [MessageController::class, 'show']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);
    Route::get('/emergencies/active', [EmergencyController::class, 'active']);
    Route::get('/emergency-contacts', [EmergencyContactController::class, 'index']);
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
});
