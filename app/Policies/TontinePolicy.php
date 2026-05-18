<?php

namespace App\Policies;

use App\Models\Tontine;
use App\Models\User;

class TontinePolicy
{
    // ✅ Admin bypasse TOUT sauf les tontines
    public function before(User $user, string $ability): ?bool
    {
        // Admin ne bypass PAS les tontines → il n'y a pas accès
        if ($user->isAdmin()) return false;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isOrganisateur() || $user->isMembre();
    }

    public function view(User $user, Tontine $tontine): bool
    {
        if ($user->isOrganisateur()) {
            // ✅ Seulement SES tontines
            return $tontine->organisateur_id === $user->id;
        }
        if ($user->isMembre() && $user->membre) {
            return $tontine->membres()
                           ->where('membre_id', $user->membre_id)
                           ->exists();
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isOrganisateur();
    }

    public function update(User $user, Tontine $tontine): bool
    {
        return $user->isOrganisateur()
            && $tontine->organisateur_id === $user->id;
    }

    public function delete(User $user, Tontine $tontine): bool
    {
        return $user->isOrganisateur()
            && $tontine->organisateur_id === $user->id;
    }
}
