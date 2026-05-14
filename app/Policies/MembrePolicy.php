<?php

namespace App\Policies;

use App\Models\Membre;
use App\Models\User;

class MembrePolicy
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

    public function view(User $user, Membre $membre): bool
    {
        if ($user->isAdminOrOrganisateur()) return true;
        // Membre voit seulement son propre profil
        return $user->membre_id === $membre->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdminOrOrganisateur();
    }

    public function update(User $user, Membre $membre): bool
    {
        if ($user->isAdminOrOrganisateur()) return true;
        return $user->membre_id === $membre->id;
    }

    public function delete(User $user, Membre $membre): bool
    {
        return $user->isAdminOrOrganisateur();
    }
}