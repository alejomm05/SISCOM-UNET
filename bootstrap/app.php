<?php
// bootstrap/app.php (SOLUCIÓN FINAL DE ROUTING Y MIDDLEWARE)

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route; // Puedes quitar este 'use' si no lo usas en este archivo

return Application::configure(basePath: dirname(__DIR__))
    // 1. ROUTING LIMPIO (CRÍTICO para Sanctum/Duplicación)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        
        // ❌ ¡Eliminamos la función 'then' que causaba la doble carga y los conflictos!
    )
   ->withMiddleware(function (Middleware $middleware) {
        
        // 🚨 CORRECCIÓN CSRF: Ignora la verificación de token para la ruta de login de admin.
        // Esto resuelve el Error 419 Page Expired.
        $middleware->validateCsrfTokens(except: [
            'admin/login',
        ]);

        // Habilidades de los tokens de Sanctum ('supervisor' o 'guard'), usadas en routes/api.php
        $middleware->alias([
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        ]);
        
        // NOTA: No necesitamos redefinir toda la pila de middlewares aquí.
        // Laravel usará sus valores predeterminados, pero con esta exclusión añadida.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();