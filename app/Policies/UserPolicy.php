<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(Admin $admin): bool
    {
        return true; // Solo admins pueden ver/gestionar usuarios
    }

    public function create(Admin $admin): bool
    {
        return true;
    }
}