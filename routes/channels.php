<?php

// routes/channels.php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log; 

Broadcast::channel('guardians-chat', function ($user) {
    Log::info('Intento de conexión al chat', ['user_id' => $user?->id]);
    return true;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    
    // Si el usuario autenticado (obtenido del token de Flutter) no existe o no es un guardia.
    // Admin y User tienen IDs independientes: sin este chequeo el admin #1 podría escuchar al guardia #1.
    if (!$user instanceof User) {
        return false;
    }
    
    // La verificación crítica: El ID del usuario autenticado debe coincidir con el ID en el canal.
    return (int) $user->id === (int) $userId;
});