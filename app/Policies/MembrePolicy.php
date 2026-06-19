<?php

namespace App\Policies;

use App\Models\Membre;
use App\Models\User;

class MembrePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) return false; // Admin pas accès membres
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isOrganisateur();
    }

    public function view(User $user, Membre $membre): bool
    {
        if ($user->isOrganisateur()) return true;
        return $user->membre_id === $membre->id;
    }

    public function create(User $user): bool
    {
        return $user->isOrganisateur();
    }

    public function update(User $user, Membre $membre): bool
    {
        return $user->isOrganisateur();
    }

    public function delete(User $user, Membre $membre): bool
    {
        // Un organisateur ne peut supprimer que les membres de SES tontines
        return $user->isOrganisateur()
            && $membre->tontines()
                      ->where('organisateur_id', $user->id)
                      ->exists();
    }
}
