<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WebLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuardiaController;
use App\Http\Controllers\Admin\TaskAdminController;
use App\Http\Controllers\Admin\ReportAdminController; 
use App\Http\Controllers\Admin\EmergencyAdminController;
use App\Http\Controllers\Admin\EmergencyContactAdminController;

// --- RUTAS PÚBLICAS Y DE PRUEBA ---
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// --- 1. LOGIN DEL SUPERVISOR (Público) ---
Route::get('admin/login', [WebLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [WebLoginController::class, 'login']);

// --- 2. PANEL WEB PROTEGIDO (Sólo sesión de Admin) ---
Route::middleware(['auth:admin_web'])->prefix('admin')->name('admin.')->group(function () {
    
    // Logout 
    Route::post('logout', [WebLoginController::class, 'logout'])->name('logout'); 
    
    // DASHBOARD (Ruta Raíz del Panel)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD GUARDIAS 
    Route::resource('guardias', GuardiaController::class);

    // TAREAS (Historial, Asignación, Eliminación)
    Route::get('tasks/history', [TaskAdminController::class, 'history'])->name('tasks.history'); 
    Route::post('tasks/assign', [TaskAdminController::class, 'assign'])->name('tasks.assign');
    // Vaciar todas las tareas (Eliminación masiva).
    // Debe ir ANTES de tasks/{task}, o 'clear-all' se toma como ID de tarea y da 404.
    Route::delete('tasks/clear-all', [TaskAdminController::class, 'clearAll'])->name('tasks.clearAll');
    // Eliminar individualmente
    Route::delete('tasks/{task}', [TaskAdminController::class, 'destroy'])->name('tasks.destroy');

    // REPORTES (Listado, Eliminación)
    Route::get('reports', [ReportAdminController::class, 'index'])->name('reports.index');
    // Vaciar todos los reportes (Eliminación masiva). Debe ir ANTES de reports/{report}.
    Route::delete('reports/clear-all', [ReportAdminController::class, 'clearAll'])->name('reports.clearAll');
    // Eliminar individualmente
    Route::delete('reports/{report}', [ReportAdminController::class, 'destroy'])->name('reports.destroy');

    // GESTIÓN DE CONTACTOS DE EMERGENCIA (CRUD)
    Route::resource('contacts', EmergencyContactAdminController::class)->except(['show']);

    // GESTIÓN DE EMERGENCIAS (Alertas)
    Route::controller(EmergencyAdminController::class)->prefix('emergencies')->name('emergencies.')->group(function () {
        Route::get('/', 'index')->name('index'); 
        Route::post('/', 'store')->name('store');
        Route::put('{emergency}/resolve', 'resolve')->name('resolve'); 
        Route::post('{emergency}/assign-task', 'assignTask')->name('assignTask'); 
    });
});
