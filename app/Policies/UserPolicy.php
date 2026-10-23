<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;

class UserPolicy
{
    // El token puede pertenecer a un Admin o a un User (guardia); solo los admins pasan
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof Admin;
    }

    public function create(Authenticatable $user): bool
    {
        return $user instanceof Admin;
    }
}
