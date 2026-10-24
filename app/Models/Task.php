<?php
//modelo Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'assigned_to',
        'title',
        'description',
        'due_date',
        'priority', // 💡 NUEVO
        'emergency_id', // 💡 NUEVO
        'status',
    ];


    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * La Tarea pertenece a un Usuario (Guardia).
     */
    public function user(): BelongsTo
    {
        // 🚨 CORRECCIÓN: Usar 'assigned_to' para coincidir con la columna en la tabla tasks
        return $this->belongsTo(User::class, 'assigned_to'); 
    }

        public function emergency(): BelongsTo
    {
        return $this->belongsTo(Emergency::class);
    }
}