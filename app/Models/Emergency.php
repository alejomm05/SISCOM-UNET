<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Nueva importación

class Emergency extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'location',
        'status',
        'attended_at', // 💡 NUEVO
    ];
    
    protected $casts = [
        'attended_at' => 'datetime', // 💡 NUEVO
    ];
    
    // Relación: una emergencia pertenece a un usuario (Guardia que la reportó)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relación: una emergencia puede generar varias tareas
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}