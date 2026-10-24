<?php
//modelo Report.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

protected $fillable = [
    'user_id', // ID del Guardia que reporta
    'type',
    'description',
    'location',     
    'media_url',     
    'image_path',    
    'status',
];
    protected $casts = [
        'created_at' => 'datetime',
        'location' => 'json',
    ];

    /**
     * El Reporte pertenece a un Usuario (Guardia).
     */
    public function user(): BelongsTo
    {
        // Asumimos que la clave foránea es 'user_id'
        return $this->belongsTo(User::class, 'user_id');
    }
}