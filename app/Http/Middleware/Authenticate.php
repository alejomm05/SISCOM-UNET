<?php
// app/Http/Middleware/Authenticate.php (Asegúrate de que este archivo exista)

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Si no es una petición JSON/API, entonces intentamos una redirección.
        if (! $request->expectsJson()) {
            
            // 🚨 LÓGICA CLAVE: Si la ruta tiene prefijo 'admin', vamos al login de admin.
            if ($request->routeIs('admin.*')) {
                return route('admin.login');
            }

            // Para el guard 'web' por defecto (si lo usas)
            return route('login'); 
        }

        return null;
    }
}