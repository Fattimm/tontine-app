<?php

namespace App\Policies;

use App\Models\Tontine;
use App\Models\User;

class TontinePolicy
{
    // ✅ Admin voit tout
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdminOrOrganisateur();
    }

    public function view(User $user, Tontine $tontine): bool
    {
        // Organisateur voit ses tontines
        // Membre voit les tontines où il est inscrit
        if ($user->isOrganisateur()) return true;
        if ($user->isMembre() && $user->membre) {
            return $tontine->membres()->where('membre_id', $user->membre_id)->exists();
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdminOrOrganisateur();
    }

    public function update(User $user, Tontine $tontine): bool
    {
        return $user->isAdminOrOrganisateur();
    }

    public function delete(User $user, Tontine $tontine): bool
    {
        return $user->isAdminOrOrganisateur();
    }
}
