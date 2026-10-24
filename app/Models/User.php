<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importación para claridad

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Incluimos 'shift' que estás usando en el controlador de Guardias.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'code', 
        'role',
        'shift', // 💡 Agregado: para permitir al GuardiaController.php usar este campo
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // --------------------------------------------------------
    // 🚨 RELACIONES AÑADIDAS (Corrige el BadMethodCallException)
    // --------------------------------------------------------
    
    /**
     * Un Usuario (Guardia) tiene muchos Reportes.
     */
    public function reports(): HasMany
    {
        // Asumimos que la clave foránea en la tabla reports es 'user_id'
        return $this->hasMany(Report::class, 'user_id');
    }

    /**
     * Un Usuario (Guardia) tiene muchas Tareas asignadas.
     */
    public function tasks(): HasMany
    {
        // 💡 Importante: El modelo Task.php usa 'assigned_to' como clave foránea.
        // Debemos especificarla aquí.
        return $this->hasMany(Task::class, 'assigned_to');
    }
}