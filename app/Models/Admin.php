<?php
//modelo Admin.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['name', 'email', 'password'];

        protected $hidden = [
        'password',
        'remember_token',
        'tokens', // <-- FIX CRÍTICO
    ];
}