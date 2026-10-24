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
    /**
     * Ubicación legible para el panel. Api\ReportController guarda json_encode(location) y el cast
     * 'json' vuelve a codificarla, así que al leer queda un string con comillas ("Edificio B").
     * Se decodifica una vez más si hace falta; no se cambia el guardado para no alterar lo que recibe la app.
     */
    public function getLocationTextAttribute(): ?string
    {
        $location = $this->location;

        if (is_string($location)) {
            $decoded = json_decode($location, true);
            $location = json_last_error() === JSON_ERROR_NONE ? $decoded : $location;
        }

        if (is_array($location)) {
            return implode(', ', $location);
        }

        return $location === null ? null : (string) $location;
    }

    public function user(): BelongsTo
    {
        // Asumimos que la clave foránea es 'user_id'
        return $this->belongsTo(User::class, 'user_id');
    }
}