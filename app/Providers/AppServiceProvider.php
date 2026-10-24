<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
       Gate::policy(User::class, UserPolicy::class);

       // Forzar HTTPS solo cuando APP_URL es https (p. ej. detrás del túnel de Serveo).
       // Con php artisan serve en http://127.0.0.1:8000 no se fuerza, o los formularios apuntarían a https.
       if (str_starts_with(config('app.url'), 'https://')) {
           URL::forceScheme('https');
       }
    }
}
