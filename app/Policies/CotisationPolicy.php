<?php

namespace App\Policies;

use App\Models\Cotisation;
use App\Models\User;

class CotisationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) return false;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isOrganisateur() || $user->isMembre();
    }

    public function view(User $user, Cotisation $cotisation): bool
    {
        if ($user->isOrganisateur()) {
            return $cotisation->tontine?->organisateur_id === $user->id;
        }
        return $user->membre_id === $cotisation->membre_id;
    }

    public function create(User $user): bool
    {
        return $user->isOrganisateur() || $user->isMembre();
    }

    public function delete(User $user, Cotisation $cotisation): bool
    {
        return $user->isOrganisateur()
            && $cotisation->tontine?->organisateur_id === $user->id;
    }
}
