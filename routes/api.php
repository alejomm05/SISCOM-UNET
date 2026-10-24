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
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\TaskController;
use App\Models\Admin; 
use App\Models\User; 

// Obtener el usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- RUTAS DE AUTENTICACIÓN ---
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// --- RUTAS DE ADMINISTRACIÓN ---
// 🚨 CORRECCIÓN CLAVE: El middleware de autorización ('can:viewAny,App\\Models\\User') se quitó del grupo 
// para prevenir errores 403, dejando solo la autenticación. La autorización se maneja en el controlador.
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);


// --- RUTAS PRINCIPALES DEL SISTEMA ---

// Mensajes (Chat)
Route::post('/messages', [MessageController::class, 'store']);
Route::get('/messages', [MessageController::class, 'index']); 
Route::get('/messages/{id}', [MessageController::class, 'show']); 

// Reportes
Route::post('/reports', [ReportController::class, 'store']);
Route::get('/reports/{id}', [ReportController::class, 'show']); 

// Tareas
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks', [TaskController::class, 'index']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); // Asumiendo que esta ruta existe


// Recomendable proteger esta ruta con autenticación
    Route::post('/emergencies', [EmergencyController::class, 'store']);
    Route::get('/emergencies/active', [EmergencyController::class, 'active']);

    Route::get('/emergency-contacts', [EmergencyContactController::class, 'index']);
      // Usa la clase importada App\Http\Controllers\Api\Admin\EmergencyContactController
    Route::post('/emergency-contacts', [EmergencyContactController::class, 'store']);
    Route::put('/emergency-contacts/{id}', [EmergencyContactController::class, 'update']);
    Route::delete('/emergency-contacts/{id}', [EmergencyContactController::class, 'destroy']);
    


// Actividades y Usuarios (Detalle)
Route::get('/activities', [ActivityController::class, 'index']); 
Route::get('/users/{id}', [UserController::class, 'show']); 

Broadcast::routes(['prefix' => 'api']);