<?php

namespace App\Policies;

use App\Models\Tour;
use App\Models\User;

class TourPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isOrganisateur();
    }

    public function view(User $user, Tour $tour): bool
    {
        if ($user->isOrganisateur()) {
            return $tour->tontine->organisateur_id === $user->id;
        }
        if ($user->isMembre() && $user->membre) {
            return $tour->tontine->membres()
                ->where('membre_id', $user->membre_id)
                ->exists();
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isOrganisateur();
    }

    public function update(User $user, Tour $tour): bool
    {
        return $user->isOrganisateur()
            && $tour->tontine->organisateur_id === $user->id;
    }

    public function delete(User $user, Tour $tour): bool
    {
        return $user->isOrganisateur()
            && $tour->tontine->organisateur_id === $user->id;
    }

    public function confirmer(User $user, Tour $tour): bool
    {
        return $user->isOrganisateur()
            && $tour->tontine->organisateur_id === $user->id;
    }
}
