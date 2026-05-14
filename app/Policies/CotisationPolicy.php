<?php

namespace App\Policies;

use App\Models\Cotisation;
use App\Models\User;

class CotisationPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdminOrOrganisateur();
    }

    public function view(User $user, Cotisation $cotisation): bool
    {
        if ($user->isAdminOrOrganisateur()) return true;
        return $user->membre_id === $cotisation->membre_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdminOrOrganisateur();
    }

    public function delete(User $user, Cotisation $cotisation): bool
    {
        return $user->isAdminOrOrganisateur();
    }
}
