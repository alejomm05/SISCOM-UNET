<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  // autor guardia
        'admin_id', // autor supervisor
        'content',
        'file_path',
        'file_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Autor del mensaje en el formato que lee la app: message['user']['name'].
     * Incluye 'role' ('guard' o 'supervisor') porque los IDs de users y admins se solapan.
     */
    public function authorData(): ?array
    {
        $author = $this->user_id ? $this->user : $this->admin;

        if (!$author) {
            return null;
        }

        return [
            'id' => $author->id,
            'name' => $author->name,
            'email' => $author->email,
            'role' => $this->user_id ? 'guard' : 'supervisor',
        ];
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        unset($data['admin']);
        $data['user'] = $this->authorData();

        return $data;
    }
}
