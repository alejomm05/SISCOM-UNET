<?php

namespace App\Providers; // <--- ¡AÑADE ESTA LÍNEA!

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider; // <--- Asegúrate que esta línea esté aquí
use Illuminate\Support\Facades\Log; // <--- Y esta para nuestro log de debug
use Illuminate\Support\Facades\URL; // <--- PARA FORZAR HTTPS
class AppServiceProvider extends ServiceProvider
{
    // ... el resto del código ...
    public function boot(): void
    {
       Gate::policy(User::class, UserPolicy::class);
       if ($this->app->environment('local')) {
       URL::forceScheme('https');
   }
       // Usamos 'guards' para forzar la autenticación por token API (Sanctum)
      // Broadcast::routes(['guards' => ['sanctum']]); 
    }
}