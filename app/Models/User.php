<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens; // ← ¡Aquí está lo importante!

    protected $fillable = ['name', 'email', 'password', 'code'];
}