<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;

class UserPolicy
{
    // El usuario puede ser un Admin o un User (guardia); solo los admins pasan.
    // No tipar como Admin: Laravel llamaría igual con un User y daría TypeError (500) en vez de 403.
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof Admin;
    }

    public function create(Authenticatable $user): bool
    {
        return $user instanceof Admin;
    }
}
